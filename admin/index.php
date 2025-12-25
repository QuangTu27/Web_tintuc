<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 1. Kết nối DB
if (file_exists('../connect.php')) {
    include '../connect.php';
} else {
    die("Lỗi: Không tìm thấy file connect.php");
}

// 2. Gọi Header (Sidebar đen của mày)
if (file_exists('header_admin.php')) {
    include 'header_admin.php';
}
?>

<div class="main-content" style="padding: 20px;">
    <?php
    // Lấy thông tin từ URL
    $mod = isset($_GET['mod']) ? $_GET['mod'] : 'dashboard';
    $act = isset($_GET['act']) ? $_GET['act'] : 'list';

    // --- TRƯỜNG HỢP 1: TRANG CHỦ DASHBOARD ---
    if ($mod == 'dashboard') {
    ?>
        <div class="dashboard-welcome">
            <h2>👋 Xin chào, <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Quản trị viên'; ?>!</h2>
            <p>Chào mừng bạn quay trở lại hệ thống quản trị website tin tức.</p>

            <div style="display: flex; gap: 20px; margin-top: 30px;">
                <div style="background: #007bff; color: white; padding: 20px; border-radius: 8px; flex: 1;">
                    <h3><a href="index.php?mod=tintuc&act=list" style="color:white; text-decoration:none;">Tin tức</a></h3>
                    <p>Quản lý bài viết</p>
                </div>
                <div style="background: #28a745; color: white; padding: 20px; border-radius: 8px; flex: 1;">
                    <h3>Danh mục</h3>
                    <p>Quản lý chuyên mục</p>
                </div>
                <div style="background: #ffc107; color: black; padding: 20px; border-radius: 8px; flex: 1;">
                    <h3>Thành viên</h3>
                    <p>Quản lý user</p>
                </div>
            </div>
        </div> 
    <?php
    } 
    // --- TRƯỜNG HỢP 2: GỌI CÁC MODULE (Tin tức, Danh mục...) ---
    else {
        $path = "modules/" . $mod . "/" . $act . ".php";
        
        if (file_exists($path)) {
            include $path;
        } else {
            echo "<div style='padding:20px; color:red;'>";
            echo "<h3>❌ Không tìm thấy file: $path</h3>";
            echo "<p>Mày kiểm tra lại folder <b>modules/tintuc/</b> xem có file <b>list.php</b> chưa?</p>";
            echo "</div>";
        }
    }
    ?>
</div> <?php
// 6. Gọi giao diện Footer
if (file_exists('footer_admin.php')) {
    include 'footer_admin.php';
}
?>