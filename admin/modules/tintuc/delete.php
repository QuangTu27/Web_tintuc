<?php
include '../../../connect.php'; // Đường dẫn ra file connect của mày

$id = $_GET['id'];

// Lấy tên ảnh để xóa file
$res = mysqli_query($conn, "SELECT hinhanh FROM tbl_news WHERE id = '$id'");
$row = mysqli_fetch_array($res);

if($row['hinhanh'] != '' && file_exists('../../../images/'.$row['hinhanh'])){
    unlink('../../../images/'.$row['hinhanh']);
}

// Xóa trong database
mysqli_query($conn, "DELETE FROM tbl_news WHERE id = '$id'");

header('Location: ../../index.php?mod=tintuc&act=list');
?>