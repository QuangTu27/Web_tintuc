
<a href="/Web_tintuc/crawler/vnexpress.php"
   target="_blank"
   class="btn btn-add"
   onclick="return confirm('Lấy tin mới từ VnExpress?')">
   🔄 Lấy tin tự động
</a>
<?php


// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// 1. KIỂM TRA QUYỀN
$isAdminOrEditor = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'editor');
$currentUserId = $_SESSION['admin_id'];

// Truy vấn danh sách
$sql_list = "SELECT n.*, c.name AS category_name, u.hoten AS author_name, u.username 
             FROM tbl_news n
             LEFT JOIN tbl_categories c ON n.category_id = c.id
             LEFT JOIN tbl_users u ON n.author_id = u.id
             ORDER BY n.id DESC";
$query_list = mysqli_query($conn, $sql_list);
?>

<div class="admin-header-inline">
    <h2 class="admin-title"></i> QUẢN LÝ TIN TỨC</h2>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div id="status-msg" class="alert alert-success">
        <?php
        switch ($_GET['msg']) {
            case 'added':
                echo "✅ Thêm bài viết thành công!";
                break;
            case 'updated':
                echo "✅ Cập nhật thành công!";
                break;
            case 'deleted':
                echo "🗑️ Đã xoá bài viết.";
                break;
            case 'approved':
                echo "🎉 Đã DUYỆT bài viết thành công!";
                break;
            case 'rejected':
                echo "⛔ Đã từ chối bài viết.";
                break;
            case 'hidden':
                echo "📁 Đã gỡ bài viết về bản nháp.";
                break;
        }
        ?>
    </div>
    <script>
        setTimeout(() => document.getElementById('status-msg').style.display = 'none', 3000);
    </script>
<?php endif; ?>

