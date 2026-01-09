<?php
session_start(); // Quan trọng: Khởi động session

// Kết nối CSDL (Dùng đường dẫn tuyệt đối cho an toàn)
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit();
}

/**
 * Hàm xử lý xóa một bài viết
 */
function deleteNewsItem($conn, $id)
{
    $id = (int)$id;

    // 1. Lấy thông tin bài viết
    $sql_check = "SELECT * FROM tbl_news WHERE id = $id";
    $res = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($res) == 0) {
        return false; // Bài viết không tồn tại
    }

    $row = mysqli_fetch_assoc($res);

    // 2. KIỂM TRA QUYỀN (Admin/Editor xóa tất, Tác giả chỉ xóa bài mình)
    $isAdminOrEditor = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'editor');
    $isAuthor = ($row['author_id'] == $_SESSION['admin_id']);

    if (!$isAdminOrEditor && !$isAuthor) {
        return 'no_permission';
    }

    // 3. XÓA ẢNH TRÊN SERVER (Dùng đường dẫn tuyệt đối)
    if ($row['hinhanh'] != '') {
        $img_path = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/images/news/' . $row['hinhanh'];

        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }

    // 4. XÓA DỮ LIỆU (Có thể cần xóa bình luận liên quan trước nếu chưa set cascade)
    mysqli_query($conn, "DELETE FROM tbl_comments WHERE news_id = $id"); // Mở dòng này nếu có bảng comment

    $sql_delete = "DELETE FROM tbl_news WHERE id = $id";
    if (mysqli_query($conn, $sql_delete)) {
        return true;
    }
    return false;
}

// =================================================================
// XỬ LÝ YÊU CẦU
// =================================================================

// 1. Xóa 1 bài (GET)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = deleteNewsItem($conn, $id);

    if ($status === 'no_permission') {
        echo "<script>alert('Bạn không có quyền xóa bài viết này!'); window.location.href='../../index.php?mod=tintuc&act=list';</script>";
    } elseif ($status === true) {
        header('Location: ../../index.php?mod=tintuc&act=list&msg=deleted');
    } else {
        echo "<script>alert('Lỗi SQL khi xóa!'); window.location.href='../../index.php?mod=tintuc&act=list';</script>";
    }
}

// 2. Xóa nhiều bài (POST Checkbox)
elseif (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $countSuccess = 0;
    foreach ($_POST['ids'] as $id) {
        $status = deleteNewsItem($conn, $id);
        if ($status === true) {
            $countSuccess++;
        }
    }
    header('Location: ../../index.php?mod=tintuc&act=list&msg=deleted');
} else {
    // Không có dữ liệu thì quay về
    header('Location: ../../index.php?mod=tintuc&act=list');
}
