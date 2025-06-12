

<?php
include_once('config.php');
include_once('action.php');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
$data = file_get_contents("php://input");
//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
$Post = json_decode($data, true);
if ($requestMethod == "POST") {
    if (empty($Post['row']) && empty($Post['newEmp']) && empty($Post['newEmpName'])) {
        echo json_encode(['message' => 'Data is empty']);
        http_response_code(400);
        return;
    }
    $newEmp = trim($Post['newEmpName']);
    $empId = str_replace("new", "", $Post['newEmp']);
    checkifDup($Post['row'], $empId);
    updateWorkerToAssing($empId, $newEmp, $Post['row']);
    echo json_encode(['message' => 'บันทึกรายชื่อพนักงานเรียบร้อยแล้ว']);
    http_response_code(200); // HTTP OK
    return;
}

function updateWorkerToAssing($emp_id, $emp_name, $row)
{
    global $conn85;
    $sql = "UPDATE [working_allocate_outsource].[dbo].[assing_by_vendor] SET emp_id = ? , [emp_name] = ?  WHERE id = ?";
    $params = [$emp_id, $emp_name, $row];
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Delete was successful
    }
    return false; // Delete failed
}
function checkifDup($row, $new)
{
    // ถ้ามีชื่อพนักงานนี้แล้วในช่วงวันที่นี้
    // getdate start stop old emp
    $oldEmpData = getDateStartOldEmp($row);
    $startDate = $oldEmpData[0]['dateStart'];
    $stopDate = $oldEmpData[0]['dateStop'];
    // check there is new emp and same startDate
    if (!empty(getDataStartNewEmp($new, $startDate))) {
        echo json_encode(['message' => 'พนักงานคนนี้มีรายชื่อในวันนี้อยู่แล้ว']);
        http_response_code(400); // HTTP OK
        die();
        return;
    }
}
function getDateStartOldEmp($row)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1000) [id]
        ,[request_doc]
        ,[emp_id]
        ,[emp_name]
        ,[work_location]
        ,[work_type]
        ,[dateStart]
        ,[dateStop]
        ,[checkIn]
        ,[checkOut]
        ,[status]
        FROM [working_allocate_outsource].[dbo].[assing_by_vendor] where id = '{$row}'
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
function getDataStartNewEmp($newEmp, $start)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1000) [id]
        ,[request_doc]
        ,[emp_id]
        ,[emp_name]
        ,[work_location]
        ,[work_type]
        ,[dateStart]
        ,[dateStop]
        ,[checkIn]
        ,[checkOut]
        ,[status]
        FROM [working_allocate_outsource].[dbo].[assing_by_vendor] where emp_id = '{$newEmp}' 
        and dateStart = '{$start->format('Y-m-d H:i')}'
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
