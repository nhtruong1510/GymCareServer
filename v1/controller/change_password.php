<?php

require_once('db.php');
require_once('../model/User.php');
require_once('../model/Response.php');

// attempt to set up connections to read and write db connections
try {
    $writeDb = DB::connectWriteDb();
    $readDb = DB::connectReadDb();
} catch (PDOException $ex) {
    // log connection error for troubleshooting and return a json error response
    error_log("Connection Error: " . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database connection error");
    $response->send();
    exit;
}

// within this if/elseif statement, it is important to get the correct order (if query string GET param is used in multiple routes)
// check if userid is in the url e.g. /foods/1

// check if userid is in the url e.g. /foods/1
if (empty($_GET)) {
    // if request is a GET e.g. get stores
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // create food
        try {
            // check request's content type header is JSON
            if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {

                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Content Type header not set to JSON");
                $response->send();
                exit;
            }


            $rawPostData = file_get_contents('php://input');

            if (!$jsonData = json_decode($rawPostData)) {

                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit;
            }

            $userId = $jsonData->userId;
            $password = $jsonData->password;
            $newPass = $jsonData->newPass;

            $query0 = $readDb->prepare('SELECT password from user where user_id = :userid');
            $query0->bindParam(':userid', $userId, PDO::PARAM_STR);
            $query0->execute();
            while ($row = $query0->fetch(PDO::FETCH_ASSOC)) {
                if ($password != $row['password']) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("Mật khẩu cũ không chính xác");
                    $response->send();
                    exit;
                }
            }

            $query = $writeDb->prepare('UPDATE user SET `password`=:password where user_id=:user_id');
            $query->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $query->bindParam(':password', $newPass, PDO::PARAM_STR);
            $query->execute();
            $rowCount = $query->rowCount();

            // get row count
            $rowCount = $query->rowCount();

            $returnData = array();

            $returnData['rows_returned'] = $rowCount;
            $returnData['user'] = null;

            //set up response for successful return
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Đổi mật khẩu thành công");
            $response->setData($returnData);
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
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
}
