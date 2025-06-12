

<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {

    if (empty($Post['currentInfoId']) || empty($Post['startDateCurrent']) || empty($Post['stopDateCurrent']) || empty($Post['doc']) || empty($Post['work_type']) || empty($Post['workLocation']) || empty($Post['company']) || empty($Post['empsList'])) {
        echo json_encode(['message' => 'Data is empty']);
        http_response_code(400);
        return;
    }
    $doc = $Post['doc'];
    $mcname = $Post['company'];
    $work_type = $Post['work_type'];
    $work_location = $Post['workLocation'];
    $empsList = $Post['empsList'];
    $countEmp = count($empsList);
    $getDateByDoc = getDateByDoc($doc, $work_type);
    $startDate = $Post['startDateCurrent'];
    $stopDate = $Post['stopDateCurrent'];
    $countDay = getCountdays($startDate, $stopDate);
    // แปลงเวลาเป็น timestamp
    $startTime = substr($startDate, -5);
    $endTime = substr($stopDate, -5);
    $results = [];
    if ($startTime > $endTime) {
        // กรณีข้ามเที่ยงคืน - 1 วันเนื่องจากวันสุดท้ายจะเกิน Stopdate
        $countDay -= 1;
    }
    // Insert แยกวัน
    for ($i = 0; $i < $countDay; $i++) {
        // ตั้งค่า startDate สำหรับแต่ละวัน
        $currentStart = new DateTime($startDate);
        if ($i > 0) {
            $currentStart->modify("+{$i} day");
        }

        // ตั้งเวลาให้ตรงกับ $startDate
        list($startHour, $startMinute) = explode(':', $startTime);
        $currentStart->setTime($startHour, $startMinute);
        $currentDate = $currentStart->format('Y-m-d H:i');

        // ตั้งค่า stopDate โดยข้ามไปอีกวันและตั้งเวลาให้ตรงกับ $stopDate
        $nextDate = clone $currentStart;
        if ($startTime > $endTime) {
            $nextDate->modify('+1 day');
        }
        list($endHour, $endMinute) = explode(':', $endTime);
        $nextDate->setTime($endHour, $endMinute);
        $newEndDate = $nextDate->format('Y-m-d H:i');
        // เก็บผลลัพธ์ใน array
        // $results[] = [
        //     "startDate" => $currentDate,
        //     "stopDate" => $newEndDate
        // ];

        //Check dup assign
        foreach ($empsList as $emp_id) {
            $getEmp_by_id = get_emp_by_id($emp_id);
            $emp_name = $getEmp_by_id[0]['th_name'];
            $onlyCurrentDate = substr($currentDate, 0, 10);
            if (!empty(checkWorkerDupAssign($emp_id, $onlyCurrentDate))) {
                http_response_code(400); // HTTP OK
                echo json_encode(['message' => "พนักงานชื่อ {$emp_name} มีรายชื่อในวันนี้อยู่แล้ว"]);
                return;
            }
        }

        foreach ($empsList as $emp_id) {
            $getEmp_by_id = get_emp_by_id($emp_id);
            $emp_name = $getEmp_by_id[0]['th_name'];
            insertWorkerToDoc($doc, $emp_id, $mcname, $emp_name, $work_location, $work_type, $currentDate, $newEndDate, $Post['currentInfoId']);
        }
    }
    UpdateReceiveinfoDoc($countEmp, $doc, $work_type, $startDate, $stopDate, $Post['currentInfoId']);
    echo json_encode(['message' => 'บันทึกรายชื่อพนักงานเรียบร้อยแล้ว']);
    http_response_code(200); // HTTP OK
    return;
}
