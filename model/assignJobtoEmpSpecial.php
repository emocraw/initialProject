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
    if (empty($Post)) {
        echo json_encode(['message' => 'Data is empty']);
        http_response_code(400);
        return;
    }
    $jobId = $Post['jobId'];
    $listchecked = $Post['checkedIds'];
    $receivers = [];
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
        // $machineData = getCC($work_location);
        // if (empty($machineData)) {
        //     echo json_encode(['message' => 'ไม่พบข้อมูลเครื่องจักร']);
        //     http_response_code(400);
        //     return;
        // }
        // $costcenter = $machineData[0]['cost_center_hana'];

        // ฟังก์ชันหาค่า shift 
        // A,b,c,night,day
        $shift = findShift($checkIn, $checkOut);
        if ($shift == "ไม่เข้ากะ") {
            $shift = "ข้อมูลการ Check in ไม่ครบ";
        }
        $working_date = getWorkingDate($checkIn);
        $GetJobData = getPrice($jobId);

        // ดึงข้อมูล CC จากงาน
        $costcenter = $GetJobData[0]['cc'];
        if (empty($GetJobData)) {
            echo json_encode(['message' => 'ไม่พบข้อมูล Cost center ของงานนี้']);
            http_response_code(400);
            return;
        }
        $jobName = $GetJobData[0]['work_name'];
        $price = $GetJobData[0]['Prices_manpower'];
        $supEmail = "";
        $supName = "";
        $getSupEmailBycc = findSupEmail($costcenter);
        if (empty($getSupEmailBycc)) {
            echo json_encode(['message' => 'ไม่พบข้อมูล Email ของ Sup ปลายทาง']);
            http_response_code(400);
            return;
        }
        $mcReceiver = $getSupEmailBycc[0]["Name"];
        $supEmail = $getSupEmailBycc[0]["Sup_Mail"];
        $supName = $getSupEmailBycc[0]["Sup_Name_Surname"];
        # code...
        UpdateJobSpecialtoEmp($costcenter, $price, $jobName, $mcReceiver, $emp_id, $checkIn);
        // updateWokingData($checkIn, $emp_id, $jobName);
        $exists = false;
        foreach ($receivers as $receiver) {
            if ($receiver['supEmail'] === $supEmail && $receiver['supName'] === $supName && $receiver['receiver'] === $mcReceiver) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $receivers[] = array(
                "work_location" => $work_location,
                "receiver" => $mcReceiver,
                "supEmail" => $supEmail,
                "supName" => $supName,
            );
        }
    }
    foreach ($receivers as $receiver) {
        $departsend = $departReci = $dateStart = $dateStop = $supMail = "";
        $departsend = $receiver['work_location'];
        $departReci = $receiver['receiver'];
        $supMail = $receiver['supEmail'];
        $supName = $receiver['supName'];
        # code...
        sendMail($departsend, $departReci, $working_date, $shift, $supMail, $supName);
    }
    // เหลือส่งเมล อัพเดทอะไรไปบ้างให้ส่ง Mail เท่านั้น
    echo json_encode(['message' => 'Insert successful']);
    http_response_code(200); // HTTP OK
    return;
}
if ($requestMethod == "GET") {
}
if ($requestMethod == "PUT") {
}
