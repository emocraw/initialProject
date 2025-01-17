<?php
// connection
session_start();
$serverName = '10.61.30.99'; // serverName\\instanceName, portNumber (default is 1433) Sample localhost\\sqlexpress, 1542
$connectionInfo = [
    'Database' => "allorawmat",
    'UID' => 'mes',
    'PWD' => 'me$12',
    'CharacterSet' => 'UTF-8',
];

$serverName2 = '10.61.30.85'; // serverName\\instanceName, portNumber (default is 1433) Sample localhost\\sqlexpress, 1542
$connectionInfo2 = [
    'Database' => 'rawmatconsum',
    'UID' => 'sa',
    'PWD' => 'P@ssw0rd',
    'CharacterSet' => 'UTF-8',
];

$conn = sqlsrv_connect($serverName, $connectionInfo);
$conn2 = sqlsrv_connect($serverName2, $connectionInfo2);
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

$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");

//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
