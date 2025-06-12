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
    if (empty($Post['username']) && empty($Post['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'ข้อมูลผิดพลาด']);
        return;
    }
    $username = $Post['username'];
    $password = $Post['password'];
    $dataLogin = [];
    if ($username == "purchaser" || $password == "purchaser@@1234") {
        http_response_code(200);
        $dataLogin['Type'] = 'purchaser';
        echo json_encode(["message" => "ok", "UserInfo" => $dataLogin]);
        return;
    }
    $dataLogin = Login($username, $password);
    if (!empty($dataLogin)) {
        http_response_code(200);
        echo json_encode(["message" => "ok", "UserInfo" => $dataLogin]);
        return;
    }
    http_response_code(400);
    echo json_encode(['message' => 'Login fail']);
    return;
}
if ($requestMethod == "PUT") {
}
function Login($user, $password)
{
    global $conn85;
    $data = array();
    $sql = "SELECT TOP (100) [ID]
    ,[machine]
    ,[Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    ,DE_Password 
    from
    (
    select ID
    ,machine
    ,[Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    , CONVERT( VARCHAR( 100 ) , DECRYPTBYPASSPHRASE( 'KEY' , Password ) ) AS DE_Password 
    from
    (SELECT 'ATC' as machine,ID
    ,[Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].ATC
    union all
    SELECT 'CTS' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].CTS
    union all
     SELECT 'SFT' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].[SAFETY]
    union all
    SELECT 'D16' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].D16
    union all
    SELECT 'DPL' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].DPL
    union all
    SELECT 'DT2' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].DT2
    union all
    SELECT 'DT3' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].DT3
    union all
    SELECT 'DT4' as machine ,ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].DT4
    union all
    SELECT 'PL1' as machine , ID,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PL1
    union all
    SELECT 'FL2' as machine,ID ,[Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].FL2
    union all
    SELECT 'PL3' as machine ,ID  ,[Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PL3
    union all
    SELECT 'FL4' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].FL4
    union all
        SELECT 'PL4' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PL4
    union all
    SELECT 'PL5' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PL5
    union all
    SELECT 'LAB' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].LAB
    union all
    SELECT 'M06' as machine,ID  ,
    [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M06
    union all
    SELECT 'M07' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M07
    union all
    SELECT 'M08' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M08
    union all
    SELECT 'M12' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M12
    union all
    SELECT 'M13' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M13
    union all
    SELECT 'M14' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M14
    union all
    SELECT 'M15' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M15
    union all
    SELECT 'M16' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M16
    union all
    SELECT 'M17' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M17
    union all
    SELECT 'M18' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M18
    union all
    SELECT 'M19' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M19
    union all
    SELECT 'M20' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].M20
    union all
    SELECT 'MES' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].MES
    union all
    SELECT 'PMN' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P01
    union all
    SELECT 'PMN' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P02
    union all
    SELECT 'PMN' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P04
    union all
    SELECT 'PAUTO' as machine ,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P05
    union all
    SELECT 'PAUTO' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P06
    union all
    SELECT 'PAUTO' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P07
    union all
    SELECT 'PAUTO' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P08
    union all
    SELECT 'PAUTO' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P09
    union all
    SELECT 'PAUTO' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P10
    union all
    SELECT 'PAUTO' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].P11
    union all
    SELECT 'PAUL' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PAUL
    union all
    SELECT 'PFM' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PFM
    union all
    SELECT 'PL4' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PL4
    union all
    SELECT 'PU1' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PU1
    union all
    SELECT 'MTN' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].MTN
    union all
    SELECT 'PU2' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].PU2
    union all
        SELECT 'QA' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].QA
    union all
    SELECT 'QC' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].QC
    union all
        SELECT 'RK1' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].RK1
    union all
    SELECT 'RK2' as machine,ID  , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].RK2
    union all
    SELECT 'SAP' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].SAP
    union all
    SELECT 'FAC4' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].FAC4
    union all
    SELECT 'SK1' as machine ,ID , [Name_Surname]
    ,[Password]
    ,[Type]
    ,[User_ID]
    ,[Recorded]
    FROM [User_Management].[dbo].SK1)a )b where [Type] = 'Admin' and ID ='{$user}' and DE_Password = '{$password}' order by ID ";
    $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $data['Admin'] = true;
                $data['ID'] = $show['ID'];
                // ตรวจสอบก่อนเพิ่ม machine
                if (!isset($data['machine']) || !in_array($show['machine'], $data['machine'])) {
                    $data['machine'][] = $show['machine'];
                }
                $data['Name_Surname'] = $show['Name_Surname'];
                if ($show['machine'] == "SFT") {
                    $data['Type'] = 'safety';
                } else {
                    $data['Type'] = 'requester';
                }
            }
            $token = encriptFunc($user);
            $data['Token'] = $token;
            insertToken($user, $token, $data['Type']);
        }
    }
    return $data;
}

function encriptFunc($user)
{
    $plaintext = $user;
    $key = "mysecretkeyUnknow"; // คีย์ต้องมีความยาวที่เหมาะสม
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
    $encrypted = base64_encode($iv . $encrypted); // เข้ารหัสและเก็บ IV
    return $encrypted;
}
function insertToken($user, $token, $type)
{
    global $conn85;

    // Define the SQL query
    $sql = "  MERGE INTO [working_allocate_outsource].[dbo].[user_session] AS target
        USING (SELECT '{$user}' AS id, '{$token}' AS token, '{$type}' AS [user_type]) AS source
        ON target.id = source.id
        WHEN MATCHED THEN 
            UPDATE SET target.token = source.token, target.[user_type] = source.[user_type], target.updatetime = GETDATE()
        WHEN NOT MATCHED THEN 
            INSERT ([id], [token], [user_type], [updatetime]) 
        VALUES (source.id, source.token, source.user_type, GETDATE());";
    // Execute the query
    $stmt = sqlsrv_query($conn85, $sql);

    if ($stmt === false) {
        // Handle errors
        die(print_r(sqlsrv_errors(), true));
    }

    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }

    return false; // Insert failed
}
