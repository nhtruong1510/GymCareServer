<?php

require_once('../model/Response.php');

$inputDataTotal = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // create food
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

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "gymcare://";
        $vnp_TmnCode = "GJU252Q5"; //Mã website tại VNPAY 
        $vnp_HashSecret = "VYOWUPSBQSMPAABYHFNGWSMCKXAYRHWW"; //Chuỗi bí mật

        $vnp_TxnRef = rand(1, 100000); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này 
        $vnp_OrderInfo = "Thanh toan goi tap";
        $vnp_OrderType = "NCB";
        $vnp_Amount = trim($jsonData->vnp_Amount);
        $vnp_Locale = "vn";
        // $vnp_BankCode = $_POST['bank_code'];
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        $vnp_ExpireDate = trim($jsonData->vnp_ExpireDate);
        //Billing
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        );
        $inputDataTotal = $inputData;
        // if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        //     $inputData['vnp_BankCode'] = $vnp_BankCode;
        // }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );
        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            echo json_encode($returnData);
        }
        // vui lòng tham khảo thêm tại code demo

        // $returnData = array();
        // $response = new Response();
        // $response->setHttpStatusCode(200);
        // $response->setSuccess(true);
        // $response->addMessage($returnData[2]);
        // $response->setData(null);
        // $response->send();

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
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "gymcare://";
    $vnp_TmnCode = "GJU252Q5"; //Mã website tại VNPAY 
    $vnp_HashSecret = "VYOWUPSBQSMPAABYHFNGWSMCKXAYRHWW"; //Chuỗi bí mật

    $vnp_TxnRef = rand(1, 100000); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này 
    $vnp_OrderInfo = "Thanh toan goi tap";
    $vnp_OrderType = "NCB";
    $vnp_Amount = $_GET['vnp_Amount'];
    $vnp_ExpireDate = $_GET['vnp_ExpireDate'];
    $vnp_Locale = "vn";
    // $vnp_BankCode = $_POST['bank_code'];
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    //Add Params of 2.0.1 Version
    //Billing
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
        "vnp_ExpireDate" => $vnp_ExpireDate,
    );
    $inputDataTotal = $inputData;
    // if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    //     $inputData['vnp_BankCode'] = $vnp_BankCode;
    // }
    if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
        $inputData['vnp_Bill_State'] = $vnp_Bill_State;
    }
    
    //  require_once("./config.php");
     $returnData = array();
     
    //  foreach ($_GET as $key => $value) {
    //      if (substr($key, 0, 4) == "vnp_") {
    //          $inputData[$key] = $value;
    //      }
    //  }
     
     $vnp_SecureHash = $inputData['vnp_SecureHash'];
     unset($inputData['vnp_SecureHash']);
     ksort($inputData);
     $i = 0;
     $hashData = "";
     foreach ($inputData as $key => $value) {
         if ($i == 1) {
             $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
         } else {
             $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
             $i = 1;
         }
     }
     
     $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    //  $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
    //  $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
     $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi
     
     $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo 
    //  URL thanh toán.
     $orderId = $inputData['vnp_TxnRef'];
     
     try {
         //Check Orderid    
         //Kiểm tra checksum của dữ liệu
         if ($secureHash == $vnp_SecureHash) {
             //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
             //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
             //Giả sử: $order = mysqli_fetch_assoc($result);   
     
             $order = NULL;
             if ($order != NULL) {
                 if($order["Amount"] == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền 
                //  kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                 {
                     if ($order["Status"] != NULL && $order["Status"] == 0) {
                         if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                             $Status = 1; // Trạng thái thanh toán thành công
                         } else {
                             $Status = 2; // Trạng thái thanh toán thất bại / lỗi
                         }
                         //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                         //
                         //
                         //
                         //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công                
                         $returnData['RspCode'] = '00';
                         $returnData['Message'] = 'Confirm Success';
                     } else {
                         $returnData['RspCode'] = '02';
                         $returnData['Message'] = 'Order already confirmed';
                     }
                 }
                 else {
                     $returnData['RspCode'] = '04';
                     $returnData['Message'] = 'invalid amount';
                 }
             } else {
                 $returnData['RspCode'] = '01';
                 $returnData['Message'] = 'Order not found';
             }
         } else {
             $returnData['RspCode'] = '97';
             $returnData['Message'] = 'Invalid signature';
         }
     } catch (Exception $e) {
         $returnData['RspCode'] = '99';
         $returnData['Message'] = 'Unknow error';
     }
     //Trả lại VNPAY theo định dạng JSON
     echo json_encode($returnData);
} else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}
