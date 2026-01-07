<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (!isset($_GET['id'])) {
    header("Location: index.php?mod=ads&act=list");
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
   XỬ LÝ SUBMIT UPDATE
========================= */
if (isset($_POST['btn_update'])) {
    $title    = mysqli_real_escape_string($conn, trim($_POST['title']));
    $link     = mysqli_real_escape_string($conn, trim($_POST['link']));
    $position = $_POST['position'];
    $status   = $_POST['status'];

    // Mặc định giữ lại thông tin cũ
    $media_file = $ads['media_file'];
    $media_type = $ads['media_type'];

    // Kiểm tra nếu người dùng chọn file mới
    if (!empty($_FILES['image_file']['name'])) {
        $media_file = time() . '_' . $_FILES['image_file']['name'];

        // Xác định type mới
        $ext = strtolower(pathinfo($media_file, PATHINFO_EXTENSION));
        $video_exts = ['mp4', 'webm', 'ogg'];
        $media_type = in_array($ext, $video_exts) ? 'video' : 'image';

        move_uploaded_file(
            $_FILES['image_file']['tmp_name'],
            $_SERVER['DOCUMENT_ROOT'] . "/Web_tintuc/images/ads/$media_file"
        );
    }

    $sqlUpdate = "
    UPDATE tbl_ads 
    SET title='$title',
        media_file='$media_file',
        media_type='$media_type',
        link='$link',
        position='$position',
        status='$status'
    WHERE id=$id
    ";

    if (mysqli_query($conn, $sqlUpdate)) {
        header("Location: index.php?mod=ads&act=list&msg=updated");
        exit;
    }
}
?>

<div class="admin-container">
    <h2 class="admin-title">CẬP NHẬT QUẢNG CÁO</h2>
    <form method="post" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" value="<?= htmlspecialchars($ads['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Media hiện tại</label>
            <div style="margin-bottom: 10px;">
                <?php if ($ads['media_type'] === 'video'): ?>
                    <video width="200" autoplay muted loop playsinline style="border-radius: 4px; border: 1px solid #ddd;">
                        <source src="/Web_tintuc/images/ads/<?= $ads['media_file'] ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <img src="/Web_tintuc/images/ads/<?= $ads['media_file'] ?>" style="max-width: 200px; border-radius: 4px; border: 1px solid #ddd;">
                <?php endif; ?>
            </div>
            <label>Chọn file mới (Ảnh hoặc Video - Bỏ qua nếu không muốn đổi)</label>
            <input type="file" name="image_file" accept="image/*,video/mp4">
        </div>

        <div class="form-group">
            <label>Link liên kết</label>
            <input type="text" name="link" value="<?= htmlspecialchars($ads['link']) ?>" required>
        </div>

        <div class="form-group">
            <label>Vị trí hiển thị</label>
            <select name="position">
                <option value="top_home" <?= $ads['position'] == 'top_home' ? 'selected' : '' ?>>Đầu trang (top_home)</option>
                <option value="sidebar_left" <?= $ads['position'] == 'sidebar_left' ? 'selected' : '' ?>>Cột trái (sidebar_left)</option>
                <option value="sidebar_right" <?= $ads['position'] == 'sidebar_right' ? 'selected' : '' ?>>Cột phải (sidebar_right)</option>
                <option value="inline_home" <?= $ads['position'] == 'inline_home' ? 'selected' : '' ?>>Giữa nội dung (inline_home)</option>
                <option value="footer_home" <?= $ads['position'] == 'footer_home' ? 'selected' : '' ?>>Cuối trang (footer_home)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="hien" <?= $ads['status'] === 'hien' ? 'selected' : '' ?>>Hiển thị</option>
                <option value="an" <?= $ads['status'] === 'an' ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">💾 Cập nhật</button>
            <a href="index.php?mod=ads&act=list" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>