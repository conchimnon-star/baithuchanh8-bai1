# Bài 1 – Gửi email cố định

Ứng dụng PHP gửi email HTML tiếng Việt đến một người nhận cấu hình sẵn, sử dụng PHPMailer và Gmail SMTP. Thư viện PHPMailer được cài bằng Composer; thông tin SMTP cục bộ không được đưa lên Git.

## Cài đặt

1. Cài PHP 8 trở lên và Composer.
2. Trong thư mục này, chạy:

   ```sh
   composer install
   ```

3. Sao chép `config.local.php.example` thành `config.local.php`. Điền Gmail gửi, App Password, email người nhận và tên người nhận.
4. Trên tài khoản Google, bật xác minh 2 bước và tạo App Password nếu tài khoản đủ điều kiện. Không dùng mật khẩu Gmail thông thường.
5. Đặt dự án trong `htdocs`, bật Apache trên XAMPP, rồi mở:

   `http://localhost/Lab8_Email/Bai1_GuiEmailCoDinh/`

6. Bấm **Gửi email thử** và kiểm tra hộp thư đến cùng thư mục Spam.

Cấu hình mặc định dùng `smtp.gmail.com`, cổng `587`, STARTTLS và UTF-8. Có thể đặt đường dẫn tệp đọc được vào `attachment_path` trong `config.local.php`; để trống nếu không đính kèm. Không tắt xác minh chứng chỉ TLS.
