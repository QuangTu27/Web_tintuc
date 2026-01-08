<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Xử lý khi submit form
if (isset($_POST['btn_add'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $hoten    = mysqli_real_escape_string($conn, $_POST['hoten']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $role     = $_POST['role'];

    $check = "SELECT * FROM tbl_users WHERE username='$username'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        $error = "Username đã tồn tại";
    } else {
        $sql = "INSERT INTO tbl_users(username, password, hoten, email, role)
                VALUES ('$username', '$password', '$hoten', '$email', '$role')";
        mysqli_query($conn, $sql);
        header('Location: index.php?mod=user&act=list&msg=added');
        exit();
    }
}
?>

<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title" style="margin: 0; flex-grow: 1; text-align: center;">THÊM NGƯỜI DÙNG</h2>
        <div style="width: 140px;"></div>
    </div>

    <?php if (isset($error)): ?>
        <p class="form-error" style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px;"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" class="admin-form">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Nhập tên đăng nhập (ví dụ: admin_24h)" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Nhập mật khẩu ít nhất 6 ký tự..." required>
            <small class="form-hint">*Mật khẩu nên bao gồm cả chữ cái và chữ số.*</small>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="hoten" placeholder="Nhập họ và tên đầy đủ...">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="vidu@gmail.com">
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select name="role">
                <option value="editor">Editor</option>
                <option value="phongvien">Phóng viên</option>
                <option value="nhabao">Nhà báo</option>
                <option value="ctv">Cộng tác viên</option>
                <option value="user">User</option>
            </select>
        </div>

        <div class="btn-group-center" style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
            <button type="submit" name="btn_add" class="btn btn-OK">💾 Lưu người dùng</button>
            <a href="index.php?mod=user&act=list" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>