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
    if (empty($Post['workGroup'])) {
        http_response_code(400);
        echo json_encode(['message' => 'กรุณาเลือกกลุ่มงาน']);
        return;
    }
    // groupmachine ต้องตรงกับใน Database ด้วย [working_allocate_outsource].[dbo].[group_machine]
    $groupMachine = "";
    switch ($Post['workGroup']) {
        case 'PMN':
        case 'PAUTO':
            # code...
            $groupMachine = "Packing";
            break;
        case 'DPL':
            # code...
            $groupMachine = "Depallet";
            break;
        case 'MTN1':
        case 'MTN2':
        case 'MNT3':
            # code...
            $groupMachine = "Maintenance";
            break;
        case 'PL1':
        case 'PL3':
        case 'PL4':
        case 'PL5':
            # code...
            $groupMachine = "Painting";
            break;
        default:
            $groupMachine = $Post['workGroup'];
            break;
    }
    $getJobs = getJobsByGroups($groupMachine);
    if (empty($getJobs)) {
        http_response_code(400);
        echo json_encode(['message' => 'ไม่พบข้อมูล']);
        return;
    }
    http_response_code(200);
    echo json_encode($getJobs);
    return;
}
if ($requestMethod == "PUT") {
}
