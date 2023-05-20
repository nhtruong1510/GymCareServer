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
if (array_key_exists("id", $_GET)) {
    // get food id from query string
    $userid = $_GET['id'];

    //check to see if food id in query string is not empty and is number, if not return json error
    // if ($userid == '' || !is_numeric($userid)) {
    //     $response = new Response();
    //     $response->setHttpStatusCode(400);
    //     $response->setSuccess(false);
    //     $response->addMessage("Cart ID cannot be blank or must be numeric");
    //     $response->send();
    //     exit;
    // }

    // if request is a GET, e.g. get food
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // attempt to query the database
        try {
            $query = $readDb->prepare('SELECT * from customer where id = :userid');
            $query->bindParam(':userid', $userid, PDO::PARAM_INT);
            $query->execute();

            // get row count
            $rowCount = $query->rowCount();

            // create food array to store returned food
            $foodArray = array();

            if ($rowCount === 0) {
                // set up response for unsuccessful return
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Food not found");
                $response->send();
                exit;
            }
            $userArray = array();
            // for each row returned
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                // create new food object for each row
                $user = new Customer(
                    [
                        '_id' => $row['id'],
                        '_name' => $row['name'],
                        '_avatar' => $row['avatar'],
                        '_gender' => $row['gender'],
                    ]
                );
                // $userArray[] = $user->returnUserInforAsArray();
            }
            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['user'] = $user->returnUserInforAsArray();

            // set up response for successful return
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("");
            $response->setData($returnData);
            $response->send();
            exit;
        } catch (PDOException $ex) {
            error_log("Database Query Error: " . $ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Lỗi rồi");
            $response->send();
            exit;
        }
    }
    // // else if request if a DELETE e.g. delete food
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // // attempt to query the database
        try {

            if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                // set up response for unsuccessful request
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Content Type header not set to JSON");
                $response->send();
                exit;
            }

            // get PATCH request body as the PATCHed data will be JSON format
            $rawPatchData = file_get_contents('php://input');

            if (!$jsonData = json_decode($rawPatchData)) {
                // set up response for unsuccessful request
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit;
            }
            $foodId = $jsonData->foodId;
            // create db query
            $query1 = $writeDb->prepare('DELETE from cartdetail where cart_id = :userid AND food_id =:food_id');
            $query1->bindParam(':userid', $userid, PDO::PARAM_INT);
            $query1->bindParam(':food_id', $foodId, PDO::PARAM_INT);
            $query1->execute();

            $rowCount = $query1->rowCount();

            if ($rowCount === 0) {
                // set up response for unsuccessful return
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Không tìm thấy đơn hàng");
                $response->send();
                exit;
            }

            // set up response for successful return
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Đã xóa đơn đặt hàng");
            $response->send();
            exit;
        }
        // if error with sql query return a json error
        catch (PDOException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to delete food order");
            $response->send();
            exit;
        }
    }

    //     // // if any other request method apart from GET, PATCH, DELETE is used then return 405 method not allowed
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
} elseif (empty($_GET)) {
    // if request is a GET e.g. get stores
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // create food
        try {
            // check request's content type header is JSON
            if (!isset($_SERVER['CONTENT_TYPE']) || strpos($_SERVER['CONTENT_TYPE'], "multipart/form-data; boundary=") === false) {
                // set up response for unsuccessful request
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Content Type header not set to form-data");
                $response->send();
                exit;
            }
            if (!isset($_POST['data'])) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Attributes missing from body of request");
                $response->send();
                exit;
            }

            // get POST request body as the POSTed data will be JSON format
            // $rawPostData = file_get_contents('php://input');

            if (!$jsonData = json_decode($_POST['data'])) {
                // set up response for unsuccessful request
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit;
            }

            $userId = $jsonData->userId;
            $username = isset($jsonData->username) ? $jsonData->username : "";
            $email = isset($jsonData->email) ? $jsonData->email : "";

            $displayName = isset($jsonData->displayName) ? $jsonData->displayName : "";

            if (!isset($_FILES['imageFile']) || $_FILES['imageFile']['error'] !== 0) {
                $query = $writeDb->prepare('UPDATE user SET `username`=:username,`email`=:email,`display_name`=:displayName where user_id=:user_id');
                $query->bindParam(':user_id', $userId, PDO::PARAM_STR);
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':displayName', $displayName, PDO::PARAM_STR);
                $query->execute();
                $rowCount = $query->rowCount();

                // if ($rowCount === 0) {
                //     $response = new Response();
                //     $response->setHttpStatusCode(404);
                //     $response->setSuccess(false);
                //     $response->addMessage("Không có gì thay đổi >.<");
                //     $response->send();
                //     exit;
                // }

            } else {
                $file_path = $_SERVER["DOCUMENT_ROOT"] . "/android/myfoodee/user/" . $_FILES['imageFile']['name'];
                if (move_uploaded_file($_FILES['imageFile']['tmp_name'],  $file_path)) {
                    $avatar = basename($file_path);
                    $query = $writeDb->prepare('UPDATE user SET `username`=:username,`email`=:email,`avatar`=:avatar,`display_name`=:displayName where user_id=:user_id');
                    $query->bindParam(':user_id', $userId, PDO::PARAM_STR);
                    $query->bindParam(':username', $username, PDO::PARAM_STR);
                    $query->bindParam(':email', $email, PDO::PARAM_STR);
                    $query->bindParam(':avatar', $avatar, PDO::PARAM_STR);
                    $query->bindParam(':displayName', $displayName, PDO::PARAM_STR);
                    $query->execute();
                    $rowCount = $query->rowCount();

                    // if ($rowCount === 0) {
                    //     $response = new Response();
                    //     $response->setHttpStatusCode(404);
                    //     $response->setSuccess(false);
                    //     $response->addMessage("Không có gì thay đổi >.<");
                    //     $response->send();
                    //     exit;
                    // }
                } else {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("Đã xảy ra lỗi");
                    $response->send();
                    exit;
                }
            }


            $query = $readDb->prepare('SELECT * from user where user_id = :userid');
            $query->bindParam(':userid', $userId, PDO::PARAM_STR);
            $query->execute();

            // get row count
            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                // set up response for unsuccessful return
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Đã xảy ra lỗi");
                $response->send();
                exit;
            }
            $userArray = array();
            // for each row returned
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                // create new food object for each row
                $user = new User(
                    [
                        '_username' => $row['username'],
                        '_email' => $row['email'],
                        '_phone' => $row['phone'],
                        '_avatar' => $row['avatar'],
                        '_displayName' => $row['display_name'],
                        '_id' => $userId
                    ]
                );
                $query1 = $readDb->prepare('SELECT cart.cart_id  from user INNER JOIN cart ON user.user_id = cart.user_id where user.user_id=:user_id');
                $query1->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $query1->execute();
                while ($row1 = $query1->fetch(PDO::FETCH_ASSOC)) {
                    $user->set_cartId($row1['cart_id']);
                }
                $userArray[] = $user->returnUserInforAsArray();
            }
            $returnData = array();

            $returnData['rows_returned'] = $rowCount;
            $returnData['user'] = $userArray;

            //set up response for successful return
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Cập nhật thành công");
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
