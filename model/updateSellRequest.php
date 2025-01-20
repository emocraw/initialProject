<?php
require "head.php";

if ($requestMethod == "POST") {

    if (!empty($Post)) {
        $iserror = false;
        foreach ($Post as $key => $value) {
            $Id = $value['Id'];
            $Image = $value['Image'];      // ชื่อภาพที่รับจากคำขอ
            $Location = $value['Location'];
            $Base64Image = $value['Base64Image'];
            
            $directory = '../assets/scrapImg/';
            $existingImages = array_map('basename', scandir($directory));  // ดึงรายชื่อไฟล์ในโฟลเดอร์
            $imageExists = false;
            $imgName = $Image;  // ใช้ชื่อที่ได้รับจากคำขอเริ่มต้น

            // ตรวจสอบว่าไฟล์ที่มีชื่อเดียวกันมีอยู่แล้วในโฟลเดอร์หรือไม่
            if (in_array($Image, $existingImages)) {
                $imageExists = true;  // ถ้ามีไฟล์ชื่อเดียวกันแล้ว
            } else {
                // ถ้าไม่มีไฟล์ชื่อเดียวกัน
                $imgData = base64_decode($Base64Image);  // แปลง Base64 เป็นข้อมูลภาพ
                $ramdomName = uniqid();                   // สร้างชื่อไฟล์แบบสุ่ม
                $imgPath = $directory . $ramdomName . '.png';  // เส้นทางไฟล์ใหม่
                $imgName = $ramdomName . '.png';          // ชื่อไฟล์ที่สร้างใหม่
                file_put_contents($imgPath, $imgData);   // สร้างไฟล์ใหม่
            }
            // Update the item with the new or existing image name
            if (!updateitem($Id, $Location, $imgName)) {
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


function updateitem($Id, $Location, $imgName)
{
    global $conn2;
    $sql = "UPDATE [Scrap_management].[dbo].[sell_request_item]
            SET
                [image_name] = ?,   -- ชื่อไฟล์ภาพใหม่ (สามารถเป็นชื่อที่ได้รับจากคำขอหรือชื่อที่สร้างใหม่)
                [location] = ?      -- สถานที่ (ข้อมูลที่ได้รับจากคำขอ)
            WHERE [id] = ?
            ";
        // Define the parameters for the query
    $params = [$imgName, $Location, $Id];
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
