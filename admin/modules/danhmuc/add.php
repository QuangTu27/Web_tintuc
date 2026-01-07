<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Kiểm tra quyền hạn (Admin hoặc Editor mới được vào)
if (!isset($_SESSION['admin_role']) || ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'editor')) {
    die('Bạn không có quyền thao tác danh mục');
}

// 1. LẤY DANH SÁCH DANH MỤC GỐC (CHA)
$sql_parents = "SELECT * FROM tbl_categories WHERE parent_id = 0 ORDER BY id DESC";
$res_parents = mysqli_query($conn, $sql_parents);

// 2. LẤY DANH SÁCH EDITOR (CHỈ DÀNH CHO ADMIN)
$resEditor = null;
if ($_SESSION['admin_role'] === 'admin') {
    $sqlEditor = "SELECT * FROM tbl_users WHERE role = 'editor'";
    $resEditor = mysqli_query($conn, $sqlEditor);
}

// =================================================================
// 3. XỬ LÝ KHI SUBMIT FORM
// =================================================================
if (isset($_POST['btn_add'])) {
    $name = trim($_POST['name']);
    $name = mysqli_real_escape_string($conn, $name);
    $parent_id = (int)$_POST['parent_id'];

    // Xử lý Người quản lý (Manager ID)
    if ($_SESSION['admin_role'] === 'admin') {
        // Admin được quyền chọn người quản lý từ form
        $manager_id = (int)$_POST['manager_id'];
    } else {
        // Nếu là Editor tạo, tự động gán chính họ làm quản lý
        $manager_id = $_SESSION['admin_id'];
    }

    $manager_sql_value = ($manager_id == 0) ? "NULL" : $manager_id;

    // Validate
    if (empty($name)) {
        $error = "Tên danh mục không được để trống";
    } else {
        // Kiểm tra trùng tên
        $check = "SELECT * FROM tbl_categories WHERE name='$name'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Tên danh mục này đã tồn tại!";
        } else {
            // INSERT ĐẦY ĐỦ: Tên + Cha + Người quản lý
            $sql = "INSERT INTO tbl_categories(name, parent_id, manager_id) 
                    VALUES ('$name', $parent_id, $manager_sql_value)";

            if (mysqli_query($conn, $sql)) {
                header('Location: index.php?mod=danhmuc&act=list&msg=added');
                exit();
            } else {
                $error = "Lỗi hệ thống: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">THÊM DANH MỤC</h2>
        <div style="width: 140px;"></div>
    </div>

    <?php if (isset($error)) { ?>
        <div class="alert alert-warning" id="error-alert">
            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php } ?>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>*Tên danh mục</label>
            <input type="text" name="name" placeholder="Ví dụ: Bóng đá, Thời sự..." required>
        </div>

        <div class="form-group">
            <label>Thuộc danh mục (Cha)</label>
            <select name="parent_id">
                <option value="0">-- Là danh mục gốc (Không có cha) --</option>
                <?php
                if (mysqli_num_rows($res_parents) > 0) {
                    while ($row = mysqli_fetch_assoc($res_parents)):
                ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                <?php
                    endwhile;
                }
                ?>
            </select>
            <small class="form-hint">Chọn danh mục gốc nếu đây là danh mục con (VD: Chọn 'Thể thao' cho 'Bóng đá').</small>
        </div>

        <div class="form-group">
            <label>*Người phụ trách (Trưởng ban)</label>

            <?php if ($_SESSION['admin_role'] === 'admin') { ?>
                <select name="manager_id" class="form-control">
                    <option value="0">-- Chưa phân công --</option>
                    <?php
                    if ($resEditor && mysqli_num_rows($resEditor) > 0) {
                        while ($editor = mysqli_fetch_assoc($resEditor)) {
                            echo "<option value='{$editor['id']}'>{$editor['hoten']} ({$editor['username']})</option>";
                        }
                    }
                    ?>
                </select>
                <small class="form-hint">* Admin phân công ai thì người đó mới được đăng bài vào mục này.</small>

            <?php } else { ?>
                <input type="text" value="<?= $_SESSION['user_name'] ?? 'Tôi' ?> (Tự động gán)" disabled style="background-color: #e9ecef;">
                <input type="hidden" name="manager_id" value="<?= $_SESSION['admin_id'] ?>">
                <small class="form-hint">* Bạn tạo danh mục này nên bạn sẽ là người quản lý.</small>

            <?php } ?>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_add" class="btn btn-OK">
                💾 Lưu danh mục
            </button>
            <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
                ❌ Huỷ
            </a>
        </div>

    </form>
</div>