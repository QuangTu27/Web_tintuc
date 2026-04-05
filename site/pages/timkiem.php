<?php
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo "<div class='container' style='padding: 50px 15px;'><h2 style='text-align:center;'>Vui lòng nhập từ khoá tìm kiếm!</h2></div>";
    return;
}

$q_safe = mysqli_real_escape_string($conn, $q);
// Tuỳ thuộc vào trạng thái bài viết (ví dụ: da_dang) nếu có
$sql_search = "SELECT n.*, c.name as cat_name 
               FROM tbl_news n 
               LEFT JOIN tbl_categories c ON n.category_id = c.id 
               WHERE (n.tieude LIKE '%$q_safe%' OR n.tomtat LIKE '%$q_safe%') 
                 AND n.trangthai = 'da_dang'
               ORDER BY n.ngaydang DESC";
$res_search = mysqli_query($conn, $sql_search);
$total = mysqli_num_rows($res_search);
?>

<div class="container" style="padding: 30px 15px; min-height: 500px;">
    <h2><i class="fas fa-search"></i> Kết quả tìm kiếm cho: "<strong style="color:#007bff;"><?= htmlspecialchars($q) ?></strong>"</h2>
    <p style="color: #666; margin-bottom: 20px;">Tìm thấy <?= $total ?> bài viết phù hợp.</p>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php if ($total > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($res_search)): ?>
                <div style="display: flex; gap: 15px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                    <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" style="flex-shrink: 0;">
                        <img src="/Web_tintuc/images/news/<?= $row['hinhanh'] ?>" onerror="this.src='/Web_tintuc/images/default_news.jpg'" style="width: 200px; height: 130px; object-fit: cover; border-radius: 4px;">
                    </a>
                    <div>
                        <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" style="text-decoration: none; color: #333;">
                            <h3 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.4;"><?= htmlspecialchars($row['tieude']) ?></h3>
                        </a>
                        <p style="margin: 0 0 10px 0; color: #666; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($row['tomtat']) ?>
                        </p>
                        <div style="font-size: 12px; color: #999;">
                            <span style="margin-right: 15px; color: #d9534f; font-weight: bold;"><?= htmlspecialchars($row['cat_name']) ?></span>
                            <span><i class="far fa-clock"></i> <?= date('d/m/Y - H:i', strtotime($row['ngaydang'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="padding: 50px 0; text-align: center; color: #888;">
                <i class="fas fa-folder-open" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                <p>Không tìm thấy bài viết nào phù hợp.</p>
                <a href="index.php" style="color: #007bff; text-decoration: none;">← Quay lại Trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>
