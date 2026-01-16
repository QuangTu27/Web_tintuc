<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_register'])) {
    $hoten = trim($_POST['hoten']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql_check = "SELECT * FROM tbl_users WHERE username = '$username'";
    if (!empty($email)) {
        $sql_check .= " OR email = '$email'";
    }
    $res_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($res_check) > 0) {
        echo "<script>alert('Tên đăng nhập (hoặc Email) đã tồn tại!'); window.history.back();</script>";
        exit();
    }

    //insert
    if (empty($email)) {
        $sql = "INSERT INTO tbl_users (hoten, username, email, password, role) 
                VALUES ('$hoten', '$username', NULL, '$password', 'user')";
    } else {
        $sql = "INSERT INTO tbl_users (hoten, username, email, password, role) 
                VALUES ('$hoten', '$username', '$email', '$password', 'user')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Đăng ký thành công! Vui lòng đăng nhập.');
            window.location.href = '/Web_tintuc/index.php';
        </script>";
    } else {
        echo "<script>alert('Lỗi đăng ký tài khoản!'); window.history.back();</script>";
    }
}
