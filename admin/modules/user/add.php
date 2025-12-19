<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');


// Xử lý khi submit form
if (isset($_POST['btn_add'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hoten    = $_POST['hoten'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];

    $check = "SELECT * FROM tbl_users WHERE username='$username'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color:red'>Username đã tồn tại</p>";
    } else {
        $sql = "INSERT INTO tbl_users(username, password, hoten, email, role)
                VALUES ('$username', '$password', '$hoten', '$email', '$role')";
        mysqli_query($conn, $sql);
        echo "<p style='color:green'>Thêm user thành công</p>";
        header('Location: index.php?mod=user&act=list');
        exit();
    }
}
?>



<h2>Thêm người dùng</h2>

<?php if (isset($error)) { ?>
    <p style="color:red"><?= $error ?></p>
<?php } ?>

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
            <td>Quyền</td>
            <td>
                <select name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <button type="submit" name="btn_add">Thêm user</button>
                <a href="index.php?mod=user&act=list">Quay lại</a>
            </td>
        </tr>
    </table>
</form>