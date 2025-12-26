<?php
// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_login'])) {
    header("Location: index.php");
    exit();
}

// 2. LẤY THÔNG TIN USER
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tbl_users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Đường dẫn ảnh
$avatar_img = !empty($user['avatar']) ? $user['avatar'] : 'default.png';
$join_date = date('m/Y', strtotime($user['created_at']));

// =================================================================
// 3. XỬ LÝ CẬP NHẬT DỮ LIỆU (PHP)
// =================================================================

// A. Cập nhật Avatar
if (isset($_POST['btn_update_avatar'])) {
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/avatars/";
        // Tạo tên file mới để tránh trùng: ID_Timestamp.jpg
        $extension = pathinfo($_FILES["avatar_file"]["name"], PATHINFO_EXTENSION);
        $new_filename = $user_id . '_' . time() . '.' . $extension;
        $target_file = $target_dir . $new_filename;

        // Cho phép các định dạng ảnh
        $allow_types = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($extension), $allow_types)) {
            if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_file)) {
                // Update Database
                $sql_avt = "UPDATE tbl_users SET avatar = '$new_filename' WHERE id = $user_id";
                mysqli_query($conn, $sql_avt);

                // Update Session
                $_SESSION['user_avatar'] = $new_filename;

                echo "<script>alert('Đổi ảnh đại diện thành công!'); window.location.href='index.php?p=thongtincanhan';</script>";
            } else {
                echo "<script>alert('Lỗi khi tải ảnh lên server.');</script>";
            }
        } else {
            echo "<script>alert('Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF.');</script>";
        }
    }
}

// B. Cập nhật Họ tên
if (isset($_POST['btn_update_name'])) {
    $new_name = trim($_POST['hoten']);
    if (!empty($new_name)) {
        $sql_name = "UPDATE tbl_users SET hoten = '$new_name' WHERE id = $user_id";
        mysqli_query($conn, $sql_name);
        $_SESSION['user_name'] = $new_name; // Cập nhật session tên hiển thị
        echo "<script>alert('Đổi tên thành công!'); window.location.href='index.php?p=thongtincanhan';</script>";
    }
}

// C. Cập nhật Email (Cần check pass cũ)
if (isset($_POST['btn_update_email'])) {
    $pass_check = $_POST['pass_check'];
    $new_email = trim($_POST['new_email']);

    // Check pass cũ (So sánh thô vì DB của bạn chưa hash, nếu hash dùng password_verify)
    if ($pass_check == $user['password']) {
        // Check xem email mới có trùng ai không
        $check_exist = mysqli_query($conn, "SELECT id FROM tbl_users WHERE email = '$new_email' AND id != $user_id");
        if (mysqli_num_rows($check_exist) == 0) {
            $sql_email = "UPDATE tbl_users SET email = '$new_email' WHERE id = $user_id";
            mysqli_query($conn, $sql_email);
            echo "<script>alert('Đổi email thành công!'); window.location.href='index.php?p=thongtincanhan';</script>";
        } else {
            echo "<script>alert('Email này đã được sử dụng bởi tài khoản khác.');</script>";
        }
    } else {
        echo "<script>alert('Mật khẩu hiện tại không đúng!');</script>";
    }
}

// D. Cập nhật Mật khẩu
if (isset($_POST['btn_update_pass'])) {
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];

    if ($pass_old == $user['password']) {
        $sql_pass = "UPDATE tbl_users SET password = '$pass_new' WHERE id = $user_id";
        mysqli_query($conn, $sql_pass);
        echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='index.php?act=logout';</script>";
    } else {
        echo "<script>alert('Mật khẩu cũ không đúng!');</script>";
    }
}
?>

