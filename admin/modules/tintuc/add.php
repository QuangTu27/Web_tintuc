<?php
// Kiểm tra nếu người dùng nhấn nút Lưu
if (isset($_POST['btn_them'])) {
    // Lấy dữ liệu từ form và chống lỗi SQL Injection
    $tieude = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $cat_id = $_POST['category_id'];

    // Xử lý File Ảnh
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
    
    // Đổi tên ảnh theo thời gian để không bị trùng file trong thư mục
    $hinhanh_name = time() . '_' . $hinhanh;

    // Câu lệnh SQL (đúng các cột trong ảnh DB mày gửi)
    $sql_add = "INSERT INTO tbl_news (tieude, tomtat, noidung, hinhanh, ngaydang, category_id) 
                VALUES ('$tieude', '$tomtat', '$noidung', '$hinhanh_name', NOW(), '$cat_id')";
    
    if (mysqli_query($conn, $sql_add)) {
        // Nếu lưu DB thành công thì mới đẩy ảnh vào thư mục
        move_uploaded_file($hinhanh_tmp, "../images/news/" . $hinhanh_name);
        
        echo "<script>alert('Thêm bài viết thành công!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    } else {
        echo "<h3 style='color:red;'>Lỗi truy vấn: " . mysqli_error($conn) . "</h3>";
    }
}
?>

<div style="padding: 20px; background: #white;">
    <h2>➕ Thêm Bài Viết Mới</h2>
    <form method="POST" enctype="multipart/form-data" style="max-width: 800px; border: 1px solid #ccc; padding: 20px;">
        <p><b>Tiêu đề bài viết:</b></p>
        <input type="text" name="tieude" style="width: 100%; padding: 8px;" required>

        <p><b>Danh mục (ID):</b></p>
        <input type="number" name="category_id" value="1" style="padding: 8px;">

        <p><b>Ảnh minh họa:</b></p>
        <input type="file" name="hinhanh" accept="image/*" required>

        <p><b>Tóm tắt ngắn:</b></p>
        <textarea name="tomtat" rows="3" style="width: 100%; padding: 8px;"></textarea>

        <p><b>Nội dung chi tiết:</b></p>
        <textarea name="noidung" rows="10" style="width: 100%; padding: 8px;" required></textarea>

        <br><br>
        <button type="submit" name="btn_them" style="background: #28a745; color: white; padding: 10px 25px; border: none; cursor: pointer; font-weight: bold;">
            LƯU BÀI VIẾT
        </button>
        <a href="index.php?mod=tintuc&act=list" style="margin-left: 15px; color: #666;">Hủy bỏ</a>
    </form>
</div>