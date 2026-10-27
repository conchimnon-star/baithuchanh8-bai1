<?php
declare(strict_types=1);

$localConfigPath = __DIR__ . '/config.local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
if (!is_array($localConfig)) {
    throw new RuntimeException('config.local.php phải trả về một mảng cấu hình.');
}

$mailConfig = array_replace([
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => getenv('LAB8_SMTP_USERNAME') ?: '',
    'smtp_password' => getenv('LAB8_SMTP_PASSWORD') ?: '',
    'from_email' => getenv('LAB8_SMTP_USERNAME') ?: '',
    'from_name' => 'Lab 8 - Bai 1',
    'to_email' => '',
    'to_name' => '',
    'subject' => 'Email thu tu Lab 8 - Bai 1',
    'body_html' => '<h2>Xin chao!</h2><p>Day la email thu tu Bai 1, co noi dung tieng Viet.</p>',
    'attachment_path' => '',
], $localConfig);
