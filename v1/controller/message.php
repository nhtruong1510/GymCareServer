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
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from message WHERE chat_id=:chat_id");
        $firstQuery->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $message = new Message([
                '_id' => $rowa['id'],
                '_content' => $rowa['content'],
                '_image' => $rowa['image'],
                '_ins_datetime' => $rowa['ins_datetime'],
                '_is_customer' => $rowa['is_customer'],
            ]);

            array_push($notificationArray, $message->returnClassAsArray());
        }

        $is_read = 1;
        $query2 = $writeDb->prepare('UPDATE chat SET is_read_customer = :is_read WHERE id=:chat_id');
        $query2->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
        $query2->bindParam(':is_read', $is_read, PDO::PARAM_INT);
        $query2->execute();

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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $chat_id = trim($jsonData->chat_id);
        $content = trim($jsonData->content);
        $ins_datetime = trim($jsonData->ins_datetime);

        $is_read = 0;
        $is_customer = 1;

        $query = $writeDb->prepare('insert into message (chat_id, content, is_customer, ins_datetime) 
        values (:chat_id, :content, :is_customer, STR_TO_DATE(:ins_datetime, \'%d/%m/%Y %H:%i:%s\'))');
        $query->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $query->bindParam(':ins_datetime', $ins_datetime, PDO::PARAM_STR);
        $query->execute();

        $query2 = $writeDb->prepare('UPDATE chat SET is_read_trainer = :is_read WHERE id=:chat_id');
        $query2->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
        $query2->bindParam(':is_read', $is_read, PDO::PARAM_INT);

        $query2->execute();

        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Gửi thành công");
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
} else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}
