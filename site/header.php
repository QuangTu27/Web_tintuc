<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách danh mục cho Menu
$sql_cat = "SELECT * FROM tbl_categories ORDER BY id ASC";
$res_cat = mysqli_query($conn, $sql_cat);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tin tức 24H</title>
    <link rel="stylesheet" href="/Web_tintuc/site/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <?php include 'auth_modal.php'; ?>

    <header>
        <div class="top-bar">
            <div class="container">
                <span><i class="far fa-clock"></i> <?= date('d/m/Y') ?></span>

                <div class="user-action">
                    <?php if (isset($_SESSION['user_login'])): ?>
                        <span>Xin chào, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b></span>
                        <span style="margin: 0 5px;">|</span>
                        <a href="index.php?act=logout" onclick="return confirm('Bạn muốn đăng xuất?')">Đăng xuất</a>
                    <?php else: ?>
                        <a href="javascript:void(0)" onclick="openAuthModal('login')">
                            <i class="fas fa-user"></i> Đăng nhập
                        </a>
                        <span style="margin: 0 5px;">|</span>
                        <a href="javascript:void(0)" onclick="openAuthModal('register')">
                            Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="container main-header">
            <a href="index.php" class="logo">TINTUC<span style="color:#333">24H</span></a>

            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <?php while ($cat = mysqli_fetch_assoc($res_cat)): ?>
                        <li>
                            <a href="index.php?p=danhmuc&id=<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </nav>
        </div>
    </header>