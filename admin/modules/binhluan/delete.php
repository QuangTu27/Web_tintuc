<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Xóa comment con
    mysqli_query($conn, "DELETE FROM tbl_comments WHERE parent_id = $id");

    // Xóa comment chính
    mysqli_query($conn, "DELETE FROM tbl_comments WHERE id = $id");
}

header("Location: index.php");
exit;
