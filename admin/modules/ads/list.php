<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. XỬ LÝ LOGIC TÌM KIẾM
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT * FROM tbl_ads";
if ($search != '') {
    // Tìm kiếm theo Tiêu đề, Vị trí hoặc Link
    $sql .= " WHERE title LIKE '%$search%' 
              OR position LIKE '%$search%' 
              OR link LIKE '%$search%'";
}
$sql .= " ORDER BY id ASC";

$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ QUẢNG CÁO</h2>

<?php if (isset($_GET['msg'])): ?>
    <div id="status-msg" class="alert <?php echo (in_array($_GET['msg'], ['added', 'updated'])) ? 'alert-success' : 'alert-warning'; ?>"
        style="padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: bold;">
        <?php
        switch ($_GET['msg']) {
            case 'added':
                echo "✅ Thêm quảng cáo mới thành công!";
                break;
            case 'updated':
                echo "✅ Cập nhật quảng cáo thành công!";
                break;
            case 'deleted':
                echo "🗑️ Đã xoá quảng cáo.";
                break;
            case 'deleted_multiple':
                echo "🗑️ Đã xoá các quảng cáo đã chọn.";
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

<div class="admin-toolbar">
    <div class="admin-controls">
        <a href="index.php?mod=ads&act=add" class="btn btn-add">➕ Thêm quảng cáo</a>
        <button type="submit"
            form="mainForm"
            id="btnDeleteSelected"
            class="btn btn-delete btn-disabled"
            disabled>
            🗑️ Xoá 0 quảng cáo
        </button>
    </div>

    <div class="search-box">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="mod" value="ads">
            <input type="hidden" name="act" value="list">

            <input type="text" name="search"
                placeholder="Tìm tiêu đề, vị trí..."
                value="<?= htmlspecialchars($search) ?>"
                class="search-input">

            <button type="submit" class="btn btn-OK">🔍 Tìm Kiếm</button>

            <?php if ($search != ''): ?>
                <a href="index.php?mod=ads&act=list" class="btn btn-view">🔄 Làm Mới</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<form method="post"
    id="mainForm"
    action="index.php?mod=ads&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các quảng cáo đã chọn?')">

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Media</th>
                    <th>Link</th>
                    <th>Vị trí</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td class="ads-media" style="width: 150px;">
                                <?php if ($row['media_type'] === 'video'): ?>
                                    <video width="120" height="auto" autoplay muted loop playsinline style="border-radius: 4px;">
                                        <source src="/Web_tintuc/images/ads/<?= $row['media_file'] ?>" type="video/mp4">
                                    </video>
                                <?php else: ?>
                                    <img src="/Web_tintuc/images/ads/<?= $row['media_file'] ?>"
                                        style="width: 120px; height: auto; border-radius: 4px;"
                                        alt="<?= htmlspecialchars($row['title']) ?>">
                                <?php endif; ?>
                            </td>
                            <td><a href="<?= $row['link'] ?>" target="_blank">Xem link</a></td>
                            <td><?= htmlspecialchars($row['position']) ?></td>
                            <td>
                                <span class="status-badge <?= $row['status'] ?>">
                                    <?= $row['status'] === 'hien' ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn btn-edit" href="index.php?mod=ads&act=edit&id=<?= $row['id'] ?>">✏️ Sửa</a>
                                    <a class="btn btn-delete" href="index.php?mod=ads&act=delete&id=<?= $row['id'] ?>"
                                        onclick="return confirm('Bạn có chắc muốn xoá quảng cáo này?')">🗑️ Xoá</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-table-cell" style="text-align: center; padding: 40px; color: #666;">
                            <?php if ($search != ''): ?>
                                Không tìm thấy quảng cáo nào khớp với: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                            <?php else: ?>
                                Danh sách quảng cáo trống.
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
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} quảng cáo`;
        } else {
            btnDelete.disabled = true;
            btnDelete.classList.add('btn-disabled');
            btnDelete.innerHTML = `🗑️ Xoá 0 quảng cáo`;
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