<?php
$id = $_GET['id'];
$row = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM tbl_news WHERE id = '$id'"));

if(isset($_POST['suatintuc'])){
    $tieude = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $danhmuc = $_POST['danhmuc'];
    $trangthai = $_POST['trangthai'];
    
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
    
    if($hinhanh != ''){
        // Đổi ảnh
        $hinhanh_time = time().'_'.$hinhanh;
        move_uploaded_file($hinhanh_tmp, '../images/'.$hinhanh_time);
        
        // Xóa ảnh cũ
        if(file_exists('../images/'.$row['hinhanh'])){ unlink('../images/'.$row['hinhanh']); }

        $sql_update = "UPDATE tbl_news SET tieude='$tieude', tomtat='$tomtat', noidung='$noidung', category_id='$danhmuc', trangthai='$trangthai', hinhanh='$hinhanh_time' WHERE id='$id'";
    } else {
        // Giữ ảnh cũ
        $sql_update = "UPDATE tbl_news SET tieude='$tieude', tomtat='$tomtat', noidung='$noidung', category_id='$danhmuc', trangthai='$trangthai' WHERE id='$id'";
    }
    
    mysqli_query($conn, $sql_update);
    header('Location: index.php?mod=tintuc&act=list');
}
?>

<div class="form-container" style="background: white; padding: 20px; border-radius: 8px;">
    <h3><i class="fas fa-edit"></i> Chỉnh sửa bài viết</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <p>Tiêu đề: <br> <input type="text" name="tieude" value="<?php echo $row['tieude']; ?>" style="width:100%; padding: 8px;"></p>
        <p>Nội dung: <br> <textarea name="noidung" id="editor" rows="10" style="width:100%;"><?php echo $row['noidung']; ?></textarea></p>
        <p>Ảnh hiện tại: <br> <img src="../images/<?php echo $row['hinhanh']; ?>" width="150" style="margin: 10px 0;"></p>
        <p>Chọn ảnh mới (để trống nếu không đổi): <input type="file" name="hinhanh"></p>
        <p>Danh mục: 
            <select name="danhmuc" style="padding: 8px;">
                <?php
                $sql_cate = mysqli_query($conn, "SELECT * FROM tbl_categories");
                while($c = mysqli_fetch_array($sql_cate)){
                    $selected = ($c['id'] == $row['category_id']) ? 'selected' : '';
                    echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                }
                ?>
            </select>
        </p>
        <button type="submit" name="suatintuc" style="background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">Cập nhật bài viết</button>
    </form>
</div>