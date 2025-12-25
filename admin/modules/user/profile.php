<?php
// Bắt buộc đăng nhập
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['admin_id'];

// Lấy thông tin user hiện tại
$sql = "SELECT * FROM tbl_users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

/* =========================
   UPDATE AVATAR
========================= */
if (isset($_POST['btn_update'])) {

    if (!empty($_FILES['avatar']['name'])) {
        $avatar = time() . '_' . $_FILES['avatar']['name'];

        move_uploaded_file(
            $_FILES['avatar']['tmp_name'],
            $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/avatars/$avatar"
        );

        mysqli_query($conn, "
            UPDATE tbl_users 
            SET avatar='$avatar'
            WHERE id=$user_id
        ");

        // cập nhật session để header đổi ngay
        $_SESSION['admin_avatar'] = $avatar;
    }

    header("Location: index.php?mod=user&act=profile");
    exit;
}
?>

<div class="admin-container">
    <a href="index.php?mod=user&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>
    <h2 class="admin-title">
        Thông tin cá nhân
    </h2>

    <form method="post" enctype="multipart/form-data" class="admin-form">

        <!-- AVATAR -->
        <div class="form-group" style="text-align:center">
            <img
                src="/Web_tintuc/images/avatars/<?php echo $user['avatar']; ?>"
                style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin-bottom:10px;border:3px solid #007bff;">
            <input type="file" name="avatar" accept="image/*">
        </div>
        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Cập nhật avatar
            </button>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?php echo $user['username']; ?>" disabled>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" value="<?php echo $user['hoten']; ?>" disabled>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text" value="<?php echo $user['email']; ?>" disabled>
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <input type="text" value="<?php echo $user['role']; ?>" disabled>
        </div>

        <div class="form-group">
            <label>Ngày tham gia</label>
            <input type="text" value="<?php echo $user['created_at']; ?>" disabled>
        </div>

    </form>

</div>