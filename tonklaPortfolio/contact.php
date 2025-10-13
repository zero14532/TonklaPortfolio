<?php
// ใส่ Webhook URL ตรงนี้
$WEBHOOK_URL = 'https://discord.com/api/webhooks/1405921499625427005/rEu0aYIn1WPE36pnaFaoYIkrK5BuUgLwks-bKRUffUnPr5E0W8A9oouZkEq1Mx9CvCMF';

// รับข้อมูลจากฟอร์ม
$name    = $_POST['name']    ?? '';
$email   = $_POST['email']   ?? '';
$phone   = $_POST['phone']   ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// เตรียมข้อมูลส่งไป Discord
$embed = [
    'title' => $subject ?: 'New Contact Form Submission',
    'fields' => [
        ['name' => 'Name',    'value' => $name,  'inline' => true],
        ['name' => 'Email',   'value' => $email, 'inline' => true],
        ['name' => 'Phone',   'value' => $phone ?: '—', 'inline' => true],
        ['name' => 'Message', 'value' => $message]
    ],
    'timestamp' => date('c')
];

$payload = json_encode(['embeds' => [$embed]], JSON_UNESCAPED_UNICODE);

// ส่งไป Discord
$ch = curl_init($WEBHOOK_URL);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ตอบกลับ
if ($httpCode >= 200 && $httpCode < 300) {
    echo 'ส่งข้อความเรียบร้อยแล้ว';
} else {
    echo 'ส่งไม่สำเร็จ';
}
