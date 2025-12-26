<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

/* =================================================
   TRUY VẤN ADS
   ================================================= */
// Top Banner
$sql_ads_top = "SELECT * FROM tbl_ads WHERE position='top_home' AND status='hien'";
$res_ads_top = mysqli_query($conn, $sql_ads_top);

// Sidebar Banner (Dọc)
$sql_ads_sidebar = "SELECT * FROM tbl_ads WHERE position='sidebar_right' AND status='hien'";
$res_ads_sidebar = mysqli_query($conn, $sql_ads_sidebar);

// Inline Banner (Giữa nội dung)
$sql_ads_inline = "SELECT * FROM tbl_ads WHERE position='inline_home' AND status='hien' LIMIT 1";
$res_ads_inline = mysqli_query($conn, $sql_ads_inline);
$ads_inline = mysqli_fetch_assoc($res_ads_inline);

// Footer Banner
$sql_ads_footer = "SELECT * FROM tbl_ads WHERE position='footer_home' AND status='hien'";
$res_ads_footer = mysqli_query($conn, $sql_ads_footer);
?>

<?php if (mysqli_num_rows($res_ads_top) > 0): ?>
    <div class="home-top-ads container">
        <?php while ($ad = mysqli_fetch_assoc($res_ads_top)): ?>
            <a href="<?php echo $ad['link_lien_ket']; ?>" target="_blank">
                <img src="/Web_tintuc/images/ads/<?php echo $ad['image']; ?>" alt="Quảng cáo đầu trang">
            </a>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<div class="container main-wrapper">

    <div class="content-area">

        <h2 class="widget-title">TIN MỚI NHẤT</h2>

        <div class="news-grid">
            <div style="height: 1000px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;">
                Khu vực hiển thị danh sách tin tức (Cao 1000px để test scroll sidebar)
            </div>


            <?php if ($ads_inline): ?>
                <div class="ads-inline">
                    <a href="<?php echo $ads_inline['link_lien_ket']; ?>" target="_blank">
                        <img src="/Web_tintuc/images/ads/<?php echo $ads_inline['image']; ?>" alt="Quảng cáo giữa bài">
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <aside class="sidebar-area">

        <?php if (mysqli_num_rows($res_ads_sidebar) > 0): ?>
            <div class="widget-ads">
                <?php while ($ad = mysqli_fetch_assoc($res_ads_sidebar)): ?>
                    <a href="<?php echo $ad['link']; ?>" target="_blank" class="ads-item">
                        <img src="/Web_tintuc/images/ads/<?php echo $ad['image']; ?>" alt="Quảng cáo sidebar">
                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="widget">
            <h3 class="widget-title">TIN XEM NHIỀU</h3>
        </div>

    </aside>

</div>

<?php if (mysqli_num_rows($res_ads_footer) > 0): ?>
    <div class="home-footer-ads container">
        <?php while ($ad = mysqli_fetch_assoc($res_ads_footer)): ?>
            <a href="<?php echo $ad['link_lien_ket']; ?>" target="_blank">
                <img src="/Web_tintuc/images/ads/<?php echo $ad['image']; ?>" alt="Quảng cáo cuối trang">
            </a>
        <?php endwhile; ?>
    </div>
<?php endif; ?>