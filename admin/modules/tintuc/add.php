<?php
if(isset($_POST['themtintuc'])){
    $tieude = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $danhmuc = $_POST['danhmuc'];
    $trangthai = $_POST['trangthai'];
    $author_id = $_SESSION['admin_id']; // Lấy từ phiên đăng nhập

    // Xử lý ảnh
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
    $hinhanh_time = time().'_'.$hinhanh; // Tránh trùng tên ảnh

    $sql_add = "INSERT INTO tbl_news(tieude, tomtat, noidung, category_id, trangthai, hinhanh, author_id) 
                VALUES('$tieude', '$tomtat', '$noidung', '$danhmuc', '$trangthai', '$hinhanh_time', '$author_id')";
    
    if(mysqli_query($conn, $sql_add)){
        move_uploaded_file($hinhanh_tmp, '../images/'.$hinhanh_time);
        echo "<script>alert('Thêm tin thành công!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    }
}
?>

<div class="form-container" style="background: white; padding: 20px; border-radius: 8px;">
    <h3><i class="fas fa-plus-circle"></i> Thêm bài viết mới</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <p>Tiêu đề bài viết: <br> <input type="text" name="tieude" style="width:100%; padding: 8px;" required></p>
        <p>Tóm tắt bài viết: <br> <textarea name="tomtat" rows="3" style="width:100%; padding: 8px;"></textarea></p>
        <p>Nội dung chi tiết: <br> <textarea name="noidung" id="editor" rows="10" style="width:100%; padding: 8px;"></textarea></p>
        <p>Danh mục: 
            <select name="danhmuc" style="padding: 8px;">
                <?php
                $sql_cate = "SELECT * FROM tbl_categories ORDER BY id DESC";
                $query_cate = mysqli_query($conn, $sql_cate);
                while($row_cate = mysqli_fetch_array($query_cate)){
                    echo '<option value="'.$row_cate['id'].'">'.$row_cate['name'].'</option>';
                }
                ?>
            </select>
        </p>
        <p>Ảnh minh họa: <input type="file" name="hinhanh" required></p>
        <p>Trạng thái: 
            <select name="trangthai" style="padding: 8px;">
                <option value="cho_duyet">Gửi duyệt</option>
                <option value="da_dang">Đăng ngay (Admin)</option>
                <option value="ban_nhap">Lưu bản nháp</option>
            </select>
        </p>
        <button type="submit" name="themtintuc" style="background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">Đăng bài</button>
    </form>
</div>