<?php
session_start();
// 1. Gọi file kết nối CSDL (ra ngoài 1 cấp thư mục)
include '../connect.php';

// 2. KIỂM TRA BẢO MẬT (Chặn không cho vào nếu chưa đăng nhập)
// Nếu chưa có session 'admin_login', đá về trang login ngay
if (!isset($_SESSION['admin_login'])) {
    header('location: login.php');
    exit();
}

// 3. Gọi giao diện phần Đầu (Menu, Logo...)
include 'header_admin.php';
?>

<?php
// Lấy thông tin từ URL (Ví dụ: index.php?mod=tintuc&act=add)
// Nếu không có 'mod' thì mặc định là 'dashboard' (Trang chủ)
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'dashboard';
$act = isset($_GET['act']) ? $_GET['act'] : 'list';

// --- TRƯỜNG HỢP 1: TRANG CHỦ DASHBOARD (Mới vào Admin) ---
if ($mod == 'dashboard') {
    // Bạn có thể viết code thống kê đơn giản ở đây
?>
    <div class="dashboard-welcome">
        <h2>👋 Xin chào, <?php echo $_SESSION['admin_name']; ?>!</h2>
        <p>Chào mừng bạn quay trở lại hệ thống quản trị website tin tức.</p>

        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <div style="background: #007bff; color: white; padding: 20px; border-radius: 8px; flex: 1;">
                <h3>Tin tức</h3>
                <p>Đang quản lý bài viết</p>
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
    </div
        <?php
    }
    // --- TRƯỜNG HỢP 2: GỌI CÁC MODULE CON ---
    else {
        // Tạo đường dẫn đến file cần gọi
        // Ví dụ: modules/tintuc/list.php
        $path = "modules/{$mod}/{$act}.php";

        // Kiểm tra file có tồn tại không rồi mới include
        if (file_exists($path)) {
            include $path;
        } else {
            // Nếu không tìm thấy file, báo lỗi đẹp
            echo "<div style='color: red; padding: 20px; background: #fff3cd; border: 1px solid #ffeeba;'>";
            echo "<h3>❌ Lỗi 404: Không tìm thấy chức năng này!</h3>";
            echo "<p>File không tồn tại: <b>{$path}</b></p>";
            echo "<p>Vui lòng kiểm tra lại tên thư mục hoặc tên file.</p>";
            echo "</div>";
        }
    }
        ?>

        <?php
        // 4. Gọi giao diện phần Chân (Đóng thẻ div)
        include 'footer_admin.php';
        ?>