<?php
// 1. KẾT NỐI & TỐI ƯU TRUY VẤN
if (!isset($conn)) {
    $path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
    if (file_exists($path_connect)) include_once $path_connect;
}

// 2. CẤU HÌNH PHÂN TRANG (Giảm limit xuống để load nhanh hơn)
$limit = 10; 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// 3. TỐI ƯU SQL: Chỉ lấy các cột cần thiết thay vì SELECT *
// Điều này giúp giảm tải bộ nhớ RAM và băng thông database
$sql_fields = "n.id, n.tieude, n.tomtat, n.hinhanh, n.ngaydang, n.view_count, c.name as cat_name";

// Lấy tổng số tin (Sử dụng COUNT(id) trên cột có index)
$res_count = mysqli_query($conn, "SELECT COUNT(id) as total FROM tbl_news WHERE trangthai='da_dang'");
$total_records = mysqli_fetch_assoc($res_count)['total'];
$total_pages = ceil($total_records / $limit);

// Truy vấn lấy tin: Thêm INDEX nếu có thể trong Database cho cột 'trangthai' và 'ngaydang'
$sql_news = "SELECT $sql_fields 
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             WHERE n.trangthai='da_dang' 
             ORDER BY n.ngaydang DESC 
             LIMIT $start, $limit";
$query_news = mysqli_query($conn, $sql_news);
?>

<div class="container" style="max-width: 1100px; margin: 20px auto; padding: 0 15px;">
    <h2 style="font-weight: 800; margin-bottom: 20px;">TIN MỚI NHẤT</h2>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php while ($row = mysqli_fetch_assoc($query_news)): ?>
            <article style="display: flex; gap: 20px; background: #fff; padding: 15px; border-radius: 8px; border-bottom: 1px solid #eee;">
                <div style="flex: 0 0 240px; height: 150px;">
                    <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>">
                        <img src="images/news/<?= $row['hinhanh'] ?>" 
                             loading="lazy" 
                             onerror="this.src='images/default_news.jpg'" 
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                    </a>
                </div>
                
                <div style="flex: 1;">
                    <small style="color: #28a745; font-weight: bold;"><?= htmlspecialchars($row['cat_name']) ?></small>
                    <h3 style="margin: 5px 0 10px 0; font-size: 20px;">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" style="text-decoration: none; color: #222;">
                            <?= htmlspecialchars($row['tieude']) ?>
                        </a>
                    </h3>
                    <p style="color: #666; font-size: 14px; line-height: 1.5;">
                        <?= mb_substr(strip_tags($row['tomtat']), 0, 160, 'UTF-8') ?>...
                    </p>
                    <div style="margin-top: 10px; font-size: 12px; color: #999;">
                        <span>📅 <?= date('d/m/Y', strtotime($row['ngaydang'])) ?></span>
                        <span style="margin-left: 15px;">👁️ <?= number_format($row['view_count']) ?> lượt xem</span>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div style="text-align: center; margin: 30px 0;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=<?= $i ?>" 
                   style="display: inline-block; padding: 8px 16px; margin: 0 4px; border: 1px solid #ddd; text-decoration: none; color: <?= ($i==$page)?'#fff':'#333' ?>; background: <?= ($i==$page)?'#28a745':'#fff' ?>;">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>