<?php


require_once('db.php');
require_once('../model/Customer.php');
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
        $queryaa = $readDb->prepare('SELECT * FROM customer');
        $queryaa->execute();
        $classArray = [];
        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
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
            array_push($classArray, $user->returnUserInforAsArray());
        }

        $returnData = array();
        // $returnData['customer'] = $classArray;

        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($classArray);
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