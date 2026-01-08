<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * Hàm xử lý xóa một bài viết cụ thể
 * Trả về: true (thành công), false (lỗi), 'no_permission' (không có quyền)
 */
function deleteNewsItem($conn, $id)
{
    $id = (int)$id;

    // 1. Lấy thông tin bài viết để kiểm tra quyền và lấy tên ảnh
    $sql_check = "SELECT * FROM tbl_news WHERE id = $id";
    $res = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($res) == 0) {
        return false; // Bài viết không tồn tại
    }

    $row = mysqli_fetch_assoc($res);

    // 2. KIỂM TRA QUYỀN SỞ HỮU
    // - Admin/Editor: Quyền sinh sát (Xóa được hết)
    // - Tác giả: Chỉ xóa được bài của mình
    $isAdminOrEditor = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'editor');
    $isAuthor = ($row['author_id'] == $_SESSION['admin_id']);

    if (!$isAdminOrEditor && !$isAuthor) {
        return 'no_permission';
    }

    // 3. XÓA ẢNH KHỎI SERVER (Nếu có)
    // Đường dẫn tương đối từ file này (admin/modules/tintuc) ra thư mục ảnh
    $img_path = '../../images/news/' . $row['hinhanh'];

    if ($row['hinhanh'] != '' && file_exists($img_path)) {
        unlink($img_path);
    }

    // 4. XÓA DỮ LIỆU TRONG DATABASE
    $sql_delete = "DELETE FROM tbl_news WHERE id = $id";
    if (mysqli_query($conn, $sql_delete)) {
        return true;
    }
    return false;
}

// =================================================================
// XỬ LÝ KHI NHẬN YÊU CẦU XÓA
// =================================================================

// Trường hợp 1: Xóa 1 bài (Bấm nút Xóa ở dòng)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = deleteNewsItem($conn, $id);

    if ($status === 'no_permission') {
        echo "<script>alert('Bạn không có quyền xóa bài viết của người khác!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    } elseif ($status === true) {
        header('Location: index.php?mod=tintuc&act=list&msg=deleted');
    } else {
        echo "<script>alert('Lỗi khi xóa bài viết!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    }
}

// Trường hợp 2: Xóa nhiều bài (Chọn checkbox rồi bấm nút Xóa hết)
elseif (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $countSuccess = 0;
    foreach ($_POST['ids'] as $id) {
        $status = deleteNewsItem($conn, $id);
        if ($status === true) {
            $countSuccess++;
        }
    }
    header('Location: index.php?mod=tintuc&act=list&msg=deleted_multi');
} else {
    header('Location: index.php?mod=tintuc&act=list');
}
