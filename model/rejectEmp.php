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
    $receivers = [];
    if (empty($Post['checkedIds'])) {
        echo json_encode(['message' => 'Bad parameter']);
        http_response_code(400);
        return;
    }
    $mcSendBack = $Post['mcName'];
    foreach ($Post['checkedIds'] as $row) {
        # code...

        $id = $th_name = $checkIn = $checkOut = $cardcode = $working_date = $shift = '';
        $id =  $row['id'];
        $th_name =  $row['th_name'];
        $checkIn =  $row['checkIn'];
        $checkOut =  $row['checkOut'];
        $cardcode =  $row['cardcode'];
        $oldcc = "xxxxxxxxxx";
        $shift = findShift($checkIn, $checkOut);
        if ($shift == "ไม่เข้ากะ") {
            $shift = "ข้อมูลการ Check in ไม่ครบ";
        }
        $working_date = getWorkingDate($checkIn);
        if (empty($cardcode) || empty($th_name) || empty($checkIn) || empty($id)) {
            echo json_encode(['message' => 'Bad parameter']);
            http_response_code(400);
            return;
        }

        $getos_working_log = GetOs_working_log($conn85, $id);
        $mcEntry = $getos_working_log[0]["work_location"];
        $supEmail = $getos_working_log[0]["email_receive"];
        $supName = $getos_working_log[0]["name_receive"];
        // canCelJobfrom checkin Log
        cancelSpecialWorkingLog($cardcode, $checkIn);
        // Send mail cancel
        $exists = false;
        foreach ($receivers as $receiver) {
            if ($receiver['supEmail'] === $supEmail && $receiver['supName'] === $supName && $receiver['receiver'] === $mcEntry) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $receivers[] = array(
                "work_location" => $mcSendBack,
                "receiver" => $mcEntry,
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
        sendMailReject($departsend, $departReci, $working_date, $shift, $supMail, $supName);
    }
    echo json_encode(['message' => 'Insert successful']);
    http_response_code(200); // HTTP OK
    return;
}
