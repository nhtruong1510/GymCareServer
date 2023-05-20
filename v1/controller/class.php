<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/SumMonth.php');
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
        // create db query
        $query = $readDb->prepare("SELECT * from bigClass");
        $query->execute();
        $rowCount = $query->rowCount();

        $queryaa = $readDb->prepare('SELECT * FROM bigClass');
        $queryaa->execute();

        $classArray = [];
    
        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
            $class = new ClassModel([
                '_id' => $rowaa['id'],
                '_name' => $rowaa['name'],
                '_description' => $rowaa['description'],
                '_benefit' => $rowaa['benefit'],
                '_image' => $rowaa['image'],

                // '_max_participate' => $rowaa['max_participate'],
                // '_current_participate' => $rowaa['current_participate'],
                // '_description' => $rowaa['description'],
                // '_benefit' => $rowaa['benefit'],
            ]);
            array_push($classArray, $class->returnOnlyClassAsArray()); 
        }

        // $date0->set_time($time->returnTimeAsArray());
        // $class->set_date_id($date0->returnDateAsArray());
        // $class->set_sum_month($sumMonth->returnSumMonthAsArray());
        $returnData = array();
        $returnData['rows_returned'] = $rowCount;
        $returnData['classes'] = $classArray;

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

        $name = trim($jsonData->name);
        $money = trim($jsonData->money);
        $address_id = trim($jsonData->address_id);
        $bigClass_id = trim($jsonData->bigClass_id);
        $max_participate = trim($jsonData->max_participate);
        $date = trim($jsonData->date);
        $time = trim($jsonData->time);
        $current_participate = 0;

        $dateArray = explode(PHP_EOL, $date);
        $timeArray = explode(PHP_EOL, $time);

        $query = $writeDb->prepare('insert into class (name, max_participate, current_participate, money, address_id, bigClass_id) 
        values (:name, :max_participate, :current_participate, :money, :address_id, :bigClass_id)');
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':max_participate', $max_participate, PDO::PARAM_INT);
        $query->bindParam(':current_participate', $current_participate, PDO::PARAM_INT);
        $query->bindParam(':money', $money, PDO::PARAM_INT);
        $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query->bindParam(':bigClass_id', $bigClass_id, PDO::PARAM_INT);
        $query->execute();
        $class_id = $writeDb->lastInsertId();
        foreach ($dateArray as $dates) {
            // $date = $data->date;
            $query2 = $writeDb->prepare('insert into date (day, class_id) values (:day, :class_id)');
            $query2->bindParam(':day', $dates, PDO::PARAM_STR);
            $query2->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query2->execute();
            $date_id = $writeDb->lastInsertId();
            foreach ($timeArray as $time) {
                $query3 = $writeDb->prepare('insert into time (date_id, time, max_participate, current_participate) values (:date_id, :time, :max_participate, :current_participate)');
                $query3->bindParam(':time', $time, PDO::PARAM_STR);
                $query3->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                $query3->bindParam(':max_participate', $max_participate, PDO::PARAM_INT);
                $query3->bindParam(':current_participate', $current_participate, PDO::PARAM_INT);
                $query3->execute();
            }
        }
        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Tạo thành công");
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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

        $class_id = $jsonData->class_id;
        $query1 = $writeDb->prepare('DELETE from class where id=:class_id');
        $query1->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query1->execute();

        $rowCount = $query1->rowCount();

        if ($rowCount === 0) {
            // set up response for unsuccessful return
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Không tìm thấy lớp học");
            $response->send();
            exit;
        }
        $dateArray = [];

        $queryaa = $readDb->prepare('SELECT id FROM date where class_id=:class_id');
        $queryaa->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $queryaa->execute();
        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
            array_push($dateArray, $rowaa['id']); 
        }

        $query2 = $writeDb->prepare('DELETE from date where class_id=:class_id');
        $query2->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query2->execute();

        foreach ($dateArray as $dates) {
            // $date = $data->date;
            $query3 = $writeDb->prepare('DELETE from time where date_id=:date_id');
            $query3->bindParam(':date_id', $dates, PDO::PARAM_STR);
            $query3->execute();
        }
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Đã xóa lớp học");
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
