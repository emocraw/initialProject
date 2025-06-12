<?php
include_once('config.php');
include_once('action.php');
include_once('sendEmailfuc.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {
    if (empty($Post['checkedIds'])) {
        echo json_encode(['message' => 'Bad parameter']);
        http_response_code(400);
        return;
    }

    foreach ($Post['checkedIds'] as $row) {
        # code...
        $id = $th_name = $checkIn = $checkOut = $cardcode = '';
        $id =  $row['id'];
        $th_name =  $row['th_name'];
        $checkIn =  $row['checkIn'];
        $checkOut =  $row['checkOut'];
        $cardcode =  $row['cardcode'];
        $oldcc = "xxxxxxxxxx";
        updateConfirmStatus($conn85, $id);
    }
    echo json_encode(['message' => 'Insert successful']);
    http_response_code(200); // HTTP OK
    return;
}
