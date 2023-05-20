<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Schedule.php');
require_once('../model/Address.php');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $trainer_id = trim($jsonData->trainer_id);
        $customer_id = trim($jsonData->customer_id);
        $address_id = trim($jsonData->address_id);
        $class_id = trim($jsonData->class_id);
        $day = trim($jsonData->day);
        $start_date = trim($jsonData->start_date);
        $end_date = trim($jsonData->end_date);
        $time = trim($jsonData->time);
        $method = trim($jsonData->method);
        $money = trim($jsonData->money);
        $date_id = trim($jsonData->date_id);
        $time_id = trim($jsonData->time_id);

        $date_create = date("Y-m-d");

        // $is_cancelled = trim($jsonData->0);

        // $sum_month = trim($jsonData->sum_month);
        // $sum_session = trim($jsonData->sum_session);

        $dateArray = [];
        if ($day == 'Hàng ngày') {
            $begin = new DateTime($start_date);
            $end = new DateTime($end_date);
            // $end = $end->modify('+1 day');

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end);
            foreach ($daterange as $key => $value) {
                $value1 = $value->format('Y-m-d');
                array_push($dateArray, $value1);
            }
        } else {
            $end = new DateTime($end_date);
            if (str_contains($day, '2')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 1));
            }
            if (str_contains($day, '3')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 2));
            }
            if (str_contains($day, '4')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 3));
            }
            if (str_contains($day, '5')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 4));
            }
            if (str_contains($day, '6')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 5));
            }
            if (str_contains($day, '7')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 6));
            }
            if (str_contains($day, 'Chủ nhật')) {
                array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 7));
            }
        }

        $query = $readDb->prepare('SELECT * from schedule WHERE class_id=:class_id AND customer_id=:customer_id');
        $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query->execute();
        $rowCount = $query->rowCount();

        // $queryFirst = $readDb->prepare('SELECT * FROM time WHERE id=:time_id');
        // $queryFirst->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        // $queryFirst->execute();
        // while ($row0 = $queryFirst->fetch(PDO::FETCH_ASSOC)) {
        //     $max_participate = $row0['max_participate'];
        // }

        // $querya = $readDb->prepare('SELECT id from schedule WHERE time_id=:time_id');
        // $querya->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        // $querya->execute();
        // $current_participate = $querya->rowCount();
        if ($rowCount === 0) {
            $query2 = $writeDb->prepare('UPDATE time SET current_participate = :current_participate + 1 WHERE id=:time_id');
            $query2->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query2->bindParam(':current_participate', $current_participate, PDO::PARAM_INT);
            $query2->execute();
        }
        // if ($current_participate >= $max_participate && $day != 'Hàng ngày') {
        //     $response = new Response();
        //     $response->setHttpStatusCode(200);
        //     $response->setSuccess(false);
        //     $response->addMessage("Số lượng học viên đã đạt tối đa");
        //     $response->setData(null);
        //     $response->send();
        //     return;
        // }
        $change_number = 1;
        // if ($rowCount === 0) {
        if ($day != 'Hàng ngày') {
            $query1 = $writeDb->prepare('insert into schedule (class_id, trainer_id, customer_id, address_id, time, day, start_date, end_date, change_number, date_id, time_id) 
            values (:class_id, :trainer_id,:customer_id, :address_id, :time, :day, :start_date, :end_date, :change_number, :date_id, :time_id)');
            $query1->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        } else {
            $query1 = $writeDb->prepare('insert into schedule (class_id, customer_id, address_id, time, day, start_date, end_date, change_number, date_id, time_id) 
            values (:class_id,:customer_id, :address_id, :time, :day, :start_date, :end_date, :change_number, :date_id, :time_id)');
        }
        $query1->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query1->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query1->bindParam(':time', $time, PDO::PARAM_STR);
        $query1->bindParam(':day', $day, PDO::PARAM_STR);
        $query1->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $query1->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $query1->bindParam(':change_number', $change_number, PDO::PARAM_INT);
        $query1->bindParam(':date_id', $date_id, PDO::PARAM_INT);
        $query1->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $query1->execute();
        $schedule_id = $writeDb->lastInsertId();

        foreach ($dateArray as $dates) {
            // $date = $data->date;
            if ($day == 'Hàng ngày') {
                $query2 = $writeDb->prepare('insert into date (schedule_id, date) values (:schedule_id, :date)');
                $query2->bindParam(':date', $dates, PDO::PARAM_STR);
                $query2->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                $query2->execute();
                $date_id = $writeDb->lastInsertId();
                $query3 = $writeDb->prepare('insert into time (date_id, time) values (:date_id, :time)');
                $query3->bindParam(':time', $time, PDO::PARAM_STR);
                $query3->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                // $query3->bindParam(':is_cancelled', $is_cancelled, PDO::PARAM_INT);
                $query3->execute();
            } else {
                foreach ($dates as $date) {
                    $query2 = $writeDb->prepare('insert into date (schedule_id, date) values (:schedule_id, :date)');
                    $query2->bindParam(':date', $date, PDO::PARAM_STR);
                    $query2->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                    $query2->execute();
                    $date_id = $writeDb->lastInsertId();
                    $query3 = $writeDb->prepare('insert into time (date_id, time, trainer_id) values (:date_id, :time, :trainer_id)');
                    $query3->bindParam(':time', $time, PDO::PARAM_STR);
                    $query3->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                    $query3->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                    // $query3->bindParam(':is_cancelled', $is_cancelled, PDO::PARAM_INT);
                    $query3->execute();
                }
            }
        }
        $date1 = $start_date . ' - ' . $end_date;
        if ($day != 'Hàng ngày') {
            $query0 = $writeDb->prepare('insert into payment (class_id, trainer_id, address_id, customer_id, method, money, time, day, date, date_create) values (:class_id, :trainer_id, :address_id, :customer_id, :method, :money, :time, :day, :date, :date_create)');
            $query0->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        } else {
            $query0 = $writeDb->prepare('insert into payment (class_id, address_id, customer_id, method, money, time, day, date, date_create) values (:class_id,:address_id, :customer_id, :method, :money, :time, :day, :date, :date_create)');
        }

        $query0->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query0->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query0->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query0->bindParam(':method', $method, PDO::PARAM_INT);
        $query0->bindParam(':money', $money, PDO::PARAM_INT);
        $query0->bindParam(':time', $time, PDO::PARAM_STR);
        $query0->bindParam(':day', $day, PDO::PARAM_STR);
        $query0->bindParam(':date', $date1, PDO::PARAM_STR);
        $query0->bindParam(':date_create', $date_create, PDO::PARAM_STR);

        $query0->execute();

        if ($day != 'Hàng ngày') {
            $is_read = 0;
            $content = "Bạn có lịch dạy mới";
            $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, day, start_date, end_date, time, content, is_read, date_id, time_id) 
                                            values (:trainer_id, :customer_id, :address_id, :class_id, :day, :start_date, :end_date, :time, :content, :is_read, :date_id, :time_id)');
            $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query->bindParam(':day', $day, PDO::PARAM_STR);
            $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $query->bindParam(':time', $time, PDO::PARAM_STR);
            $query->bindParam(':content', $content, PDO::PARAM_STR);
            $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
            $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query->execute();
            $query5 = $readDb->prepare('SELECT id from chat WHERE trainer_id=:trainer_id AND customer_id=:customer_id');
            $query5->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $query5->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query5->execute();
            $rowCount = $query5->rowCount();
            if ($rowCount === 0) {
                $query4 = $writeDb->prepare('insert into chat (trainer_id, customer_id, is_read_customer, is_read_trainer) 
                    values (:trainer_id, :customer_id, :is_read_customer, :is_read_trainer)');
                $query4->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                $query4->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
                $query4->bindParam(':is_read_customer', $is_read, PDO::PARAM_INT);
                $query4->bindParam(':is_read_trainer', $is_read, PDO::PARAM_INT);
                $query4->execute();
            }
        }

        $returnData = array();
        $returnData['rows_returned'] = $rowCount;
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Đăng ký lịch tập thành công");
        $response->setData(null);
        $response->send();
        //set up response for successful return

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
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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

        $trainer_id = trim($jsonData->trainer_id);
        $customer_id = trim($jsonData->customer_id);
        $address_id = trim($jsonData->address_id);
        $class_id = trim($jsonData->class_id);
        $day = trim($jsonData->day);
        $start_date = trim($jsonData->start_date);
        $end_date = trim($jsonData->end_date);
        $time = trim($jsonData->time);
        $schedule_id = trim($jsonData->schedule_id);

        // $is_cancelled = trim($jsonData->0);

        // $sum_month = trim($jsonData->sum_month);
        // $sum_session = trim($jsonData->sum_session);

        $date_id = [];

        $query = $readDb->prepare('SELECT id from schedule WHERE class_id=:class_id AND customer_id=:customer_id');
        $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query->execute();
        while ($row0 = $queryFirst->fetch(PDO::FETCH_ASSOC)) {
            $time_id1 = $row0['time_id'];
        }

        // $queryFirst = $readDb->prepare('SELECT * FROM time WHERE id=:time_id');
        // $queryFirst->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        // $queryFirst->execute();
        // while ($row0 = $queryFirst->fetch(PDO::FETCH_ASSOC)) {
        //     $max_participate = $row0['max_participate'];
        // }

        // $querya = $readDb->prepare('SELECT id from schedule WHERE time_id=:time_id');
        // $querya->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        // $querya->execute();
        // $current_participate = $querya->rowCount();
        // if ($current_participate >= $max_participate && $day != 'Hàng ngày') {
        //     $response = new Response();
        //     $response->setHttpStatusCode(200);
        //     $response->setSuccess(false);
        //     $response->addMessage("Số lượng học viên đã đạt tối đa");
        //     $response->setData(null);
        //     $response->send();
        //     return;
        // }

        if ($time_id !== $time_id1) {
            $query2 = $writeDb->prepare('UPDATE time SET current_participate = :current_participate + 1 WHERE id=:time_id');
            $query2->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query2->bindParam(':current_participate', $current_participate, PDO::PARAM_INT);
            $query2->execute();
        }
    
        $queryfirst = $readDb->prepare('SELECT id from date where schedule_id=:schedule_id');
        $queryfirst->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $queryfirst->execute();
        while ($row0 = $queryfirst->fetch(PDO::FETCH_ASSOC)) {
            array_push($date_id, $row0['id']);
        }

        $querySecond = $readDb->prepare('DELETE from date where schedule_id=:schedule_id');
        $querySecond->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $querySecond->execute();

        foreach ($date_id as $dateId) {
            $queryThird = $readDb->prepare('DELETE from time where date_id=:date_id');
            $queryThird->bindParam(':date_id', $dateId, PDO::PARAM_INT);
            $queryThird->execute();
        }

        $query0 = $readDb->prepare('SELECT * FROM class WHERE id=:class_id');
        $query0->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query0->execute();
        while ($row0 = $query0->fetch(PDO::FETCH_ASSOC)) {
            $end_date_class = $row0['end_date'];
            $current_participate = $row0['current_participate'];
            $max_participate = $row0['max_participate'];
        }

        $dateArray = [];
        if (str_contains($day, '2')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 1));
        }
        if (str_contains($day, '3')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 2));
        }
        if (str_contains($day, '4')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 3));
        }
        if (str_contains($day, '5')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 4));
        }
        if (str_contains($day, '6')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 5));
        }
        if (str_contains($day, '7')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 6));
        }
        if (str_contains($day, 'Chủ nhật')) {
            array_push($dateArray, getDateForSpecificDayBetweenDates($start_date, $end_date, 7));
        }

        if ($rowCount === 0) {
            $returnData = array();
            // $returnData['rows_returned'] = $rowCount;
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(false);
            $response->addMessage("Không tìm thấy lịch tập");
            $response->setData($returnData);
            $response->send();
        } else {
            $query1 = $writeDb->prepare('UPDATE schedule SET class_id = :class_id, address_id = :address_id, time = :time, day = :day, date_id = :date_id, time_id = :time_id WHERE id=:schedule_id');
            $query1->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $query1->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query1->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query1->bindParam(':time', $time, PDO::PARAM_STR);
            $query1->bindParam(':day', $day, PDO::PARAM_STR);
            $query1->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query1->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query1->execute();
            foreach ($dateArray as $dates) {
                foreach ($dates as $date) {
                    $query2 = $writeDb->prepare('insert into date (schedule_id, date) values (:schedule_id, :date)');
                    $query2->bindParam(':date', $date, PDO::PARAM_STR);
                    $query2->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                    $query2->execute();
                    $date_id = $writeDb->lastInsertId();
                    if ($trainer_id === "") {
                        $query3 = $writeDb->prepare('insert into time (date_id, time) values (:date_id, :time)');
                        $query3->bindParam(':time', $time, PDO::PARAM_STR);
                        $query3->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                        // $query3->bindParam(':is_cancelled', $is_cancelled, PDO::PARAM_INT);
                        $query3->execute();
                    } else {
                        $query3 = $writeDb->prepare('insert into time (date_id, time, trainer_id) values (:date_id, :time, :trainer_id)');
                        $query3->bindParam(':time', $time, PDO::PARAM_STR);
                        $query3->bindParam(':date_id', $date_id, PDO::PARAM_INT);
                        $query3->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                        // $query3->bindParam(':is_cancelled', $is_cancelled, PDO::PARAM_INT);
                        $query3->execute();
                    }
                }
            }

            $is_read = 0;
            $content = "Bạn có lịch dạy mới";
            $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, day, start_date, end_date, time, content, is_read, date_id, time_id) 
                                        values (:trainer_id, :customer_id, :address_id, :class_id, :day, :start_date, :end_date, :time, :content, :is_read, :date_id, :time_id)');
            $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query->bindParam(':day', $day, PDO::PARAM_STR);
            $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $query->bindParam(':time', $time, PDO::PARAM_STR);
            $query->bindParam(':content', $content, PDO::PARAM_STR);
            $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
            $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query->execute();

            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Bạn đã sửa lịch tập thành công");
            $response->setData(null);
            $response->send();
        }
        //set up response for successful return
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
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
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
        // create db query
        $time_id = trim($jsonData->time_id);
        // $customer_id = trim($jsonData->customer_id);
        // $time = trim($jsonData->time);
        // $date = trim($jsonData->date);
        // $schedule_id = trim($jsonData->schedule_id);
        // $address_id = trim($jsonData->address_id);
        // $class_id = trim($jsonData->class_id);

        $firstQuery = $readDb->prepare("UPDATE time SET is_cancelled = 1 WHERE id=:time_id");
        $firstQuery->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $firstQuery->execute();

        // $is_read = 0;s
        // $content = "Học viên đã huỷ lịch tập";
        // $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, start_date, time, content, is_read) 
        //                             values (:trainer_id, :customer_id, :address_id, :class_id, :start_date, :time, :content, :is_read)');
        // $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        // $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        // $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        // $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        // $query->bindParam(':start_date', $date, PDO::PARAM_STR);
        // $query->bindParam(':time', $time, PDO::PARAM_STR);
        // $query->bindParam(':content', $content, PDO::PARAM_STR);
        // $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
        // $query->execute();

        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Huỷ giờ tập thành công");
        $response->setData(null);
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
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $customer_id = $_GET['customer_id'];
        $scheduleArray = [];
        $firstQuery = $readDb->prepare("SELECT * from schedule WHERE customer_id=:customer_id");
        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $schedule_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $trainer_id = $rowa['trainer_id'];
            $address_id = $rowa['address_id'];

            $schedule = new Schedule([
                '_id' => $rowa['id'],
                '_time' => $rowa['time'],
                '_day' => $rowa['day'],
                '_start_date' => $rowa['start_date'],
                '_end_date' => $rowa['end_date'],
                '_change_number' => $rowa['change_number'],
                '_date_id' => $rowa['date_id'],
                '_time_id' => $rowa['time_id'],
            ]);

            $query = $readDb->prepare('SELECT * FROM address WHERE id=:address_id');
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $address = new Address([
                    '_id' => $row['id'],
                    '_address' => $row['address'],
                    // '_max_participate' => $rowaa['max_participate'],
                    // '_current_participate' => $rowaa['current_participate'],
                    // '_description' => $rowaa['description'],
                    // '_benefit' => $rowaa['benefit'],
                ]);
            }

            $query0 = $readDb->prepare('SELECT * FROM class WHERE id=:class_id');
            $query0->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query0->execute();
            while ($row0 = $query0->fetch(PDO::FETCH_ASSOC)) {
                $class = new ClassModel([
                    '_id' => $row0['id'],
                    '_name' => $row0['name'],
                    // '_max_participate' => $rowaa['max_participate'],
                    // '_current_participate' => $rowaa['current_participate'],
                    // '_description' => $rowaa['description'],
                    // '_benefit' => $rowaa['benefit'],
                ]);
            }

            $query1 = $readDb->prepare('SELECT * FROM date WHERE schedule_id=:schedule_id');
            $query1->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $query1->execute();
            $dateArray = [];

            while ($row2 = $query1->fetch(PDO::FETCH_ASSOC)) {
                $date0 = new Date([
                    '_id' => $row2['id'],
                    '_date' => $row2['date'],
                    '_day' => $row2['day'],
                ]);
                $query2 = $readDb->prepare('SELECT * FROM time WHERE date_id=:date_id');
                $query2->bindParam(':date_id', $row2['id'], PDO::PARAM_INT);
                $query2->execute();
                while ($row3 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $time = new Time([
                        '_id' => $row3['id'],
                        '_time' => $row3['time'],
                        '_is_cancelled' => $row3['is_cancelled'],
                        '_class' => $class->get_name(),
                        '_address' => $address->get_address(),
                    ]);
                }
                $date0->set_time($time->returnTimeAsArray());
                array_push($dateArray, $date0->returnDateAsArray());
            }

            $schedule->set_date($dateArray);
            $schedule->set_class($class->returnOnlyClassAsArray());
            $schedule->set_address($address->returnClassAsArray());

            if ($trainer_id != null) {
                $query3 = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
                $query3->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
                $query3->execute();
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                    $trainer = new Trainer([
                        '_id' => $row3['id'],
                        '_name' => $row3['name'],
                        '_avatar' => $row3['avatar'],
                        '_gender' => $row3['gender'],
                    ]);
                }

                $schedule->set_trainer($trainer->returnUserInforAsArray());
            }

            array_push($scheduleArray, $schedule->returnClassAsArray());

            // $query4 = $readDb->prepare('SELECT * FROM bigClass WHERE id=:class_id');
            // $query4->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            // $query4->execute();
            // while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {
            //     $trainer = new Trainer([
            //         '_id' => $row4['id'],
            //         '_name' => $row4['name'],
            //     ]);
            // }
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['schedules'] = $scheduleArray;
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

function getDaysInRange($dateFromString, $dateToString, $day, $modify)
{
    $dateFrom = new \DateTime($dateFromString);
    $dateTo = new \DateTime($dateToString);
    $dates = [];

    if ($dateFrom > $dateTo) {
        return $dates;
    }

    if (1 != $dateFrom->format('N')) {
        $dateFrom->modify('next monday');
    }

    while ($dateFrom <= $dateTo) {
        $dates[] = $dateFrom->format('Y-m-d');
        $dateFrom->modify('+1 week');
    }

    return $dates;
}

function getDateForSpecificDayBetweenDates($startDate, $endDate, $day_number)
{
    $date_array = [];

    $endDate = strtotime($endDate);
    $days = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
    for ($i = strtotime($days[$day_number], strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i)) {
        $date_array[] = date('Y-m-d', $i);
    }
    return $date_array;
}
 // set de mang array bao gom date (time, hlv, lop hoc)