<style>
    /* Ẩn các form edit mặc định */
    .edit-container {
        display: none;
        margin-top: 15px;
    }

    /* Box edit Avatar (nét đứt) */
    .avatar-edit-box {
        border: 2px dashed #ddd;
        padding: 30px;
        text-align: center;
        border-radius: 4px;
        background: #fdfdfd;
    }

    /* Box edit thông thường (nét liền) */
    .normal-edit-box {
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 4px;
        background: #fff;
    }

    /* Input style */
    .form-control {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        /* Fix lỗi tràn */
    }

    /* Buttons */
    .btn-save {
        background: #00b686;
        color: white;
        padding: 8px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-save:hover {
        background: #a01925;
    }

    .btn-upload {
        background: #fff;
        border: 1px solid #ccc;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .btn-close-edit {
        float: right;
        font-size: 13px;
        color: #999;
        text-decoration: underline;
        cursor: pointer;
    }

    .hidden {
        display: none !important;
    }
</style>

<div class="container profile-container">

    <div class="profile-sidebar">
        <div class="user-card">
            <div class="user-card-header">
                <?php if ($user['avatar']): ?>
                    <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" class="avatar-circle">
                <?php else: ?>
                    <div class="avatar-circle" style="background:#ccc;">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <div class="user-meta">
                    <h4><?= htmlspecialchars($user['username']) ?></h4>
                    <span>Tham gia từ <?= $join_date ?></span>
                </div>
            </div>

            <ul class="profile-menu">
                <li class="active"><a href="index.php?p=thongtincanhan">Thông tin chung</a></li>
                <li><a href="index.php?p=my_comments">Ý kiến của bạn (0)</a></li>
                <li><a href="index.php?p=tin_da_luu">Tin đã lưu</a></li>
                <li><a href="index.php?p=tin_da_xem">Tin đã xem</a></li>
                <li class="logout-item">
                    <a href="index.php?act=logout" onclick="return confirm('Đăng xuất?')">Thoát <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>

        <div class="support-box">
            Cần hỗ trợ, vui lòng liên hệ:<br>
            <a href="mailto:bandoc@web24h.net">bandoc@web24h.net</a>
        </div>
    </div>

    <div class="profile-content">
        <h2 class="page-title">Thông tin tài khoản</h2>

        <div class="info-section">
            <div class="info-row avatar-row" id="view-avatar">
                <div>
                    <span class="info-label">Ảnh đại diện</span>
                    <div style="margin-top: 10px;">
                        <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('avatar')">Thay ảnh đại diện</a>
            </div>

            <div class="edit-container" id="edit-avatar">
                <div style="margin-bottom: 10px; overflow: hidden;">
                    <span class="info-label">Ảnh đại diện</span>
                    <span class="btn-close-edit" onclick="toggleEdit('avatar')">Đóng</span>
                </div>
                <form method="POST" enctype="multipart/form-data" class="avatar-edit-box">
                    <div style="margin-bottom: 15px;">
                        <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                    </div>

                    <input type="file" name="avatar_file" id="file-upload" required style="margin-bottom: 15px;">
                    <br>
                    <button type="submit" name="btn_update_avatar" class="btn-save">Lưu thay đổi</button>
                </form>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #f0f0f0; margin: 0;">

        <div class="info-section">
            <div class="info-row" id="view-name">
                <div>
                    <span class="info-label">Họ tên</span>
                    <span class="info-value">
                        <?= !empty($user['hoten']) ? htmlspecialchars($user['hoten']) : 'Chưa có dữ liệu' ?>
                    </span>
                </div>
                <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('name')">Thay đổi</a>
            </div>

            <div class="edit-container" id="edit-name">
                <div style="margin-bottom: 10px; overflow: hidden;">
                    <span class="info-label">Họ tên</span>
                    <span class="btn-close-edit" onclick="toggleEdit('name')">Đóng</span>
                </div>
                <form method="POST" class="normal-edit-box">
                    <label style="font-size:13px; color:#666; margin-bottom:5px; display:block;">Nhập họ tên</label>
                    <input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($user['hoten']) ?>" placeholder="Nhập họ và tên">
                    <button type="submit" name="btn_update_name" class="btn-save" style="background: #e0e0e0; color: #333;">Đổi tên</button>
                </form>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #f0f0f0; margin: 0;">

        <div class="info-section">
            <div class="info-row" id="view-email">
                <div>
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <?= !empty($user['email']) ? $user['email'] : 'Chưa cập nhật email' ?>
                    </span>
                </div>
                <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('email')">Thay đổi</a>
            </div>

            <div class="edit-container" id="edit-email">
                <div style="margin-bottom: 10px; overflow: hidden;">
                    <span class="info-label">Email</span>
                    <span class="btn-close-edit" onclick="toggleEdit('email')">Đóng</span>
                </div>
                <form method="POST" class="normal-edit-box">
                    <label style="font-size:13px; color:#666; margin-bottom:5px; display:block;">Nhập mật khẩu hiện tại</label>
                    <input type="password" name="pass_check" class="form-control" placeholder="Nhập mật khẩu" required>

                    <label style="font-size:13px; color:#666; margin-bottom:5px; display:block;">Nhập email mới</label>
                    <input type="email" name="new_email" class="form-control" value="<?= $user['email'] ?>" placeholder="Nhập email mới" required>

                    <button type="submit" name="btn_update_email" class="btn-save" style="background: #e0e0e0; color: #333;">Đổi email</button>
                </form>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #f0f0f0; margin: 0;">

        <div class="info-section">
            <div class="info-row" id="view-pass">
                <div>
                    <span class="info-label">Mật khẩu</span>
                    <span class="info-value">•••••••••••••</span>
                </div>
                <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('pass')">Thay đổi</a>
            </div>

            <div class="edit-container" id="edit-pass">
                <div style="margin-bottom: 10px; overflow: hidden;">
                    <span class="info-label">Mật khẩu</span>
                    <span class="btn-close-edit" onclick="toggleEdit('pass')">Đóng</span>
                </div>
                <form method="POST" class="normal-edit-box">
                    <label style="font-size:13px; color:#666; margin-bottom:5px; display:block;">Nhập mật khẩu hiện tại</label>
                    <input type="password" name="pass_old" class="form-control" placeholder="Nhập mật khẩu hiện tại" required>

                    <label style="font-size:13px; color:#666; margin-bottom:5px; display:block;">Nhập mật khẩu mới</label>
                    <input type="password" name="pass_new" class="form-control" placeholder="Tạo mật khẩu mới" required>

                    <button type="submit" name="btn_update_pass" class="btn-save" style="background: #e0e0e0; color: #333;">Đổi mật khẩu</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function toggleEdit(type) {
        var viewRow = document.getElementById('view-' + type);
        var editBox = document.getElementById('edit-' + type);

        // Logic Toggle
        if (editBox.style.display === "block") {
            editBox.style.display = "none";
            viewRow.classList.remove('hidden'); // Hiện lại view cũ
        } else {
            // Reset các cái khác đang mở (Nếu muốn chỉ mở 1 cái 1 lúc)
            document.querySelectorAll('.edit-container').forEach(e => e.style.display = 'none');
            document.querySelectorAll('.info-row').forEach(e => e.classList.remove('hidden'));

            editBox.style.display = "block";
            viewRow.classList.add('hidden'); // Ẩn view cũ đi
        }
    }
</script>