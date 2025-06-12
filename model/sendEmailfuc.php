<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
$urlReceiver = "http://shopfloor.shera.com/worker_allocate/view/receiveEmpSpecial.php?machine=";
$urlSender = "http://shopfloor.shera.com/worker_allocate/view/empManagement.php";
function sendMail($departsend, $departReci, $dateStart, $shift, $supMail, $supName)
{
    global $urlReceiver;
    try {
        $mail = new PHPMailer;
        // $mail->SMTPDebug = 2;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'mf13_admin@shera.com';   // SMTP username
        $mail->Password = 'Ad_min@404305';
        $mail->SMTPSecure = 'STARTTLS'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        // Sender info
        $mail->setFrom('mf13_admin@shera.com', 'Auto Allocate_shera');
        //$mail->addReplyTo('addReplyToEmail@gmail.com', 'ReplyName');
        // Add a recipient
        // ผู้รับ
        // $mail->addAddress($supMail);
        $mail->addAddress('Thanakorn_m@shera.com');
        // $mail->addCC('thanakorn_m@shera.com');
        $mail->addCC('Thanakorn_m@shera.com');
        // CC
        // $mail->addCC('kanyarut_a@shera.com');   
        //$mail->addBCC('bcc@example.com');
        // Set email format to HTML
        $mail->isHTML(true);
        // Mail subject
        $mail->Subject = 'Email สำหรับยืนยันการับค่าใช้จ่าย (ค่าแรง) ผู้รับเหมา ที่ปฏิบัติงานสำหรับหน่วยงานของท่าน';

        $bodyContent = "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <title>Document</title>
            <style>              
                .report {
                    display: block;
                    /* Green background */
                    text-align: center;
                    /* 2px black border */
                    cursor: pointer;
                }
                .icon {
                    display: inline-block;
                    width: 40px;
                    /* Adjust icon size as needed */
                    height: 40px;
                    margin-right: 10px;
                    /* Space between icon and text */
                    vertical-align: middle;
                    /* Align icon vertically with text */
                }
            </style>
        </head>
        <body>
            &nbsp;เรียน หัวหน้างาน {$supName}
            <br>
            <div style='margin-bottom: 40px'>
                <div style='margin-bottom: 40px'>
                    <div style='margin-bottom: 1px'>
                        <p style='text-align: center;'>รบกวนตรวจสอบและยืนยันข้อมูลการ รับค่าใช้จ่ายผู้รับเหมา จากแผนก <span style='color: red;'>{$departsend}</span> ไปยังแผนก <span style='color: red;'>{$departReci}</span> วันที่ {$dateStart} กะ {$shift}</p>                            
                            <a href='{$urlReceiver}{$departReci}' class='report' style='display: block;text-align: center;cursor: pointer;'>
                                (Please consider attached  link on this mail)<img class='icon' style='display: inline-block;
                    width: 40px;
                    height: 40px;
                    margin-right: 10px;
                    vertical-align: middle;' src='https://shopfloor.shera.com/working-api/Click.gif' alt=''>
                            </a>          
                        <p>
                            ขอแสดงความนับถือ
                        </p>          
                    </div>            
                </div>
            </div>
        </body>
        </html>";
        $mail->Body = $bodyContent;
        // Send email 
        $mail->send();
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
function sendMailReject($departsend, $departReci, $dateStart, $shift, $supMail, $supName)
{
    global $urlSender;
    try {
        $mail = new PHPMailer;
        // $mail->SMTPDebug = 2;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'mf13_admin@shera.com';   // SMTP username
        $mail->Password = 'Ad_min@404305';
        $mail->SMTPSecure = 'STARTTLS'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        // Sender info
        $mail->setFrom('mf13_admin@shera.com', 'Auto Allocate_shera');
        //$mail->addReplyTo('addReplyToEmail@gmail.com', 'ReplyName');
        // Add a recipient
        // ผู้รับ
        // $mail->addAddress($supMail);
        $mail->addAddress('Thanakorn_m@shera.com');
        // $mail->addCC('thanakorn_m@shera.com');
        $mail->addCC('Thanakorn_m@shera.com');
        // CC
        // $mail->addCC('kanyarut_a@shera.com');   
        //$mail->addBCC('bcc@example.com');
        // Set email format to HTML
        $mail->isHTML(true);
        // Mail subject
        $mail->Subject = "Email สำหรับการตอบกลับ ไม่ยอมรับ (ค่าแรง) ผู้รับเหมาที่ส่งไปปฏิบัติงานแผนก {$departReci}";

        $bodyContent = "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <title>Document</title>
            <style>              
                .report {
                    display: block;
                    /* Green background */
                    text-align: center;
                    /* 2px black border */
                    cursor: pointer;
                }
                .icon {
                    display: inline-block;
                    width: 40px;
                    /* Adjust icon size as needed */
                    height: 40px;
                    margin-right: 10px;
                    /* Space between icon and text */
                    vertical-align: middle;
                    /* Align icon vertically with text */
                }
            </style>
        </head>
        <body>
            &nbsp;เรียน หัวหน้างาน {$supName}
            <br>
            <div style='margin-bottom: 40px'>
                <div style='margin-bottom: 40px'>
                    <div style='margin-bottom: 1px'>
                        <p style='text-align: center;'>มีการยกเลิกการรับค่าใช้จ่ายผู้รับเหมาจากแผนก {$departReci} <span style='color: red;'>กรุณาระบุงานของพนักงานใหม่ หรือติดต่อหน่วยงานที่ยกเลิก ไม่งั้นระบบจะไม่คำนวนค่าจ้างให้กับพนักงานที่ส่งไปยังแผนก {$departsend}</span> วันที่ {$dateStart} กะ {$shift}</p>                            
                            <a href='{$urlSender}' class='report' style='display: block;text-align: center;cursor: pointer;'>
                                (Please consider attached  link on this mail)<img class='icon' style='display: inline-block;
                    width: 40px;
                    height: 40px;
                    margin-right: 10px;
                    vertical-align: middle;' src='https://shopfloor.shera.com/working-api/Click.gif' alt=''>
                            </a>          
                        <p>
                            ขอแสดงความนับถือ
                        </p>          
                    </div>            
                </div>
            </div>
        </body>
        </html>";
        $mail->Body = $bodyContent;
        // Send email 
        $mail->send();
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
