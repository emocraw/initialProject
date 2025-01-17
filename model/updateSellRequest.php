<?php
require "head.php";

if ($requestMethod == "POST") {

    if (!empty($Post)) {
        $iserror = false;
        foreach ($Post as $key => $value) {
            $Id = $value['Id'];
            $Image = $value['Image'];
            $Location = $value['Location'];
            $Base64Image = $value['Base64Image'];
            $imgData = base64_decode($Base64Image);
            $ramdomName = uniqid();
            $imgPath = '../assets/scrapImg/' . $ramdomName . '.png';
            $imgName = $ramdomName . '.png';
            file_put_contents($imgPath, $imgData);

            if (!updateitem($Id, $Image, $Location, $imgName)) {
                $iserror = true;
                return;
            }
        }
        if (!$iserror) {
            http_response_code(201);
            echo json_encode(array("message" => "Insert data success"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "No data received"));
    }
}

function updateitem()
{
    global $conn2;
    $sql = "INSERT INTO [Scrap_management].[dbo].[sell_request_item] ([scarp_code]
    ,[scrap_name]
    ,[sell_qty]
    ,[unit]
    ,[price]
    ,[vendor_name]
    ,[create_date],doc_id,image_name,[location]) VALUES (?,?,?,?,?,?,GETDATE(),? ,?, ?)";
        // Define the parameters for the query
    $params = [$scrapcode, $name, $qty, $unit, $price, $vendor, $doc_id, $imgName, $location];
        // Execute the query
    $stmt = sqlsrv_query($conn2, $sql, $params);
    if ($stmt === false) {
        // Handle errors
        $errors = sqlsrv_errors();
        echo json_encode(array("message" => "Insert error", "errors" => $errors, "sql" => $sql, "params" => $params));
        return false;
    }
    // Check the number of affected rows
    $rows_affected = sqlsrv_rows_affected($stmt);
    if ($rows_affected > 0) {
        return true; // Insert was successful
    }
    return false; // Insert failed
}

function getimagename()
{
    global $conn2;
    $data = [];
    $sql = "SELECT DISTINCT [image_name]
            FROM [Scrap_management].[dbo].[sell_request_item]";
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
