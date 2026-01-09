<?php
// BẮT BUỘC ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert-box'>⚠️ Vui lòng đăng nhập để xem lịch sử.</div>";
    return;
}

$uid = $_SESSION['user_id'];
$cookie_name = 'viewed_news_' . $uid;

$viewed_ids = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];
$data_news = [];

if (!empty($viewed_ids)) {
    $list_id = implode(',', array_map('intval', $viewed_ids));

    // JOIN thêm bảng categories để lấy tên danh mục (c.name)
    $sql = "SELECT n.*, c.name AS cat_name 
            FROM tbl_news n
            LEFT JOIN tbl_categories c ON n.category_id = c.id
            WHERE n.id IN ($list_id) 
            ORDER BY FIELD(n.id, $list_id)";

    $query = mysqli_query($conn, $sql);
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data_news[] = $row;
        }
    }
}
?>

<style>
    .news-list-vertical {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .news-item-row {
        display: flex;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        transition: 0.2s;
        text-decoration: none;
        color: #333;
    }

    .news-item-row:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #00b686;
    }

    .row-thumb {
        width: 220px;
        height: 140px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .row-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s;
    }

    .news-item-row:hover .row-thumb img {
        transform: scale(1.05);
    }

    .row-body {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .row-body h4 {
        margin: 0 0 8px;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .row-meta {
        font-size: 13px;
        color: #888;
    }
</style>

<div style="margin-top: 10px; margin-bottom: 50px;">
    <h3 style="border-left: 4px solid #00b686; padding-left: 10px; margin-bottom: 20px; color: #333;">
        LỊCH SỬ XEM TIN
    </h3>

    <?php if (!empty($data_news)): ?>
        <div class="news-list-vertical">
            <?php foreach ($data_news as $row): ?>

                <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" class="news-item-row">
                    <div class="row-thumb">
                        <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'">
                    </div>

                    <div class="row-body">
                        <h4><?= htmlspecialchars($row['tieude']) ?></h4>

                        <div class="row-meta">
                            <span class="cat-badge">
                                <?= htmlspecialchars($row['cat_name'] ?? 'Tin tức') ?>
                            </span>

                            <span style="float: right;">📅 Xem: <?= date('d/m/Y', strtotime($row['ngaydang'])) ?></span>
                        </div>
                    </div>
                </a>

            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">
            <p style="color: #666;">Chưa có lịch sử xem tin.</p>
        </div>
    <?php endif; ?>
</div>