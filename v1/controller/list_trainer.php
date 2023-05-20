<?php


require_once('db.php');
require_once('../model/Trainer.php');
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

        $queryaa = $readDb->prepare('SELECT * FROM trainer');
        $queryaa->execute();
        $classArray = [];
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
            array_push($classArray, $user->returnUserInforAsArray());
        }

        $returnData = array();
        // $returnData['trainer'] = $classArray;

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
