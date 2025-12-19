<?php
include 'C:/xampp/htdocs/Web_tintuc/connect.php';
if (isset($_POST['btn_add'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hoten    = $_POST['hoten'];
    $email    = $_POST['email'];

    $check = "SELECT * FROM tbl_users WHERE username='$username'";
    $result = mysqli_query($conn, $check);
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color:red'>Username đã tồn tại</p>";
    } else {
        $sql = "INSERT INTO tbl_users(username, password, hoten, email, role)
                VALUES ('$username', '$password', '$hoten', '$email', 'user')";
        mysqli_query($conn, $sql);
        echo "<p style='color:green'>Đăng ký thành công</p>";
        header('Location: index.php?mod=user&act=list');
        exit();
    }
}
?>

<h2>ĐĂNG KÝ NGƯỜI DÙNG MỚI</h2>
<form method="post">
    <table cellpadding="5">
        <tr>
            <td>Username</td>
            <td><input type="text" name="username" required></td>
        </tr>

        <tr>
            <td>Password</td>
            <td><input type="password" name="password" required></td>
        </tr>

        <tr>
            <td>Họ tên</td>
            <td><input type="text" name="hoten"></td>
        </tr>

        <tr>
            <td>Email</td>
            <td><input type="email" name="email"></td>
        </tr>

        <tr>
            <td colspan="2">
                <button type="submit" name="btn_add">Đăng ký</button>
            </td>
        </tr>
    </table>
</form>