<?php

require_once('db.php');
require_once('Response.php');

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

// check if post request contains full name, username and password in body as they are mandatory
// if(!isset($jsonData->fullname) || !isset($jsonData->username) || !isset($jsonData->password)) {
//   $response = new Response();
//   $response->setHttpStatusCode(400);
//   $response->setSuccess(false);
//   // add message to message array where necessary
//   (!isset($jsonData->fullname) ? $response->addMessage("Full name not supplied") : false);
//   (!isset($jsonData->username) ? $response->addMessage("Username not supplied") : false);
//   (!isset($jsonData->password) ? $response->addMessage("Password not supplied") : false);
//   $response->send();
//   exit;
// }

// // check to make sure that full name username and password are not empty and less than 255 long
// if(strlen($jsonData->fullname) < 1 || strlen($jsonData->fullname) > 255 || strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 100) {
//   $response = new Response();
//   $response->setHttpStatusCode(400);
//   $response->setSuccess(false);
//   (strlen($jsonData->fullname) < 1 ? $response->addMessage("Full name cannot be blank") : false);
//   (strlen($jsonData->fullname) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
//   (strlen($jsonData->username) < 1 ? $response->addMessage("Username cannot be blank") : false);
//   (strlen($jsonData->username) > 255 ? $response->addMessage("Username cannot be greater than 255 characters") : false);
//   (strlen($jsonData->password) < 1 ? $response->addMessage("Password cannot be blank") : false);
//   (strlen($jsonData->password) > 100 ? $response->addMessage("Password cannot be greater than 100 characters") : false);
//   $response->send();
//   exit;
// }

// trim any leading and trailing blank spaces from full name and username only - password may contain a leading or trailing space
$phone = trim($jsonData->phone);
$password = $jsonData->password;

// attempt to query the database to check if username already exists
try {
  // create db query

  $query = $readDB->prepare('SELECT * from trainer where phone=:phone OR email=:email AND password=:password');
  $query->bindParam(':email', $phone, PDO::PARAM_STR);
  $query->bindParam(':phone', $phone, PDO::PARAM_STR);
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

  //   // create db query to create user
  //   $query = $writeDB->prepare('INSERT into tblusers (fullname, username, password) values (:fullname, :username, :password)');
  //   $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
  //   $query->bindParam(':username', $username, PDO::PARAM_STR);
  //   $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
  //   $query->execute();

  //   // get row count
  //   $rowCount = $query->rowCount();

  //   if($rowCount === 0) {
  //     // set up response for error
  //     $response = new Response();
  //     $response->setHttpStatusCode(500);
  //     $response->setSuccess(false);
  //     $response->addMessage("There was an error creating the user account - please try again");
  //     $response->send();
  //     exit;
  //   }

  //   // get last user id so we can return the user id in the json
  //   $lastUserID = $writeDB->lastInsertId();

  // build response data array which contains basic user details

  $returnData = array();
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    // create new cate object for each row
    $returnData['id'] = $row['id'];
    $returnData['name'] = $row['name'];
    $returnData['phone'] = $row['phone'];
    $returnData['email'] = $row['email'];
    $returnData['avatar'] = $row['avatar'];
    $returnData['address'] = $row['address'];
    $returnData['gender'] = $row['gender'];
    $returnData['birth'] = $row['birth'];

    // $query1 = $readDB->prepare('SELECT * from customer where customer.id=:id');
    // $query1->bindParam(':id', $row['id'], PDO::PARAM_STR);
    // $query1->execute();
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
