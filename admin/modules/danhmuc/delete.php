<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. KIỂM TRA QUYỀN (Chỉ Admin mới được xoá)
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    die('Bạn không có quyền thao tác danh mục');
}

// =============================================================
// XỬ LÝ XOÁ ĐƠN LẺ (Khi bấm nút Xoá ở từng dòng)
// =============================================================
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // BƯỚC 1: Xử lý các danh mục con (Nếu có)
    // Chuyển tất cả con của danh mục này thành danh mục gốc (parent_id = 0)
    // Để tránh việc xoá cha xong con bị mất tích trên menu
    $sql_promote_children = "UPDATE tbl_categories SET parent_id = 0 WHERE parent_id = $id";
    mysqli_query($conn, $sql_promote_children);

    // BƯỚC 2: Xoá danh mục
    $sql = "DELETE FROM tbl_categories WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        // Thêm msg=deleted để hiện thông báo bên list.php
        header('Location: index.php?mod=danhmuc&act=list&msg=deleted');
        exit();
    } else {
        echo "<script>alert('Lỗi khi xoá: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}

// =============================================================
// XỬ LÝ XOÁ NHIỀU DÒNG (Khi tick chọn nhiều rồi bấm nút Xoá)
// =============================================================
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    // Lọc dữ liệu cho an toàn
    $ids = array_map('intval', $_POST['ids']);
    $idList = implode(',', $ids);

    if (!empty($idList)) {
        // BƯỚC 1: Giải phóng các danh mục con của những danh mục sắp xoá
        $sql_promote_children = "UPDATE tbl_categories SET parent_id = 0 WHERE parent_id IN ($idList)";
        mysqli_query($conn, $sql_promote_children);

        // BƯỚC 2: Xoá danh sách đã chọn
        $sql = "DELETE FROM tbl_categories WHERE id IN ($idList)";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?mod=danhmuc&act=list&msg=deleted_multiple');
            exit();
        }
    }
}

// Nếu không vào trường hợp nào
header('Location: index.php?mod=danhmuc&act=list');
exit();
