<?php
// Bắt buộc khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// =============================================================
// 1. KIỂM TRA QUYỀN HẠN & LẤY ĐÚNG ID NGƯỜI DÙNG
// =============================================================
// Fix lỗi ID=0: Kiểm tra tất cả các trường hợp tên biến session có thể xảy ra
if (isset($_SESSION['admin_id'])) {
    $current_user_id = $_SESSION['admin_id']; // Ưu tiên admin_id (như file edit.php)
} elseif (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
} elseif (isset($_SESSION['id'])) {
    $current_user_id = $_SESSION['id'];
} else {
    $current_user_id = 0; // Chưa đăng nhập hoặc lỗi session
}

$current_role = $_SESSION['admin_role'] ?? '';

$isAdmin  = ($current_role === 'admin');
$isEditor = ($current_role === 'editor');

// Nếu chưa đăng nhập hoặc không có quyền -> Chặn
if (!$isAdmin && !$isEditor) {
    die("❌ Lỗi: Bạn không có quyền truy cập hoặc phiên đăng nhập đã hết hạn. (Role: $current_role)");
}

$canEdit   = ($isAdmin || $isEditor);
$canDelete = ($isAdmin);
$canAdd    = ($isAdmin);

// =============================================================
// 2. XỬ LÝ LỌC
// =============================================================
$sql_parents = "SELECT id, name FROM tbl_categories WHERE parent_id = 0 ORDER BY id ASC";
$res_parents = mysqli_query($conn, $sql_parents);
$filter_id = isset($_GET['filter_id']) ? intval($_GET['filter_id']) : 0;

// =============================================================
// 3. XÂY DỰNG CÂU TRUY VẤN
// =============================================================
$conditions = [];

// A. LOGIC QUYỀN EDITOR:
// Editor thấy danh mục nếu:
// 1. Họ quản lý trực tiếp (c.manager_id)
// 2. HOẶC Họ quản lý cha (p.manager_id)
if ($isEditor) {
    $conditions[] = "(c.manager_id = $current_user_id OR p.manager_id = $current_user_id)";
}

// B. LOGIC BỘ LỌC:
if ($filter_id > 0) {
    $conditions[] = "(c.id = $filter_id OR c.parent_id = $filter_id)";
}

$where_sql = "";
if (!empty($conditions)) {
    $where_sql = " WHERE " . implode(" AND ", $conditions);
}

// C. TRUY VẤN CHÍNH
$sql = "SELECT c.*, 
               u.hoten AS manager_name, 
               p.name AS parent_name, 
               p.manager_id AS parent_manager_id,
               pu.hoten AS parent_manager_name 
        FROM tbl_categories c
        LEFT JOIN tbl_users u ON c.manager_id = u.id
        LEFT JOIN tbl_categories p ON c.parent_id = p.id
        LEFT JOIN tbl_users pu ON p.manager_id = pu.id 
        $where_sql
        ORDER BY c.parent_id ASC, c.id ASC";

$result = mysqli_query($conn, $sql);

// Debug lỗi SQL
if (!$result) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}
?>

<h2 class="admin-title">QUẢN LÝ DANH MỤC</h2>

<?php if (isset($_GET['msg'])): ?>
    <div id="status-msg" class="alert alert-success" style="padding:10px; background:#d4edda; color:#155724; margin-bottom:15px; border-radius:4px;">
        Thao tác thành công!
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('status-msg').style.display = 'none';
        }, 3000);
    </script>
<?php endif; ?>

