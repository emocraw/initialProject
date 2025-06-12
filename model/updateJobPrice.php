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
    if (empty($Post['jobId']) || empty($Post['workName']) || empty($Post['price']) || empty($Post['group_id']) || empty($Post['group_text'])) {
        http_response_code('400');
        echo json_encode(["message" => "ข้อมูลไม่ครบถ้วน"]);
        return;
    }

    $updateJobDetail = updateJobDetail($Post['jobId'], $Post['workName'], $Post['price'], $Post['group_id'], $Post['group_text']);
    http_response_code('200');
    echo json_encode(["message" => "อัพเดทข้อมูลเรียบร้อย"]);
    return;


    http_response_code(200);
    echo json_encode($findVendorCode[0]);
    return;
}
if ($requestMethod == "PUT") {
}
