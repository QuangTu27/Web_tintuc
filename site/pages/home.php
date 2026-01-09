<?php
// Kết nối CSDL
include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

/* =================================================
   1. TRUY VẤN DỮ LIỆU (Dùng logic chuẩn của file ADS cũ)
   ================================================= */
// Lấy Quảng cáo theo vị trí (Sửa lỗi 'vitri' thành 'position')
$sql_ads_top     = "SELECT * FROM tbl_ads WHERE position='top_home' AND status='hien'";
$res_ads_top     = mysqli_query($conn, $sql_ads_top);

$sql_ads_sidebar = "SELECT * FROM tbl_ads WHERE position='sidebar_right' AND status='hien'";
$res_ads_sidebar = mysqli_query($conn, $sql_ads_sidebar);

$sql_ads_inline  = "SELECT * FROM tbl_ads WHERE position='inline_home' AND status='hien'";
$res_ads_inline  = mysqli_query($conn, $sql_ads_inline);

$sql_ads_footer  = "SELECT * FROM tbl_ads WHERE position='footer_home' AND status='hien'";
$res_ads_footer  = mysqli_query($conn, $sql_ads_footer);

// Lấy Tin tức (Giữ nguyên logic file hiện tại)
$sql_news = "SELECT * FROM tbl_news WHERE trangthai='da_dang' ORDER BY ngaydang DESC LIMIT 10";
$res_news = mysqli_query($conn, $sql_news);

$sql_top_views = "SELECT * FROM tbl_news WHERE trangthai='da_dang' ORDER BY view_count DESC LIMIT 5";
$res_top_views = mysqli_query($conn, $sql_top_views);

/**
 * Hàm hỗ trợ hiển thị Media (Ảnh hoặc Video)
 */
function renderAdsMedia($ad)
{
    $filePath = "images/ads/" . $ad['media_file']; // Đường dẫn tương đối từ thư mục gốc

    // Kiểm tra nếu là Video
    if ($ad['media_type'] === 'video') {
        return '
        <video autoplay muted loop playsinline class="ads-video">
            <source src="' . $filePath . '" type="video/mp4">
        </video>';
    } else {
        // Nếu là Ảnh
        return '<img src="' . $filePath . '" alt="' . htmlspecialchars($ad['title']) . '">';
    }
}
?>

<style>

</style>

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

                    <?php if ($countNews == 5 && mysqli_num_rows($res_ads_inline) > 0): ?>
                        <div class="ads-inline">
                            <div class="ads-slider" data-speed="6000">
                                <?php
                                mysqli_data_seek($res_ads_inline, 0); // Reset con trỏ
                                $j = 0;
                                while ($ad = mysqli_fetch_assoc($res_ads_inline)):
                                ?>
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
            <?php else: ?>
                <p>Đang cập nhật tin tức...</p>
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
                            <?= mb_substr($top['tieude'], 0, 50, 'UTF-8') ?>...
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
            if (items.length <= 1) return; // Nếu chỉ có 1 quảng cáo thì không cần chạy slide

            let currentIndex = 0;
            // Lấy tốc độ từ data-speed hoặc mặc định 5s
            const speed = parseInt(slider.getAttribute('data-speed')) || 5000;

            setInterval(() => {
                items[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % items.length;
                items[currentIndex].classList.add('active');

                // Nếu slide mới là video, tự động phát lại từ đầu
                const video = items[currentIndex].querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play();
                }
            }, speed);
        });
    });
</script>