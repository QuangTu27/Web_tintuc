<?php
// Kiểm tra đăng nhập để bảo mật
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Đăng nhập đi mới xem được tin đã lưu mày ơi!'); window.location.href='index.php?p=dangnhap';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn JOIN 2 bảng để lấy thông tin bài viết từ ID đã lưu
$sql_list = "SELECT n.*, b.id AS b_id 
             FROM tbl_bookmarks b
             JOIN tbl_news n ON b.news_id = n.id
             WHERE b.user_id = '$user_id'
             ORDER BY b.id DESC";
$query_list = mysqli_query($conn, $sql_list);
?>

<div class="container" style="padding-top: 30px; min-height: 600px;">
    <h2 class="section-title"><i class="fas fa-heart"></i> DANH SÁCH TIN ĐÃ LƯU</h2>

    <div class="bookmark-container" style="margin-top: 20px; background: #fff; padding: 20px; border-radius: 8px;">
        <?php
        if (mysqli_num_rows($query_list) > 0) {
            while ($row = mysqli_fetch_array($query_list)) {
        ?>
                <div class="bookmark-item" style="display: flex; gap: 20px; padding: 15px 0; border-bottom: 1px solid #eee;">
                    <div class="item-thumb" style="flex: 1;">
                        <img src="images/<?php echo $row['hinhanh']; ?>" style="width: 100%; border-radius: 5px; height: 120px; object-fit: cover;">
                    </div>

                    <div class="item-content" style="flex: 3;">
                        <a href="index.php?p=chitiet_tintuc&id=<?php echo $row['id']; ?>" style="text-decoration: none;">
                            <h3 style="color: #333; margin-bottom: 10px;"><?php echo $row['tieude']; ?></h3>
                        </a>
                        <p style="color: #666; font-size: 14px;"><?php echo substr($row['tomtat'], 0, 150); ?>...</p>

                        <div style="margin-top: 10px;">
                            <a href="index.php?p=bookmark_delete&id=<?php echo $row['id']; ?>"
                                onclick="return confirm('Bạn chắc chắn muốn bỏ lưu bài này chứ?')"
                                style="color: #e74c3c; font-size: 13px; text-decoration: none;">
                                <i class="fas fa-trash-alt"></i> Bỏ lưu bài này
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<div style='text-align:center; padding: 40px;'><p>Mày chưa lưu bài nào cả. Qua trang chủ xem tin đi!</p></div>";
        }
        ?>
    </div>
</div>