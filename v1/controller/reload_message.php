<?php


require_once('db.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Message.php');
require_once('../model/Response.php');
require_once('../model/Chat.php');

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
        $chat_id = $_GET['chat_id'];
        $ins_datetime = $_GET['ins_datetime'];
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from message WHERE chat_id=:chat_id AND ins_datetime>:ins_datetime");
        $firstQuery->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
        $firstQuery->bindParam(':ins_datetime', $ins_datetime, PDO::PARAM_STR);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $message = new Message([
                '_id' => $rowa['id'],
                '_content' => $rowa['content'],
                '_image' => $rowa['image'],
                '_ins_datetime' => $rowa['ins_datetime'],
                '_is_customer' => $rowa['is_customer'],
            ]);

            array_push($notificationArray, $chat->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['messages'] = $notificationArray;
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
