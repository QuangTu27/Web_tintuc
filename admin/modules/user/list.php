<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM tbl_users";
if ($search != '') {
    $sql .= " WHERE username LIKE '%$search%' 
              OR hoten LIKE '%$search%' 
              OR email LIKE '%$search%'";
}
$sql .= " ORDER BY id ASC";

$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ TÀI KHOẢN</h2>

<?php if (isset($_GET['msg'])): ?>
    <div id="status-msg" class="alert <?php echo (in_array($_GET['msg'], ['added', 'updated'])) ? 'alert-success' : 'alert-warning'; ?>">
        <?php
        switch ($_GET['msg']) {
            case 'added':
                echo "✅ Thêm người dùng mới thành công!";
                break;
            case 'updated':
                echo "✅ Cập nhật thông tin thành công!";
                break;
            case 'deleted':
                echo "🗑️ Đã xoá người dùng.";
                break;
            case 'deleted_multiple':
                echo "🗑️ Đã xoá các tài khoản đã chọn.";
                break;
        }
        ?>
    </div>
    <!-- Thông báo trạng thái -->
    <script>
        setTimeout(function() {
            var msg = document.getElementById('status-msg');
            if (msg) msg.style.display = 'none';
        }, 3000);
    </script>
<?php endif; ?>

<div class="admin-toolbar">
    <div class="admin-controls">
        <a href="index.php?mod=user&act=add" class="btn btn-add">➕ Thêm người dùng</a>
        <button type="submit"
            form="mainForm"
            id="btnDeleteSelected"
            class="btn btn-delete btn-disabled"
            disabled>
            🗑️ Xoá 0 user
        </button>
    </div>

    <div class="search-box">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="mod" value="user">
            <input type="hidden" name="act" value="list">

            <input type="text" name="search"
                placeholder="Tìm kiếm user..."
                value="<?= htmlspecialchars($search) ?>"
                class="search-input">

            <button type="submit" class="btn btn-OK">🔍 Tìm Kiếm</button>

            <?php if ($search != ''): ?>
                <a href="index.php?mod=user&act=list" class="btn btn-view">🔄 Làm Mới</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<form method="post"
    id="mainForm"
    action="index.php?mod=user&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các user đã chọn?')">

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
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
                            <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['hoten']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= $row['role'] ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-edit" href="index.php?mod=user&act=edit&id=<?= $row['id'] ?>">✏️ Sửa</a>
                                    <a class="btn btn-delete" href="index.php?mod=user&act=delete&id=<?= $row['id'] ?>"
                                        onclick="return confirm('Bạn có chắc muốn xoá user này?')">🗑️ Xoá</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-table-cell" style="text-align: center; padding: 40px;">
                            <?php if ($search != ''): ?>
                                Không tìm thấy kết quả cho: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                            <?php else: ?>
                                Danh sách người dùng trống.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    const checkAll = document.getElementById('checkAll');
    const btnDelete = document.getElementById('btnDeleteSelected');

    function updateDeleteButton() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]:checked');
        const checkedCount = checkboxes.length;

        if (checkedCount > 0) {
            btnDelete.disabled = false;
            btnDelete.classList.remove('btn-disabled');
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} user`;
        } else {
            btnDelete.disabled = true;
            btnDelete.classList.add('btn-disabled');
            btnDelete.innerHTML = `🗑️ Xoá 0 user`;
        }
    }

    checkAll.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });

    document.addEventListener('change', function(e) {
        if (e.target.name === 'ids[]') {
            updateDeleteButton();
        }
    });
</script>