<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials', 'true');

use function PHPSTORM_META\type;

require_once('db.php');
require_once('../model/Date.php');
require_once('../model/Time.php');
require_once('../model/Class.php');
require_once('../model/Trainer.php');
require_once('../model/Customer.php');
require_once('../model/Schedule.php');
require_once('../model/Address.php');
require_once('../model/Response.php');
require_once('../model/Payment.php');

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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // create db query
        $classArray = [];
        $notificationArray = [];
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
        $type = $_GET['type'];
        $address_id = $_GET['address_id'];

        $firstQuery = $readDb->prepare("SELECT * from payment WHERE date_create >= :start_date AND date_create<=:end_date AND address_id=:address_id");
        $firstQuery->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $firstQuery->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $firstQuery->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $firstQuery->execute();

        while ($rowa = $firstQuery->fetch(PDO::FETCH_ASSOC)) {
            $notification_id = $rowa['id'];
            $class_id = $rowa['class_id'];
            $trainer_id = $rowa['trainer_id'];
            $address_id = $rowa['address_id'];
            $notification = new Payment([
                '_money' => $rowa['money'],
                '_date_create' => $rowa['date_create'],
            ]);

            $query0 = $readDb->prepare('SELECT * FROM class WHERE id=:class_id');
            $query0->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $query0->execute();
            while ($rowaa = $query0->fetch(PDO::FETCH_ASSOC)) {
                $className = $rowaa['name'];
            }
            $notification->set_class($className);
            array_push($notificationArray, $notification->returnStatisticAsArray());
        }
        $query = $readDb->prepare('SELECT * FROM class WHERE address_id=:address_id');
        $query->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $query->execute();

        while ($rowaa = $query->fetch(PDO::FETCH_ASSOC)) {
            $className = $rowaa['name'];
            array_push($classArray, $className);
        }
        if ($address_id == 2) {
            array_push($classArray, $classArray[0]);
            unset($classArray[0]);
        }
        $end_datetime = new DateTime($end_date);
        $interval = new DateInterval(('P1D'));
        $end_datetime->add($interval);
        $period = new DatePeriod(
            new DateTime($start_date),
            $interval,
            $end_datetime
        );

        $listNewPayments = [];
        $listNewPaymentMonths = [];
        foreach ($period as $key => $value) {
            $money = 0;
            $notification = new Payment([
                '_money' => $money,
                '_date_create' => $value->format('Y-m-d'),
            ]);
            array_push($listNewPayments, $notification->returnStatisticAsArray());
        }

        if ($type == 1) {
            $end_datetime = new DateTime($end_date);
            $interval = new DateInterval(('P1M'));
            $end_datetime->add($interval);
            $periodMonth = new DatePeriod(
                new DateTime($start_date),
                $interval,
                $end_datetime
            );
            foreach ($periodMonth as $key => $value) {
                $notification = new Payment([
                    '_money' => $money,
                    '_date_create' => $value->format('Y-m'),
                ]);
                array_push($listNewPaymentMonths, $notification->returnStatisticAsArray());
            }
        }
        $classes = array_column($notificationArray, 'class');
        $moneys = array_column($listNewPayments, 'money');
        $dateCreates = array_column($listNewPayments, 'date_create');
        $moneysNoti = array_column($notificationArray, 'money');
        $dateCreatesNoti = array_column($notificationArray, 'date_create');

        $totalList = array();
        $totalListMonth = array();
        foreach ($classArray as $keyClass => $valueClass) {
            $listPayments = $listNewPayments;
            foreach ($dateCreates as $key => $value) {
                $money = 0;
                foreach ($dateCreatesNoti as $keyNoti => $valueNoti) {
                    if ($valueNoti == $value && $classes[$keyNoti] == $valueClass) {
                        $money += $moneysNoti[$keyNoti];
                        if ($type == 1) {
                            $dateCreate1 = substr_replace($dateCreates[$key], "", -1);
                            $dateCreate2 = substr_replace($dateCreate1, "", -1);
                            $dateCreate3 = substr_replace($dateCreate2, "", -1);
                            $listPayments[$key] = new Payment([
                                'money' => $money,
                                'date_create' => $dateCreate3,
                            ]);
                        } else {
                            $listPayments[$key] = new Payment([
                                'money' => $money,
                                'date_create' => $dateCreates[$key],
                            ]);
                        }
                    } else if ($type == 1 && $money == 0) {
                        $dateCreate1 = substr_replace($dateCreates[$key], "", -1);
                        $dateCreate2 = substr_replace($dateCreate1, "", -1);
                        $dateCreate3 = substr_replace($dateCreate2, "", -1);
                        $listPayments[$key] = new Payment([
                            'money' => $money,
                            'date_create' => $dateCreate3,
                        ]);
                    }
                }
            }
            if ($type == 0) {
                array_push($totalList, array_column($listPayments, 'money'));
            } else {
                array_push($totalList, $listPayments);
            }
        }

        if ($type == 1) {
            foreach ($classArray as $keyTotal => $valueClass) {
                $moneyMonths = array_column($totalList[$keyTotal], 'money');
                $dateCreates = array_column($totalList[$keyTotal], 'date_create');
                $dateCreatesMonth = array_column($listNewPaymentMonths, 'date_create');
                array_push($totalListMonth, []);
                foreach ($dateCreatesMonth as $key => $value) {
                    $money = 0;
                    foreach ($dateCreates as $keyNoti => $valueNoti) { 
                        if ($valueNoti == $value) {
                            $money += $moneyMonths[$keyNoti];
                            $listNewPaymentMonths[$key] = new Payment([
                                'money' => $money,
                                'date_create' => $dateCreatesMonth[$key],
                            ]);
                            $totalListMonth[$keyTotal] = array_column($listNewPaymentMonths, 'money');
                        }
                    }
                }
            }
        }
        // $returnData['rows_returned'] = $rowCount;
        $returnData = array();
        $returnData['payments'] = $listNewPayments;
        if ($type == 1) {
            $returnData['payments'] = $listNewPaymentMonths;
        }
        // set up response for successful return
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);

        if ($type == 1) {
            $response->setData($totalListMonth);
        } else {
            $response->setData($totalList);
        }
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

