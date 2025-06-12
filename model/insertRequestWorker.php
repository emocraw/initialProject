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
    // EmptyPost
    if (empty($Post)) {

        echo json_encode(['message' => 'EmptyPost']);
        http_response_code(400); // HTTP Bad Request
    }
    $machine = $Post[0]['machine'];
    $getlastDoc = getDocBymc($machine);
    if ($getlastDoc) {
        $lastDoc = $getlastDoc[0]['doc_number'];
        if (strlen($lastDoc) < 12) {
            $year = date('y');
            $newDoc = "{$machine}RWD{$year}00001";
        } else {
            // แยกส่วนของ Prefix, ปี และตัวเลข
            $prefix = substr($lastDoc, 0, 6); // M08RWC
            $year = substr($lastDoc, 6, 2); // 25
            $number = substr($lastDoc, 8); // 00001
            // ตรวจสอบว่าปีในสตริงตรงกับปีปัจจุบันหรือไม่
            $currentYear = date('y');
            if ($year == $currentYear) {
                // ถ้าปีตรงกัน ให้บวกเลขท้าย
                $number = intval($number) + 1;
            } else {
                // ถ้าปีไม่ตรงกัน ให้เริ่มนับใหม่ที่ 00001
                $year = $currentYear;
                $number = 1;
            }

            // แปลงกลับเป็นสตริงโดยเติมเลข 0 นำหน้าให้ครบ 5 หลัก
            $newNumber = str_pad($number, 5, "0", STR_PAD_LEFT);

            // รวม Prefix, ปี และเลขท้ายเข้าด้วยกัน
            $newDoc = $prefix . $year . $newNumber;
        }
    } else {
        $year = date('y');
        $newDoc = "{$machine}RWD{$year}00001";
    }
    $sumQty = 0;
    foreach ($Post as $row) {
        $sumQty += $row['peopleCount'];
    }
    $insertDoc = insertDoc($newDoc, $machine, $sumQty);
    if ($insertDoc) {
        foreach ($Post as $row) {
            $skill = $row['skill'];
            $qty = $row['peopleCount'];
            str_replace("T", " ", $row['startDate']);
            $workStart = str_replace("T", " ", $row['startDate']);
            $workStop = str_replace("T", " ", $row['endDate']);
            if (!insertDetail($skill, $machine, $qty, $workStart, $workStop, $newDoc)) {
                // ถ้า Insert Detail ล้มเหลว ให้ลบ Doc ออก
                deleteDoc($newDoc);
                echo json_encode(['message' => 'Insert Detail failed']);
                http_response_code(400); // HTTP Bad Request
                return;
            }
        }
        echo json_encode(['message' => 'Insert successful']);
        http_response_code(200); // HTTP OK
        return;
    } else {
        echo json_encode(['message' => 'Insert failed']);
        http_response_code(400); // HTTP Bad Request
    }
    return;
}
if ($requestMethod == "PUT") {
}
