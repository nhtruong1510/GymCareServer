<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/SumMonth.php');
require_once('../model/Address.php');
require_once('../model/Response.php');

try {
    $writeDb = DB::connectWriteDb();
    $readDb = DB::connectReadDb();
} catch (PDOException $ex) {

    error_log("Connection Error: " . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database connection error");
    $response->send();
    exit;
}

// if request is a GET, e.g. get store
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // attempt to query the database
    try {
        // create db query
        $class_id = $_GET['class_id'];

        $firstQuery = $readDb->prepare("SELECT * from class WHERE bigClass_id=:class_id");
        $firstQuery->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $firstQuery->execute();

        $addressArray = [];
        $address_id = [];
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            array_push($address_id, $rowa['address_id']);
        }
        foreach ($address_id as $id) {
            $query = $readDb->prepare("SELECT * from address WHERE id=:id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $rowCount = $query->rowCount();
            while ($row0 = $query->fetch(PDO::FETCH_ASSOC)) {
                $address = new Address([
                    '_id' => $row0['id'],
                    '_longitude' => $row0['longitude'],
                    '_lattitude' => $row0['lattitude'],
                    '_address' => $row0['address'],
                    '_image' => $row0['image'],
                ]);
                $queryaa = $readDb->prepare('SELECT * FROM class WHERE bigClass_id=:class_id AND address_id=:id');
                $queryaa->bindParam(':class_id', $class_id, PDO::PARAM_INT);
                $queryaa->bindParam(':id', $id, PDO::PARAM_INT);
                $queryaa->execute();
                $dateArray = [];

                while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
                    $class = new ClassModel([
                        '_id' => $rowaa['id'],
                        '_name' => $rowaa['name'],
                        '_max_participate' => $rowaa['max_participate'],
                        '_current_participate' => $rowaa['current_participate'],
                        '_description' => $rowaa['description'],
                        '_benefit' => $rowaa['benefit'],
                        '_money' => $rowaa['money'],
                    ]);
                    $query1 = $readDb->prepare('SELECT * FROM date WHERE class_id=:class_id');
                    $query1->bindParam(':class_id', $rowaa['id'], PDO::PARAM_INT);
                    $query1->execute();

                    while ($row2 = $query1->fetch(PDO::FETCH_ASSOC)) {
                        $date0 = new Date([
                            '_id' => $row2['id'],
                            '_date' => $row2['date'],
                            '_day' => $row2['day'],
                        ]);
                        $timeArray = [];
                        $query2 = $readDb->prepare('SELECT * FROM time WHERE date_id=:date_id');
                        $query2->bindParam(':date_id', $row2['id'], PDO::PARAM_INT);
                        $query2->execute();
                        while ($row3 = $query2->fetch(PDO::FETCH_ASSOC)) {
                            $time = new Time([
                                '_id' => $row3['id'],
                                '_time' => $row3['time'],
                                '_trainer_id' => $row3['trainer_id']
                            ]);
                            array_push($timeArray, $time->returnTimeAsArray());
                        }
                        $date0->set_time($timeArray);
                        array_push($dateArray, $date0->returnDateAsArray());
                    }
                    // $query3 = $readDb->prepare('SELECT * FROM sumMonth WHERE id=:sum_month_id');
                    // $query3->bindParam(':sum_month_id', $rowaa['id'], PDO::PARAM_INT);
                    // $query3->execute();
                    // while ($row4 = $query3->fetch(PDO::FETCH_ASSOC)) {
                    //     $sumMonth = new SumMonth([
                    //         '_id' => $row4['id'],
                    //         '_number_month' => $row4['number_month'],
                    //     ]);
                    // }
                }
    
                $class->set_date_id($dateArray);
                // $class->set_sum_month($sumMonth->returnSumMonthAsArray());
                $address->set_class($class->returnClassAsArray());
                array_push($addressArray, $address->returnClassAsArray()); 
            }
        }

        $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['address'] = $addressArray;
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($returnData);
        $response->send();
        exit;
    } catch (PDOException $ex) {
        error_log("Database Query Error: " . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to get store");
        $response->send();
        exit;
    }
}
