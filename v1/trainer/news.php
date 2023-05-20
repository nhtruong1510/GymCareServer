<?php


require_once('db.php');
require_once('../model/Response.php');
require_once('../model/News.php');

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
        $role = 2;
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from news WHERE role = :role");
        $firstQuery->bindParam(':role', $role, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $chat = new News([
                '_id' => $rowa['id'],
                '_image' => $rowa['image'],
                '_title' => $rowa['title'],
                '_url' => $rowa['url'],
                '_role' => $rowa['role'],
            ]);

            array_push($notificationArray, $chat->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['chats'] = $notificationArray;
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($notificationArray);
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
