<?php
session_start();
// Đảm bảo đường dẫn include chính xác
$path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
include_once $path_connect;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $uid = $_SESSION['user_id'];
    $comment_id = intval($_POST['comment_id']);

    if ($action === 'edit' && isset($_POST['noidung'])) {
        $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
        
        // Kiểm tra quyền
        $check = mysqli_query($conn, "SELECT id FROM tbl_comments WHERE id = $comment_id AND user_id = $uid AND status = 1");
        if (mysqli_num_rows($check) > 0) {
            $sql = "UPDATE tbl_comments SET noidung = '$noidung' WHERE id = $comment_id";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success', 'noidung' => nl2br(htmlspecialchars($noidung))]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống khi cập nhật.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền hoặc bình luận không tồn tại!']);
        }
    } 
    elseif ($action === 'delete') {
        // Kiểm tra quyền
        $check = mysqli_query($conn, "SELECT id FROM tbl_comments WHERE id = $comment_id AND user_id = $uid");
        if (mysqli_num_rows($check) > 0) {
            // Cập nhật status = 2 để đánh dấu là đã bị xoá ẩn (giữ nguyên parent-child)
            $sql = "UPDATE tbl_comments SET status = 2 WHERE id = $comment_id";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống khi xoá.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xoá bình luận này!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ!']);
}
