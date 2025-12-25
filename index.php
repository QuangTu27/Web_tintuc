<?php
session_start();
// Kết nối CSDL
include 'connect.php';

//xử lý đăng xuất
if (isset($_GET['act']) && $_GET['act'] == 'logout') {
    // Xóa tất cả các biến session
    session_unset();

    // Hủy phiên làm việc hoàn toàn
    session_destroy();

    // Thông báo và chuyển hướng về trang chủ
    echo "<script>
        alert('Bạn đã đăng xuất thành công!');
        window.location.href = 'index.php';
    </script>";
    exit(); // Dừng code tại đây không chạy tiếp phần dưới
}

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
