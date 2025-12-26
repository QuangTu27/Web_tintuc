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
    $sql = "SELECT * FROM tbl_users 
            WHERE (username = '$input_user' OR email = '$input_user') 
            AND password = '$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // 3. Đăng nhập thành công -> Lưu Session
        $row = mysqli_fetch_assoc($result);

        $_SESSION['user_login']    = true;
        $_SESSION['user_id']       = $row['id'];
        $_SESSION['user_name']     = $row['hoten'];
        $_SESSION['user_username'] = $row['username'];
        $_SESSION['avatar']        = $row['avatar'];
        // 4. PHÂN QUYỀN CHUYỂN HƯỚNG (Redirect)
        // Nếu là Admin hoặc Editor, Phóng viên -> Vào trang Quản trị
        if ($row['role'] != 'user') {
            echo "<script>
                alert('Chào mừng " . $row['role'] . " quay trở lại!');
                window.location.href = '/Web_tintuc/admin/index.php';
            </script>";
        } else {
            header('Location: /Web_tintuc/index.php');
            exit();
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
