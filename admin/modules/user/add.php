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
        $error = "Username đã tồn tại";
    } else {
        $sql = "INSERT INTO tbl_users(username, password, hoten, email, role)
                VALUES ('$username', '$password', '$hoten', '$email', '$role')";
        mysqli_query($conn, $sql);
        header('Location: index.php?mod=user&act=list');
        exit();
    }
}
?>

<div class="admin-container">
    <a href="index.php?mod=user&act=list" class="btn btn_back">
        Quay lại
    </a>
    <h2 class="admin-title">
        Thêm người dùng
    </h2>

    <?php if (isset($error)) { ?>
        <p class="form-error"><?= $error ?></p>
    <?php } ?>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="hoten">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email">
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" name="btn_add" class="btn btn-primary">
                Thêm user
            </button>
            <a href="index.php?mod=user&act=list" class="btn btn-secondary">
                Huỷ
            </a>
        </div>

    </form>
</div>