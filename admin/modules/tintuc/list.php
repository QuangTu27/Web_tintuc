<?php
// Truy vấn lấy danh sách tin và tên danh mục tương ứng
$sql_list = "SELECT n.*, c.name AS category_name 
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             ORDER BY n.id DESC";
$query_list = mysqli_query($conn, $sql_list);
?>

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><i class="fas fa-newspaper"></i> Danh sách Tin tức</h2>
    <a href="index.php?mod=tintuc&act=add" class="btn-add" style="background: #28a745; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
        <i class="fas fa-plus"></i> Thêm bài viết mới
    </a>
</div>

<table class="admin-table" width="100%" border="1" style="border-collapse: collapse; background: white;">
    <thead>
        <tr style="background: #f4f4f4;">
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tiêu đề & Thông tin</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_array($query_list)){ ?>
        <tr>
            <td align="center"><?php echo $row['id']; ?></td>
            <td align="center">
                <img src="../images/<?php echo $row['hinhanh']; ?>" width="100" style="border-radius: 5px; object-fit: cover;">
            </td>
            <td style="padding: 10px;">
                <strong><?php echo $row['tieude']; ?></strong> <br>
                <small style="color: #666;">
                    <i class="fas fa-eye"></i> <?php echo $row['view_count']; ?> lượt xem | 
                    <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($row['ngaydang'])); ?>
                </small>
            </td>
            <td align="center"><?php echo $row['category_name']; ?></td>
            <td align="center">
                <?php
                $status_labels = [
                    'ban_nhap' => '<span style="color: gray;">Bản nháp</span>',
                    'cho_duyet' => '<span style="color: orange;">Chờ duyệt</span>',
                    'da_dang' => '<span style="color: green;">Đã đăng</span>',
                    'bi_tu_choi' => '<span style="color: red;">Bị từ chối</span>'
                ];
                echo $status_labels[$row['trangthai']] ?? $row['trangthai'];
                ?>
            </td>
            <td align="center">
                <a href="index.php?mod=tintuc&act=edit&id=<?php echo $row['id']; ?>" title="Sửa" style="color: #007bff; margin-right: 10px;"><i class="fas fa-edit"></i></a>
                <a href="modules/tintuc/delete.php?id=<?php echo $row['id']; ?>" title="Xóa" style="color: red;" onclick="return confirm('Mày có chắc chắn muốn xóa bài viết này không?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>