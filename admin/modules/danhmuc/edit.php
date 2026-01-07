<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. KIỂM TRA QUYỀN
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'editor') {
    die('Bạn không có quyền thao tác danh mục');
}

if (!isset($_GET['id'])) {
    header("Location: index.php?mod=danhmuc&act=list");
    exit;
}

$id = (int)$_GET['id'];

// 2. LẤY THÔNG TIN DANH MỤC HIỆN TẠI
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

// 3. CHẶN EDITOR NẾU KHÔNG PHẢI QUẢN LÝ
if ($_SESSION['admin_role'] === 'editor') {
    if ($category['manager_id'] != $_SESSION['admin_id']) {
        echo "<script>
            alert('Bạn không phải người quản lý danh mục này nên không được phép sửa!');
            window.location.href = 'index.php?mod=danhmuc&act=list';
        </script>";
        exit;
    }
}

// 4. LẤY DANH SÁCH DANH MỤC CHA (Để hiển thị vào dropdown)
// Quan trọng: Phải loại trừ chính nó (AND id != $id) để không chọn chính mình làm cha
$sql_parents = "SELECT * FROM tbl_categories WHERE parent_id = 0 AND id != $id ORDER BY id DESC";
$res_parents = mysqli_query($conn, $sql_parents);

// 5. LẤY DANH SÁCH EDITOR (Chỉ Admin mới cần dùng)
$resEditor = null;
if ($_SESSION['admin_role'] === 'admin') {
    $sqlEditor = "SELECT * FROM tbl_users WHERE role = 'editor'";
    $resEditor = mysqli_query($conn, $sqlEditor);
}

// =================================================================
// 6. XỬ LÝ KHI BẤM NÚT CẬP NHẬT
// =================================================================
if (isset($_POST['btn_update'])) {
    $name = trim($_POST['name']);
    $name = mysqli_real_escape_string($conn, $name);
    $parent_id = (int)$_POST['parent_id'];

    if ($_SESSION['admin_role'] === 'admin') {
        $manager_id = (int)$_POST['manager_id'];

        // --- ĐOẠN SỬA LỖI Ở ĐÂY ---
        // Chuyển 0 thành NULL
        $manager_sql_value = ($manager_id == 0) ? "NULL" : $manager_id;

        // Cập nhật dùng $manager_sql_value
        $sqlUpdate = "UPDATE tbl_categories 
                      SET name = '$name', parent_id = $parent_id, manager_id = $manager_sql_value 
                      WHERE id = $id";
    } else {
        // Editor không đổi người quản lý
        $sqlUpdate = "UPDATE tbl_categories 
                      SET name = '$name', parent_id = $parent_id 
                      WHERE id = $id";
    }

    if (mysqli_query($conn, $sqlUpdate)) {
        header("Location: index.php?mod=danhmuc&act=list&msg=updated");
        exit;
    } else {
        $error = "Lỗi hệ thống: " . mysqli_error($conn);
    }
}
?>

<div class="admin-container">

    <div class="admin-header-inline">
        <a href="index.php?mod=danhmuc&act=list" class="btn btn_back">
            ⬅ Quay lại
        </a>
        <h2 class="admin-title">CẬP NHẬT DANH MỤC</h2>
        <div style="width: 140px;"></div>
    </div>

    <?php if (isset($error)) { ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php } ?>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>Tên danh mục</label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Thuộc danh mục (Cha)</label>
            <select name="parent_id">
                <option value="0">-- Là danh mục gốc --</option>
                <?php
                if (mysqli_num_rows($res_parents) > 0) {
                    while ($row = mysqli_fetch_assoc($res_parents)):
                        $selected_parent = ($category['parent_id'] == $row['id']) ? 'selected' : '';
                ?>
                        <option value="<?= $row['id'] ?>" <?= $selected_parent ?>>
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                <?php
                    endwhile;
                }
                ?>
            </select>
            <small class="form-hint">Thay đổi mục này để chuyển danh mục Gốc thành Con hoặc ngược lại.</small>
        </div>

        <div class="form-group">
            <label>Người phụ trách (Trưởng ban)</label>

            <?php if ($_SESSION['admin_role'] === 'admin') { ?>

                <select name="manager_id" class="form-control">
                    <option value="0">-- Chưa phân công --</option>
                    <?php
                    if ($resEditor && mysqli_num_rows($resEditor) > 0) {
                        while ($editor = mysqli_fetch_assoc($resEditor)) {
                            $selected = ($editor['id'] == $category['manager_id']) ? 'selected' : '';
                            echo "<option value='{$editor['id']}' $selected>{$editor['hoten']} ({$editor['username']})</option>";
                        }
                    }
                    ?>
                </select>
                <small class="form-hint">* Admin có quyền thay đổi người quản lý.</small>

            <?php } else { ?>

                <input type="text" value="<?= htmlspecialchars($category['manager_name']) ?>" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                <input type="hidden" name="manager_id" value="<?= $category['manager_id'] ?>"> <small class="form-hint">* Bạn chỉ được xem, không được thay đổi người phân công.</small>

            <?php } ?>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Cập nhật
            </button>
            <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
                ❌ Huỷ bỏ
            </a>
        </div>

    </form>
</div>