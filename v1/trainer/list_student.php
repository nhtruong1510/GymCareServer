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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $time_id = $_GET['time_id'];
        $is_cancelled = $_GET['is_cancelled'];
        $scheduleArray = [];
        $query0 = $readDb->prepare("SELECT * from time WHERE id=:time_id");
        $query0->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $query0->execute();
        while ($row = $query0->fetch(PDO::FETCH_ASSOC)) {
            $date_id = $row['date_id'];
            // $is_cancelled = $row['is_cancelled'];     
        }

        $firstQuery = $readDb->prepare("SELECT * from date WHERE id=:date_id");
        $firstQuery->bindParam(':date_id', $date_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $schedule_id = $rowa['schedule_id'];    

            $query = $readDb->prepare('SELECT * FROM schedule WHERE id=:schedule_id');
            $query->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $customer_id = $row['customer_id'];    
            }

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
            }

            $user->set_is_cancelled($is_cancelled);

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
            array_push($scheduleArray, $user->returnUserInforCancelAsArray());

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
        $response->toCache(true);
        $response->setData($user->returnUserInforCancelAsArray());
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
