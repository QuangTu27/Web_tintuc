<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

/* =================================================
   TRUY VẤN ADS - Không dùng LIMIT để lấy hết phục vụ Slideshow
   ================================================= */
$sql_ads_top     = "SELECT * FROM tbl_ads WHERE position='top_home' AND status='hien'";
$res_ads_top     = mysqli_query($conn, $sql_ads_top);

$sql_ads_sidebar = "SELECT * FROM tbl_ads WHERE position='sidebar_right' AND status='hien'";
$res_ads_sidebar = mysqli_query($conn, $sql_ads_sidebar);

$sql_ads_inline  = "SELECT * FROM tbl_ads WHERE position='inline_home' AND status='hien'";
$res_ads_inline  = mysqli_query($conn, $sql_ads_inline);

$sql_ads_footer  = "SELECT * FROM tbl_ads WHERE position='footer_home' AND status='hien'";
$res_ads_footer  = mysqli_query($conn, $sql_ads_footer);

/**
 * Hàm hỗ trợ hiển thị Media
 */
function renderAdsMedia($ad)
{
    $filePath = "/Web_tintuc/images/ads/" . $ad['media_file'];
    if ($ad['media_type'] === 'video') {
        return '
            <video autoplay muted loop playsinline class="ads-video">
                <source src="' . $filePath . '" type="video/mp4">
            </video>';
    } else {
        // Không cần inline style nữa, để CSS xử lý
        return '<img src="' . $filePath . '" alt="' . htmlspecialchars($ad['title']) . '">';
    }
}
?>

<?php if (mysqli_num_rows($res_ads_top) > 0): ?>
    <div class="home-top-ads container">
        <div class="ads-slider" data-speed="5000">
            <?php $i = 0;
            while ($ad = mysqli_fetch_assoc($res_ads_top)): ?>
                <div class="ads-item <?= ($i++ == 0) ? 'active' : '' ?>">
                    <a href="<?= $ad['link'] ?>" target="_blank"><?= renderAdsMedia($ad) ?></a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<div class="container main-wrapper">
    <div class="content-area">
        <h2 class="widget-title">TIN MỚI NHẤT</h2>
        <div class="news-grid">
            <div style="height: 500px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 20px;">
                Khu vực hiển thị danh sách tin tức
            </div>

            <?php if (mysqli_num_rows($res_ads_inline) > 0): ?>
                <div class="ads-inline">
                    <div class="ads-slider" data-speed="4000">
                        <?php $i = 0;
                        while ($ad = mysqli_fetch_assoc($res_ads_inline)): ?>
                            <div class="ads-item <?= ($i++ == 0) ? 'active' : '' ?>">
                                <a href="<?= $ad['link'] ?>" target="_blank"><?= renderAdsMedia($ad) ?></a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <aside class="sidebar-area">
        <div class="widget">
            <h3 class="widget-title">TIN XEM NHIỀU</h3>
        </div>

        <?php if (mysqli_num_rows($res_ads_sidebar) > 0): ?>
            <div class="widget-ads">
                <div class="ads-slider" data-speed="6000">
                    <?php $i = 0;
                    while ($ad = mysqli_fetch_assoc($res_ads_sidebar)): ?>
                        <div class="ads-item <?= ($i++ == 0) ? 'active' : '' ?>">
                            <a href="<?= $ad['link'] ?>" target="_blank"><?= renderAdsMedia($ad) ?></a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>

    </aside>
</div>

<?php if (mysqli_num_rows($res_ads_footer) > 0): ?>
    <div class="home-footer-ads container">
        <div class="ads-slider" data-speed="5000">
            <?php $i = 0;
            while ($ad = mysqli_fetch_assoc($res_ads_footer)): ?>
                <div class="ads-item <?= ($i++ == 0) ? 'active' : '' ?>">
                    <a href="<?= $ad['link'] ?>" target="_blank"><?= renderAdsMedia($ad) ?></a>
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

                // Nếu slide mới là video, phát lại từ đầu
                const video = items[currentIndex].querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play();
                }
            }, speed);
        });
    });
</script>