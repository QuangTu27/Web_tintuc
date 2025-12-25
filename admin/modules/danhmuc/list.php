<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách danh mục
$sql = "SELECT * FROM tbl_categories ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ DANH MỤC</h2>

<form method="post"
    action="index.php?mod=danhmuc&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các danh mục đã chọn?')">

    <p class="list-actions">
        <a href="index.php?mod=danhmuc&act=add" class="btn btn-add">➕ Thêm danh mục</a>
        <button type="submit"
            id="btnDeleteSelected"
            class="btn btn-delete"
            disabled>
            🗑️ Xoá 0 danh mục
        </button>
    </p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="checkAll">
                </th>
                <th>ID</th>
                <th>Tên danh mục</th>
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
                                value="<?= $row['id'] ?>">
                        </td>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a class="btn btn-edit"
                                href="index.php?mod=danhmuc&act=edit&id=<?= $row['id'] ?>">
                                ✏️ Sửa
                            </a>

                            <a class="btn btn-delete"
                                href="index.php?mod=danhmuc&act=delete&id=<?= $row['id'] ?>"
                                onclick="return confirm('Bạn có chắc muốn xoá danh mục này?')">
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