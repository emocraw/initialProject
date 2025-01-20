<?php
require "head.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($requestMethod == "GET") {
}
if ($requestMethod == "POST") {
}
if ($requestMethod == "PUT") {
}
$lastDoc = getLastDoc();
$data = getData($lastDoc);
$htmlTag = setTableHtml($data);
foreach ($data as $rowData) {
    $get_sr_data = gettMail($rowData["CC"]);
    if (empty($get_sr_data)) {
        echo "ไม่พบอีเมล";
        return;
    }
    $groupCost = $rowData["CC"];
    $doc = $rowData["doc_id"];
    $sr_mail = $get_sr_data[0]["Sr_Mail"];
    // $sr_mail = "kanyarut_a@shera.com";
    $sr_name = $get_sr_data[0]["Sr_Name_Surname"];
    // sendMail($sr_name, $sr_mail, $groupCost, $doc, $htmlTag);
    if (empty($emails)) {
        sendMail($sr_name, $sr_mail, $groupCost, $doc, $htmlTag);
        // sendMail($sr_name , "kanyarut_a@shera.com" , $groupCost , $doc, $htmlTag );
        $emails[] = $sr_mail;
    } else {
        $dup = false;
        foreach ($emails as $email) {
            if ($email == $sr_mail) {
                $dup = true;
            }
        }
        if (!$dup) {
            sendMail($sr_name, $sr_mail, $groupCost, $doc, $htmlTag);
            $emails[] = $sr_mail;
        }
    }
}
// sendMail("kanyarut" , "kanyarut_a@shera.com" , "215102203" , "SHE240626000", $htmlTag );
function getData($lastDoc)
{
    global $conn2;
    $data = [];
    $sql = "SELECT TOP (1000) [id]
      ,[Posting_key]
      ,[Account]
      ,[Doc_date]
      ,[Currency]
      ,[Doc_Type]
      ,[DocHeaderText]
      ,[Reference]
      ,[Assignment]
      ,[Text]
      ,[Amount]
      ,[new_CC] as [CC]
      ,[Tax_Code]
      ,[doc_id]
      ,[baht_send]
      ,[baht_reci]
      ,[approve_status]
      ,[approve_date]
      ,[confirm_status]
      ,[confirm_date]
      ,[send_mail_date]
  FROM [Allocate_Working_Time].[dbo].[summary_for_export] where send_mail_date is null and doc_id = '{$lastDoc}'";
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
function setTableHtml($data)
{
    $html = "";
    $row = 1;
    $totalReci = 0;
    $totalSend = 0;
    foreach ($data as $rowdata) {
        $receiver = 0;
        $sender = 0;
        $minus = $rowdata['baht_reci'] - $rowdata['baht_send'];
        if ($minus < 0) {
            $sender = $minus * -1;
        } else {
            $receiver = $minus;
        }
        $senderText = number_format($sender, 2);
        $receiverText = number_format($receiver, 2);
        $html .= "<tr>
            <td style=' background-color: #fff; border: 1px solid #ddd;
                                padding: 8px;
                                text-align: left;' >{$row}</td>
            <td style='background-color: #fff; border: 1px solid #ddd;
                                padding: 8px;
                                text-align: left;' >{$rowdata["Assignment"]}</td>
            <td style='background-color: #fff; border: 1px solid #ddd;
                                padding: 8px; text-align: center;'>{$rowdata['CC']}</td>
            <td style='background-color: #fff; border: 1px solid #ddd;
                                padding: 8px; text-align: right;'>{$senderText}</td>
            <td style='background-color: #fff; border: 1px solid #ddd;
                                padding: 8px; text-align: right;'>{$receiverText}</td>
        </tr>";
        $totalReci += $receiver;
        $totalSend += $sender;
        $row++;
    }
    $totalSend = number_format($totalSend, 2);
    $totalReci = number_format($totalReci, 2);
    $html .= "<tr>
            <td style='border: 1px solid #ddd;
                                padding: 8px; background-color: #D7DBD9' colspan='3'>Total</td>
            <td style='border: 1px solid #ddd;
                                padding: 8px; text-align: right; background-color: #D7DBD9'>{$totalReci}</td>
            <td style='border: 1px solid #ddd;
                                padding: 8px; text-align: right; background-color: #D7DBD9'>{$totalSend}</td>
        </tr>";
    return $html;
}
function getLastDoc()
{
    global $conn2;
    $doc = "";
    $sql = "SELECT TOP 1 [doc_id]
    FROM [Allocate_Working_Time].[dbo].[Working_Time] 
    where doc_id is not null order by id desc";
    $result = sqlsrv_query($conn2, $sql, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
    if ($result) {
        $answer = sqlsrv_num_rows($result);
        if ($answer) {
            while ($show = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $doc = $show['doc_id'];
            }
        }
    }
    return $doc;
}
function sendMail($email, $server, $doc)
{
    try {
        $mail = new PHPMailer;
        $mail->SMTPDebug = 2;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = "mf13_admin@shera.com"; // SMTP username
        $mail->Password = "Ad_min@404305"; // SMTP password
        $mail->SMTPSecure = 'STARTTLS'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        // Sender info
        $mail->setFrom('mf13_admin@shera.com', 'Auto Allocate_shera');
        //$mail->addReplyTo('addReplyToEmail@gmail.com', 'ReplyName');
        // Add a recipient
        // ผู้รับ
        $mail->addAddress("{$email}");
        // $mail->addAddress('thanakorn_m@shera.com');
        $mail->addCC('thanakorn_m@shera.com');
        $mail->addCC('Wasana_N@Shera.com');
        $mail->addCC('mf13_environment@Shera.com');
        // CC
        // $mail->addCC('kanyarut_a@shera.com');   
        //$mail->addBCC('bcc@example.com');
        // Set email format to HTML
        $mail->isHTML(true);
        // Mail subject
        $mail->Subject = 'ใบขออนุมัติขายเศษซากออนไลน์';
        $link = "{$server}/scrapManagement/view/approveView.php?doc={$doc}";
        $mail->Body    = '<p>Hello,</p>
                      <p>Please click the link below to visit the page:</p>
                      <a href="' . $link . '">Visit the Page</a>
                      <p>Thank you!</p>';
        // $mail->SMTPDebug = 2;
        // Send email 
        $mail->send();
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
function gettMail($groupId)
{
    global $conn2;
    $data = [];
    $sql = "SELECT [Sr_Name_Surname]
      ,[Sr_Mail]
    FROM [Allocate_Working_Time].[dbo].[Cost_center_detail] where Group_id = '{$groupId}'";
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
