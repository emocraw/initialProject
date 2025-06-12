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
    'Database' => 'working_allocate_outsource',
    'UID' => 'sa',
    'PWD' => 'P@ssw0rd',
    'CharacterSet' => 'UTF-8',
];

$conn99 = sqlsrv_connect($serverName, $connectionInfo);
$conn85 = sqlsrv_connect($serverName2, $connectionInfo2);
date_default_timezone_set("Asia/Bangkok");
$webhookUrl = "https://sherasolutions.webhook.office.com/webhookb2/d183ebb2-6bd8-4ac2-b0ed-319e1eb2656f@3a9136c1-b8dc-48b7-9de6-1264ac6ced3e/IncomingWebhook/5b8980498acb4c999c89b22562d98b76/643df471-f76d-41d3-9b55-111bb0ee9009/V2LZPnecex-x5FGaMxriIpAR31ZRlc8dslNhpCM58Mjcs1";
$titleAlert = "Bot auto update check out os management";
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
