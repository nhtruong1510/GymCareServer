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
        $firstQuery = $readDb->prepare("SELECT * from schedule");
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $schedule_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $trainer_id = $rowa['trainer_id'];
            $address_id = $rowa['address_id'];
            $customer_id = $rowa['customer_id'];
            $schedule = new Schedule([
                '_id' => $rowa['id'],
                '_start_date' => $rowa['start_date'],
                '_end_date' => $rowa['end_date'],
                '_day' => $rowa['day'],
                '_time' => $rowa['time'],
            ]);


            $query = $readDb->prepare('SELECT * FROM address WHERE id=:address_id');
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $addressName = $row['address'];
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
                $className = $row0['name'];
                $class = new ClassModel([
                    '_id' => $row0['id'],
                    '_name' => $row0['name'],
                    // '_max_participate' => $rowaa['max_participate'],
                    // '_current_participate' => $rowaa['current_participate'],
                    // '_description' => $rowaa['description'],
                    // '_benefit' => $rowaa['benefit'],
                ]);
            }

            $query2 = $readDb->prepare('SELECT * FROM customer WHERE id=:customer_id');
            $query2->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query2->execute();
            while ($row3 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $customerName = $row3['name'];
                $customer = new Customer([
                    '_id' => $row3['id'],
                    '_name' => $row3['name'],
                    '_avatar' => $row3['avatar'],
                    '_gender' => $row3['gender'],
                ]);
            }

            $schedule->set_customer($customerName);
            $schedule->set_class($className);
            $schedule->set_address($addressName);

            if ($trainer_id != null) {
                $query3 = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
                $query3->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                $query3->execute();
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                    $trainerName = $row3['name'];
                    $trainer = new Trainer([
                        '_id' => $row3['id'],
                        '_name' => $row3['name'],
                        '_avatar' => $row3['avatar'],
                        '_gender' => $row3['gender'],
                    ]);
                }

                $schedule->set_trainer($trainerName);
            }

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