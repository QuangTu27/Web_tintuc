<?php
session_start();
// Đảm bảo đường dẫn include chính xác
$path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
include_once $path_connect;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
    exit;
}

if (isset($_POST['noidung']) && isset($_POST['news_id'])) {
    $uid = $_SESSION['user_id'];
    $nid = intval($_POST['news_id']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    
    // Lấy tên từ Session 'user_name' (khớp với file Login của bạn)
    $ten = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Thành viên';

    // Thực hiện chèn vào Database
    $sql = "INSERT INTO tbl_comments (news_id, user_id, ten_nguoi_binh, noidung, status) 
            VALUES ($nid, $uid, '$ten', '$noidung', 1)";

    if (mysqli_query($conn, $sql)) {
        $thoigian = date('d/m/Y H:i');
        // Trả về HTML để hiển thị ngay lập tức
        $html = "<div style='margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f1f1;'>
                    <strong style='color: #28a745;'>" . htmlspecialchars($ten) . "</strong>
                    <small style='color: #bbb; margin-left: 10px;'>" . $thoigian . "</small>
                    <p style='margin: 8px 0; color: #333;'>" . nl2br(htmlspecialchars($noidung)) . "</p>
                </div>";
        echo json_encode(['status' => 'success', 'html' => $html]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi SQL: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
}