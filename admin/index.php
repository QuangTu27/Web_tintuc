<?php
session_start();
// 1. Gọi file kết nối CSDL (ra ngoài 1 cấp thư mục)
include '../connect.php';

if (isset($_GET['act']) && $_GET['act'] == 'logout') {
    // Xóa toàn bộ session
    session_destroy();
    // Hoặc xóa từng cái nếu muốn giữ lại setting khác:
    // unset($_SESSION['admin_login']);

    // Chuyển hướng về trang đăng nhập
    header('location: login.php');
    exit();
}

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

// --- TRƯỜNG HỢP 1: TRANG CHỦ DASHBOARD ---
if ($mod == 'dashboard') {
    // 1. LẤY SỐ LIỆU THỐNG KÊ
    // Đếm tổng bài viết
    $count_news = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_news"))['total'];

    // Đếm bài viết chờ duyệt (Quan trọng với Editor)
    $count_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_news WHERE trangthai='cho_duyet'"))['total'];

    // Đếm tổng danh mục
    $count_cats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_categories"))['total'];

    // Đếm tổng thành viên (Chỉ Admin thấy)
    $count_users = 0;
    if ($_SESSION['admin_role'] == 'admin') {
        $count_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_users"))['total'];
    }

    // Lấy 5 bài viết mới nhất chờ duyệt
    $sql_pending_list = "SELECT id, tieude, ngaydang, author_id FROM tbl_news WHERE trangthai='cho_duyet' ORDER BY id DESC LIMIT 5";
    $res_pending = mysqli_query($conn, $sql_pending_list);
?>
    <div class="dashboard-container">
        <div class="welcome-banner" style="background: linear-gradient(135deg, #0a9e54 0%, #28a745 100%); padding: 30px; border-radius: 12px; color: white; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
            <h2 style="margin: 0 0 10px 0; font-size: 24px;">👋 Xin chào, <?php echo $_SESSION['admin_name']; ?>!</h2>
            <p style="margin: 0; opacity: 0.9;">Chúc bạn một ngày làm việc hiệu quả. Dưới đây là tổng quan hệ thống hôm nay.</p>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">

            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #007bff;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 28px; color: #333;"><?php echo number_format($count_news); ?></h3>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Tổng bài viết</p>
                    </div>
                    <i class="fas fa-newspaper" style="font-size: 30px; color: #007bff; opacity: 0.2;"></i>
                </div>
            </div>

            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #ffc107;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 28px; color: #333;"><?php echo number_format($count_pending); ?></h3>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Bài chờ duyệt</p>
                    </div>
                    <i class="fas fa-clock" style="font-size: 30px; color: #ffc107; opacity: 0.3;"></i>
                </div>
            </div>

            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #28a745;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 28px; color: #333;"><?php echo number_format($count_cats); ?></h3>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Chuyên mục</p>
                    </div>
                    <i class="fas fa-list" style="font-size: 30px; color: #28a745; opacity: 0.2;"></i>
                </div>
            </div>

            <?php if ($_SESSION['admin_role'] == 'admin'): ?>
                <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #17a2b8;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; font-size: 28px; color: #333;"><?php echo number_format($count_users); ?></h3>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Thành viên</p>
                        </div>
                        <i class="fas fa-users" style="font-size: 30px; color: #17a2b8; opacity: 0.2;"></i>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="recent-tasks" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; font-size: 18px; color: #333;">
                <i class="fas fa-tasks" style="color: #ffc107; margin-right: 10px;"></i> Bài viết mới cần duyệt
            </h3>

            <?php if (mysqli_num_rows($res_pending) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #f8f9fa; color: #555; text-align: left;">
                            <th style="padding: 10px; border-bottom: 2px solid #eee;">Tiêu đề</th>
                            <th style="padding: 10px; border-bottom: 2px solid #eee;">Ngày gửi</th>
                            <th style="padding: 10px; border-bottom: 2px solid #eee;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($res_pending)): ?>
                            <tr>
                                <td style="padding: 12px 10px; border-bottom: 1px solid #eee;">
                                    <a href="index.php?mod=tintuc&act=edit&id=<?= $row['id'] ?>" style="text-decoration: none; color: #333; font-weight: 500;">
                                        <?= htmlspecialchars($row['tieude']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px 10px; border-bottom: 1px solid #eee; color: #777; font-size: 14px;">
                                    <?= date('d/m/Y H:i', strtotime($row['ngaydang'])) ?>
                                </td>
                                <td style="padding: 12px 10px; border-bottom: 1px solid #eee;">
                                    <a href="index.php?mod=tintuc&act=edit&id=<?= $row['id'] ?>" style="padding: 5px 10px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;">Xem & Duyệt</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; margin-top: 20px;">Tuyệt vời! Không có bài viết nào đang chờ duyệt.</p>
            <?php endif; ?>
        </div>
    </div>
<?php
}
// --- TRƯỜNG HỢP 2: GỌI CÁC MODULE CON ---
else {
    // Tạo đường dẫn đến file cần gọi
    // Ví dụ: modules/tintuc/list.php
    $path = "modules/{$mod}/{$act}.php";

    $role = $_SESSION['admin_role'];

    if ($mod == 'ads' && $role != 'admin') {
        die('Bạn không có quyền truy cập');
    }

    if ($mod == 'binhluan' && $role != 'editor' && $role != 'admin') {
        die('Bạn không có quyền truy cập');
    }
    if (($mod == 'user' &&  $role != 'admin') && ($act != 'profile')) {
        die('Bạn không có quyền truy cập');
    }

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