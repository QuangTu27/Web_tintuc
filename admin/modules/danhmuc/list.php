<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$isAdmin  = ($role === 'admin');
$isEditor = ($role === 'editor');

// quyền cụ thể
$canEdit   = ($isAdmin || $isEditor);
$canDelete = ($isAdmin);
$canAdd    = ($isAdmin);

// Lấy danh sách danh mục
$sql = "SELECT c.*, u.hoten AS manager_name
    FROM tbl_categories c
    LEFT JOIN tbl_users u ON c.manager_id = u.id
    ORDER BY c.id ASC
    ";
$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ DANH MỤC</h2>

<form method="post"
    action="index.php?mod=danhmuc&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các danh mục đã chọn?')">


    <p class="list-actions">
        <a href="<?= $canAdd ? 'index.php?mod=danhmuc&act=add' : 'javascript:void(0)' ?>"
            class="btn btn-add <?= !$canAdd ? 'btn-disabled' : '' ?>">
            ➕ Thêm danh mục
        </a>
        <button type="submit"
            id="btnDeleteSelected "
            class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>"
            <?= !$canDelete ? 'disabled title="Chỉ Admin được xoá danh mục"' : 'disabled' ?>>
            🗑️ Xoá 0 danh mục
        </button>
    </p>


    <table class="admin-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox"
                        id="checkAll"
                        <?= !$canDelete ? 'disabled title="Chỉ Admin được xoá"' : '' ?>>
                </th>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Người phụ trách</th>
                <th>Thao tác</th>
            </tr>
        </thead>

        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <input type="checkbox"
                                name="ids[]"
                                value="<?= $row['id'] ?>"
                                <?= !$canDelete ? 'disabled title="Chỉ Admin được xoá"' : '' ?>>
                        </td>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <?= $row['manager_name'] ?? '<i>Chưa phân công</i>' ?>
                        </td>
                        <td>

                            <a class="btn btn-edit <?= !$canEdit ? 'btn-disabled' : '' ?>"
                                href="<?= $canEdit ? 'index.php?mod=danhmuc&act=edit&id=' . $row['id'] : 'javascript:void(0)' ?>">
                                ✏️ Sửa
                            </a>

                            <a class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>"
                                href="<?= $canDelete ? 'index.php?mod=danhmuc&act=delete&id=' . $row['id'] : 'javascript:void(0)' ?>"
                                <?= $canDelete ? 'onclick="return confirm(\'Bạn có chắc muốn xoá?\')"' : '' ?>>
                                🗑️ Xoá
                            </a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td>
                        Chưa có danh mục nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>

<script>
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const btnDelete = document.getElementById('btnDeleteSelected');

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('input[name="ids[]"]:checked').length;

        if (checkedCount > 0) {
            btnDelete.disabled = false;
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} danh mục`;
        } else {
            btnDelete.disabled = true;
            btnDelete.innerHTML = `🗑️ Xoá 0 danh mục`;
        }
    }

    // Chọn / bỏ chọn tất cả
    checkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });

    // Tick từng checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });
</script>