function setStatisticList($startDate, $endDate, $listPayments)
{
    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'),
        new DateTime($endDate)
    );
    foreach ($period as $key => $value) {
        $value->format('Y-m-d');
        $money = 0;
        $notification = new Payment([
            '_money' => $money,
            '_date_create' => $value,
        ]);
        array_push($notificationArray, $notification->returnStatisticAsArray());
    }
    foreach ($notificationArray as $key => $value) {
        if (end($listNewPayments)->get_date_create() == $value->get_date_create()) {
            $money = $value->get_money() + end($listNewPayments)->get_money();
            end($listNewPayments)->set_date_create($money);
        }
    }
}

function unique_multidim_array($array, $key)
{

    $temp_array = array();

    $i = 0;

    $key_array = array();



    foreach ($array as $val) {

        if (!in_array($val[$key], $key_array)) {

            $key_array[$i] = $val[$key];

            $temp_array[$i] = $val;
        }

        $i++;
    }

    return $temp_array;
}

// function setListPayment(Payment $listPayments = []) {
//     $listNewPayments = array();
//     foreach ($listPayments as $key => $value) {
//         if (count($listNewPayments) == 0) {
//             $notification = new Payment([
//                 '_money' => $value->get_money(),
//                 '_date_create' => $value->get_date_create(),
//             ]);
//             array_push($listNewPayments, $notification->returnStatisticAsArray());
//         } else {
//             if (end($listNewPayments)->get_date_create() == $value->get_date_create()) {
//                 $money = $value->get_money() + end($listNewPayments)->get_money();
//                 end($listNewPayments)->set_date_create($money);
//             } else {
//                 array_push($listNewPayments, $value->returnStatisticAsArray());
//             }
//         }
//     }
// }