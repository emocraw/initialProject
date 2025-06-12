<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);

if ($requestMethod == "GET") {
    if (empty($_GET['vendor'])) {
        http_response_code(400);
        echo json_encode(['message' => 'ข้อมูลผิดพลาด']);
    }
    http_response_code(200);
    echo json_encode(getDocByVendor($_GET['vendor']));
}
if ($requestMethod == "POST") {
}
if ($requestMethod == "PUT") {
}
