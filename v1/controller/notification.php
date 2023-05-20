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
require_once('../model/Notification.php');

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
        $is_create = trim($jsonData->is_create);
        $money = trim($jsonData->money);
        $status = trim($jsonData->status);
        $schedule_id = trim($jsonData->schedule_id);
        $date_id = trim($jsonData->date_id);
        $time_id = trim($jsonData->time_id);

        $queryFirst = $readDb->prepare('SELECT * FROM time WHERE id=:time_id');
        $queryFirst->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $queryFirst->execute();
        while ($row0 = $queryFirst->fetch(PDO::FETCH_ASSOC)) {
            $max_participate = $row0['max_participate'];
        }

        $current_date = date("Y-m-d");

        $querya = $readDb->prepare('SELECT id from schedule WHERE time_id=:time_id AND end_date > :current_date');
        $querya->bindParam(':current_date', $current_date, PDO::PARAM_STR);
        $querya->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $querya->execute();
        $current_participate = $querya->rowCount();
        if ($current_participate >= $max_participate) {
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(false);
            $response->addMessage("Số lượng học viên đã đạt tối đa");
            $response->setData(null);
            $response->send();
            return;
        }

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

        $is_read = 0;
        $content = "";
        $is_customer = 0;
        $date_create = date("Y-m-d");
        if ($is_create === '0') {
            $content = "Bạn có yêu cầu lịch tập mới: " . $userName;
            $end_date_array = [];
            $start_date_array = [];

            $secondQuery = $readDb->prepare("SELECT end_date, start_date from schedule WHERE customer_id=:customer_id AND class_id=:class_id");
            $secondQuery->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $secondQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

            $secondQuery->execute();

            while ($rowa = $secondQuery->fetch(PDO::FETCH_ASSOC)) {
                array_push($end_date_array, $rowa['end_date']);
                array_push($start_date_array, $rowa['start_date']);
            }
            foreach ($end_date_array as $end) {
                if (($end) > ($start_date)) {
                    $response = new Response();
                    $response->setHttpStatusCode(200);
                    $response->setSuccess(false);
                    $response->addMessage("Bạn đã đăng ký trong khoảng thời gian này.\nHãy kiểm tra lại");
                    $response->setData(null);
                    $response->send();
                    exit;
                }
            }
            foreach ($start_date_array as $start) {
                if (($end_date) < ($start)) {
                    $response = new Response();
                    $response->setHttpStatusCode(200);
                    $response->setSuccess(false);
                    $response->addMessage("Bạn đã đăng ký trong khoảng thời gian này.\nHãy kiểm tra lại");
                    $response->setData(null);
                    $response->send();
                    exit;
                }
            }

        } else {
            $content = $userName ." yêu cầu đổi lịch tập";
            $firstQuery = $readDb->prepare("SELECT * from schedule WHERE id=:schedule_id");
            $firstQuery->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $firstQuery->execute();
            while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
                $change_number = $rowa['change_number'];
            }

            if ($change_number >= 3) {
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(false);
                $response->addMessage("Số lần sửa lịch tập đã đạt tối đa.");
                $response->setData(null);
                $response->send();
                exit;
            }

        }

        if ($schedule_id === "") {
            $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, start_date, end_date, time, day, content, is_read, is_customer, date_create, money, status, date_id, time_id) 
                                        values (:trainer_id, :customer_id, :address_id, :class_id, :start_date, :end_date, :time, :day, :content, :is_read, :is_customer, :date_create, :money, :status, :date_id, :time_id)');

            $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $query->bindParam(':time', $time, PDO::PARAM_STR);
            $query->bindParam(':day', $day, PDO::PARAM_STR);
            $query->bindParam(':content', $content, PDO::PARAM_STR);
            $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
            $query->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
            $query->bindParam(':date_create', $date_create, PDO::PARAM_STR);
            $query->bindParam(':money', $money, PDO::PARAM_INT);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query->execute();
            sendCloudMessaseToSmartPhone("", $content, ['id' => $writeDb->lastInsertId()]);

        } else {
            $query = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, start_date, end_date, time, day, content, is_read, is_customer, date_create, money, status, schedule_id, date_id, time_id) 
                                        values (:trainer_id, :customer_id, :address_id, :class_id, :start_date, :end_date, :time, :day, :content, :is_read, :is_customer, :date_create, :money, :status, :schedule_id, :date_id, :time_id)');
            $query->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $query->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $query->bindParam(':time', $time, PDO::PARAM_STR);
            $query->bindParam(':day', $day, PDO::PARAM_STR);
            $query->bindParam(':content', $content, PDO::PARAM_STR);
            $query->bindParam(':is_read', $is_read, PDO::PARAM_INT);
            $query->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
            $query->bindParam(':date_create', $date_create, PDO::PARAM_STR);
            $query->bindParam(':money', $money, PDO::PARAM_INT);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $query->bindParam(':date_id', $date_id, PDO::PARAM_INT);
            $query->bindParam(':time_id', $time_id, PDO::PARAM_INT);
            $query->execute();
            sendCloudMessaseToSmartPhone("", $content, ['id' => $writeDb->lastInsertId()]);

        }
        $content = "Lịch tập đã được gửi tới HLV ". $trainerName;
        $is_customer = 1;
        $date_create = date("Y-m-d");
        $query1 = $writeDb->prepare('insert into notification (trainer_id, customer_id, address_id, class_id, start_date, end_date, time, day, content, is_read, is_customer, date_create, money, status, date_id, time_id) 
                                    values (:trainer_id, :customer_id, :address_id, :class_id, :start_date, :end_date, :time, :day, :content, :is_read, :is_customer, :date_create, :money, :status, :date_id, :time_id)');
        $query1->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
        $query1->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $query1->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query1->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $query1->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $query1->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $query1->bindParam(':time', $time, PDO::PARAM_STR);
        $query1->bindParam(':day', $day, PDO::PARAM_STR);
        $query1->bindParam(':content', $content, PDO::PARAM_STR);
        $query1->bindParam(':is_read', $is_read, PDO::PARAM_INT);
        $query1->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $query1->bindParam(':date_create', $date_create, PDO::PARAM_STR);
        $query1->bindParam(':money', $money, PDO::PARAM_INT);
        $query1->bindParam(':status', $status, PDO::PARAM_INT);
        $query1->bindParam(':date_id', $date_id, PDO::PARAM_INT);
        $query1->bindParam(':time_id', $time_id, PDO::PARAM_INT);
        $query1->execute();

        $returnData = array();
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Thông báo đã được gửi tới huấn luyện viên");
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
        $customer_id = $_GET['customer_id'];
        $is_customer = 1;
        $notificationArray = [];
        $firstQuery = $readDb->prepare("SELECT * from notification WHERE customer_id=:customer_id AND is_customer=:is_customer");
        $firstQuery->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $firstQuery->bindParam(':is_customer', $is_customer, PDO::PARAM_INT);
        $firstQuery->execute();
        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $notification_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $trainer_id = $rowa['trainer_id'];
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
            $notification->set_trainer($user->returnUserInforAsArray());

            array_push($notificationArray, $notification->returnClassAsArray());
        }

        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['notifications'] = $notificationArray;
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