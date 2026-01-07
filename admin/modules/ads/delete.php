<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. XỬ LÝ XOÁ ADS ĐƠN LẺ
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Lấy tên file để xoá vật lý (Tránh rác server)
    $res = mysqli_query($conn, "SELECT media_file FROM tbl_ads WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/ads/" . $row['media_file'];
        if (!empty($row['media_file']) && file_exists($path)) {
            unlink($path);
        }
    }

    $sql = "DELETE FROM tbl_ads WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        // Chuyển hướng kèm theo tham số thông báo trên URL
        header('Location: index.php?mod=ads&act=list&msg=deleted');
        exit();
    } else {
        die("Lỗi hệ thống: " . mysqli_error($conn));
    }
}

// 2. XỬ LÝ XOÁ NHIỀU DÒNG (Checkboxes)
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $idList = implode(',', $ids);

    // Xoá hàng loạt file vật lý
    $res = mysqli_query($conn, "SELECT media_file FROM tbl_ads WHERE id IN ($idList)");
    while ($row = mysqli_fetch_assoc($res)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/ads/" . $row['media_file'];
        if (!empty($row['media_file']) && file_exists($path)) {
            unlink($path);
        }
    }

    $sql = "DELETE FROM tbl_ads WHERE id IN ($idList)";
    if (mysqli_query($conn, $sql)) {
        header('Location: index.php?mod=ads&act=list&msg=deleted_multiple');
        exit();
    }
}
