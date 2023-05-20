<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Schedule.php');
require_once('../model/Address.php');
require_once('Response.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // create food
    try {

    } catch (PDOException $ex) {
        error_log("Database Query Error: " . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to insert food into database - check submitted data for errors");
        $response->send();
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $trainer_id = $_GET['trainer_id'];
        $scheduleArray = [];
        $firstQuery = $readDb->prepare("SELECT * from schedule WHERE trainer_id=:trainer_id");
        $firstQuery->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $schedule_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            // $trainer_id = $rowa['trainer_id'];
            $address_id = $rowa['address_id'];

            $schedule = new Schedule(['_id' => $rowa['id']]);


            $query = $readDb->prepare('SELECT * FROM address WHERE id=:address_id');
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $address = new Address([
                    '_id' => $row['id'],
                    '_address' => $row['address'],
                    // '_max_participate' => $rowaa['max_participate'],
                    // '_current_participate' => $rowaa['current_participate'],
                    // '_description' => $rowaa['description'],
                    // '_benefit' => $rowaa['benefit'],
                ]);
            }

            $query0 = $readDb->prepare('SELECT * FROM class WHERE id=:class_id');
            $query0->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query0->execute();
            while ($row0 = $query0->fetch(PDO::FETCH_ASSOC)) {
                $class = new ClassModel([
                    '_id' => $row0['id'],
                    '_name' => $row0['name'],
                    // '_max_participate' => $rowaa['max_participate'],
                    // '_current_participate' => $rowaa['current_participate'],
                    // '_description' => $rowaa['description'],
                    // '_benefit' => $rowaa['benefit'],
                ]);
            }
        
            $query1 = $readDb->prepare('SELECT * FROM date WHERE schedule_id=:schedule_id');
            $query1->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $query1->execute();
            $dateArray = [];

            while ($row2 = $query1->fetch(PDO::FETCH_ASSOC)) {
                $date0 = new Date([
                    '_id' => $row2['id'],
                    '_date' => $row2['date'],
                    '_day' => $row2['day'],
                ]);
                $query2 = $readDb->prepare('SELECT * FROM time WHERE date_id=:date_id');
                $query2->bindParam(':date_id', $row2['id'], PDO::PARAM_INT);
                $query2->execute();
                while ($row3 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $time = new Time([
                        '_id' => $row3['id'],
                        '_time' => $row3['time'],
                        '_is_cancelled' => $row3['is_cancelled'],
                        '_class' => $class->get_name(), 
                        '_address' => $address->get_address(), 
                    ]);
                }
                $date0->set_time($time->returnTimeAsArray());
                array_push($dateArray, $date0->returnDateAsArray());
            }

            $schedule->set_date($dateArray);
            $schedule->set_class($class->returnOnlyClassAsArray());

            // $query3 = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
            // $query3->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            // $query3->execute();
            // while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
            //     $trainer = new Trainer([
            //         '_id' => $row3['id'],
            //         '_name' => $row3['name'],
            //         '_avatar' => $row3['avatar'],
            //         '_gender' => $row3['gender'],
            //     ]);
            // }

            // $schedule->set_trainer($trainer->returnUserInforAsArray());
            array_push($scheduleArray, $schedule->returnClassAsArray());

            // $query4 = $readDb->prepare('SELECT * FROM bigClass WHERE id=:class_id');
            // $query4->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            // $query4->execute();
            // while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {
            //     $trainer = new Trainer([
            //         '_id' => $row4['id'],
            //         '_name' => $row4['name'],
            //     ]);
            // }
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['schedules'] = $scheduleArray;
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
} else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}
