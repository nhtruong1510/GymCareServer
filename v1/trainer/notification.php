<?php


require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Schedule.php');
require_once('../model/Notification.php');
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
        $status = trim($jsonData->status);
        $notification_id = trim($jsonData->notification_id);
        $money = trim($jsonData->money);
        $date_id = trim($jsonData->date_id);
        $time_id = trim($jsonData->time_id);

        $is_read = 0;
        $content = "";
        $is_customer = 1;
        $date_create = date("Y-m-d");

        $queryaa = $readDb->prepare('SELECT * FROM customer WHERE id=:customer_id');
        $queryaa->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $queryaa->execute();

        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
            $userName = $rowaa['name'];
        }

        $queryb = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
        $queryb->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $queryb->execute();

        while ($rowaa = $queryb->fetch(PDO::FETCH_ASSOC)) {
            $trainerName = $rowaa['name'];
        }

        if ($status === '0') {
            $content = "HLV " .$trainerName . " đã từ chối bạn";
        } else {
            $content = "HLV " .$trainerName . " đã chấp nhận lịch tập của bạn";
        }
        $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, day, start_date, end_date, time, content, is_read, is_customer, date_create, status, money, date_id, time_id) 
                                    values (:trainer_id, :customer_id, :address_id, :class_id, :day, :start_date, :end_date, :time, :content, :is_read, :is_customer, :date_create, :status, :money, :date_id, :time_id)');
        $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query->bindParam(':day', $day, PDO::PARAM_STR);
        $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $query->bindParam(':time', $time, PDO::PARAM_STR);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
        $query->bindParam(':date_create', $date_create, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':money', $money, PDO::PARAM_INT);
        $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
        $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $query->execute();
        sendCloudMessaseToSmartPhone("", $content, ['id' => $writeDb->lastInsertId()]);

        $message = "Thông báo đã được gửi tới người tập";
        if ($status === '0') {
            $content = "Bạn đã từ chối yêu cầu của " .$userName;
        } else {
            $content = "Bạn đã chấp nhận yêu cầu của " .$userName;
        }
        // $status = '2';
        $query1 = $writeDb->prepare('UPDATE notification SET content = :content, status = :status WHERE id=:notification_id');
        $query1->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
        $query1->bindParam(':content', $content, PDO::PARAM_STR);
        $query1->bindParam(':status', $status, PDO::PARAM_STR);
        $query1->execute();

        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage($message);
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
        $status = trim($jsonData->status);
        $notification_id = trim($jsonData->notification_id);
        $schedule_id = trim($jsonData->schedule_id);
        $date_id = trim($jsonData->date_id);
        $time_id = trim($jsonData->time_id);

        $is_read = 0;
        $content = "";
        $is_customer = 1;
        $date_create = date("Y-m-d");

        $queryaa = $readDb->prepare('SELECT * FROM customer WHERE id=:customer_id');
        $queryaa->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $queryaa->execute();

        while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
            $userName = $rowaa['name'];
        }

        $queryb = $readDb->prepare('SELECT * FROM trainer WHERE id=:trainer_id');
        $queryb->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $queryb->execute();

        while ($rowaa = $queryb->fetch(PDO::FETCH_ASSOC)) {
            $trainerName = $rowaa['name'];
        }

        if ($status === '0') {
            $content = "HLV " .$trainerName . " đã từ chối bạn";
        } else {
            $content = "HLV " .$trainerName . " đã chấp nhận lịch tập của bạn";
        }
        $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, day, start_date, end_date, time, content, is_read, is_customer, date_create, status, money, date_id, time_id) 
                                    values (:trainer_id, :customer_id, :address_id, :class_id, :day, :start_date, :end_date, :time, :content, :is_read, :is_customer, :date_create, :status, :money, :date_id, :time_id)');
        $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query->bindParam(':day', $day, PDO::PARAM_STR);
        $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $query->bindParam(':time', $time, PDO::PARAM_STR);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
        $query->bindParam(':date_create', $date_create, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':money', $money, PDO::PARAM_INT);
        $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
        $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $query->execute();
        sendCloudMessaseToSmartPhone("", $content, ['id' => $writeDb->lastInsertId()]);

        $message = "Bạn đã đổi lịch dạy thành công.";
        if ($status === '0') {
            $message = "Thông báo đã được gửi đến người tập";
            $content = "Bạn đã từ chối yêu cầu của " .$userName;
        } else {
            $content = "Bạn đã chấp nhận yêu cầu của " .$userName;
            if ($status === '1') {
                $date_ids = [];
                $queryfirst = $readDb->prepare('SELECT id from date where schedule_id=:schedule_id');
                $queryfirst->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                $queryfirst->execute();
                while ($row0 = $queryfirst->fetch(PDO::FETCH_ASSOC)) {
                    array_push($date_ids, $row0['id']);
                }
        
                $querySecond = $readDb->prepare('DELETE from date where schedule_id=:schedule_id');
                $querySecond->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                $querySecond->execute();
        
                foreach ($date_ids as $dateId) {
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
        
                $query = $readDb->prepare('SELECT id from schedule WHERE class_id=:class_id AND customer_id=:customer_id');
                $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
                $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
                $query->execute();
                $rowCount = $query->rowCount();
        
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
                    $query1 = $writeDb->prepare('UPDATE schedule SET class_id = :class_id, address_id = :address_id, time = :time, day = :day, change_number = change_number + 1, date_id = :date_id, time_id = :time_id WHERE id=:schedule_id');
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
                }
            }
        }
        // $status = '2';
        $is_read = 0;
        $query1 = $writeDb->prepare('UPDATE notification SET content = :content, status = :status, is_read = :is_read WHERE id=:notification_id');
        $query1->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
        $query1->bindParam(':content', $content, PDO::PARAM_STR);
        $query1->bindParam(':status', $status, PDO::PARAM_STR);
        $query1->bindParam(':is_read', $is_read, PDO::PARAM_STR);
        $query1->execute();

        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage($message);
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
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $trainer_id = $_GET['trainer_id'];
        $is_customer = 0;
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from notification WHERE trainer_id=:trainer_id AND is_customer=:is_customer");
        $firstQuery->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $firstQuery->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $notification_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $customer_id = $rowa['customer_id'];
            $address_id = $rowa['address_id'];

            $notification = new Notification([
                '_id' => $rowa['id'],
                '_content' => $rowa['content'],
                '_date_create' => $rowa['date_create'],
                '_is_read' => $rowa['is_read'],
                '_day' => $rowa['day'],
                '_time' => $rowa['time'],
                '_start_date' => $rowa['start_date'],
                '_end_date' => $rowa['end_date'],
                '_status' => $rowa['status'],
                '_money' => $rowa['money'],
                '_schedule_id' => $rowa['schedule_id'],
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
            $notification->set_address($address->returnClassAsArray());

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
            $notification->set_class($class->returnClassAsArray());

            $queryaa = $readDb->prepare('SELECT * FROM customer WHERE id=:customer_id');
            $queryaa->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $queryaa->execute();

            while ($rowaa = $queryaa->fetch(PDO::FETCH_ASSOC)) {
                $user = new Customer(
                    [
                        '_name' => $rowaa['name'],
                        '_email' => $rowaa['email'],
                        '_phone' => $rowaa['phone'],
                        '_avatar' => $rowaa['avatar'],
                        '_id' => $rowaa['id']
                    ]
                );
            }
            $notification->set_customer($user->returnUserInforAsArray());

            array_push($notificationArray, $notification->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['notifications'] = $notificationArray;
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
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

function sendCloudMessaseToSmartPhone($deviceToken = "", $message = "", $push_data = array()) {        
    $url = 'https://fcm.googleapis.com/fcm/send ';
    $serverKey = "AAAAmmICaLE:APA91bFnJsoydFARfh0OwAddrbgQiLuQm9yAScLZVVXhkOZgNYblh46foumqQ7DV1f2mzofkVogkAEg_4tQKaL9sifjTXnMp53W6LsBtPCQQgPZbXNBFsh3qYq-vT8zbiq3dgDvItVcW"; 
    $fields = array();
    $fields['data'] = $push_data;
    $notification = array();
    $notification['title'] = "Thông báo";
    $notification['body'] = $message;
    $fields['notification'] = $notification;
    if (is_array($deviceToken)) {
        $fields['registration_ids'] = "cXmJVlRGfEnurfNso2Ed2p:APA91bEDg1KrtFFnNStctbwDiWuFr1TAY42YiZ-xUYji3nRCaIkXFNXmm2VdG6-9kyMPYl6tvKkzgxuf71AHNxieTSdl91osBwJIHhybKw7D5iJCHYaU_SFCa7BZ7H3nl1_UyVPkM-K7";
    } else {
        $fields['to'] = "cXmJVlRGfEnurfNso2Ed2p:APA91bEDg1KrtFFnNStctbwDiWuFr1TAY42YiZ-xUYji3nRCaIkXFNXmm2VdG6-9kyMPYl6tvKkzgxuf71AHNxieTSdl91osBwJIHhybKw7D5iJCHYaU_SFCa7BZ7H3nl1_UyVPkM-K7";
    }
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $serverKey
    );   
    // echo $fields['to'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    // echo $result;
    if ($result === FALSE) {
        die('FCM Send Error: '  .  curl_error($ch));
    }
    curl_close($ch);
    return $result;
}   