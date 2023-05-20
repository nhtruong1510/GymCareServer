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
require_once('../model/Payment.php');

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
        $customer_id = $_GET['customer_id'];
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from payment WHERE customer_id=:customer_id");
        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $notification_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $trainer_id = $rowa['trainer_id'];
            $address_id = $rowa['address_id'];

            $notification = new Payment([
                '_id' => $rowa['id'],
                '_day' => $rowa['day'],
                '_date' => $rowa['date'],
                '_time' => $rowa['time'],
                '_money' => $rowa['money'],
                '_date_create' => $rowa['date_create'],
            ]);
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
            $notification->set_address($address->returnClassAsArray());

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
            $notification->set_class($class->returnClassAsArray());

            if ($trainer_id != "") {
                $queryaa = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
                $queryaa->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                $queryaa->execute();
    
                while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
                    $user = new Trainer(
                        [
                            '_name' => $rowaa['name'],
                            '_email' => $rowaa['email'],
                            '_phone' => $rowaa['phone'],
                            '_avatar' => $rowaa['avatar'],
                            '_id' => $rowaa['id']
                        ]
                    );
                }
                $notification->set_trainer($user->returnUserInforAsArray());
            }

            array_push($notificationArray, $notification->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['payments'] = $notificationArray;
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
