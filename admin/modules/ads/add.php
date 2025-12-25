<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_POST['btn_add'])) {
    $title = $_POST['title'];
    $link = $_POST['link'];
    $position = $_POST['position'];
    $status = $_POST['status'];

    $image = $_FILES['image']['name'];
    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/ads/$image"
    );

    $sql = "INSERT INTO tbl_ads(title, image, link, position, status)
            VALUES ('$title','$image','$link','$position','$status')";
    mysqli_query($conn, $sql);

    header("Location: index.php?mod=ads&act=list");
    exit;
}
?>

<h2 class="admin-title">THÊM QUẢNG CÁO</h2>

<form method="post" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label>Tiêu đề</label>
        <input type="text" name="title" required>
    </div>

    <div class="form-group">
        <label>Hình ảnh</label>
        <input type="file" name="image" required>
    </div>

    <div class="form-group">
        <label>Link</label>
        <input type="text" name="link">
    </div>

    <div class="form-group">
        <label>Vị trí</label>
        <select name="position">
            <option value="sidebar_right">Sidebar_right</option>
            <option value="sidebar_left">Sidebar_left</option>
            <option value="header">Header</option>
            <option value="footer">Footer</option>
        </select>
    </div>

    <div class="form-group">
        <label>Trạng thái</label>
        <select name="status">
            <option value="hien">Hiển thị</option>
            <option value="an" selected>Ẩn</option>
        </select>
    </div>

    <div class="form-actions">
        <button class="btn btn-OK" name="btn_add">Lưu</button>
        <a href="index.php?mod=ads&act=list" class="btn btn-Cancel">Huỷ</a>
    </div>
</form>