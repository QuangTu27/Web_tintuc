<?php
$host = "localhost";      // Server MySQL
$user = "root";           // Tài khoản mặc định của XAMPP
$pass = "";               // Mật khẩu để trống
$dbname = "web_tintuc";     // Tên database bạn đã tạo

$conn = mysqli_connect($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Thiết lập mã hóa UTF-8 cho tiếng Việt
mysqli_set_charset($conn, "utf8");
?>
