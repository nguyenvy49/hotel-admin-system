<?php
require "vnpay_config.php";

// Validate config
if (!isset($vnp_config)) die("VNPay config not loaded");

// Get param
$invoiceId = intval($_GET["hoa_don"] ?? 0);
$amount    = intval($_GET["amount"] ?? 0);

// VNPay quy định số tiền hợp lệ
if ($amount < 5000) $amount = 5000;
if ($amount >= 1000000000) die("Amount too large");

// ====== FIX CODE 15 – Chuẩn hóa thời gian ======
$date = new DateTime("now", new DateTimeZone("Asia/Ho_Chi_Minh"));
$vnp_CreateDate = $date->format("YmdHis");

$date->modify("+15 minutes"); // phải >= 5 phút
$vnp_ExpireDate = $date->format("YmdHis");

// VNPay fields
$inputData = [
    "vnp_Version"   => "2.1.0",
    "vnp_Command"   => "pay",
    "vnp_TmnCode"   => $vnp_config["TmnCode"],
    "vnp_Amount"    => $amount * 100,
    "vnp_CurrCode"  => "VND",
    "vnp_TxnRef"    => $invoiceId,
    "vnp_OrderInfo" => "Thanh toan dat coc hoa don #" . $invoiceId,
    "vnp_OrderType" => "hotel-deposit",
    "vnp_Locale"    => "vn",
    "vnp_ReturnUrl" => $vnp_config["ReturnUrl"],
    "vnp_IpAddr"    => $_SERVER['REMOTE_ADDR'],
    "vnp_CreateDate"=> $vnp_CreateDate,
    "vnp_ExpireDate"=> $vnp_ExpireDate
];

// Build query
ksort($inputData);

$query = "";
$hashData = "";

foreach ($inputData as $k => $v) {
    $query .= urlencode($k)."=".urlencode($v)."&";
    $hashData .= urlencode($k)."=".urlencode($v)."&";
}

$query = rtrim($query, "&");
$hashData = rtrim($hashData, "&");

// Secure hash
$vnp_SecureHash = hash_hmac("sha512", $hashData, $vnp_config["HashSecret"]);

$vnp_Url = $vnp_config["BaseUrl"]."?".$query."&vnp_SecureHash=".$vnp_SecureHash;

header("Location: $vnp_Url");
exit;
