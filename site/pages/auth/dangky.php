<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_register'])) {
    // 1. Lấy dữ liệu
    $hoten = trim($_POST['hoten']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']); // Có thể rỗng
    $password = $_POST['password'];

    // 2. Validate cơ bản (Chỉ check Username và Pass)
    if (empty($hoten) || empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập các trường bắt buộc!'); window.history.back();</script>";
        exit();
    }

    // 3. Kiểm tra trùng lặp USERNAME
    // Chỉ cần kiểm tra username thôi vì email có thể họ không nhập
    $sql_check = "SELECT * FROM tbl_users WHERE username = '$username'";

    // Nếu họ CÓ nhập email, thì kiểm tra xem email đó đã ai dùng chưa
    if (!empty($email)) {
        $sql_check .= " OR email = '$email'";
    }

    $res_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($res_check) > 0) {
        echo "<script>alert('Tên đăng nhập (hoặc Email) đã tồn tại!'); window.history.back();</script>";
        exit();
    }

    // 4. XỬ LÝ INSERT (QUAN TRỌNG)
    // Nếu email rỗng -> set là NULL trong câu lệnh SQL
    if (empty($email)) {
        $sql = "INSERT INTO tbl_users (hoten, username, email, password, role) 
                VALUES ('$hoten', '$username', NULL, '$password', 'user')";
    } else {
        $sql = "INSERT INTO tbl_users (hoten, username, email, password, role) 
                VALUES ('$hoten', '$username', '$email', '$password', 'user')";
    }

    // 5. Thực thi
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Đăng ký thành công! Vui lòng đăng nhập.');
            window.location.href = 'Web_tintuc/index.php';
        </script>";
    } else {
        echo "<script>alert('Lỗi: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
