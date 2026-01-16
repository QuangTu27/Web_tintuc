<?php
session_start();
include '../connect.php';

if (isset($_SESSION['admin_login'])) {
    header('location: index.php');
    exit();
}

if (isset($_POST['btn_login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $sql = "SELECT * FROM tbl_users WHERE username = '$u' AND password = '$p' AND role NOT IN ('user')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_name'] = $row['hoten'];
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_role']   = $row['role'];
        $_SESSION['admin_avatar'] = $row['avatar'];

        header('location: index.php');
        exit();
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