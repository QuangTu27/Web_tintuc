<?php
if (!isset($conn)) {
    die("Biến kết nối database không tồn tại!");
}
$sql = "SELECT * FROM tbl_news ORDER BY id DESC";
$query = mysqli_query($conn, $sql);
if (!$query) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
?>
<div style="display:flex; justify-content:space-between; margin-bottom:20px;">
    <h3>📰 Danh sách bài viết</h3>
    <a href="index.php?mod=tintuc&act=add" style="background:green; color:white; padding:8px 15px; text-decoration:none; border-radius:5px;">+ Thêm bài mới</a>
</div>

<table border="1" style="width:100%; border-collapse:collapse; background:white;">
    <tr style="background:#eee;">
        <th>ID</th>
        <th>Hình ảnh</th>
        <th>Tiêu đề</th>
        <th>Tóm tắt</th>
        <th>Ngày đăng</th>
        <th>Quản lý</th>
    </tr>
    <?php while ($row = mysqli_fetch_array($query)) { ?>
        <tr align="center">
            <td><?php echo $row['id']; ?></td>
            <td><img src="../images/news/<?php echo $row['hinhanh']; ?>" width="80" onerror="this.src='https://via.placeholder.com/80x50'"></td>
            <td align="left" style="padding:10px;"><?php echo $row['tieude']; ?></td>
            <td align="left" style="padding:10px; font-size:13px;"><?php echo $row['tomtat']; ?></td>
            <td><?php echo $row['ngaydang']; ?></td>
            <td>
                <a href="index.php?mod=tintuc&act=edit&id=<?php echo $row['id']; ?>">Sửa</a> |
                <a href="index.php?mod=tintuc&act=delete&id=<?php echo $row['id']; ?>
            " onclick="return confirm('Xóa bài này?')">Xóa</a>
            </td>
        </tr>
    <?php } ?>
</table>