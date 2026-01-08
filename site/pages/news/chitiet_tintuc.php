<?php
$id = $_GET['id'];
// Tăng view
mysqli_query($conn, "UPDATE tbl_news SET view_count = view_count + 1 WHERE id = '$id'");

$sql = "SELECT n.*, c.name as category_name FROM tbl_news n 
        JOIN tbl_categories c ON n.category_id = c.id 
        WHERE n.id = '$id' LIMIT 1";
$query = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($query);
?>

<div class="detail-container">
    <div class="breadcrumb">Trang chủ / <?php echo $row['category_name']; ?></div>
    
    <h1 class="main-title">
        <?php echo $row['tieude']; ?>
        <span class="bookmark-wrapper">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php?p=bookmark_add&news_id=<?php echo $row['id']; ?>" class="btn-bookmark" title="Lưu bài viết">
                    <i class="fas fa-bookmark"></i> Lưu tin
                </a>
            <?php else: ?>
                <a href="index.php?p=dangnhap" class="btn-bookmark guest" onclick="return confirm('Mày cần đăng nhập để lưu tin!')">
                    <i class="far fa-bookmark"></i> Đăng nhập để lưu
                </a>
            <?php endif; ?>
        </span>
    </h1>

    <div class="content-body">
        <p class="summary"><strong><?php echo $row['tomtat']; ?></strong></p>
        <div class="full-text">
            <?php echo $row['noidung']; ?>
        </div>
    </div>
</div>