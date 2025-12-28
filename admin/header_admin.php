<?php
$roleMap = [
    'admin'      => 'Admin',
    'editor'     => 'Biên tập viên',
    'nhabao'     => 'Nhà báo',
    'phongvien'  => 'Phóng viên',
    'ctv'        => 'CTV'
];

$roleText = $roleMap[$_SESSION['admin_role']] ?? 'Admin';

$avatar = !empty($_SESSION['admin_avatar'])
    ? '/Web_tintuc/images/avatars/' . $_SESSION['admin_avatar']
    : '/Web_tintuc/images/avatars/default_avatar.svg';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị Tin Tức</title>
    <link rel="stylesheet" href="/Web_tintuc/admin/css/style_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.user-dropdown');
        if (!dropdown.contains(e.target)) {
            document.getElementById('userMenu').style.display = 'none';
        }
    });
</script>

<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="brand">
                <h2>ADMIN PANEL</h2>
            </div>
            <ul class="menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Trang chủ</a></li>

                <li><a href="index.php?mod=tintuc&act=list"><i class="fas fa-newspaper"></i> Quản lý Tin tức</a></li>
                <li><a href="index.php?mod=danhmuc&act=list"><i class="fas fa-list"></i> Quản lý Danh mục</a></li>

                <li><a href="index.php?mod=binhluan&act=list"><i class="fas fa-comments"></i> Quản lý Bình luận</a></li>

                <li><a href="index.php?mod=ads&act=list"><i class="fas fa-ad"></i> Quản lý Quảng cáo</a></li>
                <li><a href="index.php?mod=user&act=list"><i class="fas fa-users"></i> Quản lý Tài khoản</a></li>
            </ul>
            <!-- Phần thông tin user và đăng xuất -->
            <div class="user-dropdown">
                <div class="user-info" onclick="toggleUserMenu()">
                    <img src="<?php echo $avatar; ?>" alt="Avatar">

                    <div class="user-text">
                        <span class="user-name">
                            <?php echo $_SESSION['admin_name']; ?>
                        </span>
                        <small class="user-role">
                            <?php echo $roleText; ?>
                        </small>
                    </div>

                    <i class="fas fa-caret-up"></i>
                </div>

                <ul class="user-menu" id="userMenu">
                    <li>
                        <a href="index.php?mod=user&act=profile">
                            <i class="fas fa-user"></i> Thông tin cá nhân
                        </a>
                    </li>
                    <li>
                        <a href="index.php?act=logout"
                            onclick="return confirm('Bạn có chắc muốn đăng xuất?');">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main-content">
            <header class="top-bar">
                <span>Xin chào, <b><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></b></span>
            </header>

            <div class="content-body">