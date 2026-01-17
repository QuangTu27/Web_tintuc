<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_login'])) {
    $input_user = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM tbl_users 
            WHERE (username = '$input_user' OR email = '$input_user') 
            AND password = '$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['user_login']    = true;
        $_SESSION['user_id']       = $row['id'];
        $_SESSION['user_name']     = $row['hoten'];
        $_SESSION['user_username'] = $row['username'];
        $_SESSION['avatar']        = $row['avatar'];

        //Redirect
        if ($row['role'] != 'user') {
            echo "<script>
                alert('Chào mừng " . $row['role'] . " quay trở lại!');
                window.location.href = '/Web_tintuc/admin/index.php';
            </script>";
        } else {
            header('Location: /Web_tintuc/index.php');
            exit;
        }
    } else {
        echo "<script>
            alert('Sai tên đăng nhập hoặc mật khẩu! Vui lòng thử lại.');
            window.history.back(); 
        </script>";
    }
} else {
    header('Location: ../../index.php');
}
