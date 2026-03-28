<?php
$host = "localhost";     
$user = "root";           
$pass = "";               
$dbname = "web_tintuc";    

$conn = mysqli_connect($host, $user, $pass, $dbname);
date_default_timezone_set('Asia/Ho_Chi_Minh');
mysqli_query($conn, "SET time_zone = '+07:00'");
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>
