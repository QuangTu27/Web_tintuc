<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'connect.php';

//xử lý đăng xuất
if (isset($_GET['act']) && $_GET['act'] == 'logout') {
    // Xóa tất cả các biến session
    session_unset();

    // Hủy phiên làm việc hoàn toàn
    session_destroy();

    // Thông báo và chuyển hướng về trang chủ
    header('Location: /Web_tintuc/index.php');
    exit();
}

// 1. Gọi Header
include 'site/header.php';

//mapping url
$routes = [
    'home'           => 'site/pages/home.php',
    'tintuc'         => 'site/pages/news/tintuc.php',
    'chitiet_tintuc' => 'site/pages/news/chitiet_tintuc.php', // Cấu trúc: site -> pages -> news -> file
    'danhmuc'        => 'site/pages/danhmuc.php',

    'dangky'         => 'site/pages/auth/dangky.php',
    'dangnhap'       => 'site/pages/auth/dangnhap.php',

    'thongtincanhan' => 'site/pages/user/thongtincanhan.php',
    'my_comments'    => 'site/pages/user/my_comments.php',
    'tin_da_luu'     => 'site/pages/user/tin_da_luu.php',
    'tin_da_xem'     => 'site/pages/user/tin_da_xem.php',

    'bookmark_add'   => 'site/pages/bookmark/bookmark_add.php',
    'bookmark_delete' => 'site/pages/bookmark/bookmark_delete.php',
    'bookmark_list'  => 'site/pages/bookmark/bookmark_list.php',
];

// 2. Điều hướng nội dung (Router đơn giản)
// Mặc định vào trang home
$page = $_GET['p'] ?? 'home';

if (isset($routes[$page]) && file_exists($routes[$page])) {
    include $routes[$page];
} else {
    include 'site/pages/404.php';
}

// 3. Gọi Footer
include 'site/footer.php';
