<?php
// 1) ใส่ Discord Webhook URL ตรงนี้
$WEBHOOK_URL = 'https://discord.com/api/webhooks/1405921499625427005/rEu0aYIn1WPE36pnaFaoYIkrK5BuUgLwks-bKRUffUnPr5E0W8A9oouZkEq1Mx9CvCMF';

// ตรวจสอบว่าใส่ Webhook แล้วหรือยัง
if ($WEBHOOK_URL === 'https://discord.com/api/webhooks/1405921499625427005/rEu0aYIn1WPE36pnaFaoYIkrK5BuUgLwks-bKRUffUnPr5E0W8A9oouZkEq1Mx9CvCMF') {
    http_response_code(500);
    exit('Please set $WEBHOOK_URL in contact.php');
}

// อนุญาตเฉพาะ POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// รับค่าจากฟอร์ม
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// ตรวจสอบค่าว่าง
if ($name === '' || $email === '' || $message === '') {
    http_response_code(422);
    exit('Missing required fields.');
}

// เตรียมข้อมูลที่จะส่งไป Discord
$embed = [
    'title' => $subject !== '' ? $subject : 'New Contact Form',
    'type' => 'rich',
    'fields' => [
        ['name' => 'Name', 'value' => $name, 'inline' => true],
        ['name' => 'Email', 'value' => $email, 'inline' => true],
        ['name' => 'Phone', 'value' => $phone !== '' ? $phone : '—', 'inline' => true],
        ['name' => 'Message', 'value' => $message],
    ],
    'timestamp' => date('c'),
];

$payload = json_encode([
    'content' => '**มีข้อความใหม่จาก Contact Form**',
    'embeds' => [$embed]
], JSON_UNESCAPED_UNICODE);

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
    echo json_encode(['ok' => true, 'message' => 'ส่งสำเร็จ']);
} else {
    http_response_code(500);
    echo 'ส่งไม่สำเร็จ';
}
