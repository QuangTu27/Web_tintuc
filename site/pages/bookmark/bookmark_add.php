<?php
// Không cần session_start nếu file index đã gọi, nhưng thêm cho chắc nếu gọi ajax
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.history.back();</script>";
    exit();
}

// Kết nối DB nếu chưa có
if (!isset($conn)) include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$user_id = $_SESSION['user_id'];
$news_id = isset($_GET['news_id']) ? intval($_GET['news_id']) : 0;

if ($news_id > 0) {
    // Kiểm tra đã lưu chưa
    $check = mysqli_query($conn, "SELECT id FROM tbl_bookmarks WHERE user_id=$user_id AND news_id=$news_id");

    if (mysqli_num_rows($check) > 0) {
        // Đã lưu -> Xóa
        mysqli_query($conn, "DELETE FROM tbl_bookmarks WHERE user_id=$user_id AND news_id=$news_id");
        echo "<script>alert('Đã bỏ lưu tin này!'); window.history.back();</script>";
    } else {
        // Chưa lưu -> Thêm (Có cột ngay_luu)
        // Sử dụng NOW() của MySQL để lấy thời gian hiện tại
        $sql_add = "INSERT INTO tbl_bookmarks (user_id, news_id, ngay_luu) VALUES ($user_id, $news_id, NOW())";
        mysqli_query($conn, $sql_add);
        echo "<script>alert('Đã lưu tin thành công!'); window.history.back();</script>";
    }
} else {
    echo "<script>window.history.back();</script>";
}
