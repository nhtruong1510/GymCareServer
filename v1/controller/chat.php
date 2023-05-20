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
        $customer_id = $_GET['customer_id'];
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from chat WHERE customer_id=:customer_id");
        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $chat_id = $rowa['id'];
            $trainer_id = $rowa['trainer_id'];

            $chat = new Chat([
                '_id' => $rowa['id'],
                '_is_read_customer' => $rowa['is_read_customer'],
                '_ins_datetime' => $rowa['ins_datetime'],
            ]);
            $query = $readDb->prepare('SELECT * FROM message WHERE chat_id=:chat_id');
            $query->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
            $query->execute();
            $last_message = "";
            $ins_datetime = "";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $last_message = $row['content'];
                $ins_datetime = $row['ins_datetime'];
            }
            $chat->set_last_message($last_message);
            $chat->set_ins_datetime($ins_datetime);

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
            $chat->set_trainer($user->returnUserInforAsArray());

            array_push($notificationArray, $chat->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['chats'] = $notificationArray;
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
