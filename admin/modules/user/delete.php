<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$current_admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;

// Xử lý xoá user đơn lẻ
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xoá user khỏi CSDL
    $sql = "DELETE FROM tbl_users WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        if ($id == $current_admin_id) {
            session_destroy(); // Hủy phiên đăng nhập
            echo "<script>
                alert('Bạn đã xóa tài khoản của chính mình. Vui lòng đăng nhập lại!');
                window.location.href = 'login.php';
            </script>";
            exit();
        }

        // Nếu xoá người khác -> Quay về danh sách
        header('Location: index.php?mod=user&act=list&msg=deleted');
        exit();
    } else {
        echo "<p style='color:red'>Lỗi khi xoá user: " . mysqli_error($conn) . "</p>";
    }
}

// --- XỬ LÝ XOÁ NHIỀU DÒNG---
if (isset($_POST['ids']) && is_array($_POST['ids'])) {

    $ids = array_map('intval', $_POST['ids']);
    $idList = implode(',', $ids);

    $sql = "DELETE FROM tbl_users WHERE id IN ($idList)";
    if (mysqli_query($conn, $sql)) {
        //in_array kiểm tra xem ID hiện tại có nằm trong mảng bị xóa không
        if (in_array($current_admin_id, $ids)) {
            session_destroy();
            echo "<script>
                alert('Trong danh sách xóa có tài khoản của bạn. Hệ thống sẽ đăng xuất!');
                window.location.href = 'login.php';
            </script>";
            exit();
        }

        // Nếu xoá người khác -> Quay về danh sách
        header('Location: index.php?mod=user&act=list&msg=deleted_multiple');
        exit();
    } else {
        echo "<script>alert('Lỗi xoá nhiều: " . mysqli_error($conn) . "');</script>";
    }
}
