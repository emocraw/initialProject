<?php
require "head.php";
if ($requestMethod == "GET") {
    echo json_encode(getMatScrap());
}
if ($requestMethod == "POST") {
    // Password is correct, return success
    http_response_code(200); // HTTP OK
    echo json_encode(['message' => 'Login successful']);
    http_response_code(401); // HTTP Unauthorized
    echo json_encode(['message' => 'Incorrect password']);
}
if ($requestMethod == "PUT") {
}
function getMatScrap()
{
    global $conn2;
    $data = [];
    $sql = "SELECT [Id]
      ,[Description]
      ,[Sell_qty]
      ,[unit]
      ,[Price_unit]
      ,[Vendor]
      ,[update_time]
  FROM [Scrap_management].[dbo].[scrap_master]";
    $result = sqlsrv_query($conn2, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
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
