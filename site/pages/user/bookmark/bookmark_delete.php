<?php
// Kiểm tra quyền sở hữu
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?p=dangnhap');
    exit();
}

$user_id = $_SESSION['user_id'];
$news_id = $_GET['id']; // ID bài viết cần xóa

if (isset($news_id)) {
    // Chỉ xóa bài của chính user đó để tránh xóa nhầm của người khác
    $sql_del = "DELETE FROM tbl_bookmarks WHERE user_id = '$user_id' AND news_id = '$news_id'";
    
    if (mysqli_query($conn, $sql_del)) {
        echo "<script>alert('Đã bỏ lưu tin thành công!'); window.location.href='index.php?p=bookmark_list';</script>";
    } else {
        echo "<script>alert('Lỗi rồi mày ơi, thử lại xem!'); window.history.back();</script>";
    }
} else {
    header('Location: index.php?p=bookmark_list');
}
?>