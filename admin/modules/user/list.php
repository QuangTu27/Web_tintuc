<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách user
$sql = "SELECT * FROM tbl_users ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ TÀI KHOẢN</h2>
<form method="post"
    action="index.php?mod=user&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các user đã chọn?')">

    <p class="list-actions">
        <a href="index.php?mod=user&act=add" class="btn btn-add">➕ Thêm người dùng</a>
        <button type="submit"
            id="btnDeleteSelected"
            class="btn btn-delete"
            disabled>
            🗑️ Xoá 0 user
        </button>
    </p>
    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Quyền</th>
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
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['hoten']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= $row['role'] ?></td>
                            <td>
                                <a class="btn btn-edit"
                                    href="index.php?mod=user&act=edit&id=<?= $row['id'] ?>">
                                    ✏️ Sửa
                                </a>

                                <a class="btn btn-delete"
                                    href="index.php?mod=user&act=delete&id=<?= $row['id'] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xoá user này?')">
                                    🗑️ Xoá
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td>
                            Chưa có người dùng nào
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
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} user`;
        } else {
            btnDelete.disabled = true;
            btnDelete.innerHTML = `🗑️ Xoá 0 user`;
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