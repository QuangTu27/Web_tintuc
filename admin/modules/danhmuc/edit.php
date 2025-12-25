<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'editor') {
    die('Bạn không có quyền thao tác danh mục');
}

if (!isset($_GET['id'])) {
    header("Location: /Web_tintuc/admin/index.php?mod=danhmuc&act=list");
    exit;
}

$id = (int)$_GET['id'];

$sql = "
    SELECT c.*, u.hoten AS manager_name
    FROM tbl_categories c
    LEFT JOIN tbl_users u ON c.manager_id = u.id
    WHERE c.id = $id
";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?mod=danhmuc&act=list");
    exit;
}

$category = mysqli_fetch_assoc($result);

// Nếu là Editor, và ID người quản lý danh mục KHÁC ID đang đăng nhập -> Chặn ngay
if ($_SESSION['admin_role'] === 'editor') {
    if ($category['manager_id'] != $_SESSION['admin_id']) {
        echo "<script>
            alert('Bạn không phải người quản lý danh mục này nên không được phép sửa!');
            window.location.href = 'index.php?mod=danhmuc&act=list';
        </script>";
        exit;
    }
}

// 4. LẤY DANH SÁCH EDITOR (CHỈ DÀNH CHO ADMIN ĐỂ HIỆN DROPDOWN)
$list_editors = [];
if ($_SESSION['admin_role'] === 'admin') {
    $sqlEditor = "SELECT * FROM tbl_users WHERE role = 'editor'";
    $resEditor = mysqli_query($conn, $sqlEditor);
}

// =================================================================
// 5. XỬ LÝ KHI BẤM NÚT CẬP NHẬT
// =================================================================
if (isset($_POST['btn_update'])) {
    $name = trim($_POST['name']);

    // Nếu là Admin thì lấy thêm manager_id từ form
    if ($_SESSION['admin_role'] === 'admin') {
        $manager_id = (int)$_POST['manager_id'];

        // Cập nhật cả Tên và Người quản lý
        $sqlUpdate = "UPDATE tbl_categories SET name = '$name', manager_id = $manager_id WHERE id = $id";
    } else {
        // Nếu là Editor thì chỉ cập nhật Tên
        $sqlUpdate = "UPDATE tbl_categories SET name = '$name' WHERE id = $id";
    }

    if (mysqli_query($conn, $sqlUpdate)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php?mod=danhmuc&act=list';</script>";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}
?>

<div class="admin-container">

    <div>
        <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <h2 class="admin-title">CẬP NHẬT DANH MỤC</h2>

    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>Tên danh mục</label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Người phụ trách (Trưởng ban)</label>

            <?php if ($_SESSION['admin_role'] === 'admin') { ?>

                <select name="manager_id" class="form-control">
                    <option value="0">-- Chưa phân công --</option>
                    <?php
                    if (mysqli_num_rows($resEditor) > 0) {
                        while ($editor = mysqli_fetch_assoc($resEditor)) {
                            $selected = ($editor['id'] == $category['manager_id']) ? 'selected' : '';
                            echo "<option value='{$editor['id']}' $selected>{$editor['hoten']} ({$editor['username']})</option>";
                        }
                    }
                    ?>
                </select>
                <small style="color: blue;">* Admin có quyền thay đổi người quản lý.</small>

            <?php } else { ?>

                <input type="text" value="<?= htmlspecialchars($category['manager_name']) ?>" disabled style="background-color: #e9ecef;">
                <small style="color: gray;">* Bạn chỉ được xem, không được thay đổi người phân công.</small>

            <?php } ?>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Lưu thay đổi
            </button>
            <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
                Huỷ bỏ
            </a>
        </div>

    </form>
</div>