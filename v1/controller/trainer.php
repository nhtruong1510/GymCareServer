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
        $trainer_id = $_GET['trainer_id'];
        // create db query

        $queryaa = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
        $queryaa->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $queryaa->execute();
        $rowCount = $queryaa->rowCount();

        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
            $user = new Trainer(
                [
                    '_name' => $rowaa['name'],
                    '_email' => $rowaa['email'],
                    '_phone' => $rowaa['phone'],
                    '_avatar' => $rowaa['avatar'],
                    '_gender' => $rowaa['gender'],
                    '_id' => $rowaa['id'],
                    '_certificate' => $rowaa['certificate'],
                    '_specialize' => $rowaa['specialize'],
                    '_experience' => $rowaa['experience'],
                ]
            );
        }

        $classArray[] = $user->returnUserInforAsArray();
        $returnData = array();
        $returnData['rows_returned'] = $rowCount;
        $returnData['trainer'] = $classArray;

        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setData($user->returnUserInforAsArray());
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
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

        $userId = $jsonData->id;
        $username = isset($jsonData->name) ? $jsonData->name : "";
        $phone = isset($jsonData->phone) ? $jsonData->phone : "";
        // $displayName = isset($jsonData->displayName) ? $jsonData->displayName : "";

        if (!isset($_FILES['avatarFile']) || $_FILES['avatarFile']['error'] !== 0) {
            $query = $writeDb->prepare('UPDATE trainer SET `name`=:username,`phone`=:phone where id=:user_id');
            $query->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
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
            $file_path = $_SERVER["DOCUMENT_ROOT"] . "/gymcare/user/" . $_FILES['avatarFile']['name'];
            if (move_uploaded_file($_FILES['avatarFile']['tmp_name'],  $file_path)) {
                $avatar = basename($file_path);
                $query = $writeDb->prepare('UPDATE trainer SET `name`=:username,`phone`=:phone,`avatar`=:avatar where id=:user_id');
                $query->bindParam(':user_id', $userId, PDO::PARAM_STR);
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':phone', $phone, PDO::PARAM_STR);
                $query->bindParam(':avatar', $avatar, PDO::PARAM_STR);
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


        // $query = $readDb->prepare('SELECT * from user where user_id = :userid');
        // $query->bindParam(':userid', $userId, PDO::PARAM_STR);
        // $query->execute();

        // // get row count
        // $rowCount = $query->rowCount();

        // if ($rowCount === 0) {
        //     // set up response for unsuccessful return
        //     $response = new Response();
        //     $response->setHttpStatusCode(404);
        //     $response->setSuccess(false);
        //     $response->addMessage("Đã xảy ra lỗi");
        //     $response->send();
        //     exit;
        // }
        // $userArray = array();
        // // for each row returned
        // while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        //     // create new food object for each row
        //     $user = new User(
        //         [
        //             '_username' => $row['username'],
        //             '_email' => $row['email'],
        //             '_phone' => $row['phone'],
        //             '_avatar' => $row['avatar'],
        //             '_displayName' => $row['display_name'],
        //             '_id' => $userId
        //         ]
        //     );
        //     $query1 = $readDb->prepare('SELECT cart.cart_id  from user INNER JOIN cart ON user.user_id = cart.user_id where user.user_id=:user_id');
        //     $query1->bindParam(':user_id', $userId, PDO::PARAM_INT);
        //     $query1->execute();
        //     while ($row1 = $query1->fetch(PDO::FETCH_ASSOC)) {
        //         $user->set_cartId($row1['cart_id']);
        //     }
        //     $userArray[] = $user->returnUserInforAsArray();
        // }
        // $returnData = array();

        // $returnData['rows_returned'] = $rowCount;
        // $returnData['user'] = $userArray;

        //set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Cập nhật thành công");
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
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
}
