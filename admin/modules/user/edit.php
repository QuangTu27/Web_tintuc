<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

/* =========================
   1. LẤY ID USER
========================= */
if (!isset($_GET['id'])) {
    header("Location: /Web_tintuc/admin/index.php?mod=user&act=list");
    exit;
}

$id = (int)$_GET['id'];

/* =========================
   2. LẤY THÔNG TIN USER
========================= */
$sql = "SELECT * FROM tbl_users WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?mod=user&act=list");
    exit;
}

$user = mysqli_fetch_assoc($result);

/* =========================
   3. XỬ LÝ SUBMIT UPDATE
========================= */
if (isset($_POST['btn_update'])) {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $sqlUpdate = "
            UPDATE tbl_users 
            SET hoten='$hoten',
                email='$email',
                role='$role',
                password='$password'
            WHERE id=$id
        ";
    } else {
        $sqlUpdate = "
            UPDATE tbl_users 
            SET hoten='$hoten',
                email='$email',
                role='$role'
            WHERE id=$id
        ";
    }

    mysqli_query($conn, $sqlUpdate);
    header("Location: index.php?mod=user&act=list");
    exit;
}
?>

<div class="admin-container">

    <a href="index.php?mod=user&act=list" class="btn btn-Cancel">
        Quay lại
    </a>
    <h2 class="admin-title">
        Cập nhật người dùng
    </h2>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?= $user['username'] ?>" disabled>
        </div>

        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="password">
            <small class="form-note">Bỏ trống nếu không đổi</small>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="hoten" value="<?= $user['hoten'] ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select name="role">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="phongvien" <?= $user['role'] == 'phongvien' ? 'selected' : '' ?>>Phóng viên</option>
                <option value="nhabao" <?= $user['role'] == 'nhabao' ? 'selected' : '' ?>>Nhà báo</option>
                <option value="ctv" <?= $user['role'] == 'ctv' ? 'selected' : '' ?>>Cộng tác viên</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Cập nhật
            </button>
            <a href="index.php?mod=user&act=list" class="btn btn-Cancel">
                Huỷ
            </a>
        </div>

    </form>

</div>