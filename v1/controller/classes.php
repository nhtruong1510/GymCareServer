<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Schedule.php');
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $scheduleArray = [];
        $query0 = $readDb->prepare('SELECT * FROM class');
        $query0->execute();
        while ($row0 = $query0->fetch(PDO::FETCH_ASSOC)) {
            $className = $row0['name'];
            $class = new ClassModel([
                '_id' => $row0['id'],
                '_name' => $row0['name'],
                '_max_participate' => $row0['max_participate'],
                '_current_participate' => $row0['current_participate'],
                '_money' => $row0['money'],
                // '_description' => $rowaa['description'],
                // '_benefit' => $rowaa['benefit'],
            ]);
            $dateArray = "";
            $timeArray = "";

            $query1 = $readDb->prepare('SELECT * FROM date WHERE class_id=:class_id');
            $query1->bindParam(':class_id', $row0['id'], PDO::PARAM_INT);
            $query1->execute();

            while ($row2 = $query1->fetch(PDO::FETCH_ASSOC)) {
                $date0 = new Date([
                    '_id' => $row2['id'],
                    '_date' => $row2['date'],
                    '_day' => $row2['day'],
                ]);
                $date_id = $row2['id'];
                $dateArray .= $row2['day']."\n";
            }
            $query2 = $readDb->prepare('SELECT * FROM time WHERE date_id=:date_id');
            $query2->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query2->execute();
            while ($row3 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $time = new Time([
                    '_id' => $row3['id'],
                    '_time' => $row3['time'],
                    '_trainer_id' => $row3['trainer_id']
                ]);
                $timeArray .= $row3['time']."\n";
            }
            $date0->set_time($timeArray);
            $address_id = $row0['address_id'];
            $query = $readDb->prepare('SELECT * FROM address WHERE id=:address_id');
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $addressName = $row['address'];
            }
            $class->set_time($timeArray);
            $class->set_date($dateArray);
            $class->set_address($addressName);
            array_push($scheduleArray, $class->returnListClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['schedules'] = $scheduleArray;
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($scheduleArray);
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