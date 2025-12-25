<?php
// 1. Lấy ID từ URL (Ví dụ: index.php?mod=tintuc&act=edit&id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "Không tìm thấy ID bài viết!";
    exit();
}

// 2. Truy vấn lấy dữ liệu cũ của bài viết đó
$sql_get = "SELECT * FROM tbl_news WHERE id = '$id' LIMIT 1";
$query_get = mysqli_query($conn, $sql_get);
$row = mysqli_fetch_array($query_get);

// Nếu không tìm thấy bài viết trong database
if (!$row) {
    echo "Bài viết không tồn tại!";
    exit();
}

// 3. Xử lý khi bấm nút "CẬP NHẬT"
if (isset($_POST['btn_sua'])) {
    $tieude = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $cat_id = $_POST['category_id'];

    // Kiểm tra xem người dùng có chọn ảnh mới hay không
    if ($_FILES['hinhanh']['name'] != "") {
        // Có chọn ảnh mới: Xử lý upload và cập nhật cột hinhanh
        $hinhanh = $_FILES['hinhanh']['name'];
        $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
        $hinhanh_name = time() . '_' . $hinhanh;
        
        move_uploaded_file($hinhanh_tmp, "../images/news/" . $hinhanh_name);
        
        $sql_update = "UPDATE tbl_news SET 
            tieude = '$tieude', 
            tomtat = '$tomtat', 
            noidung = '$noidung', 
            hinhanh = '$hinhanh_name', 
            category_id = '$cat_id' 
            WHERE id = '$id'";
    } else {
        // Không chọn ảnh mới: Giữ nguyên ảnh cũ (không update cột hinhanh)
        $sql_update = "UPDATE tbl_news SET 
            tieude = '$tieude', 
            tomtat = '$tomtat', 
            noidung = '$noidung', 
            category_id = '$cat_id' 
            WHERE id = '$id'";
    }

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<div style="padding: 20px;">
    <h2>✏️ Chỉnh Sửa Bài Viết</h2>
    <form method="POST" enctype="multipart/form-data" style="max-width: 800px; border: 1px solid #ccc; padding: 20px; background: #fff;">
        <p><b>Tiêu đề bài viết:</b></p>
        <input type="text" name="tieude" value="<?php echo $row['tieude']; ?>" style="width: 100%; padding: 8px;" required>

        <p><b>Danh mục (ID):</b></p>
        <input type="number" name="category_id" value="<?php echo $row['category_id']; ?>" style="padding: 8px;">

        <p><b>Ảnh hiện tại:</b></p>
        <img src="../images/news/<?php echo $row['hinhanh']; ?>" width="150" style="border: 1px solid #ddd; padding: 5px;">
        <p><i>Chọn ảnh mới nếu muốn thay đổi:</i></p>
        <input type="file" name="hinhanh" accept="image/*">

        <p><b>Tóm tắt ngắn:</b></p>
        <textarea name="tomtat" rows="3" style="width: 100%; padding: 8px;"><?php echo $row['tomtat']; ?></textarea>

        <p><b>Nội dung chi tiết:</b></p>
        <textarea name="noidung" rows="10" style="width: 100%; padding: 8px;" required><?php echo $row['noidung']; ?></textarea>

        <br><br>
        <button type="submit" name="btn_sua" style="background: #ffc107; color: black; padding: 10px 25px; border: none; cursor: pointer; font-weight: bold;">
            CẬP NHẬT BÀI VIẾT
        </button>
        <a href="index.php?mod=tintuc&act=list" style="margin-left: 15px; color: #666;">Hủy bỏ</a>
    </form>
</div>