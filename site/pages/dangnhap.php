<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_login'])) {
    // 1. Lấy dữ liệu từ form (Input này có thể là Username hoặc Email)
    $input_user = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate cơ bản
    if (empty($input_user) || empty($password)) {
        echo "<script>
            alert('Vui lòng nhập tên đăng nhập và mật khẩu!');
            window.history.back();
        </script>";
        exit();
    }

    // 2. QUERY KIỂM TRA ĐĂNG NHẬP
    // Logic: Tìm người có (Username = input HOẶC Email = input) VÀ Mật khẩu trùng khớp
    // Lưu ý: Do bạn đang lưu mật khẩu thô (chưa mã hóa) nên so sánh trực tiếp '='
    $sql = "SELECT * FROM tbl_users 
            WHERE (username = '$input_user' OR email = '$input_user') 
            AND password = '$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // 3. Đăng nhập thành công -> Lưu Session
        $row = mysqli_fetch_assoc($result);

        $_SESSION['user_login'] = true;       // Cờ đánh dấu đã login
        $_SESSION['user_id'] = $row['id'];    // ID user
        $_SESSION['user_name'] = $row['hoten']; // Họ tên hiển thị
        $_SESSION['user_role'] = $row['role']; // Quyền hạn (admin, editor, user...)

        // Lưu thêm avatar nếu có (để hiển thị cho đẹp)
        $_SESSION['user_avatar'] = isset($row['avatar']) ? $row['avatar'] : 'default.png';

        // 4. PHÂN QUYỀN CHUYỂN HƯỚNG (Redirect)
        // Nếu là Admin hoặc Editor, Phóng viên -> Vào trang Quản trị
        if ($row['role'] != 'user') {
            echo "<script>
                alert('Chào mừng " . $row['role'] . " quay trở lại!');
                window.location.href = '../../admin/index.php';
            </script>";
        } else {
            // Nếu là User thường -> Về Trang chủ xem tin tức
            // Dùng $_SERVER['HTTP_REFERER'] để quay lại trang họ đang đứng trước đó (nếu có)
            echo "<script>
                alert('Đăng nhập thành công!');
                window.location.href = '../../index.php';
            </script>";
        }
    } else {
        // 5. Đăng nhập thất bại
        echo "<script>
            alert('Sai tên đăng nhập hoặc mật khẩu! Vui lòng thử lại.');
            window.history.back(); // Quay lại form để nhập lại
        </script>";
    }
} else {
    // Nếu truy cập trực tiếp vào file này mà không bấm nút -> Đuổi về
    header('Location: ../../index.php');
}
