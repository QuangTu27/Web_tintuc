<?php
$id = $_GET['id'];
// Xóa ảnh vật lý
$res = mysqli_query($conn, "SELECT hinhanh FROM tbl_news WHERE id=$id");
$row = mysqli_fetch_array($res);
unlink("../images/news/".$row['hinhanh']);

// Xóa trong database
mysqli_query($conn, "DELETE FROM tbl_news WHERE id=$id");
header("location: index.php?mod=tintuc&act=list");
?>