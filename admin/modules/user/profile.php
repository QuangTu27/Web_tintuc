<?php
// Bắt buộc đăng nhập 
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['admin_id'];

// Lấy thông tin user
$sql = "SELECT * FROM tbl_users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

//UPDATE PROFILE
if (isset($_POST['btn_update'])) {

    $hoten  = trim($_POST['hoten']);
    $email  = trim($_POST['email']);
    $pass   = trim($_POST['password']);

    // Cập nhật avatar
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file(
            $_FILES['avatar']['tmp_name'],
            $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/avatars/$avatar"
        );
        mysqli_query($conn, "UPDATE tbl_users SET avatar='$avatar' WHERE id=$user_id");
        $_SESSION['admin_avatar'] = $avatar;
    }

    // Cập nhật họ tên + email
    mysqli_query($conn, "
        UPDATE tbl_users 
        SET hoten='$hoten', email='$email'
        WHERE id=$user_id
    ");

    // Cập nhật mật khẩu (nếu có nhập)
    if ($pass != "") {
        mysqli_query($conn, "
            UPDATE tbl_users 
            SET password='$pass'
            WHERE id=$user_id
        ");
    }

    header("Location: index.php?mod=user&act=profile");
    exit;
}
?>


<div class="admin-container">
    <a href="index.php?mod=user&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>

    <h2 class="admin-title">Thông tin cá nhân</h2>

    <form method="post" enctype="multipart/form-data" class="admin-form">

        <!-- AVATAR -->
        <div class="form-group text-center">
            <img
                src="/Web_tintuc/images/avatars/<?php echo $user['avatar']; ?>"
                class="admin-avatar">
            <input type="file" name="avatar" accept="image/*">
        </div>

        <!-- USERNAME -->
        <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?php echo $user['username']; ?>" disabled>
        </div>

        <!-- HỌ TÊN -->
        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="hoten"
                value="<?php echo $user['hoten']; ?>" required>
        </div>

        <!-- EMAIL -->
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                value="<?php echo $user['email']; ?>" required>
        </div>

        <!-- PASSWORD -->
        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="password"
                placeholder="Để trống nếu không đổi">
        </div>

        <!-- ROLE -->
        <div class="form-group">
            <label>Quyền</label>
            <input type="text" value="<?php echo $user['role']; ?>" disabled>
        </div>

        <!-- CREATED -->
        <div class="form-group">
            <label>Ngày tham gia</label>
            <input type="text" value="<?php echo $user['created_at']; ?>" disabled>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Lưu thay đổi
            </button>
        </div>
    </form>
</div>