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
    $dataLogin = LoginVendor($username, $password);
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
function LoginVendor($user, $password)
{
    global $conn85;
    $data = [];
    // check ว่า Vendor มี Password ไหม
    // Insert
    // ถ้าไม่มี Vendor จะใช้ Vendor code เป็นรหัส
    // ถ้ามี Check ปกติ
    $sql = "SELECT TOP (1) [Vendor Code] Vendor_Code
        ,[Vendor Name] Vendor_Name
        ,[Vendor_shortname]
        ,[old_short]
        ,[Password]
    FROM [Entrance_permit].[dbo].[Vendor_epro]where RIGHT([Vendor Code] , 7 ) = '{$user}' 
    and CONVERT( VARCHAR( 100 ) , DECRYPTBYPASSPHRASE( 'KEY' , [Password] ) ) = '{$password}'";
    $result = sqlsrv_query($conn85, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            $show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            $data['ID'] = $user;
            $data['Name_Surname'] = $show['Vendor_shortname'];
            $token = encriptFunc($user);
            $data['Token'] = $token;
            $data['type'] = 'vendor';
            insertToken($user, $token, $data['type']);
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
