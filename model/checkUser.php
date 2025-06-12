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
    $checkUser = checkUser($Post['user'], $Post['type'], $Post['token']);
    if (empty($checkUser)) {
        http_response_code('401');
        echo json_encode(['message' => "Unauthorized"]);
        return;
    }

    http_response_code('200');
    echo json_encode(['message' => 'ok']);
    return;
}
if ($requestMethod == "PUT") {
}
