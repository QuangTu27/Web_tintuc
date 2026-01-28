<?php

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "web_tintuc";
$port   = 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

//$conn = mysqli_connect($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Thiết lập mã hóa UTF-8 cho tiếng Việt
mysqli_set_charset($conn, "utf8");
?>
