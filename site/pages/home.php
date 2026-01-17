<?php
if (!isset($conn)) include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$sql_ads_top     = "SELECT * FROM tbl_ads WHERE position='top_home' AND status='hien'";
$res_ads_top     = mysqli_query($conn, $sql_ads_top);

$sql_ads_sidebar = "SELECT * FROM tbl_ads WHERE position='sidebar_right' AND status='hien'";
$res_ads_sidebar = mysqli_query($conn, $sql_ads_sidebar);

$sql_ads_inline  = "SELECT * FROM tbl_ads WHERE position='inline_home' AND status='hien'";
$res_ads_inline  = mysqli_query($conn, $sql_ads_inline);

$sql_ads_footer  = "SELECT * FROM tbl_ads WHERE position='footer_home' AND status='hien'";
$res_ads_footer  = mysqli_query($conn, $sql_ads_footer);



$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// 1. Đếm tổng số bài viết để tính số trang
$sql_total = "SELECT COUNT(id) as total FROM tbl_news WHERE trangthai='da_dang'";
$res_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($res_total);
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $limit);

// 2. Lấy tin tức có giới hạn 
$sql_news = "SELECT * FROM tbl_news 
             WHERE trangthai='da_dang' 
             ORDER BY ngaydang DESC 
             LIMIT $start, $limit";
$res_news = mysqli_query($conn, $sql_news);

// Tin xem nhiều Sidebar
$sql_top_views = "SELECT * FROM tbl_news WHERE trangthai='da_dang' ORDER BY view_count DESC LIMIT 5";
$res_top_views = mysqli_query($conn, $sql_top_views);

function renderAdsMedia($ad)
{
    $filePath = "images/ads/" . $ad['media_file'];
    if ($ad['media_type'] === 'video') {
        return '
        <video autoplay muted loop playsinline class="ads-video" style="width:100%">
            <source src="' . $filePath . '" type="video/mp4">
        </video>';
    } else {
        return '<img src="' . $filePath . '" alt="' . htmlspecialchars($ad['title']) . '" style="width:100%">';
    }
}
?>

