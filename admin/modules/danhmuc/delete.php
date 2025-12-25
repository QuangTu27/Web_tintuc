<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Xử lý xoá dm đơn lẻ
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xoá dm khỏi CSDL
    $sql = "DELETE FROM tbl_categories WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        // Chuyển hướng về trang danh sách sau khi xoá
        header('Location: index.php?mod=danhmuc&act=list');
        exit();
    } else {
        echo "<p style='color:red'>Lỗi khi xoá danh mục: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:red'>ID danh mục không hợp lệ.</p>";
}

// --- XỬ LÝ XOÁ NHIỀU DÒNG---
if (isset($_POST['ids']) && is_array($_POST['ids'])) {

    $ids = array_map('intval', $_POST['ids']);
    $idList = implode(',', $ids);

    $sql = "DELETE FROM tbl_categories WHERE id IN ($idList)";
    mysqli_query($conn, $sql);

    header('Location: index.php?mod=danhmuc&act=list');
    exit();
}
