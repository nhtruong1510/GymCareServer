<?php

require_once('db.php');
require_once('../model/Response.php');

// note: never cache user http requests/responses
// (our response model defaults to no cache unless specifically set)

// attempt to set up connections to db connections
try {

    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
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

// handle creating new user
// check to make sure the request is POST only - else exit with error response
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
}

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

// trim any leading and trailing blank spaces from full name and username only - password may contain a leading or trailing space
$account = trim($jsonData->account);
$password = $jsonData->password;

// attempt to query the database to check if username already exists
try {
    // create db query

    $query = $readDB->prepare('SELECT * from user where phone=:account AND password=:password');
    $query->bindParam(':account', $account, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();

    // get row count
    $rowCount = $query->rowCount();


    if ($rowCount === 0) {
        // set up response for username already exists
        $response = new Response();
        $response->setHttpStatusCode(409);
        $response->setSuccess(false);
        $response->addMessage("Tài khoản hoặc mật khẩu không chính xác");
        $response->send();
        exit;
    }

    $returnData = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        // create new cate object for each row
        $returnData['user_id'] = $row['user_id'];
        $returnData['username'] = $row['username'];
        $returnData['display_name'] = $row['display_name'];
        $returnData['phone'] = $row['phone'];
        $query1 = $readDB->prepare('SELECT * from owner where user_id=:user_id');
        $query1->bindParam(':user_id', $row['user_id'], PDO::PARAM_STR);
        $query1->execute();

       

        $row1 = $query1->rowCount();
        if($row1 !== 0){
            $returnData['is_manage'] = true;
            $query2 = $readDB->prepare('SELECT store_id from owner where user_id=:user_id');
            $query2->bindParam(':user_id', $row['user_id'], PDO::PARAM_STR);
            $query2->execute();
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $returnData['store_id'] = $row2['store_id'];
            }
        } else {
            $returnData['is_manage'] = false;
        }
    }
    $response = new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Đăng nhập thành công");
    $response->setData($returnData);
    $response->send();
    exit;
} catch (PDOException $ex) {
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Đã xảy ra lỗi");
    $response->send();
    exit;
}
