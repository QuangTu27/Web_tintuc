<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');
$sql = "SELECT * FROM tbl_ads ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="admin-title">QUẢN LÝ QUẢNG CÁO</h2>

<form method="post"
    action="index.php?mod=ads&act=delete"
    onsubmit="return confirm('Bạn có chắc muốn xoá các quảng cáo đã chọn?')">
    <p class="list-actions">
        <a href="index.php?mod=ads&act=add" class="btn btn-add">➕ Thêm quảng cáo</a>
        <button type="submit"
            id="btnDeleteSelected"
            class="btn btn-delete"
            disabled>
            🗑️ Xoá 0 quảng cáo
        </button>
    </p>
    <table>
        <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Hình ảnh</th>
            <th>Link</th>
            <th>Vị trí</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                <td><?= $row['id'] ?></td>
                <td><?= $row['title'] ?></td>
                <td class="ads-image">
                    <img src="/Web_tintuc/images/ads/<?= ($row['image']) ?>">
                </td>
                <td><?= $row['link'] ?></td>
                <td><?= $row['position'] ?></td>
                <td>
                    <?= $row['status'] === 'hien' ? 'Hiển thị' : 'Ẩn' ?>
                </td>
                <td>
                    <a class="btn btn-edit"
                        href="index.php?mod=ads&act=edit&id=<?= $row['id'] ?>">
                        ✏️ Sửa
                    </a>
                    <a class="btn btn-delete"
                        href="index.php?mod=ads&act=delete&id=<?= $row['id'] ?>"
                        onclick="return confirm('Bạn có chắc muốn xoá quảng cáo này?')">
                        🗑️ Xoá
                    </a>
                </td>

            </tr>
        <?php } ?>
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
            btnDelete.innerHTML = `🗑️ Xoá ${checkedCount} quảng cáo`;
        } else {
            btnDelete.disabled = true;
            btnDelete.innerHTML = `🗑️ Xoá 0 quảng cáo`;
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