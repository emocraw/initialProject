<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {
    if (empty($Post['cardcode']) || empty($Post['th_name']) || empty($Post['checkin']) || empty($Post['jobassign'])) {
        echo json_encode(['message' => 'Bad parameter']);
        http_response_code(400);
        return;
    }
    if ($Post['isSpecial']) {
        $empId = substr($Post['cardcode'], -5);
        // canCelWokingData
        // canCelJobfro,m checkin Log
        cancelSpecialWorkingLog($empId, $Post['checkin']);
        echo json_encode(['message' => 'Insert successful']);
        http_response_code(200); // HTTP OK
        return;
    } else {
        $empId = substr($Post['cardcode'], -5);
        // canCelWokingData
        // canCelJobfro,m checkin Log
        canCelWokingData($Post['checkin'], $empId, $Post['jobassign']);
        cancelStatusWorkingLog($empId, $Post['checkin']);
        echo json_encode(['message' => 'Insert successful']);
        http_response_code(200); // HTTP OK
        return;
    }
}
