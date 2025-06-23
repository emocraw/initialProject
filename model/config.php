<?php
// connection
session_start();
function loadEnv($file)
{
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

loadEnv('.env');

$serverName = getenv('DB1_HOST');
$connectionInfo = [
    'Database' => getenv('DB1_NAME'),
    'UID' => getenv('DB1_USER'),
    'PWD' => getenv('DB1_PASS'),
    'CharacterSet' => getenv('DB1_CHARSET'),
    "Encrypt" => 1,                     // ใช้ SSL/TLS
    "TrustServerCertificate" => 1
];

$serverName2 = getenv('DB2_HOST');
$connectionInfo2 = [
    'Database' => getenv('DB2_NAME'),
    'UID' => getenv('DB2_USER'),
    'PWD' => getenv('DB2_PASS'),
    'CharacterSet' => getenv('DB2_CHARSET'),
    "Encrypt" => 1,                     // ใช้ SSL/TLS
    "TrustServerCertificate" => 1
];

$conn99 = sqlsrv_connect($serverName, $connectionInfo);
$conn85 = sqlsrv_connect($serverName2, $connectionInfo2);
date_default_timezone_set("Asia/Bangkok");

//กำหนดค่า Access-Control-Allow-Origin ให้ เครื่อง อื่น ๆ สามารถเรียกใช้งานหน้านี้ได้
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']))
// {
//     $usernameApi = $_SERVER['PHP_AUTH_USER']; // username
//     $passwordApi = $_SERVER['PHP_AUTH_PW']; // password
// }
