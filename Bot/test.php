<?php
$webhookUrl = "https://sherasolutions.webhook.office.com/webhookb2/d183ebb2-6bd8-4ac2-b0ed-319e1eb2656f@3a9136c1-b8dc-48b7-9de6-1264ac6ced3e/IncomingWebhook/5b8980498acb4c999c89b22562d98b76/643df471-f76d-41d3-9b55-111bb0ee9009/V2LZPnecex-x5FGaMxriIpAR31ZRlc8dslNhpCM58Mjcs1";
$titleAlert = "Auto 311 prep alert";
sendAdaptiveCardToTeams($webhookUrl, $titleAlert, "Error", $sMessage);
// Team Alert
function sendAdaptiveCardToTeams($webhookUrl, $title, $status, $resMessage)
{
    $adaptiveCard = [
        "type" => "MessageCard",
        "themeColor" => "0076D7",
        "summary" => "แจ้งเตือนจากระบบ",
        "sections" => [
            [
                "activityTitle" => "**แจ้งเตือน:** {$title}",
                "activitySubtitle" => "วันที่: " . date("Y-m-d H:i:s"),
                "activityImage" => "https://via.placeholder.com/50",
                "facts" => [
                    ["name" => "สถานะ:", "value" => $status],
                    ["name" => "รายละเอียด:", "value" => $resMessage]
                ],
                "markdown" => true
            ]
        ],
    ];

    $jsonData = json_encode($adaptiveCard);

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 200) {
        echo "Adaptive Card sent successfully!";
    } else {
        echo "Failed to send Adaptive Card. HTTP Code: $httpCode";
    }
}
