<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);

if ($requestMethod == "GET") {
}
if ($requestMethod == "POST") {
    echo json_encode(getWorkerAssigned($Post['company']));
}
if ($requestMethod == "PUT") {
}
