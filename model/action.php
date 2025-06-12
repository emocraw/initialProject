<?php
function getDoc()
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1) [doc_number]
    FROM [working_allocate_outsource].[dbo].[request_worker_doc] order by [create_date] desc";
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
function getDocBymc($mcname)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1) [doc_number]
    FROM [working_allocate_outsource].[dbo].[request_worker_doc] where work_location = '{$mcname}' order by [create_date] desc";
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
function getWorkGroup()
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [group_id]
      ,[group_name]
      ,[create_date]
      ,[update_time]
    FROM [working_allocate_outsource].[dbo].[group_machine] order by group_name";
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
// function getCheckInData($mcname, $dateInput)
// {
//     global $conn85;
//     $data = [];
//     $sql = "SELECT abv.[request_doc]
//         ,abv.[emp_id]
//         ,abv.[emp_name]
//         ,abv.[work_location]
//         ,abv.[work_type]
//         ,abv.[dateStart]
//         ,abv.[dateStop]
//         ,MIN(cl.[datetime]) AS checkIn
//         ,MAX(cl.[datetime]) AS checkOut
//         ,abv.[status]
//         FROM [working_allocate_outsource].[dbo].[assing_by_vendor] AS abv
//         LEFT JOIN [Entrance_permit].[dbo].[checkin_log] AS cl
//         ON RIGHT(cl.qr_number, 5) = RIGHT(CAST(abv.emp_id AS NVARCHAR), 5) 
//         and abv.[work_location] = cl.department
//         and [datetime] BETWEEN '{$dateInput} 00:00:00' AND '{$dateInput} 23:59:59'
//         WHERE abv.work_location = '{$mcname}'
//         AND abv.dateStart BETWEEN '{$dateInput} 00:00:00' AND '{$dateInput} 23:59:59'
//         GROUP BY abv.[request_doc], abv.[emp_id], abv.[emp_name], abv.[work_location], 
//             abv.[work_type], abv.[dateStart], abv.[dateStop], abv.[checkOut], abv.[status]
//             ";
//     $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
//     if ($result) {
//         $answer = sqlsrv_num_rows($result);
//         if ($answer) {
//             while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
//                 array_push($data, $show);
//             }
//         }
//     }
//     return $data;
// }
function getCheckInData($mcname, $dateInput)
{


    $date = new DateTime($dateInput);
    $date2 = new DateTime($dateInput);
    $date2->modify("+1 day");
    $date->modify("-1 day");
    $lastDay = $date->format("Y-m-d");
    $nextDay = $date2->format("Y-m-d");
    global $conn85;
    $data = [];
    $sql = "";
    switch ($mcname) {
        case 'PMN':
            # code...
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('P01','P02','P04')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('P01','P02','P04')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
        case 'All':
            # code...
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
                COALESCE(inData.th_name, outData.th_name) AS th_name,
                COALESCE(inData.company, outData.company) AS company,
                COALESCE(inData.department, outData.department) AS department,
                jobassign,
                inData.timeIn,
                outData.timeOut
                FROM 
                    (SELECT 
                        os_employee.th_name,
                        checkin_log.qr_number, 
                        checkin_log.datetime AS timeIn, 
                        os_employee.company, 
                        checkin_log.jobassign,
                        checkin_log.department
                    FROM Entrance_permit.dbo.checkin_log
                    LEFT JOIN Entrance_permit.dbo.os_employee 
                    ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                    WHERE checkin_log.status = 'In' 
                    AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                    ) AS inData
                FULL OUTER JOIN 
                    (SELECT 
                        os_employee.th_name, 
                        checkin_log.qr_number, 
                        checkin_log.datetime AS timeOut, 
                        os_employee.company, 
                        checkin_log.department
                    FROM Entrance_permit.dbo.checkin_log
                    LEFT JOIN Entrance_permit.dbo.os_employee 
                    ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                    WHERE checkin_log.status = 'Out' 
                    AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                    ) AS outData
                ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
        case 'PAUTO':
            # code...
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
        default:
            # code...เขียนใหม่นะ
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('{$mcname}')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('{$mcname}')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
    }

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
function getnotAssigned($mcname, $dateInput)
{
    $date = new DateTime($dateInput);
    $date2 = new DateTime($dateInput);
    $date2->modify("+1 day");
    $date->modify("-1 day");
    $lastDay = $date->format("Y-m-d");
    $nextDay = $date2->format("Y-m-d");
    global $conn85;
    $data = [];
    $sql = "";
    switch ($mcname) {
        case 'PMN':
            # code...
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('P01','P02','P04')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('P01','P02','P04')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
        case 'PAUTO':
            # code...
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
        default:
            # code...เขียนใหม่นะ
            $sql = "SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('{$mcname}')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('{$mcname}')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number order by inData.timeIn desc";
            break;
    }
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
function getJobs()
{
    global $conn85;
    $data = [];
    $sql = "SELECT [job_id]
      ,[work_name]
      ,[Prices_manpower]
      ,[group_machine]
      ,[create_date]
      ,[updateTime]
  FROM [working_allocate_outsource].[dbo].[jobprice]";
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

function getJobsByGroups($group)
{
    global $conn85;
    $data = [];
    $sql = "SELECT [job_id]
      ,[work_name]
      ,[Prices_manpower]
      ,[group_machine]
      ,[create_date]
      ,[updateTime]
  FROM [working_allocate_outsource].[dbo].[jobprice] where group_machine = '{$group}'";
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
function getGroupMc()
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [group_id]
      ,[group_name]
      ,[create_date]
      ,[update_time]
    FROM [working_allocate_outsource].[dbo].[group_machine]";
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
function checkUser($user, $type, $token)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1) [id]
      ,[token]
      ,[updatetime]
      ,[user_type]
  FROM [working_allocate_outsource].[dbo].[user_session] where id = '{$user}' and token = '{$token}' and [user_type] = '{$type}'";
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
function createNewPassword($password, $vendorCode)
{
    global $conn85;

    // Define the SQL query
    $sql = "UPDATE [Entrance_permit].[dbo].[Vendor_epro] 
    SET [password] = ENCRYPTBYPASSPHRASE('KEY','{$password}') where RIGHT([Vendor Code] , 7 ) = '{$vendorCode}'";
    // Define the parameters for the query
    $params = [$password, $vendorCode];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true;
    }


    echo json_encode(['message' => "Update failed"]);
    http_response_code(400); // HTTP Bad Request
    die();
    return false; // Insert failed
}
function findVendor($VendorCode)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP (1) [Vendor Code]
        ,[Vendor Name] AS [VendorName]
        ,[Vendor_shortname]
        ,CASE 
            WHEN [password] IS NOT NULL AND DATALENGTH([password]) > 0 THEN N'มี' 
            ELSE N'ไม่มี' END AS password_status
        FROM [Entrance_permit].[dbo].[Vendor_epro] where RIGHT([Vendor Code] , 7 ) = '{$VendorCode}'";
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
function insertDoc($doc, $location, $qty)
{

    global $conn85;

    // Define the SQL query
    $sql = "  INSERT INTO [working_allocate_outsource].[dbo].[request_worker_doc] ([doc_number]
      ,[work_location]
      ,[worker_require],[create_date]) VALUES (?,?,?,GETDATE())";

    // Define the parameters for the query
    $params = [$doc, $location, $qty];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        deleteDoc($doc);
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function updateConfirmStatus($conn85, $id)
{
    global $conn85;

    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[os_working_log] 
    SET [confirm_job_transfer_status] = 'confirm' 
    , confirm_date = GETDATE()
    WHERE id = ?";
    // Define the parameters for the query
    $params = [$id];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true;
    }


    echo json_encode(['message' => "Update failed"]);
    http_response_code(400); // HTTP Bad Request
    die();
    return false; // Insert failed
}
function updateJobDetail($jobId, $workName, $price, $group_id, $group_text)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[jobprice] 
    SET Prices_manpower = ?
    , group_machine = ?
    , group_machine_id = ?
    , updateTime = GETDATE() WHERE [job_id] = ?";
    // Define the parameters for the query
    $params = [$price, $group_text, $group_id, $jobId];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function insertDetail($skill, $location, $qty, $workStart, $workStop, $doc)
{
    global $conn85;
    // Define the SQL query
    $sql = "   INSERT INTO [working_allocate_outsource].[dbo].[request_worker_info] ([work_type]
      ,[work_location]
      ,[worker_require]
      ,[work_startDate]
      ,[work_endDate]
      ,[create_date]
      ,[request_worker_doc]) VALUES (?,?,?,?,?,GETDATE(),?)";

    // Define the parameters for the query
    $params = [$skill, $location, $qty, $workStart, $workStop, $doc];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        deleteDoc($doc);
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function deleteDoc($doc)
{
    global $conn85;
    $sql = "DELETE FROM [working_allocate_outsource].[dbo].[request_worker_doc] WHERE [doc_number] = ?";
    $params = [$doc];
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
function GetOs_working_log($conn85, $id)
{
    $data = [];
    $sql = "SELECT 
            [id],
            [emp_id],
            [emp_name],
            [emp_company],
            [work_location],
            [Cost_center_detail].Cost_center AS cost_receive,
            [Cost_center_detail].Sup_Mail AS email_receive,
            [Cost_center_detail].Sup_Name_Surname AS name_receive,
            [costcenter],
            [job],
            [shift],
            [working_date],
            [price],
            [check_in],
            [check_out],
            [timestamp],
            [status],
            [job_transfer],
            [mc_receive]
        FROM [working_allocate_outsource].[dbo].[os_working_log]
        LEFT JOIN [Allocate_Working_Time].[dbo].[Cost_center_detail]
        ON CASE 
            WHEN [os_working_log].work_location = 'DPL' THEN 'DE-PALLET'
            WHEN [os_working_log].work_location = 'P01' THEN 'Manual Pack'
            WHEN [os_working_log].work_location = 'P02' THEN 'Manual Pack'
            WHEN [os_working_log].work_location = 'P03' THEN 'Manual Pack'
            WHEN [os_working_log].work_location = 'P04' THEN 'Manual Pack'
            WHEN [os_working_log].work_location = 'P05' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P06' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P07' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P08' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P09' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P10' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'P11' THEN 'Auto Pack'
            WHEN [os_working_log].work_location = 'DPL' THEN 'DE-PALLET'
			WHEN [os_working_log].work_location = 'SK1' THEN 'Skid-Manual'
            ELSE [os_working_log].work_location
            END = [Cost_center_detail].[Name]
        WHERE id = ?";
    $result = sqlsrv_query($conn85, $sql, array($id), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
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
function cancelSpecialWorkingLog($cardcode, $timeIn)
{
    global $conn85;
    // Define the SQL query
    $sql = "DECLARE @target DATETIME = '{$timeIn}';
    UPDATE [working_allocate_outsource].[dbo].[os_working_log] 
    SET [job_transfer] = null , [mc_receive] = null , costcenter = 'xxxxxxxxxx'
    WHERE emp_id like '%{$cardcode}' and check_in >= @target 
    AND [check_in] < DATEADD(MINUTE, 1, @target) and [status] is null";
    // Define the parameters for the query
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // I
}
function getAssignedSpacial($mcname, $dateInput)
{
    $date = new DateTime($dateInput);
    $date2 = new DateTime($dateInput);
    $date2->modify("+1 day");
    $date->modify("-1 day");
    $lastDay = $date->format("Y-m-d");
    $nextDay = $date2->format("Y-m-d");
    global $conn85;
    $data = [];
    $sql = "";
    switch ($mcname) {
        case 'PMN':
            # code...
            $sql = "SELECT cardcode,th_name,company,department,jobassign
            ,[os_working_log].job_transfer ,timeIn,timeOut,os_working_log.[confirm_job_transfer_status]
	        FROM	(
                SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
                COALESCE(inData.th_name, outData.th_name) AS th_name,
                COALESCE(inData.company, outData.company) AS company,
                COALESCE(inData.department, outData.department) AS department,
                jobassign,
                inData.timeIn,
                outData.timeOut
                FROM 
                        (SELECT 
                            os_employee.th_name,
                            checkin_log.qr_number, 
                            checkin_log.datetime AS timeIn, 
                            os_employee.company, 
                            checkin_log.jobassign,
                            checkin_log.department
                        FROM Entrance_permit.dbo.checkin_log
                        LEFT JOIN Entrance_permit.dbo.os_employee 
                        ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                        WHERE checkin_log.status = 'In' and checkin_log.department in ('P01','P02','P04')
                        AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                        ) AS inData
                    FULL OUTER JOIN 
                        (SELECT 
                            os_employee.th_name, 
                            checkin_log.qr_number, 
                            checkin_log.datetime AS timeOut, 
                            os_employee.company, 
                            checkin_log.department
                        FROM Entrance_permit.dbo.checkin_log
                        LEFT JOIN Entrance_permit.dbo.os_employee 
                        ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                        WHERE checkin_log.status = 'Out' and checkin_log.department in ('P01','P02','P04')
                        AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                        ) AS outData
                ON inData.qr_number = outData.qr_number where jobassign = 'special' ) checkin_out
                LEFT JOIN [working_allocate_outsource].[dbo].[os_working_log]
                ON [os_working_log].emp_id = RIGHT(checkin_out.cardcode,5) 
                where [os_working_log].check_in  BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'";
            break;
        case 'PAUTO':
            # code...
            $sql = "	SELECT cardcode,th_name,company,department,jobassign,[os_working_log].job_transfer 
            ,timeIn,timeOut,os_working_log.[confirm_job_transfer_status]
	        FROM	(SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
            COALESCE(inData.th_name, outData.th_name) AS th_name,
            COALESCE(inData.company, outData.company) AS company,
            COALESCE(inData.department, outData.department) AS department,
            jobassign,
            inData.timeIn,
            outData.timeOut
            FROM 
                (SELECT 
                    os_employee.th_name,
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeIn, 
                    os_employee.company, 
                    checkin_log.jobassign,
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'In' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                ) AS inData
            FULL OUTER JOIN 
                (SELECT 
                    os_employee.th_name, 
                    checkin_log.qr_number, 
                    checkin_log.datetime AS timeOut, 
                    os_employee.company, 
                    checkin_log.department
                FROM Entrance_permit.dbo.checkin_log
                LEFT JOIN Entrance_permit.dbo.os_employee 
                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                WHERE checkin_log.status = 'Out' and checkin_log.department in ('P05','P06','P07','P08','P09','P10','P11')
                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                ) AS outData
            ON inData.qr_number = outData.qr_number where jobassign = 'special' ) checkin_out
			LEFT JOIN [working_allocate_outsource].[dbo].[os_working_log]
			ON [os_working_log].emp_id = RIGHT(checkin_out.cardcode,5) 
			where [os_working_log].check_in  BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'";
            break;
        default:
            # code...เขียนใหม่นะ
            $sql = "SELECT cardcode,th_name,company,department,jobassign,[os_working_log].job_transfer ,timeIn,timeOut,os_working_log.[confirm_job_transfer_status]
	                FROM(
                        SELECT COALESCE(inData.qr_number, outData.qr_number) AS cardcode,
                        COALESCE(inData.th_name, outData.th_name) AS th_name,
                        COALESCE(inData.company, outData.company) AS company,
                        COALESCE(inData.department, outData.department) AS department,
                        jobassign,
                        inData.timeIn,
                        outData.timeOut
                        FROM 
                            (
                                SELECT 
                                os_employee.th_name,
                                checkin_log.qr_number, 
                                checkin_log.datetime AS timeIn, 
                                os_employee.company, 
                                checkin_log.jobassign,
                                checkin_log.department
                                FROM Entrance_permit.dbo.checkin_log
                                LEFT JOIN Entrance_permit.dbo.os_employee 
                                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                                WHERE checkin_log.status = 'In' and checkin_log.department in ('{$mcname}')
                                AND checkin_log.datetime BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'
                            ) AS inData
                            FULL OUTER JOIN 
                            (
                                SELECT 
                                os_employee.th_name, 
                                checkin_log.qr_number, 
                                checkin_log.datetime AS timeOut, 
                                os_employee.company, 
                                checkin_log.department
                                FROM Entrance_permit.dbo.checkin_log
                                LEFT JOIN Entrance_permit.dbo.os_employee 
                                ON checkin_log.qr_number = CONVERT(NVARCHAR(50), DECRYPTBYPASSPHRASE('KEY', qrcode))
                                WHERE checkin_log.status = 'Out' and checkin_log.department in ('{$mcname}')
                                AND checkin_log.datetime BETWEEN '{$dateInput} 08:00:00' AND '{$nextDay} 01:00:00'
                            ) AS outData
                            ON inData.qr_number = outData.qr_number where jobassign = 'special' ) checkin_out
                        LEFT JOIN [working_allocate_outsource].[dbo].[os_working_log]
                        ON [os_working_log].emp_id = RIGHT(checkin_out.cardcode,5) 
                        where [os_working_log].check_in  BETWEEN '{$lastDay} 19:00:00' AND '{$dateInput} 17:00:00'";
            break;
    }
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
function getWaitApproce($mcname)
{
    global $conn85;
    $data = [];
    $sql = "";
    switch ($mcname) {
        case 'PMN':
            # code...
            $sql = "SELECT TOP (1000) [id]
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
                ,[job_transfer]
                ,[mc_receive]
            FROM [working_allocate_outsource].[dbo].[os_working_log] where [mc_receive] in ('P02','P04')
            and confirm_job_transfer_status is null";
            break;
        case 'PAUTO':
            # code...
            $sql = "SELECT TOP (1000) [id]
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
                ,[job_transfer]
                ,[mc_receive]
            FROM [working_allocate_outsource].[dbo].[os_working_log] where [mc_receive] in ('P05','P06','P07','P08','P09','P10','P11')
            and confirm_job_transfer_status is null";
            break;
        default:
            # code...เขียนใหม่นะ
            $sql = "SELECT TOP (1000) [id]
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
                ,[job_transfer]
                ,[mc_receive]
            FROM [working_allocate_outsource].[dbo].[os_working_log] where [mc_receive] = '{$mcname}'
            and confirm_job_transfer_status is null
            ";
            break;
    }
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
function get_worker_info($company)
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [status]
      ,[cardcode]
      ,[th_name]
      ,[id_passport]
      ,[mobile_number]
      ,[company]
      ,[update_time]
  FROM [Entrance_permit].[dbo].[os_employee] where company = '{$company}' and status = 'Active'
  order by th_name 
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
function getWorkerAssigned($vendorName)
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [assing_by_vendor].[id]
            ,[request_doc]
            ,[assing_by_vendor].[emp_id]
            ,[assing_by_vendor].[emp_name]
            ,[assing_by_vendor].[work_location]
            ,[assing_by_vendor].[work_type]
            ,[assing_by_vendor].[dateStart]
            ,[assing_by_vendor].[dateStop]
            ,[checkIn]
            ,[checkOut]
            ,[status]
        FROM [working_allocate_outsource].[dbo].[assing_by_vendor] 
        left join [working_allocate_outsource].[dbo].request_worker_info 
        ON request_worker_info.id = assing_by_vendor.infoId
        where [assing_by_vendor].[status] = 'Active' and [checkIn] is null 
        and [assing_by_vendor].[dateStop] > GETDATE()
        and request_worker_info.vendor_assign = '{$vendorName}'
        order by [dateStart] 
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

function insertWorkerToDoc($doc, $emp_id, $mcname, $emp_name, $work_location, $work_Type, $dateStart, $dateStop, $infoId)
{
    global $conn85;
    $sql = "INSERT INTO [working_allocate_outsource].[dbo].[assing_by_vendor] (
       [request_doc]
      ,[emp_id]
      ,[vendor_name]
      ,[emp_name]
      ,[work_location]
      ,[work_type]
      ,[dateStart]
      ,[dateStop],[status],infoId
	  ) VALUES (?,?,?,?,?,?,?,?,'Active',?)";
    $params = [$doc, $emp_id, $mcname, $emp_name, $work_location, $work_Type, $dateStart, $dateStop, $infoId];
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
function getHourPerday($startDate, $endDate)
{
    // หาชมต่อวัน
    // ดึงเฉพาะเวลา (H:i)
    $startTime = $startDate->format('H:i');
    $endTime = $endDate->format('H:i');

    // แปลงเวลาเป็น timestamp
    $startTimestamp = strtotime($startTime);
    $endTimestamp = strtotime($endTime);

    // คำนวณชั่วโมง
    if ($endTimestamp > $startTimestamp) {
        $diffInSeconds = $endTimestamp - $startTimestamp;
    } else {
        // กรณีข้ามเที่ยงคืน เช่น 08:00 - 02:00
        $diffInSeconds = ($endTimestamp + 86400) - $startTimestamp;
    }
    $totalHours = $diffInSeconds / 3600;
    return $totalHours;
}
function getCountdays($startDate, $endDate)
{
    // แปลงเป็นวันที่อย่างเดียว (Y-m-d)
    $startDateOnly =  substr($startDate, 0, 10);
    $endDateOnly =  substr($endDate, 0, 10);

    // แปลงกลับเป็น DateTime อีกครั้งเพื่อคำนวณ
    $startDateOnly = new DateTime($startDateOnly);
    $endDateOnly = new DateTime($endDateOnly);

    // คำนวณผลต่างของวันที่
    $diff = $startDateOnly->diff($endDateOnly);

    // แสดงจำนวนวัน +1 เพื่อรวมวันแรกด้วย
    $totalDays = $diff->days + 1;

    return $totalDays;
}
function get_emp_by_id($emp_id)
{
    global $conn85;
    $data = [];
    $sql = "SELECT TOP 1 [status]
      ,[cardcode]
      ,[th_name]
      ,[id_passport]
      ,[mobile_number]
      ,[company]
      ,[update_time]
        FROM [Entrance_permit].[dbo].[os_employee] where cardcode = '{$emp_id}' and status = 'Active'
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

function getDateByDoc($doc, $workType)
{
    global $conn85;
    $data = [];
    $sql = "SELECT [work_startDate]
      ,[work_endDate]    
        FROM [working_allocate_outsource].[dbo].[request_worker_info] 
        where [request_worker_doc] = '{$doc}' and work_type = '{$workType}'";
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
function get_request_worker_info()
{
    global $conn85;
    $data = [];
    $sql = "SELECT [request_worker_info].[id]
            ,[request_worker_info].[work_type]
            ,[request_worker_info].[work_location]
            ,[request_worker_info].[worker_require]
            ,request_worker_info.worker_received
            ,[work_startDate]
            ,[work_endDate]
            ,[request_worker_info].[create_date]
            ,[request_worker_doc]
            ,[request_worker_doc_status]
            ,[vendor_assign]
        FROM [working_allocate_outsource].[dbo].[request_worker_info] left join [working_allocate_outsource].[dbo].[request_worker_doc]
        ON [request_worker_info].[request_worker_doc] = [request_worker_doc].doc_number
        where [request_worker_doc].status != 'cancel' order by [request_worker_info].[create_date] desc
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
function getDocByVendor($vendor)
{
    global $conn85;
    $data = [];
    $sql = "SELECT [request_worker_info].[id]
            ,[request_worker_info].[work_type]
            ,[request_worker_info].[work_location]
            ,[request_worker_info].[worker_require]
            ,request_worker_info.worker_received
            ,[work_startDate]
            ,[work_endDate]
            ,[request_worker_info].[create_date]
            ,[request_worker_doc]
            ,[request_worker_doc_status]
            ,[vendor_assign]
        FROM [working_allocate_outsource].[dbo].[request_worker_info] left join [working_allocate_outsource].[dbo].[request_worker_doc]
        ON [request_worker_info].[request_worker_doc] = [request_worker_doc].doc_number
        where [request_worker_doc].status != 'cancel' and [vendor_assign] = '{$vendor}' order by [request_worker_info].[create_date] desc
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

function deleterequest_worker_info($id)
{
    global $conn85;
    // Define the SQL query
    $sql = "DELETE [working_allocate_outsource].[dbo].[request_worker_info]
    WHERE [id] = ?";

    // Define the parameters for the query
    $params = [$id];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function updateDoc($qty, $doc)
{

    global $conn85;

    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[request_worker_doc] 
    SET worker_require -= ? WHERE doc_number = ?";

    // Define the parameters for the query
    $params = [$qty, $doc];

    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);

    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function updateDocCancel($doc)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[request_worker_doc] 
    SET [status] = 'Cancel' WHERE doc_number = ?";
    // Define the parameters for the query
    $params = [$doc];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function getQtyDoc($doc)
{
    global $conn85;
    $qty = 0;
    $sql = "SELECT TOP (1) worker_require
    FROM [working_allocate_outsource].[dbo].[request_worker_doc] where doc_number = '{$doc}'";
    $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $qty = $show['worker_require'];
            }
        }
    }
    return $qty;
}
function getCompVendor()
{
    global $conn85;
    $data = [];
    $sql = "SELECT DISTINCT company FROM [Entrance_permit].[dbo].[os_employee] where status = 'active'";
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
function checkWorkerDupAssign($empId, $dateAssing)
{
    global $conn85;
    $data = [];
    $sql = "  SELECT * FROM [working_allocate_outsource].[dbo].[assing_by_vendor] where emp_id = '{$empId}' 
    and dateStart between '{$dateAssing} 00:00:00.000' and '{$dateAssing} 23:59:59.000'";
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
function UpdateVendortoDoc($rowId, $vendor, $doc, $work_type)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE  [working_allocate_outsource].[dbo].[request_worker_info] 
    SET [vendor_assign] = ? , assign_date = GETDATE() 
    WHERE request_worker_doc = ? and work_type = ? and id = ?";
    // Define the parameters for the query
    $params = [$vendor, $doc, $work_type, $rowId];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}

function UpdateJobtoEmp($emp_id, $th_name, $company, $work_location, $costcenter, $job, $shift, $working_date, $price, $check_in, $check_out)
{
    if ($check_out == "ไม่พบข้อมูล") {
        $check_out = null;
    }
    if ($check_in == "ไม่พบข้อมูล") {
        $check_in = null;
    }
    global $conn85;
    // Define the SQL query
    $sql = "INSERT INTO [dbo].[os_working_log]
           ([emp_id]
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
           ,[timestamp])
     VALUES(
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     ?,
     GETDATE()
     )";
    // Define the parameters for the query
    $params = [$emp_id, $th_name, $company, $work_location, $costcenter, $job, $shift, $working_date, $price, $check_in, $check_out];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function UpdateJobSpecialtoEmp($costcenter, $price, $job, $mcReceive, $emp_id, $check_in)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE [working_allocate_outsource].[dbo].[os_working_log] 
    SET costcenter = ? , price = ? , job_transfer = ? , mc_receive = ?
    WHERE emp_id = ? AND CONVERT(varchar(16), check_in, 120) = ? and status is null";
    // Define the parameters for the query
    $params = [$costcenter, $price, $job, $mcReceive, $emp_id, $check_in];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function findSupEmail($cc)
{
    global $conn85;
    if (!$conn85) {
        echo json_encode(['message' => "เชื่อมต่อฐานข้อมูลไม่ได้"]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    $data = [];
    $sql = "SELECT TOP (1000) [Cost_center]
      ,[Cost_name]
      ,[Group_id]
      ,[Sup_ID]
      ,[Sup_Name_Surname]
      ,[Sup_Mail]
      ,[Mgr_ID]
      ,[Mgr_Name_Surname]
      ,[Mgr_Mail]
      ,[Sr_ID]
      ,[Sr_Name_Surname]
      ,[Sr_Mail]
      ,[Group_MC]
      ,[Name]
      ,[Mgr_Name_English]
      ,[Sr_Name_English]
      ,[Process]
      ,[MC_Factor]
  FROM [Allocate_Working_Time].[dbo].[Cost_center_detail] where Cost_center = ?";
    $result = sqlsrv_query($conn85, $sql, array($cc), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
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
function getWorkingData($check_in, $carcode)
{
    global $conn85;
    $data = [];
    $sql = "DECLARE @target DATETIME = '{$check_in}';
            SELECT  [id]
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
                WHERE [datetime] >= @target
                AND [datetime] < DATEADD(MINUTE, 1, @target)
                AND [status] = 'In'
                AND [qr_number] LIKE '%{$carcode}'";
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
function updateWokingData($check_in, $carcode, $job)
{
    global $conn85;
    // Define the SQL query
    $sql = "DECLARE @target DATETIME = '{$check_in}';
            UPDATE [Entrance_permit].[dbo].[checkin_log] 
            SET jobassign = '{$job}' ,
            [update_at] = GETDATE()
            WHERE [datetime] >= @target
            AND [datetime] < DATEADD(MINUTE, 1, @target)
            AND [status] = 'In'
            AND [qr_number] LIKE '%{$carcode}'";
    // Define the parameters for the query
    $params = [$job];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function canCelWokingData($check_in, $carcode, $job)
{
    global $conn85;
    // Define the SQL query
    $sql = "DECLARE @target DATETIME = '{$check_in}';
            UPDATE [Entrance_permit].[dbo].[checkin_log] 
            SET jobassign = null ,
            [update_at] = GETDATE()
            WHERE [datetime] >= @target
            AND [datetime] < DATEADD(MINUTE, 1, @target)
            AND [status] = 'In'
            AND [qr_number] LIKE '%{$carcode}' and jobassign = '{$job}'";
    // Define the parameters for the query
    $params = [$job];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Ins
}
function cancelStatusWorkingLog($cardcode, $timeIn)
{
    global $conn85;
    // Define the SQL query
    $sql = "DECLARE @target DATETIME = '{$timeIn}';
    UPDATE [working_allocate_outsource].[dbo].[os_working_log] SET [status] = 'Cancel' 
    WHERE emp_id like '%{$cardcode}' and check_in >= @target AND [check_in] < DATEADD(MINUTE, 1, @target) and [status] is null";
    // Define the parameters for the query
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Ins
}
function getCC($mccode)
{
    global $conn85;
    $data = [];
    $sql = "SELECt [cossch]
      ,[cosname]
      ,[cost_center_hana]
    FROM [working_allocate_outsource].[dbo].[Work_location] where cossch = '{$mccode}'";
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
function getEmpData($cardcode)
{
    $emp_code = substr($cardcode, -5);
    global $conn85;
    $data = [];
    $sql = "SELECT [id]
      ,[status]
      ,[cardcode]
      ,[th_name]
      ,[en_name]
      ,[issue_date]
      ,[expiration_date]
      ,[id_passport]
      ,[birthday]
      ,[nationality]
      ,[license_plate]
      ,[mobile_number]
      ,[company]
      ,[allowed_area]
      ,[note1]
      ,[note2]
      ,[note3]
      ,[note_prohibiting]
      ,[Donot_access_date]
      ,[update_by]
      ,[update_time]
      ,[Vendor Code]
    FROM [Entrance_permit].[dbo].[os_employee] where cardcode = '{$emp_code}' and status = 'Active'";
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
function getWorkingDate($checkIn)
{
    $checkInTime = new DateTime($checkIn);
    $hourMinute = $checkInTime->format('H:i');

    // ถ้า Check-in อยู่ในช่วง 19:00 - 21:00 ให้ถือเป็นวันที่ถัดไป
    if ($hourMinute >= '19:00' && $hourMinute <= '21:00') {
        $checkInTime->modify('+1 day');
    }

    return $checkInTime->format('Y-m-d');
}
function getPrice($job_id)
{
    global $conn85;
    $data = [];
    $sql = "SELECT  [job_id]
      ,[work_name]
      ,[Prices_manpower]
      ,[group_machine]
      ,[create_date]
      ,[updateTime]
      ,[group_machine_id]
      ,[cc]
      ,[Cost_center_detail].[Name]
    FROM [working_allocate_outsource].[dbo].[jobprice]
    LEFT JOIN [Allocate_Working_Time].[dbo].[Cost_center_detail] 
    on [jobprice].cc = [Cost_center_detail].Cost_center
    WHERE job_id = '{$job_id}'
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
// function UpdateCancelVendor($rowId)
// {
//     global $conn85;
//     // Define the SQL query
//     $sql = "UPDATE [working_allocate_outsource].[dbo].[request_worker_info] 
//     SET vendor_assign = null WHERE request_worker_doc_status = 'open' 
//     and id = ?";
//     // Define the parameters for the query
//     $params = [$rowId];
//     // Execute the query
//     $stmt = sqlsrv_query($conn85, $sql, $params);
//     if ($stmt === false) {
//         // Handle errors
//         echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
//         http_response_code(400); // HTTP Bad Request
//         die();
//     }
//     // Check the number of affected rows
//     $rows_affected = sqlsrv_rows_affected($stmt);
//     if ($rows_affected > 0) {
//         return true; // Insert was successful
//     }
//     return false; // Insert failed
// }


function UpdateCloseinfoDoc($doc, $work_type)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE  [working_allocate_outsource].[dbo].[request_worker_info] 
    set request_worker_doc_status = 'close' WHERE request_worker_doc = ? and work_type = ?";
    // Define the parameters for the query
    $params = [$doc, $work_type];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
function UpdateReceiveinfoDoc($qty, $doc, $work_type, $start, $stop, $infoId)
{
    global $conn85;
    // Define the SQL query
    $sql = "UPDATE  [working_allocate_outsource].[dbo].[request_worker_info] 
    set [worker_received] = ? WHERE request_worker_doc = ? and work_type = ? and
    [work_startDate] = ? and [work_endDate] = ? and id = ? ";
    // Define the parameters for the query
    $params = [$qty, $doc, $work_type, $start, $stop, $infoId];
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        echo json_encode(['message' => sqlsrv_errors()[0]['message']]);
        http_response_code(400); // HTTP Bad Request
        die();
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}
