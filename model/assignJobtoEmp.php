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
    $jobId = $Post['jobId'];
    $listchecked = $Post['checkedIds'];
    foreach ($listchecked as $row) {
        $th_name = $row['th_name'];
        $checkIn = $row['checkIn'];
        $checkOut = $row['checkOut'];
        $cardcode = $row['cardcode'];
        // ดึงข้อมูลพนักงานจาก cardcode
        $getEmpData = getEmpData($cardcode);
        if (empty($getEmpData)) {
            echo json_encode(['message' => 'ไม่พบรหัสพนักงานในระบบ']);
            http_response_code(400);
            return;
        }
        $emp_id = $getEmpData[0]['cardcode'];
        $th_name = $getEmpData[0]['th_name'];
        $company = $getEmpData[0]['company'];
        // ดึงข้อมูลการทำงานจาก checkIn ของพนักงาน
        $workingData = getWorkingData($checkIn, $emp_id);
        if (empty($workingData)) {
            echo json_encode(['message' => 'ไม่พบข้อมูลการทำงาน']);
            http_response_code(400);
            return;
        }
        $work_location = $workingData[0]['department'];
        // ดึงข้อมูลเครื่องจักรจาก work_location เพื่อหาค่า costcenter
        $machineData = getCC($work_location);
        if (empty($machineData)) {
            echo json_encode(['message' => 'ไม่พบข้อมูลเครื่องจักร']);
            http_response_code(400);
            return;
        }
        $costcenter = $machineData[0]['cost_center_hana'];

        // ฟังก์ชันหาค่า shift 
        // A,b,c,night,day
        $shift = findShift($checkIn, $checkOut);
        $working_date = getWorkingDate($checkIn);
        $GetJobData = getPrice($jobId);
        if (empty($GetJobData)) {
            echo json_encode(['message' => 'ไม่พบข้อมูลงาน']);
            http_response_code(400);
            return;
        }
        $jobName = $GetJobData[0]['work_name'];
        $price = $GetJobData[0]['Prices_manpower'];
        # code...
        UpdateJobtoEmp($emp_id, $th_name, $company, $work_location, $costcenter, $jobName, $shift, $working_date, $price, $checkIn, $checkOut);
        updateWokingData($checkIn, $emp_id, $jobName);
    }
    echo json_encode(['message' => 'Insert successful']);
    http_response_code(200); // HTTP OK
    return;
}
if ($requestMethod == "GET") {
}
if ($requestMethod == "PUT") {
}
