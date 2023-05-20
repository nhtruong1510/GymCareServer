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
        $addressArray = [];
        $query = $readDb->prepare("SELECT * from address");
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
            array_push($addressArray, $address->returnClassAsArray()); 
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
