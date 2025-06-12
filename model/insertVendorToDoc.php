<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {
    if (empty($Post)) {
        echo json_encode(['message' => 'Data is empty']);
        http_response_code(400);
        return;
    }
    foreach ($Post as $row) {
        $documentNumber = $row['documentNumber'];
        $jobType = $row['jobType'];
        $contractorName = $row['contractorName'];
        $rowId = $row['rowId'];
        # code...
        UpdateVendortoDoc($rowId, $contractorName, $documentNumber, $jobType);
    }
    echo json_encode(['message' => 'Insert successful']);
    http_response_code(200); // HTTP OK
    return;
}
