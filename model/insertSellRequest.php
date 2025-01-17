<?php
require "head.php";

if ($requestMethod == "POST") {

    if (!empty($Post)) {
        $iserror = false;
        foreach ($Post as $key => $value) {
            $scrapcode = $value['Code'];
            $name = $value['Name'];
            $qty = $value['Qty'];
            $unit = $value['Unit'];
            $price = $value['Price'];
            $vendor = $value['Vendor'];
            $location = $value['location'];
            $img = $value['image'];
            $imgData = base64_decode($img);
            $ramdomName = uniqid();
            $imgPath = '../assets/scrapImg/' . $ramdomName . '.png';
            $imgName = $ramdomName . '.png';
            $doc_id = $value['docNo'];
            file_put_contents($imgPath, $imgData);

            if (!insertScapList($scrapcode, $name, $qty, $unit, $price, $vendor, $doc_id, $imgName, $location)) {
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

function insertScapList($scrapcode, $name, $qty, $unit, $price, $vendor, $doc_id,  $imgName, $location)
{
    global $conn2;
    if (insertDoc($doc_id)) {
        // Define the SQL query
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
}
function insertDoc($new_doc)
{
    global $conn2;
    $sql = "INSERT INTO [Scrap_management].[dbo].[sell_request_doc] ([doc_id],[doc_date]) 
    VALUES ('{$new_doc}',GETDATE())";
    $result = sqlsrv_query($conn2, $sql);
    if ($result) {
        $rows_affected = sqlsrv_rows_affected($result);
        if ($rows_affected > 0) {
            return true;
        }
    }
    $errors = sqlsrv_errors();
    echo json_encode(array("message" => "Insert error", "errors" => $errors, "sql" => $sql));
    return false;
}
function getLastDoc()
{
    global $conn2;
    $year = date("y"); // Get the last two digits of the current year
    $sql = "SELECT TOP 1 doc_id FROM [Scrap_management].[dbo].[sell_request_item] ORDER BY doc_id DESC";
    $stmt = sqlsrv_query($conn2, $sql);
    if ($stmt === false) {
        // Handle errors
        return "SCAREQ" . $year . "0001";
    }
    if (empty($row)) {
        return "SCAREQ" . $year . "0001";
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $lastId = (int)substr($row['doc_id'], 8); // Extract the numeric part of the last ID
    $newId = $lastId + 1; // Increment by 1
    $doc_id = "SCRAPREQ" . $year . str_pad($newId, 4, "0", STR_PAD_LEFT);
    return $doc_id;
}
