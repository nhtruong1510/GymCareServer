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
        $trainer_id = $_GET['trainer_id'];
        $scheduleArray = [];
        $date_ids = [];
        $time_ids = [];
        $class_ids = [];

        $query0 = $readDb->prepare("SELECT * from time WHERE trainer_id=:trainer_id AND max_participate IS NOT NULL");
        $query0->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $query0->execute();
        while ($row = $query0->fetch(PDO::FETCH_ASSOC)) {
            array_push($date_ids, $row['date_id']);    
            array_push($time_ids, $row['id']);    
        }
        $date_ids = array_unique($date_ids);

        $current_date = date("Y-m-d");

        foreach($date_ids as $key=>$date_id) {
            $firstQuery = $readDb->prepare("SELECT * from date WHERE id=:date_id");
            $firstQuery->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $firstQuery->execute();
            while ($row = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
                $class_id = $row['class_id'];
                $date = $row['day'];
                array_push($class_ids, $row['class_id']);    
            }
        }
        $class_ids = array_unique($class_ids);

        foreach($class_ids as $key=>$class_id) {
            $schedule = new Schedule([]);
            // echo $date_id;

            $query1 = $readDb->prepare("SELECT * from class WHERE id=:class_id");
            $query1->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query1->execute();
            while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
                $address_id = $row['address_id'];
                $class = new ClassModel([
                    '_id' => $row['id'],
                    '_name' => $row['name'],
                ]);
            }
            $schedule->set_class($class->returnClassAsArray());

            $query2 = $readDb->prepare("SELECT * from address WHERE id=:address_id");
            $query2->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query2->execute();
            while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                $address = new Address([
                    '_id' => $row['id'],
                    '_address' => $row['address'],
                    '_image' => $row['image'],
                ]);
            }
            $schedule->set_address($address->returnClassAsArray());

            $timeArray = [];
            foreach($time_ids as $keyTime=>$time_id) {
                $querya = $readDb->prepare("SELECT * from time WHERE id=:time_id");
                $querya->bindParam(':time_id', $time_id, PDO::PARAM_INT);
                $querya->execute();
                while ($row = $querya->fetch(PDO::FETCH_ASSOC)) {
                    $date_id = $row['date_id'];   
                }
        
                $firstQuery = $readDb->prepare("SELECT * from date WHERE id=:date_id");
                $firstQuery->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                $firstQuery->execute();
                while ($row = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
                    $date = $row['day'];
                }

                $query = $readDb->prepare('SELECT * FROM schedule WHERE time_id=:time_id AND end_date > :current_date');
                $query->bindParam(':current_date', $current_date, PDO::PARAM_STR);
                $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
                $query->execute();
                $rowCount = $query->rowCount();
                $time = new Time([]);
                if ($rowCount > 0) {
                    $userArray = [];
                    $userIds = [];
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $customer_id = $row['customer_id'];  
                        array_push($userIds, $row['customer_id']);
                    }
                    $userIds = array_unique($userIds);

                    foreach ($userIds as $customer_id) {
                        $query0 = $readDb->prepare('SELECT * FROM customer WHERE id=:customer_id');
                        $query0->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
                        $query0->execute();
                        while ($rowaa = $query0->fetch(PDO::FETCH_ASSOC)) {
                            $user = new Customer(
                                [
                                    '_name' => $rowaa['name'],
                                    '_email' => $rowaa['email'],
                                    '_phone' => $rowaa['phone'],
                                    '_avatar' => $rowaa['avatar'],
                                    '_birth' => $rowaa['birth'],
                                    '_address' => $rowaa['address'],
                                    '_gender' => $rowaa['gender'],
                                    '_id' => $rowaa['id']
                                ]
                            );
                            array_push($userArray, $user->returnUserInforAsArray());
                        }
                        $time->set_customer($userArray);
                    }
                }
                $queryaa = $readDb->prepare('SELECT * FROM time WHERE id=:time_id');
                $queryaa->bindParam(':time_id', $time_id, PDO::PARAM_INT);
                $queryaa->execute();
                while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
                    $timeStr = $rowaa['time'];
                }
                // echo $time_id;
                $time->set_time($timeStr);
                $time->set_date($date);
                $time->set_id($time_id);
                array_push($timeArray, $time->returnTimeAsClass());
            }
            $schedule->set_time($timeArray);
            array_push($scheduleArray, $schedule->returnScheduleAsArray());

        }



        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['schedules'] = $scheduleArray;
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
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
