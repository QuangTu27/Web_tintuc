<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_add'])) {
    $title    = mysqli_real_escape_string($conn, $_POST['title']);
    $link     = mysqli_real_escape_string($conn, $_POST['link']);
    $position = $_POST['position'];
    $status   = $_POST['status'];

    // Xử lý File
    $file_name = $_FILES['media_file']['name'];
    $tmp_name  = $_FILES['media_file']['tmp_name'];

    // Tạo tên file duy nhất
    $new_file_name = time() . '_' . $file_name;

    // Lấy đuôi file để kiểm tra type
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $video_exts = ['mp4', 'webm', 'ogg'];
    $media_type = in_array($ext, $video_exts) ? 'video' : 'image';

    // Upload vào thư mục ads
    move_uploaded_file(
        $tmp_name,
        $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/ads/$new_file_name"
    );

    // SQL mới
    $sql = "INSERT INTO tbl_ads(title, media_file, media_type, link, position, status)
            VALUES ('$title', '$new_file_name', '$media_type', '$link', '$position', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?mod=ads&act=list&msg=added");
        exit;
    }
}
?>

<h2 class="admin-title">THÊM QUẢNG CÁO</h2>
<form method="post" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label>Tiêu đề</label>
        <input type="text" name="title" placeholder="Nhập tên quảng cáo..." required>
    </div>

    <div class="form-group">
        <label>Media (Hình ảnh hoặc Video)</label>
        <input type="file" name="media_file" accept="image/*,video/mp4" required>
        <small style="color: #666;">Hỗ trợ: .jpg, .png, .gif, .mp4</small>
    </div>

    <div class="form-group">
        <label>Link</label>
        <input type="text" name="link" placeholder="https://...">
    </div>

    <div class="form-group">
        <label>Vị trí</label>
        <select name="position">
            <option value="top_home">Đầu trang (top_home)</option>
            <option value="sidebar_left">Cột trái (sidebar_left)</option>
            <option value="sidebar_right">Cột phải (sidebar_right)</option>
            <option value="inline_home">Giữa nội dung (inline_home)</option>
            <option value="footer_home">Cuối trang (footer_home)</option>
        </select>
    </div>

    <div class="form-group">
        <label>Trạng thái</label>
        <select name="status">
            <option value="hien">Hiển thị</option>
            <option value="an" selected>Ẩn</option>
        </select>
    </div>

    <div class="btn-group-center">
        <button class="btn btn-OK" name="btn_add">💾 Lưu</button>
        <a href="index.php?mod=ads&act=list" class="btn btn-Cancel">❌ Huỷ</a>
    </div>
</form>