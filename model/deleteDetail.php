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
    if (empty($Post['id'])) {
        echo json_encode(['message' => 'EmptyPost']);
        http_response_code(400); // HTTP Bad Request
        return;
    }
    if (!deleterequest_worker_info($Post['id'])) {
        echo json_encode(['message' => 'deleterequest_worker_info Failed']);
        http_response_code(400); // HTTP Bad Request
        return;
    }
    if (!updateDoc($Post['qty'], $Post['doc'])) {
        echo json_encode(['message' => 'updateDoc Failed']);
        http_response_code(400); // HTTP Bad Request
        return;
    }
    $qtyleft = getQtyDoc($Post['doc']);
    if ($qtyleft < 1) {
        if (!updateDocCancel($Post['doc'])) {
            echo json_encode(['message' => 'updateDocCancel Failed']);
            http_response_code(400); // HTTP Bad Request
            return;
        }
    }
    echo json_encode(['message' => 'Success']);
    http_response_code(200); // HTTP OK
    return;
}
if ($requestMethod == "PUT") {
}
