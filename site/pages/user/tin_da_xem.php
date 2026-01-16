<?php
// 1. KIỂM TRA ĐĂNG NHẬP (Giao diện giống bookmark)
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container' style='padding:50px 0; text-align:center;'>
            <h3>⚠️ Bạn cần đăng nhập để xem lịch sử xem tin</h3>
            <a href='javascript:void(0)' onclick='openAuthModal(\"login\")' style='color:#007bff; font-weight:bold; cursor:pointer;'>Đăng nhập ngay</a>
          </div>";
    return;
}

$uid = $_SESSION['user_id'];
$cookie_name = 'viewed_news_' . $uid;

// 2. XỬ LÝ DỮ LIỆU
$viewed_ids = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];
$data_news = [];

if (!empty($viewed_ids)) {
    $list_id = implode(',', array_map('intval', $viewed_ids));

    // Query lấy tin tức
    $sql = "SELECT n.*, c.name AS cat_name 
            FROM tbl_news n
            LEFT JOIN tbl_categories c ON n.category_id = c.id
            WHERE n.id IN ($list_id) 
            ORDER BY FIELD(n.id, $list_id) DESC"; // DESC để tin mới xem lên đầu

    $query = mysqli_query($conn, $sql);
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data_news[] = $row;
        }
    }
}
?>
<div>
    <h3 style="border-left: 4px solid #00b686; padding-left: 10px; margin-bottom: 20px; color: #333;">
        LỊCH SỬ XEM TIN
    </h3>

    <?php if (!empty($data_news)): ?>
        <div class="news-grid-system">
            <?php foreach ($data_news as $row): ?>

                <div class="card-item">
                    <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">

                        <div class="img-wrap">
                            <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'">
                            <span style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($row['cat_name'] ?? 'Tin tức') ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h4><?= htmlspecialchars($row['tieude']) ?></h4>

                            <div style="margin-top: auto; padding-top: 10px; border-top: 1px dashed #eee; display: flex; justify-content: space-between; align-items: center; color: #888; font-size: 12px;">
                                <span>📅 <?= date('d/m/Y', strtotime($row['ngaydang'])) ?></span>
                            </div>
                        </div>
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px; border: 1px dashed #ced4da;">
            <i class="fas fa-history" style="font-size: 48px; color: #adb5bd; margin-bottom: 20px; display: block;"></i>
            <p style="color: #6c757d; font-size: 16px; margin-bottom: 20px;">Bạn chưa có lịch sử xem tin nào.</p>
            <a href="index.php" style="display:inline-block; padding:10px 25px; background:#007bff; color:white; border-radius:30px; text-decoration:none; font-weight: 500; box-shadow: 0 4px 6px rgba(0,123,255,0.2);">
                Đọc báo ngay
            </a>
        </div>
    <?php endif; ?>
</div>