<?php if (mysqli_num_rows($res_ads_top) > 0): ?>
    <div class="home-top-ads container">
        <div class="ads-slider" data-speed="5000">
            <?php $i = 0;
            while ($ad = mysqli_fetch_assoc($res_ads_top)): ?>
                <div class="ads-item <?= ($i++ == 0) ? 'active' : '' ?>">
                    <a href="<?= $ad['link'] ?>" target="_blank">
                        <?= renderAdsMedia($ad) ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<div class="container main-wrapper" style="display: flex; gap: 30px; margin: 20px auto; max-width: 1200px; align-items: flex-start;">

    <div class="content-area" style="flex: 2;">
        <h2 style="border-left: 5px solid #00b686; padding-left: 15px; margin-bottom: 30px; font-weight: bold; font-size: 24px;">TIN MỚI NHẤT</h2>

        <div class="news-list">
            <?php if ($res_news && mysqli_num_rows($res_news) > 0): ?>
                <?php $countNews = 0; ?>
                <?php while ($news = mysqli_fetch_assoc($res_news)): $countNews++; ?>

                    <div class="news-item" style="display: flex; gap: 20px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                        <div class="news-thumb-frame" style="flex: 0 0 240px; height: 160px; overflow: hidden; border-radius: 8px;">
                            <a href="index.php?p=chitiet_tintuc&id=<?= $news['id'] ?>">
                                <img src="images/news/<?= $news['hinhanh'] ?>"
                                    onerror="this.src='images/default_news.jpg'"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </a>
                        </div>
                        <div class="news-info" style="flex: 1;">
                            <h3 style="margin: 0 0 10px 0;">
                                <a href="index.php?p=chitiet_tintuc&id=<?= $news['id'] ?>" style="text-decoration: none; color: #222; font-weight: bold; font-size: 19px; line-height: 1.3; display: block;">
                                    <?= htmlspecialchars($news['tieude']) ?>
                                </a>
                            </h3>
                            <p style="color: #666; font-size: 14.5px; line-height: 1.5; margin-bottom: 10px;">
                                <?php
                                $desc = !empty($news['tomtat']) ? $news['tomtat'] : $news['noidung'];
                                echo mb_substr(strip_tags($desc), 0, 160, 'UTF-8') . '...';
                                ?>
                            </p>
                            <div style="font-size: 12px; color: #999;">
                                <span>📅 <?= date('d/m/Y', strtotime($news['ngaydang'])) ?></span>
                                <span style="margin-left: 15px;">👁️ <?= number_format($news['view_count']) ?> lượt xem</span>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Quảng cáo xen kẽ sau tin thứ 5
                    if ($countNews == 5 && mysqli_num_rows($res_ads_inline) > 0): ?>
                        <div class="ads-inline" style="margin: 20px 0;">
                            <div class="ads-slider" data-speed="6000">
                                <?php mysqli_data_seek($res_ads_inline, 0);
                                $j = 0;
                                while ($ad = mysqli_fetch_assoc($res_ads_inline)): ?>
                                    <div class="ads-item <?= ($j++ == 0) ? 'active' : '' ?>">
                                        <a href="<?= $ad['link'] ?>" target="_blank">
                                            <?= renderAdsMedia($ad) ?>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                        <?php if ($page > 1): ?>
                            <a href="index.php?page=<?= $page - 1 ?>" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;">« Trước</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span style="padding: 8px 16px; background: #00b686; color: #fff; border-radius: 4px; font-weight: bold;"><?= $i ?></span>
                            <?php else: ?>
                                <a href="index.php?page=<?= $i ?>" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="index.php?page=<?= $page + 1 ?>" style="padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px;">Sau »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Hiện tại chưa có bài viết nào.</p>
            <?php endif; ?>
        </div>
    </div>

    <aside style="flex: 0 0 300px; max-width: 300px; position: sticky; top: 10px;">
        <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="background: #333; color: #fff; padding: 12px; margin: 0; font-size: 16px; text-transform: uppercase;">🔥 Tin xem nhiều</h3>
            <div style="padding: 15px; background: #fff;">
                <?php while ($top = mysqli_fetch_assoc($res_top_views)): ?>
                    <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                        <img src="images/news/<?= $top['hinhanh'] ?>" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; flex-shrink: 0;" onerror="this.src='images/default_news.jpg'">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $top['id'] ?>" style="font-size: 13px; text-decoration: none; color: #333; font-weight: 500; line-height: 1.4;">
                            <?= mb_substr($top['tieude'], 0, 60, 'UTF-8') ?>...
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if (mysqli_num_rows($res_ads_sidebar) > 0): ?>
            <div class="widget-ads">
                <div class="ads-slider" data-speed="9000">
                    <?php $k = 0;
                    while ($ad = mysqli_fetch_assoc($res_ads_sidebar)): ?>
                        <div class="ads-item <?= ($k++ == 0) ? 'active' : '' ?>">
                            <a href="<?= $ad['link'] ?>" target="_blank">
                                <?= renderAdsMedia($ad) ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</div>

<?php if (mysqli_num_rows($res_ads_footer) > 0): ?>
    <div class="home-footer-ads container">
        <div class="ads-slider" data-speed="7000">
            <?php $m = 0;
            while ($ad = mysqli_fetch_assoc($res_ads_footer)): ?>
                <div class="ads-item <?= ($m++ == 0) ? 'active' : '' ?>">
                    <a href="<?= $ad['link'] ?>" target="_blank">
                        <?= renderAdsMedia($ad) ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sliders = document.querySelectorAll('.ads-slider');
        sliders.forEach(slider => {
            const items = slider.querySelectorAll('.ads-item');
            if (items.length <= 1) return;
            let currentIndex = 0;
            const speed = parseInt(slider.getAttribute('data-speed')) || 5000;

            setInterval(() => {
                items[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % items.length;
                items[currentIndex].classList.add('active');

                const video = items[currentIndex].querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play();
                }
            }, speed);
        });
    });
</script>