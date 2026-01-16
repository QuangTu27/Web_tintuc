<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$canPublish = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'editor');

// 2. LẤY DANH SÁCH DANH MỤC (Kèm tên cha để hiển thị rõ ràng)
$sql_cate = "SELECT c.*, p.name AS parent_name 
             FROM tbl_categories c 
             LEFT JOIN tbl_categories p ON c.parent_id = p.id 
             ORDER BY c.parent_id ASC, c.id ASC";
$query_cate = mysqli_query($conn, $sql_cate);

// 3. XỬ LÝ FORM
if (isset($_POST['themtintuc'])) {
    $tieude    = mysqli_real_escape_string($conn, $_POST['tieude']);
    $tomtat    = mysqli_real_escape_string($conn, $_POST['tomtat']);
    $noidung   = mysqli_real_escape_string($conn, $_POST['noidung']);
    $danhmuc   = (int)$_POST['danhmuc'];
    $author_id = $_SESSION['admin_id'];

    // XỬ LÝ TRẠNG THÁI DỰA TRÊN QUYỀN
    if ($canPublish) {
        // Nếu là Sếp: Lấy giá trị từ Form (Đăng ngay/Nháp/Chờ)
        $trangthai = $_POST['trangthai'];
    } else {
        // Nếu là Nhân viên: Bắt buộc là 'cho_duyet'
        $trangthai = 'cho_duyet';
    }

    // XỬ LÝ ẢNH
    $hinhanh = $_FILES['hinhanh']['name'];
    $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];

    if ($hinhanh != '') {
        $hinhanh_time = time() . '_' . $hinhanh; // Đổi tên tránh trùng

        // DÙNG ĐƯỜNG DẪN TUYỆT ĐỐI (An toàn nhất)
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/images/news/';

        // Kiểm tra nếu thư mục chưa có thì tự tạo
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $hinhanh_time;

        if (move_uploaded_file($hinhanh_tmp, $target_file)) {
            // Upload thành công
        } else {
            // Upload thất bại (Thường do lỗi permission hoặc dung lượng)
            echo "<script>alert('Lỗi: Không thể lưu ảnh vào thư mục images/news. Hãy kiểm tra quyền ghi.');</script>";
            // Vẫn cho lưu database nhưng ảnh sẽ lỗi
        }
    } else {
        $hinhanh_time = '';
    }

    // INSERT DỮ LIỆU
    $sql_add = "INSERT INTO tbl_news(tieude, tomtat, noidung, category_id, trangthai, hinhanh, author_id, ngaydang) 
                VALUES('$tieude', '$tomtat', '$noidung', '$danhmuc', '$trangthai', '$hinhanh_time', '$author_id', NOW())";

    if (mysqli_query($conn, $sql_add)) {
        echo "<script>alert('Thêm bài viết thành công!'); window.location.href='index.php?mod=tintuc&act=list';</script>";
    } else {
        $error = "Lỗi SQL: " . mysqli_error($conn);
    }
}
?>

<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">THÊM BÀI VIẾT MỚI</h2>
    </div>

    <?php if (isset($error)) { ?>
        <div class="alert alert-warning"><?= $error ?></div>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data" class="admin-form">

        <div class="form-group">
            <label>Tiêu đề bài viết</label>
            <input type="text" name="tieude" required placeholder="Nhập tiêu đề tin tức...">
        </div>

        <div class="form-group">
            <label>Danh mục</label>
            <select name="danhmuc" class="form-control" required>
                <option value="">-- Chọn danh mục --</option>
                <?php while ($row = mysqli_fetch_assoc($query_cate)) {
                    // Hiển thị dạng: Thể thao > Bóng đá
                    $catName = $row['name'];
                    if ($row['parent_id'] != 0 && $row['parent_name'] != null) {
                        $catName = $row['parent_name'] . ' > ' . $row['name'];
                    }
                ?>
                    <option value="<?= $row['id'] ?>"><?= $catName ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh đại diện (Thumbnail)</label>
            <input type="file" name="hinhanh" required>
        </div>

        <div class="form-group">
            <label>Tóm tắt (Sapo)</label>
            <textarea name="tomtat" rows="4" class="form-control" placeholder="Mô tả ngắn về bài viết..."></textarea>
        </div>

        <div class="form-group">
            <label>Nội dung chi tiết</label>
            <textarea name="noidung" id="editor" rows="10" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Trạng thái đăng</label>
            <select name="trangthai" class="form-control" <?= (!$canPublish) ? 'disabled' : '' ?> style="<?= (!$canPublish) ? 'background:#e9ecef' : '' ?>">

                <?php if ($canPublish): ?>
                    <option value="da_dang">✅ Đăng ngay (Hiển thị lên web)</option>
                    <option value="cho_duyet">⏳ Chờ duyệt</option>
                    <option value="ban_nhap">📝 Lưu bản nháp (Ẩn)</option>
                <?php else: ?>
                    <option value="cho_duyet" selected>⏳ Gửi chờ duyệt (Bạn không có quyền đăng ngay)</option>
                <?php endif; ?>

            </select>
            <?php if (!$canPublish): ?>
                <small class="form-hint" style="color:red">* Bài viết của bạn cần được Biên tập viên duyệt trước khi hiển thị.</small>
            <?php endif; ?>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="themtintuc" class="btn btn-OK">💾 Lưu bài viết</button>
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