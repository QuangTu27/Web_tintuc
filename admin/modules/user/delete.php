<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy ID user từ URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xoá user khỏi CSDL
    $sql = "DELETE FROM tbl_users WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        // Chuyển hướng về trang danh sách sau khi xoá
        header('Location: index.php?mod=user&act=list');
        exit();
    } else {
        echo "<p style='color:red'>Lỗi khi xoá user: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:red'>ID user không hợp lệ.</p>";
}
