<?php
// KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_login'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// LẤY THÔNG TIN USER
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tbl_users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Đường dẫn ảnh
$avatar_img = !empty($user['avatar']) ? $user['avatar'] : 'default.png';
$join_date = date('m/Y', strtotime($user['created_at']));

// XỬ LÝ CẬP NHẬT DỮ LIỆU

// A. Cập nhật Avatar
if (isset($_POST['btn_update_avatar'])) {
    if (!empty($_FILES['avatar_file']['name'])) {
        $avatar_name = time() . '_' . $_FILES['avatar_file']['name'];
        move_uploaded_file(
            $_FILES['avatar_file']['tmp_name'],
            $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/avatars/$avatar_name"
        );
        mysqli_query($conn, "UPDATE tbl_users SET avatar='$avatar_name' WHERE id=$user_id");
        $_SESSION['user_avatar'] = $avatar_name;

        echo "<script>window.location.href='index.php?p=thongtincanhan';</script>";
        exit();
    }
}

// B. Cập nhật Họ tên
if (isset($_POST['btn_update_name'])) {
    $new_name = trim($_POST['hoten']);
    if (!empty($new_name)) {
        $sql_name = "UPDATE tbl_users SET hoten = '$new_name' WHERE id = $user_id";
        mysqli_query($conn, $sql_name);
        $_SESSION['user_name'] = $new_name;
        echo "<script>alert('Đổi tên thành công!'); window.location.href='index.php?p=thongtincanhan';</script>";
    }
}

// C. Cập nhật Email
if (isset($_POST['btn_update_email'])) {
    $new_email = trim($_POST['new_email']);
    $check_exist = mysqli_query($conn, "SELECT id FROM tbl_users WHERE email = '$new_email' AND id != $user_id");
    if (mysqli_num_rows($check_exist) == 0) {
        $sql_email = "UPDATE tbl_users SET email = '$new_email' WHERE id = $user_id";
        mysqli_query($conn, $sql_email);
        echo "<script>alert('Đổi email thành công!'); window.location.href='index.php?p=thongtincanhan';</script>";
    } else {
        echo "<script>alert('Email này đã được sử dụng bởi tài khoản khác.');</script>";
    }
}

// D. Cập nhật Mật khẩu
$error_pass_old = "";
$show_pass_form = false;

if (isset($_POST['btn_update_pass'])) {
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];

    if ($pass_old == $user['password']) {
        $sql_pass = "UPDATE tbl_users SET password = '$pass_new' WHERE id = $user_id";
        mysqli_query($conn, $sql_pass);
        echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='index.php?act=logout';</script>";
    } else {
        $error_pass_old = "Mật khẩu cũ không đúng";
        $show_pass_form = true;
    }
}

// XÁC ĐỊNH TAB HIỆN TẠI 
$act = isset($_GET['act']) ? $_GET['act'] : 'general'; // Mặc định là 'general' (Thông tin chung)
?>

<link rel="stylesheet" href="/Web_tintuc/site/css/profile.css">

