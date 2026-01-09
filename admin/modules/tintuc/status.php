<?php
session_start();
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. KIỂM TRA QUYỀN (Chỉ Admin/Editor mới được duyệt)
if (!isset($_SESSION['admin_role']) || ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'editor')) {
    echo "<script>alert('Bạn không có quyền duyệt bài!'); window.history.back();</script>";
    header("Location: ../../index.php?mod=tintuc&act=list");
    exit();
}

// 2. LẤY ID VÀ HÀNH ĐỘNG
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    // Xác định trạng thái mới
    $newStatus = '';
    $msg = '';

    if ($action === 'approve') {
        $newStatus = 'da_dang';
        $msg = 'approved'; // Bài đã duyệt
    } elseif ($action === 'reject') {
        $newStatus = 'bi_tu_choi';
        $msg = 'rejected'; // Bài bị từ chối
    } elseif ($action === 'hide') {
        $newStatus = 'ban_nhap'; // Gỡ bài về nháp
        $msg = 'hidden';
    }

    if ($newStatus != '') {
        // Cập nhật CSDL
        $sql = "UPDATE tbl_news SET trangthai = '$newStatus', ngaydang = NOW() WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            header("Location: ../../index.php?mod=tintuc&act=list&msg=$msg");
            exit();
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: ../../index.php?mod=tintuc&act=list");
}
