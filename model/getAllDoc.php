<?php
require "head.php";
echo json_encode(getAllDoc());
function getAllDoc()
{
    global $conn2;
    $data = [];
    $sql = "SELECT TOP (1000) [id]
                ,doc.[doc_id]
                ,[scarp_code]
                ,[scrap_name]
                ,[sell_qty]
                ,[unit]
                ,[price]
                ,[location]
                ,[image_name]
                ,[vendor_name]
                ,item.[status] as status
                ,doc.[approve_status] as approve_status
                ,doc.[active_date] as active_date
                ,doc.[inactive_date] as inactive_date
                ,[create_date]
            FROM [Scrap_management].[dbo].[sell_request_item] as item
            LEFT join [Scrap_management].[dbo].[sell_request_doc] as doc
            ON item.doc_id = doc.doc_id
            WHERE item.status = 'inactive' and doc.[approve_status] is NULL";
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