<div class="container profile-container">

    <div class="profile-sidebar">
        <div class="user-card">
            <div class="user-card-header">
                <?php if ($user['avatar']): ?>
                    <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" class="avatar-circle">
                <?php else: ?>
                    <div class="avatar-circle">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <div class="user-meta">
                    <h4><?= htmlspecialchars($user['username']) ?></h4>
                    <span>Tham gia từ <?= $join_date ?></span>
                </div>
            </div>

            <ul class="profile-menu">
                <li class="<?= ($act == 'general') ? 'active' : '' ?>">
                    <a href="index.php?p=thongtincanhan&act=general">Thông tin chung</a>
                </li>

                <li class="<?= ($act == 'my_comments') ? 'active' : '' ?>">
                    <a href="index.php?p=thongtincanhan&act=my_comments">Ý kiến của bạn</a>
                </li>

                <li class="<?= ($act == 'bookmark_list') ? 'active' : '' ?>">
                    <a href="index.php?p=thongtincanhan&act=bookmark_list">Tin đã lưu</a>
                </li>

                <li class="<?= ($act == 'tin_da_xem') ? 'active' : '' ?>">
                    <a href="index.php?p=thongtincanhan&act=tin_da_xem">Tin đã xem</a>
                </li>

                <li class="logout-item">
                    <a href="index.php?act=logout" onclick="return confirm('Đăng xuất?')">Thoát <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>

        <div class="support-box">
            Cần hỗ trợ, vui lòng liên hệ:<br>
            <a class="contact" href="mailto:bandoc@web24h.net">bandoc@web24h.net</a>
        </div>
    </div>

    <div class="profile-content">

        <?php
        switch ($act) {
            case 'bookmark_list':
                // Gọi file tin đã lưu vào đây
                include 'bookmark_list.php';
                break;

            case 'tin_da_xem':
                // Gọi file tin đã xem vào đây
                include 'tin_da_xem.php';
                break;

            case 'my_comments':
                include 'my_comments.php';
                break;

            case 'general':
            default:
                // HIỂN THỊ FORM THÔNG TIN CHUNG (Code cũ của bạn)
        ?>
                <div class="profile-content">
                    <h2 class="page-title">Thông tin tài khoản</h2>

                    <div class="info-section">
                        <div class="info-row avatar-row" id="view-avatar">
                            <div>
                                <span class="info-label">Ảnh đại diện</span>
                                <div>
                                    <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" class="avatar-sm">
                                </div>
                            </div>
                            <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('avatar')">Thay ảnh đại diện</a>
                        </div>

                        <div class="edit-container" id="edit-avatar">
                            <div class="mb-10">
                                <span class="info-label">Ảnh đại diện</span>
                                <span class="btn-close-edit" onclick="toggleEdit('avatar')">Đóng</span>
                            </div>
                            <form method="POST" enctype="multipart/form-data" class="avatar-edit-box">
                                <div>
                                    <img src="/Web_tintuc/images/avatars/<?= $avatar_img ?>" class="avatar-md">
                                </div>
                                <input type="file" name="avatar_file" id="file-upload" required class="form-control">
                                <button type="submit" name="btn_update_avatar" class="btn-save">Lưu thay đổi</button>
                            </form>
                        </div>
                    </div>
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
                            <div class="mb-10">
                                <span class="info-label">Họ tên</span>
                                <span class="btn-close-edit" onclick="toggleEdit('name')">Đóng</span>
                            </div>
                            <form method="POST" class="normal-edit-box">
                                <label class="form-label">Nhập họ tên</label>
                                <input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($user['hoten']) ?>" placeholder="Nhập họ và tên">
                                <button type="submit" name="btn_update_name" class="btn-save">Đổi tên</button>
                            </form>
                        </div>
                    </div>
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
                            <div class="mb-10">
                                <span class="info-label">Email</span>
                                <span class="btn-close-edit" onclick="toggleEdit('email')">Đóng</span>
                            </div>
                            <form method="POST" class="normal-edit-box">
                                <label class="form-label">Nhập email mới</label>
                                <input type="email" name="new_email" class="form-control" value="<?= $user['email'] ?>" placeholder="Nhập email mới" required>
                                <button type="submit" name="btn_update_email" class="btn-save">Đổi email</button>
                            </form>
                        </div>
                    </div>
                    <div class="info-section">
                        <div class="info-row <?= $show_pass_form ? 'hidden' : '' ?>" id="view-pass">
                            <div>
                                <span class="info-label">Mật khẩu</span>
                                <span class="info-value">•••••••••••••</span>
                            </div>
                            <a href="javascript:void(0)" class="btn-change" onclick="toggleEdit('pass')">Thay đổi</a>
                        </div>

                        <div class="edit-container <?= $show_pass_form ? 'd-block' : '' ?>" id="edit-pass">
                            <div class="mb-10">
                                <span class="info-label">Mật khẩu</span>
                                <span class="btn-close-edit" onclick="toggleEdit('pass')">Đóng</span>
                            </div>

                            <form method="POST" class="normal-edit-box">
                                <label class="form-label">Nhập mật khẩu hiện tại</label>
                                <div class="password-wrapper">
                                    <input type="password" id="old_pass" name="pass_old" class="form-control"
                                        value="<?= isset($_POST['pass_old']) ? htmlspecialchars($_POST['pass_old']) : '' ?>" required>
                                    <span class="toggle-text" onclick="togglePassword('old_pass', this)">Ẩn</span>
                                </div>

                                <?php if ($error_pass_old != ""): ?>
                                    <span class="error-msg"><?= $error_pass_old ?></span>
                                <?php endif; ?>

                                <label class="form-label">Nhập mật khẩu mới</label>
                                <div class="password-wrapper">
                                    <input type="password" id="new_pass" name="pass_new" class="form-control" required>
                                    <span class="toggle-text" onclick="togglePassword('new_pass', this)">Ẩn</span>
                                </div>

                                <div class="flex-between">
                                    <button type="submit" name="btn_update_pass" class="btn-save">Đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
                break; // Kết thúc case 'general'
        }
        ?>

    </div>
</div>

<?php if (!empty($error_pass_old)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var oldPassInput = document.getElementById('old_pass');
            if (oldPassInput) {
                oldPassInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                oldPassInput.focus();
                oldPassInput.select();
            }
        });
    </script>
<?php endif; ?>

<script>
    function toggleEdit(type) {
        var viewRow = document.getElementById('view-' + type);
        var editBox = document.getElementById('edit-' + type);

        // Kiểm tra xem editBox đang hiện hay ẩn (check qua class d-block hoặc style display)
        var isVisible = editBox.style.display === "block" || editBox.classList.contains('d-block');

        if (isVisible) {
            editBox.style.display = "none";
            editBox.classList.remove('d-block'); // Xóa class ép buộc hiển thị
            viewRow.classList.remove('hidden');
        } else {
            document.querySelectorAll('.edit-container').forEach(e => {
                e.style.display = 'none';
                e.classList.remove('d-block');
            });
            document.querySelectorAll('.info-row').forEach(e => e.classList.remove('hidden'));

            editBox.style.display = "block";
            viewRow.classList.add('hidden');
        }
    }

    function togglePassword(inputId, textObj) {
        var input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            textObj.innerText = "Ẩn";
        } else {
            input.type = "password";
            textObj.innerText = "Hiện";
        }
    }
</script>