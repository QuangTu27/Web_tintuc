<?php
session_start();
// Sử dụng đường dẫn này để luôn tìm thấy file connect cho dù file like nằm ở đâu
$path = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
if (file_exists($path)) {
    include_once $path;
} else {
    die(json_encode(['status' => 'error', 'message' => 'Không tìm thấy file connect.php']));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']));
}

$uid = $_SESSION['user_id'];
$nid = intval($_POST['news_id']);

// Kiểm tra và thực hiện Like/Unlike
$check = mysqli_query($conn, "SELECT id FROM tbl_likes WHERE user_id=$uid AND news_id=$nid");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "DELETE FROM tbl_likes WHERE user_id=$uid AND news_id=$nid");
    $action = 'unliked';
} else {
    mysqli_query($conn, "INSERT INTO tbl_likes (user_id, news_id) VALUES ($uid, $nid)");
    $action = 'liked';
}

$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_likes WHERE news_id=$nid");
$count = mysqli_fetch_assoc($res)['total'];

echo json_encode(['status' => 'success', 'action' => $action, 'new_count' => $count]);
exit;