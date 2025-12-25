<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');


if (!isset($_GET['id'])) {
    header("Location: /Web_tintuc/admin/index.php?mod=ads&act=list");
    exit;
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM tbl_ads WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?mod=ads&act=list");
    exit;
}

$ads = mysqli_fetch_assoc($result);

/* =========================
   3. XỬ LÝ SUBMIT UPDATE
========================= */
if (isset($_POST['btn_update'])) {
    $title    = trim($_POST['title']);
    $image    = trim($_POST['image']);
    $link     = trim($_POST['link']);
    $position = $_POST['position'];
    $status   = $_POST['status'];

    $sqlUpdate = "
    UPDATE tbl_ads 
    SET title='$title',
        image='$image',
        link='$link',
        position='$position',
        status='$status'
    WHERE id=$id
    ";

    mysqli_query($conn, $sqlUpdate);
    header("Location: index.php?mod=ads&act=list");
    exit;
}

?>
<div class="admin-container">

    <a href="index.php?mod=ads&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>
    <h2 class="admin-title">
        Cập nhật quảng cáo
    </h2>

    <form method="post" class="admin-form">
        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" value="<?= $ads['title'] ?>" required>
        </div>

        <div class="form-group">
            <label>Hình ảnh</label>
            <input type="text" name="image" value="<?= $ads['image'] ?>" required>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="text" name="link" value="<?= $ads['link'] ?>" required>
        </div>
        <div class="form-group">
            <label>Vị trí</label>
            <select name="position">
                <option value="sidebar_right" <?= $ads['position'] == 'sidebar_right' ? 'selected' : '' ?>>Sidebar_right</option>
                <option value="sidebar_left" <?= $ads['position'] == 'sidebar_left' ? 'selected' : '' ?>>Sidebar_left</option>
                <option value="header" <?= $ads['position'] == 'header' ? 'selected' : '' ?>>Header</option>
                <option value="footer" <?= $ads['position'] == 'footer' ? 'selected' : '' ?>>Footer</option>
            </select>
        </div>
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="hien" <?= $ads['status'] === 'hien' ? 'selected' : '' ?>>
                    Hiển thị
                </option>
                <option value="an" <?= $ads['status'] === 'an' ? 'selected' : '' ?>>
                    Ẩn
                </option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Cập nhật
            </button>
            <a href="index.php?mod=ads&act=list" class="btn btn-Cancel">
                Huỷ
            </a>
        </div>
    </form>
</div>