<div class="admin-toolbar">
    <div class="admin-controls">
        <a href="<?= $canAdd ? 'index.php?mod=danhmuc&act=add' : 'javascript:void(0)' ?>"
            class="btn btn-add <?= !$canAdd ? 'btn-disabled' : '' ?>">
            ➕ Thêm danh mục
        </a>
        <button type="submit" form="mainForm" id="btnDeleteSelected"
            class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>"
            <?= !$canDelete ? 'disabled' : 'disabled' ?>>
            🗑️ Xoá 0 mục
        </button>
    </div>

    <div class="search-box">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="mod" value="danhmuc">
            <input type="hidden" name="act" value="list">
            <select name="filter_id" class="search-input" onchange="this.form.submit()">
                <option value="0">-- Tất cả danh mục --</option>
                <?php
                if (mysqli_num_rows($res_parents) > 0) {
                    mysqli_data_seek($res_parents, 0);
                    while ($p = mysqli_fetch_assoc($res_parents)):
                        $selected = ($filter_id == $p['id']) ? 'selected' : '';
                ?>
                        <option value="<?= $p['id'] ?>" <?= $selected ?>>📁 <?= $p['name'] ?></option>
                <?php endwhile;
                } ?>
            </select>
            <button type="submit" class="btn btn-OK">🔍 Lọc</button>
            <?php if ($filter_id > 0): ?>
                <a href="index.php?mod=danhmuc&act=list" class="btn btn-view">🔄</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<form method="post" id="mainForm" action="index.php?mod=danhmuc&act=delete" onsubmit="return confirm('Bạn có chắc không?')">
    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="checkAll" <?= !$canDelete ? 'disabled' : '' ?>></th>
                    <th width="50">ID</th>
                    <th>Tên danh mục</th>
                    <th>Cấp độ</th>
                    <th>Người phụ trách</th>
                    <th width="150">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" <?= !$canDelete ? 'disabled' : '' ?>></td>
                            <td><?= $row['id'] ?></td>

                            <td style="font-weight: 500;">
                                <?php
                                if ($row['parent_id'] != 0) {
                                    echo '<span style="color:#999; margin-left: 20px;">└──</span> ' . htmlspecialchars($row['name']);
                                } else {
                                    echo '<strong style="color:#d32f2f; font-size: 15px;">' . htmlspecialchars($row['name']) . '</strong>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php if ($row['parent_id'] == 0): ?>
                                    <span class="status-badge" style="background:#e3f2fd; color:#0d47a1;">Gốc</span>
                                <?php else: ?>
                                    <span class="status-badge" style="background:#f5f5f5; color:#666;">Con: <strong><?= $row['parent_name'] ?></strong></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                if (!empty($row['manager_name'])) {
                                    echo '<span style="color:#2e7d32; font-weight:600">' . $row['manager_name'] . '</span>';
                                } elseif (!empty($row['parent_manager_name'])) {
                                    echo '<span style="color:#555;">' . $row['parent_manager_name'] . '</span>';
                                    echo '<div style="font-size:11px; color:#999; font-style:italic;">(Theo danh mục cha)</div>';
                                } else {
                                    echo '<i style="color:#ccc; font-size:12px;">Chưa phân công</i>';
                                }
                                ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-edit <?= !$canEdit ? 'btn-disabled' : '' ?>" href="<?= $canEdit ? 'index.php?mod=danhmuc&act=edit&id=' . $row['id'] : '#' ?>">✏️ Sửa</a>
                                    <a class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>" href="<?= $canDelete ? 'index.php?mod=danhmuc&act=delete&id=' . $row['id'] : '#' ?>" <?= $canDelete ? 'onclick="return confirm(\'Xoá?\')"' : '' ?>>🗑️ Xoá</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:#999;">
                            <?php if ($isEditor): ?>
                                Bạn (ID: <strong><?= $current_user_id ?></strong>) chưa được phân công quản lý danh mục nào.<br>
                                <small>Vui lòng nhờ Admin phân quyền quản lý trong phần "Sửa danh mục".</small>
                            <?php else: ?>
                                Danh sách trống.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    // JS giữ nguyên
    const checkAll = document.getElementById('checkAll');
    const btnDelete = document.getElementById('btnDeleteSelected');

    function updateDeleteButton() {
        const count = document.querySelectorAll('input[name="ids[]"]:checked').length;
        btnDelete.disabled = count === 0;
        btnDelete.innerHTML = `🗑️ Xoá ${count} mục`;
        if (count > 0) btnDelete.classList.remove('btn-disabled');
        else btnDelete.classList.add('btn-disabled');
    }
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
                if (!cb.disabled) cb.checked = this.checked;
            });
            updateDeleteButton();
        });
    }
    document.addEventListener('change', e => {
        if (e.target.name === 'ids[]') updateDeleteButton();
    });
</script>