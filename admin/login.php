<?php
session_start();
// Gọi file kết nối (Lùi ra 1 cấp thư mục để tìm connect.php)
include '../connect.php';

// Kiểm tra nếu đã login rồi thì đẩy thẳng vào trang dashboard
if (isset($_SESSION['admin_login'])) {
    header('location: index.php');
    exit();
}

// Xử lý khi người dùng ấn nút Đăng nhập
if (isset($_POST['btn_login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // LƯU Ý QUAN TRỌNG:
    // 1. Tên bảng phải là 'tbl_users' (như trong hình bạn gửi)
    // 2. Cột role phải so sánh với chữ 'admin'
    $sql = "SELECT * FROM tbl_users WHERE username = '$u' AND password = '$p' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Đăng nhập thành công -> Lưu session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_name'] = $row['hoten'];
        $_SESSION['admin_id'] = $row['id'];

        // Chuyển hướng vào trang quản trị chính
        header('location: index.php');
    } else {
        $error = "Sai tài khoản, mật khẩu hoặc bạn không phải Admin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="/Web_tintuc/admin/css/login.css">
</head>

<body class="login-page">

    <div class="login-box">
        <h2>QUẢN TRỊ VIÊN</h2>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="btn_login">ĐĂNG NHẬP</button>
        </form>
    </div>

</body>

</html>