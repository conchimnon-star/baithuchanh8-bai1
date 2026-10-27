<?php
declare(strict_types=1);
session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/PHPMailer/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $message = 'Yêu cầu không hợp lệ. Hãy tải lại trang và thử lại.';
        $messageType = 'error';
    } elseif (!filter_var($mailConfig['to_email'], FILTER_VALIDATE_EMAIL)) {
        $message = 'Hãy cấu hình địa chỉ người nhận hợp lệ trong config.local.php.';
        $messageType = 'error';
    } elseif (
        !filter_var($mailConfig['smtp_username'], FILTER_VALIDATE_EMAIL)
        || !filter_var($mailConfig['from_email'], FILTER_VALIDATE_EMAIL)
        || $mailConfig['smtp_password'] === ''
    ) {
        $message = 'Thiếu Gmail SMTP. Hãy điền smtp_username, smtp_password và from_email hợp lệ trong config.local.php.';
        $messageType = 'error';
    } elseif (trim((string) $mailConfig['subject']) === '' || trim((string) $mailConfig['body_html']) === '') {
        $message = 'Tiêu đề và nội dung email trong config.php không được để trống.';
        $messageType = 'error';
    } elseif (
        $mailConfig['attachment_path'] !== ''
        && (!is_file($mailConfig['attachment_path']) || !is_readable($mailConfig['attachment_path']))
    ) {
        $message = 'Không thể đọc tệp đính kèm. Hãy kiểm tra attachment_path trong config.local.php.';
        $messageType = 'error';
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = (string) $mailConfig['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = (string) $mailConfig['smtp_username'];
            $mail->Password = (string) $mailConfig['smtp_password'];
            $mail->SMTPSecure = (string) $mailConfig['smtp_secure'];
            $mail->Port = (int) $mailConfig['smtp_port'];
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->setFrom((string) $mailConfig['from_email'], (string) $mailConfig['from_name']);
            $mail->addAddress((string) $mailConfig['to_email'], (string) $mailConfig['to_name']);
            $mail->isHTML(true);
            $mail->Subject = (string) $mailConfig['subject'];
            $mail->Body = (string) $mailConfig['body_html'];
            $mail->AltBody = trim(strip_tags((string) $mailConfig['body_html']));
            if ($mailConfig['attachment_path'] !== '') {
                $mail->addAttachment((string) $mailConfig['attachment_path']);
            }
            $mail->send();
            $message = 'Gửi email thành công đến ' . $mailConfig['to_email'] . '.';
            $messageType = 'success';
        } catch (Exception $exception) {
            $detail = str_replace(
                [(string) $mailConfig['smtp_password'], (string) $mailConfig['smtp_username']],
                '[đã ẩn]',
                $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $exception->getMessage()
            );
            $message = 'Không gửi được email. Chi tiết SMTP: ' . $detail;
            $messageType = 'error';
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bài 1 - Gửi email cố định</title>
    <style>
        :root{font-family:system-ui,-apple-system,"Segoe UI",sans-serif;color:#182230;background:#f3f6fb}
        body{margin:0;padding:40px 18px}.card{max-width:680px;margin:auto;padding:30px;background:#fff;border:1px solid #e3e8ef;border-radius:16px;box-shadow:0 12px 35px #18223012}
        h1{margin-top:0;font-size:1.75rem}.muted{color:#5d6878;line-height:1.6}.notice{padding:13px 15px;border-radius:9px;margin:20px 0}
        .success{background:#e8f7ed;color:#176b35}.error{background:#fff0ee;color:#a52a1c}button{border:0;border-radius:9px;padding:12px 18px;background:#155eef;color:#fff;font-size:1rem;font-weight:650;cursor:pointer}
        button:hover{background:#124bc2}.hint{padding:14px;background:#f2f6ff;border-radius:9px;line-height:1.6}
    </style>
</head>
<body>
<main class="card">
    <h1>Bài 1 - Gửi email cố định</h1>
    <p class="muted">Email người nhận, tiêu đề và nội dung được cấu hình trong <code>config.php</code>. Thông tin Gmail được lưu cục bộ, không đưa lên Git.</p>
    <?php if ($message !== ''): ?>
        <p class="notice <?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <div class="hint">
        <strong>Người nhận:</strong> <?= htmlspecialchars((string) ($mailConfig['to_email'] ?: 'Chưa cấu hình'), ENT_QUOTES, 'UTF-8') ?><br>
        <strong>Tiêu đề:</strong> <?= htmlspecialchars((string) $mailConfig['subject'], ENT_QUOTES, 'UTF-8') ?><br>
        <strong>Đính kèm:</strong> <?= $mailConfig['attachment_path'] === '' ? 'Không' : 'Có cấu hình tệp' ?>
    </div>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <p><button type="submit">Gửi email thử</button></p>
    </form>
</main>
</body>
</html>
