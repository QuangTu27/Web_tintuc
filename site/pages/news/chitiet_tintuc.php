<?php

/**
 * FILE: site/pages/news/chitiet_tintuc.php
 */

// 1. Đảm bảo kết nối DB luôn chạy (Fix lỗi trắng trang do sai Scope)
if (!isset($conn)) {
    $path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
    if (file_exists($path_connect)) {
        include_once $path_connect;
    }
}

// 2. Lấy ID và ép kiểu
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$row = null;

if ($id > 0 && isset($conn)) {
    // Tăng lượt xem
    mysqli_query($conn, "UPDATE tbl_news SET view_count = view_count + 1 WHERE id = $id");

    // Truy vấn lấy chi tiết + tên danh mục Con + tên danh mục Cha
    // c1: Danh mục con (gắn trực tiếp với bài viết)
    // c2: Danh mục cha (của c1)
    $sql = "SELECT n.*, 
                   c1.name as cat_name, c1.id as cat_id,
                   c2.name as parent_name, c2.id as parent_id
            FROM tbl_news n 
            LEFT JOIN tbl_categories c1 ON n.category_id = c1.id 
            LEFT JOIN tbl_categories c2 ON c1.parent_id = c2.id 
            WHERE n.id = $id LIMIT 1";

    $query = mysqli_query($conn, $sql);
    if ($query) {
        $row = mysqli_fetch_array($query);
    }
}

// 3. Nếu ID không tồn tại trong Database
if (!$row) {
    echo "<div style='padding:100px 20px; text-align:center; background:#f9f9f9;'>
            <h2 style='color:#d9534f;'>⚠️ Bài viết không tồn tại!</h2>
            <p>Có thể bài viết đã bị xóa hoặc đường dẫn không chính xác.</p>
            <a href='index.php' style='color:#007bff; text-decoration:none; font-weight:bold;'>← Quay lại trang chủ</a>
          </div>";
    return;
}

// --- LOGIC LƯU TIN ĐÃ XEM (CHỈ KHI ĐÃ ĐĂNG NHẬP) ---
if ($id > 0 && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $cookie_name = 'viewed_news_' . $uid; // Cookie riêng cho từng user

    // 1. Lấy danh sách ID từ cookie
    $viewed_news = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];

    // 2. Thêm ID mới vào đầu mảng (Xóa cũ nếu trùng)
    if (($key = array_search($id, $viewed_news)) !== false) {
        unset($viewed_news[$key]);
    }
    array_unshift($viewed_news, $id);

    // 3. Giới hạn 12 tin
    $viewed_news = array_slice($viewed_news, 0, 12);

    // 4. Lưu Cookie (30 ngày)
    setcookie($cookie_name, json_encode($viewed_news), time() + (86400 * 30), "/");
}
?>

<div class="container" style="max-width: 900px; margin: 30px auto; padding: 0 15px;">
    <article class="news-detail" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

        <nav style="font-size: 14px; color: #888; margin-bottom: 20px;">
            <?php if (!empty($row['parent_name'])): ?>
                <a href="index.php?p=danhmuc&id=<?= $row['parent_id'] ?>" style="color: #333; text-decoration: none;">
                    <?= htmlspecialchars($row['parent_name']) ?>
                </a>
                <span style="margin: 0 5px;">></span>
            <?php endif; ?>

            <a href="index.php?p=danhmuc&id=<?= $row['cat_id'] ?>" style="color: #333; text-decoration: none; font-weight: 600;">
                <?= htmlspecialchars($row['cat_name'] ?? 'Tin tức') ?>
            </a>
        </nav>

        <h1 style="font-size: 36px; line-height: 1.3; color: #222; margin-bottom: 20px; font-weight: 800;">
            <?= htmlspecialchars($row['tieude']) ?>
        </h1>

        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
            <div style="color: #999; font-size: 13px;">
                <span style="margin-right: 15px;">📅 <?= date('d/m/Y - H:i', strtotime($row['ngaydang'])) ?></span>
                <span>👁️ <?= number_format($row['view_count']) ?> lượt xem</span>
            </div>

            <div>
                <?php if (isset($_SESSION['user_id'])):
                    // KIỂM TRA TRẠNG THÁI LƯU
                    $uid = $_SESSION['user_id'];
                    $nid = $row['id'];
                    $check_save = mysqli_query($conn, "SELECT id FROM tbl_bookmarks WHERE user_id=$uid AND news_id=$nid");
                    $is_saved = (mysqli_num_rows($check_save) > 0);
                ?>

                    <?php if ($is_saved): ?>
                        <a href="index.php?p=bookmark_add&news_id=<?= $nid ?>"
                            onclick="return confirm('Bạn muốn bỏ lưu bài viết này?')"
                            style="background: #e9ecef; border: 1px solid #ced4da; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #495057; font-size: 13px; font-weight: bold;">
                            <i class="fas fa-check" style="color: #28a745;"></i> Đã lưu
                        </a>
                    <?php else: ?>
                        <a href="index.php?p=bookmark_add&news_id=<?= $nid ?>"
                            style="background: #ffc107; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #000; font-size: 13px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <i class="far fa-bookmark"></i> Lưu tin
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <a href="index.php?p=dangnhap" onclick="return confirm('Bạn cần đăng nhập để sử dụng tính năng lưu tin!')"
                        style="background: #f8f9fa; border: 1px solid #ddd; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px;">
                        <i class="far fa-bookmark"></i> Lưu tin
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($row['tomtat'])): ?>
            <div class="sapo" style="font-size: 20px; font-weight: 700; line-height: 1.6; color: #444; margin-bottom: 30px; border-left: 5px solid #28a745; padding-left: 20px;">
                <?= nl2br(htmlspecialchars($row['tomtat'])) ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 30px;">
            <img src="images/news/<?= $row['hinhanh'] ?>"
                onerror="this.src='images/default_news.jpg'"
                style="max-width: 100%; height: auto; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>

        <div class="main-content">
            <?= $row['noidung'] ?>
        </div>

        <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #333;">
            <p style="font-weight: bold; color: #000;">Nguồn: TINTUC24H</p>
            <a href="javascript:history.back()" style="display: inline-block; margin-top: 10px; color: #007bff; text-decoration: none;">← Quay lại trang trước</a>
        </div>

    </article>
</div>