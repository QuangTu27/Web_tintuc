<?php
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert-box'>⚠️ Vui lòng đăng nhập để xem tin đã xem.</div>";
    return;
}

$uid = $_SESSION['user_id'];
$cookie_name = 'viewed_news_' . $uid;

$viewed_ids = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];

if (isset($_GET['del_viewed_news'])) {
    $del_id = $_GET['del_viewed_news'];
    if (($key = array_search($del_id, $viewed_ids)) !== false) {
        unset($viewed_ids[$key]);
        $viewed_ids = array_values($viewed_ids);
        setcookie($cookie_name, json_encode($viewed_ids), time() + (86400 * 30), "/");
        echo "<script>alert('Đã xóa thành công!'); window.location.href='index.php?p=thongtincanhan&act=tin_da_xem';</script>";
        exit;
    }
}

if (isset($_GET['del_all_viewed'])) {
    setcookie($cookie_name, "", time() - 3600, "/");
    echo "<script>alert('Đã xóa toàn bộ lịch sử xem tin!'); window.location.href='index.php?p=thongtincanhan&act=tin_da_xem';</script>";
    exit;
}

$data_news = [];

if (!empty($viewed_ids)) {
    $list_id = implode(',', array_map('intval', $viewed_ids));

    $sql = "SELECT n.*, c.name AS cat_name 
            FROM tbl_news n
            LEFT JOIN tbl_categories c ON n.category_id = c.id
            WHERE n.id IN ($list_id) 
            ORDER BY FIELD(n.id, $list_id) DESC";

    $query = mysqli_query($conn, $sql);
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data_news[] = $row;
        }
    }
}
?>
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="border-left: 4px solid #00b686; padding-left: 10px; margin: 0; color: #333;">
            LỊCH SỬ XEM TIN
        </h3>
        
        <?php if (!empty($data_news)): ?>
            <a href="index.php?p=thongtincanhan&act=tin_da_xem&del_all_viewed=1" 
                onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn xóa tất cả lịch sử xem tin?')) window.location.href=this.href;"
                style="color: white; background: #dc3545; padding: 6px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500; transition: 0.2s; box-shadow: 0 2px 4px rgba(220,53,69,0.2);"
                onmouseover="this.style.background='#c82333';" 
                onmouseout="this.style.background='#dc3545';">
                <i class="fas fa-trash-alt"></i> Xóa tất cả
            </a>
        <?php endif; ?>
    </div>

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
                            <p style="font-size: 12px; color: #888; margin: 0; padding-top: 10px;">
                                📅 <?= date('d/m/Y', strtotime($row['ngaydang'])) ?>
                            </p>
                        </div>
                    </a>

                    <a href="index.php?p=thongtincanhan&act=tin_da_xem&del_viewed_news=<?= $row['id'] ?>"
                        onclick="if(!confirm('Bạn muốn xóa lịch sử xem bài viết này?')) return false;"
                        style="display:block; text-align:center; background:#fff5f5; color:#dc3545; padding:10px; font-size:13px; font-weight:600; text-decoration:none; border-top:1px solid #eee; transition: 0.2s;"
                        onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" 
                        onmouseout="this.style.background='#fff5f5'; this.style.color='#dc3545';">
                        <i class="fas fa-trash-alt"></i> Xóa
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