<form method="post" action="modules/tintuc/delete.php" onsubmit="return confirm('Bạn có chắc muốn xoá các bài viết đã chọn?')">

    <div class="list-actions">
        <a href="index.php?mod=tintuc&act=add" class="btn btn-add">➕ Viết bài mới</a>
        <?php if ($isAdminOrEditor): ?>
            <button type="submit" id="btnDeleteSelected" class="btn btn-delete btn-disabled" disabled>🗑️ Xoá đã chọn</button>
        <?php endif; ?>
    </div>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <?php if ($isAdminOrEditor): ?><th width="40"><input type="checkbox" id="checkAll"></th><?php endif; ?>
                    <th width="50">ID</th>
                    <th width="80">Ảnh</th>
                    <th>Tiêu đề & Thông tin</th>
                    <th>Danh mục</th>
                    <th width="120">Trạng thái</th>
                    <th width="150">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query_list)) {
                    $isMyPost = ($row['author_id'] == $currentUserId);
                    $canAction = ($isAdminOrEditor || $isMyPost);
                ?>
                    <tr>
                        <?php if ($isAdminOrEditor): ?>
                            <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                        <?php endif; ?>

                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="/Web_tintuc/images/news/<?= $row['hinhanh'] ?>"
                                style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;"
                                onerror="this.src='/Web_tintuc/images/news/default_news.png'">
                        </td>

                        <td>
                            <strong style="font-size: 14px; color: #333; display: block; margin-bottom: 5px;">
                                <?= htmlspecialchars($row['tieude']) ?>
                            </strong>
                            <div style="font-size: 12px; color: #888;">
                                <span>✍️ <?= htmlspecialchars($row['author_name'] ?? 'Ẩn danh') ?></span>
                                <span> | 📅 <?= date('d/m/y H:i', strtotime($row['ngaydang'])) ?></span>
                            </div>
                        </td>

                        <td><span class="badge badge-info"><?= htmlspecialchars($row['category_name']) ?></span></td>

                        <td style="white-space: nowrap;">
                            <?php
                            $stt_map = [
                                'ban_nhap' => ['text' => '📝 Nháp', 'color' => '#6c757d', 'bg' => '#e2e3e5'],
                                'cho_duyet' => ['text' => '⏳ Chờ duyệt', 'color' => '#856404', 'bg' => '#fff3cd'],
                                'da_dang' => ['text' => '✅ Đã đăng', 'color' => '#155724', 'bg' => '#d4edda'],
                                'bi_tu_choi' => ['text' => '❌ Từ chối', 'color' => '#721c24', 'bg' => '#f8d7da']
                            ];
                            $stt = $stt_map[$row['trangthai']] ?? $stt_map['ban_nhap'];
                            // Thêm display:inline-block và min-width để đều nhau
                            echo "<span class='status-badge' style='background:{$stt['bg']}; color:{$stt['color']}; white-space:nowrap; display:inline-block; min-width:90px; text-align:center;'>{$stt['text']}</span>";
                            ?>
                        </td>

                        <td>
                            <div class="action-buttons" style="display:flex; gap: 5px;">

                                <?php if ($isAdminOrEditor): ?>
                                    <?php if ($row['trangthai'] == 'cho_duyet' || $row['trangthai'] == 'ban_nhap'): ?>
                                        <a href="modules/tintuc/status.php?id=<?= $row['id'] ?>&action=approve"
                                            class="btn-icon btn-approve" title="Duyệt bài này"
                                            onclick="return confirm('Duyệt và đăng bài này lên web?')">
                                            ✅
                                        </a>
                                    <?php elseif ($row['trangthai'] == 'da_dang'): ?>
                                        <a href="modules/tintuc/status.php?id=<?= $row['id'] ?>&action=hide"
                                            class="btn-icon btn-hide" title="Gỡ bài (Về nháp)"
                                            onclick="return confirm('Gỡ bài viết này khỏi trang chủ?')">
                                            ⛔
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($canAction): ?>
                                    <a href="index.php?mod=tintuc&act=edit&id=<?= $row['id'] ?>" class="btn-icon btn-edit" title="Sửa">✏️</a>
                                    <a href="modules/tintuc/delete.php?id=<?= $row['id'] ?>" class="btn-icon btn-delete" title="Xóa" onclick="return confirm('Xoá bài này?')">🗑️</a>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</form>

<style>
    /* CSS NÚT THAO TÁC (TO HƠN) */
    .btn-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 36px;
        /* Tăng kích thước từ 32 lên 36 */
        height: 36px;
        /* Tăng kích thước từ 32 lên 36 */
        border-radius: 6px;
        text-decoration: none;
        font-size: 16px;
        /* Font to hơn chút */
        transition: 0.2s;
        border: none;
        /* Bỏ viền thừa */
    }

    /* Màu sắc nút Duyệt (Xanh lá) */
    .btn-approve {
        background: #d4edda;
    }

    .btn-approve:hover {
        background: #28a745;
    }

    /* Màu sắc nút Gỡ (Đỏ nhạt) */
    .btn-hide {
        background: #a1e4ecff;
    }

    .btn-hide:hover {
        background: #16c1d8ff;
    }

    /* Màu sắc nút Sửa (Xanh dương nhạt) */
    .btn-edit {
        background: #f7eebfff;
    }

    .btn-edit:hover {
        background: #f5d003ff;
        color: #fff;
    }

    /* Căn chỉnh cột trạng thái */
    .status-badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<?php if ($isAdminOrEditor): ?>
    <script>
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        const btnDelete = document.getElementById('btnDeleteSelected');

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('input[name="ids[]"]:checked').length;
            btnDelete.disabled = (checkedCount === 0);
            btnDelete.classList.toggle('btn-disabled', checkedCount === 0);
            btnDelete.innerHTML = checkedCount > 0 ? `🗑️ Xoá ${checkedCount} bài` : `🗑️ Xoá đã chọn`;
        }
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateDeleteButton();
        });
        checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteButton));
    </script>
<?php endif; ?>