<?php
session_start();
// Kết nối CSDL
include 'connect.php';

// 1. Gọi Header
include 'site/header.php';

// 2. Điều hướng nội dung (Router đơn giản)
// Mặc định vào trang home
$page = isset($_GET['p']) ? $_GET['p'] : 'home';

// Danh sách các trang hợp lệ
$allow_pages = ['home', 'tintuc', 'chitiettintuc', 'danhmuc', 'dangnhap', 'dangky'];

if (in_array($page, $allow_pages)) {
    // Kiểm tra file có tồn tại không
    $path = "site/pages/{$page}.php";
    if (file_exists($path)) {
        include $path;
    } else {
        echo "<div class='container' style='padding:50px 0'>Trang đang bảo trì...</div>";
    }
} else {
    echo "<div class='container' style='padding:50px 0'>404 - Không tìm thấy trang!</div>";
}

// 3. Gọi Footer
include 'site/footer.php';
