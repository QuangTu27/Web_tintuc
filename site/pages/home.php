<?php
// Kết nối CSDL - Giữ nguyên đường dẫn của mày
include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

/* =================================================
   1. TRUY VẤN DỮ LIỆU
   ================================================= */
$sql_ads_top     = "SELECT * FROM tbl_ads WHERE vitri='banner_top' AND trangthai='hien'";
$res_ads_top     = mysqli_query($conn, $sql_ads_top);

$sql_ads_sidebar = "SELECT * FROM tbl_ads WHERE vitri='sidebar_right' AND trangthai='hien'";
$res_ads_sidebar = mysqli_query($conn, $sql_ads_sidebar);

$sql_news = "SELECT * FROM tbl_news ORDER BY ngaydang DESC LIMIT 10";
$res_news = mysqli_query($conn, $sql_news);

$sql_top_views = "SELECT * FROM tbl_news ORDER BY view_count DESC LIMIT 5";
$res_top_views = mysqli_query($conn, $sql_top_views);
?>

<style>
    /* Style giữ nguyên như mày mong muốn */
    .news-thumb-frame {
        flex: 0 0 240px;
        width: 240px;
        height: 150px;
        background: #f0f0f0;
        border-radius: 6px;
        overflow: hidden;
    }
    .news-thumb-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s;
    }
    .news-item:hover img { transform: scale(1.05); }
    .news-item { display: flex; gap: 20px; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    .news-info h3 a:hover { color: #007bff !important; }
</style>

<?php if ($res_ads_top && mysqli_num_rows($res_ads_top) > 0): ?>
    <div class="container" style="margin: 10px auto; max-width: 1200px;">
        <?php while ($ad = mysqli_fetch_assoc($res_ads_top)): ?>
            <a href="<?= $ad['link_lien_ket'] ?>" target="_blank">
                <img src="images/ads/<?= $ad['hinh_anh'] ?>" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px;">
            </a>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<div class="container main-wrapper" style="display: flex; gap: 30px; margin: 20px auto; max-width: 1200px; align-items: flex-start;">
    
    <div class="content-area" style="flex: 2;">
        <h2 style="border-left: 5px solid #007bff; padding-left: 15px; margin-bottom: 30px; font-weight: bold; font-size: 24px;">TIN MỚI NHẤT</h2>
        
        <div class="news-list">
            <?php if ($res_news && mysqli_num_rows($res_news) > 0): ?>
                <?php while ($news = mysqli_fetch_assoc($res_news)): ?>
                    <div class="news-item">
                        <div class="news-thumb-frame">
                            <a href="index.php?p=chitiet_tintuc&id=<?= $news['id'] ?>">
                                <img src="images/news/<?= $news['hinhanh'] ?>" 
                                     onerror="this.src='images/default_news.jpg'">
                            </a>
                        </div>
                        <div class="news-info">
                            <h3 style="margin: 0 0 10px 0;">
                                <a href="index.php?p=chitiet_tintuc&id=<?= $news['id'] ?>" style="text-decoration: none; color: #222; font-weight: bold; font-size: 19px; line-height: 1.3; display: block;">
                                    <?= htmlspecialchars($news['tieude']) ?>
                                </a>
                            </h3>
                            <p style="color: #666; font-size: 14.5px; line-height: 1.5; margin-bottom: 10px;">
                                <?php 
                                    $desc = !empty($news['tomtat']) ? $news['tomtat'] : $news['noidung'];
                                    echo mb_substr(strip_tags($desc), 0, 130, 'UTF-8') . '...';
                                ?>
                            </p>
                            <div style="font-size: 12px; color: #999;">
                                <span>📅 <?= date('d/m/Y', strtotime($news['ngaydang'])) ?></span>
                                <span style="margin-left: 15px;">👁️ <?= number_format($news['view_count']) ?> lượt xem</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Đang cập nhật tin tức...</p>
            <?php endif; ?>
        </div>
    </div>

    <aside style="flex: 1; position: sticky; top: 10px;">
        <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="background: #333; color: #fff; padding: 12px; margin: 0; font-size: 16px; text-transform: uppercase;">🔥 Tin xem nhiều</h3>
            <div style="padding: 15px; background: #fff;">
                <?php while ($top = mysqli_fetch_assoc($res_top_views)): ?>
                    <div style="display: flex; gap: 12px; margin-bottom: 15px; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                        <img src="images/news/<?= $top['hinhanh'] ?>" style="width: 80px; height: 55px; object-fit: cover; border-radius: 4px;" onerror="this.src='images/default_news.jpg'">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $top['id'] ?>" style="font-size: 13.5px; text-decoration: none; color: #333; font-weight: 500; line-height: 1.4;">
                            <?= mb_substr($top['tieude'], 0, 50, 'UTF-8') ?>...
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if ($res_ads_sidebar && mysqli_num_rows($res_ads_sidebar) > 0): ?>
            <?php while ($ad = mysqli_fetch_assoc($res_ads_sidebar)): ?>
                <a href="<?= $ad['link_lien_ket'] ?>" target="_blank" style="display: block; margin-bottom: 15px;">
                    <img src="images/ads/<?= $ad['hinh_anh'] ?>" style="width: 100%; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
    </aside>
</div>