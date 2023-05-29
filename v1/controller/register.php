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
$phone = trim($jsonData->phone);
$name = trim($jsonData->name);
$password = $jsonData->password;

// attempt to query the database to check if username already exists
try {
    // create db query

    $query = $readDB->prepare('SELECT * from customer where phone=:phone');
    $query->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query->execute();

    // get row count
    $rowCount = $query->rowCount();


    if ($rowCount !== 0) {
        // set up response for username already exists
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(false);
        $response->addMessage("Đăng ký thất bại, số điện thoại đã tồn tại");
        $response->send();
        exit;
    }

    $date_create = date("Y-m-d");
    // create db query to create user
    $query = $writeDB->prepare('INSERT into customer(phone, name, password, date_create) values (:phone, :name, :password, :date_create)');
    $query->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':date_create', $date_create, PDO::PARAM_STR);
    $query->execute();


    // get user after created
    $query = $readDB->prepare('SELECT * from customer where phone=:phone');
    $query->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query->execute();

    // get row count
    $rowCount = $query->rowCount();

    // if ($rowCount === 0) {
    //     // set up response for error
    //     $response = new Response();
    //     $response->setHttpStatusCode(500);
    //     $response->setSuccess(false);
    //     $response->addMessage("There was an error creating the user account - please try again");
    //     $response->send();
    //     exit;
    // }

    // build response data array which contains basic user details
    $returnData = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        // create new cate object for each row
        $returnData['id'] = $row['id'];
        $returnData['name'] = $row['name'];
        $returnData['phone'] = $row['phone'];
        // $query = $writeDB->prepare('INSERT into cart(user_id) values (:user_id)');
        // $query->bindParam(':user_id', $row['user_id'], PDO::PARAM_INT);
        // $query->execute();
    }

    $response = new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Đăng ký thành công!");
    $response->setData($returnData);
    $response->send();
    exit;
} catch (PDOException $ex) {
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Đã có lỗi, xin thử lại!");
    $response->send();
    exit;
}
