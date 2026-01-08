<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// =============================================================
// 1. KIỂM TRA QUYỀN HẠN
// =============================================================
$isAdmin  = (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin');
$isEditor = (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'editor');

// Quyền cụ thể
$canEdit   = ($isAdmin || $isEditor); // Admin và Editor được sửa
$canDelete = ($isAdmin);              // Chỉ Admin được xoá
$canAdd    = ($isAdmin);              // Chỉ Admin được thêm

// =============================================================
// 2. TRUY VẤN DỮ LIỆU (KẾT HỢP CHA - CON)
// =============================================================
// Sử dụng LEFT JOIN bảng categories với chính nó (p) để lấy tên danh mục Cha
$sql = "SELECT c.*, u.hoten AS manager_name, p.name AS parent_name
        FROM tbl_categories c
        LEFT JOIN tbl_users u ON c.manager_id = u.id
        LEFT JOIN tbl_categories p ON c.parent_id = p.id
        ORDER BY c.parent_id ASC, c.id ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ DANH MỤC</h2>

<?php if (isset($_GET['msg'])): ?>
    <div id="status-msg" class="alert <?php echo (in_array($_GET['msg'], ['added', 'updated'])) ? 'alert-success' : 'alert-warning'; ?>"
        style="padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
        <?php
        switch ($_GET['msg']) {
            case 'added':
                echo "✅ Thêm danh mục mới thành công!";
                break;
            case 'updated':
                echo "✅ Cập nhật danh mục thành công!";
                break;
            case 'deleted':
                echo "🗑️ Đã xoá danh mục.";
                break;
            case 'deleted_multiple':
                echo "🗑️ Đã xoá các danh mục đã chọn.";
                break;
        }
        ?>
    </div>
    <script>
        setTimeout(function() {
            var msg = document.getElementById('status-msg');
            if (msg) msg.style.display = 'none';
        }, 3000);
    </script>
<?php endif; ?>

<form method="post"
    action="index.php?mod=danhmuc&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các danh mục đã chọn?')">

    <p class="list-actions">
        <a href="<?= $canAdd ? 'index.php?mod=danhmuc&act=add' : 'javascript:void(0)' ?>"
            class="btn btn-add <?= !$canAdd ? 'btn-disabled' : '' ?>">
            ➕ Thêm danh mục
        </a>

        <button type="submit"
            id="btnDeleteSelected"
            class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>"
            <?= !$canDelete ? 'disabled title="Chỉ Admin được xoá danh mục"' : 'disabled' ?>>
            🗑️ Xoá 0 danh mục
        </button>
    </p>

    <div class="table-scroll">
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
                    <th>Cấp độ (Cha/Con)</th>
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

                            <td style="font-weight: 500;">
                                <?php
                                if ($row['parent_id'] != 0) {
                                    // Nếu là con, thụt vào và thêm biểu tượng
                                    echo '<span style="color:#999; margin-left: 20px;">└──</span> ' . htmlspecialchars($row['name']);
                                } else {
                                    // Nếu là cha, in đậm
                                    echo '<strong style="color:#d32f2f">' . htmlspecialchars($row['name']) . '</strong>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php if ($row['parent_id'] == 0): ?>
                                    <span class="status-badge" style="background:#e3f2fd; color:#0d47a1; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        Danh mục Gốc
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge" style="background:#f5f5f5; color:#666; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        Con của: <strong><?= htmlspecialchars($row['parent_name']) ?></strong>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= $row['manager_name'] ?? '<i style="color:#999">Chưa phân công</i>' ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-edit <?= !$canEdit ? 'btn-disabled' : '' ?>"
                                        href="<?= $canEdit ? 'index.php?mod=danhmuc&act=edit&id=' . $row['id'] : 'javascript:void(0)' ?>">
                                        ✏️ Sửa
                                    </a>

                                    <a class="btn btn-delete <?= !$canDelete ? 'btn-disabled' : '' ?>"
                                        href="<?= $canDelete ? 'index.php?mod=danhmuc&act=delete&id=' . $row['id'] : 'javascript:void(0)' ?>"
                                        <?= $canDelete ? 'onclick="return confirm(\'Xoá danh mục này có thể ảnh hưởng đến bài viết. Bạn chắc chắn?\')"' : '' ?>>
                                        🗑️ Xoá
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 20px; color: #999;">
                            Chưa có danh mục nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const btnDelete = document.getElementById('btnDeleteSelected');

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('input[name="ids[]"]:checked').length;

        if (checkedCount > 0) {
            btnDelete.disabled = false;
            btnDelete.classList.remove('btn-disabled'); // Xử lý style nút
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} danh mục`;
        } else {
            btnDelete.disabled = true;
            btnDelete.classList.add('btn-disabled');
            btnDelete.innerHTML = `🗑️ Xoá 0 danh mục`;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if (!cb.disabled) cb.checked = this.checked;
            });
            updateDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });
</script>