<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. KIỂM TRA ID HỢP LỆ
if (!isset($_GET['id'])) {
    header("Location: index.php?mod=tintuc&act=list");
    exit();
}
$id = (int)$_GET['id'];

// 2. LẤY DỮ LIỆU BÀI VIẾT
$sql_get = "SELECT * FROM tbl_news WHERE id = $id";
$result_get = mysqli_query($conn, $sql_get);
if (mysqli_num_rows($result_get) == 0) {
    die("Bài viết không tồn tại!");
}
$row = mysqli_fetch_assoc($result_get);

// 3. PHÂN QUYỀN TRUY CẬP
// - Admin/Editor: Được quyền full
// - Tác giả: Chỉ được sửa bài của mình
$isAdminOrEditor = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'editor');
$isAuthor = ($row['author_id'] == $_SESSION['admin_id']);

if (!$isAdminOrEditor && !$isAuthor) {
    echo "<script>alert('Bạn không có quyền sửa bài viết của người khác!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    exit();
}

// 4. LẤY DANH MỤC ĐA CẤP
$sql_cate = "SELECT c.*, p.name AS parent_name 
             FROM tbl_categories c 
             LEFT JOIN tbl_categories p ON c.parent_id = p.id 
             ORDER BY c.parent_id ASC, c.id ASC";
$query_cate = mysqli_query($conn, $sql_cate);

// =================================================================
// 5. XỬ LÝ CẬP NHẬT
// =================================================================
if (isset($_POST['suatintuc'])) {
    $tieude  = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat  = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $danhmuc = (int)$_POST['danhmuc'];

    // LOGIC TRẠNG THÁI:
    // - Sếp sửa: Lấy theo lựa chọn (Giữ nguyên hoặc đổi)
    // - Lính sửa: Bắt buộc quay về 'cho_duyet' (Để sếp duyệt lại nội dung mới sửa)
    if ($isAdminOrEditor) {
        $trangthai = $_POST['trangthai'];
    } else {
        $trangthai = 'cho_duyet';
    }

    // XỬ LÝ ẢNH
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
    $hinhanh_time = $row['hinhanh']; // Mặc định giữ ảnh cũ

    if ($hinhanh != '') {
        // Nếu có up ảnh mới
        $hinhanh_time = time() . '_' . $hinhanh;
        move_uploaded_file($hinhanh_tmp, '../../images/news/' . $hinhanh_time);

        // Xóa ảnh cũ đi cho đỡ rác host (Kiểm tra file tồn tại trước khi xóa)
        $old_img_path = '../../images/news/' . $row['hinhanh'];
        if (file_exists($old_img_path) && !empty($row['hinhanh'])) {
            unlink($old_img_path);
        }
    }

    // UPDATE CSDL
    $sql_update = "UPDATE tbl_news SET 
                   tieude='$tieude', 
                   tomtat='$tomtat', 
                   noidung='$noidung', 
                   category_id='$danhmuc', 
                   trangthai='$trangthai', 
                   hinhanh='$hinhanh_time',
                   ngaycapnhat=NOW() 
                   WHERE id='$id'";

    if (mysqli_query($conn, $sql_update)) {
        $msg = ($isAdminOrEditor) ? "Cập nhật thành công!" : "Cập nhật thành công! Bài viết đã được chuyển sang trạng thái Chờ duyệt.";
        echo "<script>alert('$msg'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    } else {
        $error = "Lỗi SQL: " . mysqli_error($conn);
    }
}
?>

<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">CHỈNH SỬA BÀI VIẾT</h2>
    </div>

    <?php if (isset($error)) { ?>
        <div class="alert alert-warning"><?= $error ?></div>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data" class="admin-form">

        <div class="form-group">
            <label>Tiêu đề bài viết</label>
            <input type="text" name="tieude" value="<?= htmlspecialchars($row['tieude']) ?>" required>
        </div>

        <div class="form-group">
            <label>Danh mục</label>
            <select name="danhmuc" class="form-control">
                <?php while ($cat = mysqli_fetch_assoc($query_cate)) {
                    $selected = ($cat['id'] == $row['category_id']) ? 'selected' : '';

                    // Hiển thị tên cha > con
                    $catName = $cat['name'];
                    if ($cat['parent_id'] != 0 && $cat['parent_name'] != null) {
                        $catName = $cat['parent_name'] . ' > ' . $cat['name'];
                    }
                ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= $catName ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh minh họa</label>
            <div style="margin-bottom: 10px;">
                <img src="/Web_tintuc/images/news/<?= $row['hinhanh'] ?>" style="height: 150px; border-radius: 5px; border: 1px solid #ddd;" onerror="this.src='../../images/default_news.jpg'">
            </div>
            <input type="file" name="hinhanh">
            <small class="form-hint">Chọn ảnh mới nếu muốn thay đổi.</small>
        </div>

        <div class="form-group">
            <label>Tóm tắt</label>
            <textarea name="tomtat" rows="4" class="form-control"><?= htmlspecialchars($row['tomtat']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Nội dung chi tiết</label>
            <textarea name="noidung" id="editor" rows="10" class="form-control"><?= $row['noidung'] ?></textarea>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trangthai" class="form-control" <?= (!$isAdminOrEditor) ? 'disabled' : '' ?> style="<?= (!$isAdminOrEditor) ? 'background:#e9ecef' : '' ?>">

                <?php if ($isAdminOrEditor): ?>
                    <option value="da_dang" <?= ($row['trangthai'] == 'da_dang') ? 'selected' : '' ?>>✅ Đã đăng (Hiển thị)</option>
                    <option value="cho_duyet" <?= ($row['trangthai'] == 'cho_duyet') ? 'selected' : '' ?>>⏳ Chờ duyệt</option>
                    <option value="ban_nhap" <?= ($row['trangthai'] == 'ban_nhap') ? 'selected' : '' ?>>📝 Bản nháp (Ẩn)</option>
                <?php else: ?>
                    <option value="cho_duyet" selected>⏳ Gửi chờ duyệt lại (Bạn sửa nội dung, bài sẽ bị ẩn để duyệt lại)</option>
                <?php endif; ?>

            </select>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="suatintuc" class="btn btn-OK">💾 Cập nhật bài viết</button>
            <a href="index.php?mod=tintuc&act=list" class="btn btn-Cancel">❌ Hủy bỏ</a>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        height: 400,
        versionCheck: false
    });
</script>