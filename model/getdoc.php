<?php
require "head.php";
echo json_encode(getLastDoc());
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
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if (empty($row)) {
        return "SCAREQ" . $year . "0001";
    }
    $lastId = (int)substr($row['doc_id'], 10); // Extract the numeric part of the last ID
    $newId = $lastId + 1; // Increment by 1
    $doc_id = "SCRAPREQ" . $year . str_pad($newId, 4, "0", STR_PAD_LEFT);
    return $doc_id;
}
