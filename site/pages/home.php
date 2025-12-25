<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');
// 1. Lấy tin mới nhất
$sql_new = "SELECT n.*, c.name as cat_name 
            FROM tbl_news n 
            JOIN tbl_categories c ON n.category_id = c.id 
            WHERE n.trangthai = 'da_duyet' 
            ORDER BY n.ngaydang DESC LIMIT 6";
$res_new = mysqli_query($conn, $sql_new);

//2. Lấy Quảng cáo Sidebar
$sql_ads = "SELECT * FROM tbl_ads WHERE position = 'sidebar_right' AND status = 'hien'";
$res_ads = mysqli_query($conn, $sql_ads);
?>

<div class="container main-wrapper">

    <div class="content-area">
        <h2 class="widget-title">TIN MỚI NHẤT</h2>

        <div class="news-grid">
            <?php while ($row = mysqli_fetch_assoc($res_new)): ?>
                <div class="news-card">
                    <div class="news-thumb">
                        <a href="index.php?p=chitiettintuc&id=<?= $row['id'] ?>">
                            <img src="/Web_tintuc/admin/uploads/<?= !empty($row['hinhanh']) ? $row['hinhanh'] : 'no-image.jpg' ?>" alt="">
                        </a>
                    </div>
                    <div class="news-info">
                        <span class="news-cat"><?= $row['cat_name'] ?></span>
                        <h3 class="news-title">
                            <a href="index.php?p=chitiettintuc&id=<?= $row['id'] ?>">
                                <?= mb_strimwidth($row['tieude'], 0, 50, "...") ?>
                            </a>
                        </h3>
                        <div class="news-meta">
                            <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($row['ngaydang'])) ?></span>
                            <span><i class="far fa-eye"></i> <?= $row['luotxem'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <aside class="sidebar-area">

        <div class="widget">
            <h3 class="widget-title">QUẢNG CÁO</h3>
            <?php while ($ad = mysqli_fetch_assoc($res_ads)): ?>
                <a href="<?= $ad['link_lien_ket'] ?>" target="_blank">
                    <img src="/Web_tintuc/admin/images/ads/<?= $ad['image'] ?>" style="width:100%; margin-bottom:15px; border-radius:4px;">
                </a>
            <?php endwhile; ?>
        </div>

        <div class="widget">
            <h3 class="widget-title">TIN XEM NHIỀU</h3>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><a href="#">Đội tuyển Việt Nam thắng lớn...</a></li>
                <li style="margin-top:10px"><a href="#">Giá xăng dầu hôm nay giảm mạnh...</a></li>
            </ul>
        </div>

    </aside>
</div>