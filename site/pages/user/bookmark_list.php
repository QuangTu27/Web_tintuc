<?php
// BẮT BUỘC ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container' style='padding:50px 0; text-align:center;'>
            <h3>⚠️ Bạn cần đăng nhập để xem tin đã lưu</h3>
            <a href='javascript:void(0)' onclick='openAuthModal(\"login\")' style='color:#007bff; font-weight:bold;'>Đăng nhập ngay</a>
          </div>";
    return;
}

$user_id = $_SESSION['user_id'];

// JOIN bảng news và bookmarks
$sql = "SELECT n.*, b.ngay_luu 
        FROM tbl_bookmarks b
        JOIN tbl_news n ON b.news_id = n.id
        WHERE b.user_id = $user_id
        ORDER BY b.ngay_luu DESC";
$query = mysqli_query($conn, $sql);
?>
<div>
    <h3 style="border-left: 4px solid #00b686; padding-left: 10px; margin-bottom: 20px; color: #333;">
        TIN ĐÃ LƯU CỦA BẠN
    </h3>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="news-grid-system">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="card-item">
                    <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                        <div class="img-wrap">
                            <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'">
                        </div>
                        <div class="card-body">
                            <h4><?= htmlspecialchars($row['tieude']) ?></h4>
                            <p style="font-size: 12px; color: #888; margin: 0;">
                                📅 <?= date('d/m/Y', strtotime($row['ngay_luu'])) ?>
                            </p>
                        </div>
                    </a>

                    <a href="index.php?p=bookmark_add&news_id=<?= $row['id'] ?>"
                        onclick="return confirm('Bạn muốn bỏ lưu tin này?')"
                        style="display:block; text-align:center; background:#fff5f5; color:#dc3545; padding:10px; font-size:13px; font-weight:600; text-decoration:none; border-top:1px solid #eee; transition: 0.2s;">
                        <i class="fas fa-trash-alt"></i> Bỏ lưu
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px; border: 1px dashed #ced4da;">
            <i class="far fa-bookmark" style="font-size: 48px; color: #adb5bd; margin-bottom: 20px; display: block;"></i>
            <p style="color: #6c757d; font-size: 16px; margin-bottom: 20px;">Bạn chưa lưu tin tức nào vào bộ sưu tập.</p>
            <a href="index.php" style="display:inline-block; padding:10px 25px; background:#007bff; color:white; border-radius:30px; text-decoration:none; font-weight: 500; box-shadow: 0 4px 6px rgba(0,123,255,0.2);">
                Khám phá tin mới
            </a>
        </div>
    <?php endif; ?>
</div>