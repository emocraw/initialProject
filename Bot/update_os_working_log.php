<?php
include_once('config.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {
    // if (empty($Post['cardcode']) || empty($Post['th_name']) || empty($Post['checkin']) || empty($Post['jobassign'])) {
    //     echo json_encode(['message' => 'Bad parameter']);
    //     http_response_code(400);
    //     return;
    // }
    // $empId = substr($Post['cardcode'], -5);
    // // canCelWokingData
    // // canCelJobfro,m checkin Log
    // canCelWokingData($Post['checkin'], $empId, $Post['jobassign']);
    // cancelStatusWorkingLog($empId, $Post['checkin']);
    // echo json_encode(['message' => 'Insert successful']);
    // http_response_code(200); // HTTP OK
    // return;
}

if ($requestMethod == "GET") {
    $getNocheckOut = getNocheckOut();
    if (!empty($getNocheckOut)) {
        foreach ($getNocheckOut as $row) {
            # code...
            $checkOut = [];
            $checkOut = getCheckOut($row['emp_id'], $row['check_in']->format('Y-m-d H:i:s'));
            if (!empty($checkOut)) {
                $checkOut = $checkOut[0]['datetime'];
                $getShift = findShift($row['check_in']->format('Y-m-d H:i:s'), $checkOut->format('Y-m-d H:i:s'));
                $shift = $getShift;
                updateCheckOut($row['id'], $checkOut, $shift);
            }
        }
        echo json_encode(['message' => 'Update successful']);
        http_response_code(200); // HTTP OK
    }
}

function getNocheckOut()
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [id]
      ,[emp_id]
      ,[emp_name]
      ,[emp_company]
      ,[work_location]
      ,[costcenter]
      ,[job]
      ,[shift]
      ,[working_date]
      ,[price]
      ,[check_in]
      ,[check_out]
      ,[timestamp]
      ,[status]
  FROM [working_allocate_outsource].[dbo].[os_working_log] 
  where status is null 
  and check_out is null
  order by id desc";
    $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                array_push($data, $show);
            }
        }
    }
    return $data;
}
function getCheckOut($empId, $checkInDate)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1000) [id]
      ,[qr_number]
      ,[datetime]
      ,[purpose]
      ,[department]
      ,[checkin_method]
      ,[status]
      ,[remarks]
      ,[created_at]
      ,[update_at]
      ,[jobassign]
        FROM [Entrance_permit].[dbo].[checkin_log]
        WHERE qr_number LIKE '%{$empId}'
        AND datetime BETWEEN '{$checkInDate}' AND DATEADD(HOUR, 20, '{$checkInDate}')
        AND status = 'Out'
        AND department != 'ENT'
        ";
    $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                array_push($data, $show);
            }
        }
    }
    return $data;
}
function updateCheckOut($rowId, $checkOut, $shift)
{

    global $conn85;
    global $webhookUrl;
    global $titleAlert;
    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[os_working_log]
            SET check_out = ?,
            shift = ?
            WHERE id = ?";
    // Define the parameters for the query
    $params = [$checkOut, $shift, $rowId];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        $sMessage = 'Error: ' . sqlsrv_errors()[0]['message'] . "\n";
        $sMessage .= 'Time stamp :' . date('Y-m-d');
        sendAdaptiveCardToTeams($webhookUrl, $titleAlert, "Error", $sMessage);
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true;
    }
    echo json_encode(['message' => "Update failed"]);
    http_response_code(400); // HTTP Bad Request
    $sMessage = 'Error: Update failed' . "\n";
    $sMessage .= 'Row ID: ' . $rowId . "\n";
    $sMessage .= 'Time stamp :' . date('Y-m-d');
    sendAdaptiveCardToTeams($webhookUrl, $titleAlert, "Error", $sMessage);
    die();
    return false; // Insert failed

}
function findShift($checkIn, $checkOut)
{
    try {
        $checkInTime = new DateTime($checkIn);
        $checkOutTime = new DateTime($checkOut);

        $ci = $checkInTime->format('H:i');
        $co = $checkOutTime;
        $isNextDay = $checkOutTime->format('Y-m-d') !== $checkInTime->format('Y-m-d');

        // --- กะ Day: 06:00 - 10:00 in, out >= 20:00 (same day)
        if (
            $ci >= '06:00' && $ci <= '10:00' &&
            !$isNextDay &&
            $co->format('H:i') >= '20:00'
        ) {
            return 'Day';
        }

        // --- กะ A: 06:00 - 10:00 in, 16:00 <= out < 19:00 (same day)
        if (
            $ci >= '06:00' && $ci <= '10:00' &&
            !$isNextDay &&
            $co->format('H:i') >= '16:00' &&
            $co->format('H:i') < '19:00'
        ) {
            return 'A';
        }

        // --- กะ B: 15:00 - 18:00 in, out >= 00:00 ของวันถัดไป
        if (
            $ci >= '15:00' && $ci <= '18:00' &&
            $isNextDay
        ) {
            return 'B';
        }

        // --- กะ C: 23:00 - 01:00 in, out >= 08:00 ของวันถัดไป
        if (
            (
                ($ci >= '23:00' && $ci <= '23:59') ||
                ($ci >= '00:00' && $ci <= '01:00')
            ) &&
            $isNextDay &&
            $co->format('H:i') >= '08:00'
        ) {
            return 'C';
        }

        // --- กะ night: 19:00 - 21:00 in, out >= 08:00 ของวันถัดไป
        if (
            $ci >= '19:00' && $ci <= '21:00' &&
            $isNextDay &&
            $co->format('H:i') >= '08:00'
        ) {
            return 'night';
        }

        return 'ไม่เข้ากะ';
    } catch (Exception $e) {
        // One or both inputs are invalid
        return 'ไม่เข้ากะ';
    }
}
function sendAdaptiveCardToTeams($webhookUrl, $title, $status, $resMessage)
{
    $adaptiveCard = [
        "type" => "MessageCard",
        "themeColor" => "0076D7",
        "summary" => "แจ้งเตือนจากระบบ",
        "sections" => [
            [
                "activityTitle" => "**แจ้งเตือน:** {$title}",
                "activitySubtitle" => "วันที่: " . date("Y-m-d H:i:s"),
                "activityImage" => "https://via.placeholder.com/50",
                "facts" => [
                    ["name" => "สถานะ:", "value" => $status],
                    ["name" => "รายละเอียด:", "value" => $resMessage]
                ],
                "markdown" => true
            ]
        ],
    ];

    $jsonData = json_encode($adaptiveCard);

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 200) {
        echo "Adaptive Card sent successfully!";
    } else {
        echo "Failed to send Adaptive Card. HTTP Code: $httpCode";
    }
}
