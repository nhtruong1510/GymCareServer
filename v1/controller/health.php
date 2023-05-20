<?php


require_once('db.php');
require_once('../model/Customer.php');
require_once('../model/Target.php');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // create food
    try {
        // check request's content type header is JSON
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            // set up response for unsuccessful request
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Content Type header not set to JSON");
            $response->send();
            exit;
        }

        // get POST request body as the POSTed data will be JSON format
        $rawPostData = file_get_contents('php://input');

        if (!$jsonData = json_decode($rawPostData)) {
            // set up response for unsuccessful request
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Request body is not valid JSON");
            $response->send();
            exit;
        }

        $customer_id = trim($jsonData->customer_id);
        $distance = trim($jsonData->distance);
        $sleep = trim($jsonData->sleep);
        $walk_number = trim($jsonData->walk_number);
        $excercise = trim($jsonData->excercise);
        $heart_rate = trim($jsonData->heart_rate);
        $customer_id = trim($jsonData->customer_id);

        $date = date('Y-m-d');

        $firstQuery = $readDb->prepare("SELECT * from health WHERE customer_id=:customer_id AND date = :date");

        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->bindParam(':date', $date, PDO::PARAM_STR);
        $firstQuery->execute();
        $rowCount = $firstQuery->rowCount();

        if ($rowCount === 0) {

            $query1 = $writeDb->prepare('insert into health (customer_id, distance, sleep, step, excercise, heart_rate, date) 
            values (:customer_id, :distance, :sleep, :walk_number, :excercise, :heart_rate, :date)');

            $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query1->bindParam(':distance', $distance, PDO::PARAM_STR);
            $query1->bindParam(':sleep', $sleep, PDO::PARAM_STR);
            $query1->bindParam(':walk_number', $walk_number, PDO::PARAM_STR);
            $query1->bindParam(':excercise', $excercise, PDO::PARAM_STR);
            $query1->bindParam(':heart_rate', $heart_rate, PDO::PARAM_STR);
            $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query1->bindParam(':date', $date, PDO::PARAM_STR);
            $query1->execute();
        } else {
            $query1 = $writeDb->prepare('UPDATE health SET distance = :distance, sleep = :sleep, step = :walk_number, excercise = :excercise, heart_rate = :heart_rate WHERE customer_id=:customer_id AND date=:date');
            $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query1->bindParam(':distance', $distance, PDO::PARAM_STR);
            $query1->bindParam(':sleep', $sleep, PDO::PARAM_STR);
            $query1->bindParam(':walk_number', $walk_number, PDO::PARAM_STR);
            $query1->bindParam(':excercise', $excercise, PDO::PARAM_STR);
            $query1->bindParam(':heart_rate', $heart_rate, PDO::PARAM_STR);
            $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query1->bindParam(':date', $date, PDO::PARAM_STR);
            $query1->execute();
        }

        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Tạo mục tiêu thành công");
        $response->setData(null);
        $response->send();

        exit;
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
        $customer_id = $_GET['customer_id'];
        $firstQuery = $readDb->prepare("SELECT * from health WHERE customer_id=:customer_id");
        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->execute();
        $rowCount = $firstQuery->rowCount();

        $healthArray = [];

        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $schedule = new Target([
                '_id' => $rowa['id'],
                '_walk_number' => $rowa['step'],
                '_sleep' => $rowa['sleep'],
                '_distance' => $rowa['distance'],
                '_excercise' => $rowa['excercise'],
                '_heart_rate' => $rowa['heart_rate'],
                '_date' => $rowa['date'],
                '_customer_id' => $rowa['customer_id'],

            ]);

            array_push($healthArray, $schedule->returnHealthAsArray());
        }

        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($healthArray);
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
