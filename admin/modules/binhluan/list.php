<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (isset($_SESSION['admin_id'])) {
    $current_user_id = $_SESSION['admin_id'];
} elseif (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
} elseif (isset($_SESSION['id'])) {
    $current_user_id = $_SESSION['id'];
} else {
    $current_user_id = 0;
}

$current_role = $_SESSION['admin_role'] ?? '';

$isAdmin  = ($current_role === 'admin');
$isEditor = ($current_role === 'editor');

if (!$isAdmin && !$isEditor) {
    die("Bạn không có quyền truy cập.");
}


$sql_cats = "SELECT id, name FROM tbl_categories WHERE parent_id = 0 ORDER BY name ASC";
$res_cats = mysqli_query($conn, $sql_cats);

$filter_cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
$keyword       = isset($_GET['q']) ? trim($_GET['q']) : '';

$conditions = [];

if ($isEditor) {

    $conditions[] = "(cat.manager_id = $current_user_id OR parent_cat.manager_id = $current_user_id)";
}

if ($filter_cat_id > 0) {
    $conditions[] = "(n.category_id = $filter_cat_id OR cat.parent_id = $filter_cat_id)";
}

if ($keyword != '') {
    $search = mysqli_real_escape_string($conn, $keyword);
    $conditions[] = "n.tieude LIKE '%$search%'";
}

$where_sql = "";
if (!empty($conditions)) {
    $where_sql = " WHERE " . implode(" AND ", $conditions);
}

$sql = "
    SELECT 
        n.id,
        n.tieude,
        n.ngaydang,
        cat.name AS cat_name,
        parent_cat.name AS parent_name,
        COUNT(c.id) AS total_comments
    FROM tbl_news n
    LEFT JOIN tbl_categories cat ON n.category_id = cat.id
    LEFT JOIN tbl_categories parent_cat ON cat.parent_id = parent_cat.id 
    LEFT JOIN tbl_comments c ON n.id = c.news_id
    $where_sql
    GROUP BY n.id
    ORDER BY n.ngaydang DESC
";

$res = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <h2 class="admin-title">QUẢN LÝ BÌNH LUẬN</h2>

    <div class="admin-toolbar">
        <div class="admin-controls">

        </div>

        <div class="search-box">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="mod" value="binhluan">
                <input type="hidden" name="act" value="list">

                <select name="cat_id" class="search-input" style="width: 200px; cursor: pointer;" onchange="this.form.submit()">
                    <option value="0">-- Tất cả Chuyên mục --</option>
                    <?php
                    if (mysqli_num_rows($res_cats) > 0) {
                        mysqli_data_seek($res_cats, 0);
                        while ($cat = mysqli_fetch_assoc($res_cats)):
                            $selected = ($filter_cat_id == $cat['id']) ? 'selected' : '';
                    ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                                📁 <?= $cat['name'] ?>
                            </option>
                    <?php endwhile;
                    } ?>
                </select>

                <input type="text" name="q"
                    placeholder="Tìm bài viết..."
                    value="<?= htmlspecialchars($keyword) ?>"
                    class="search-input">

                <button type="submit" class="btn btn-OK">🔍 Lọc</button>

                <?php if ($filter_cat_id > 0 || $keyword != ''): ?>
                    <a href="index.php?mod=binhluan&act=list" class="btn btn-view" title="Làm mới">🔄</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Tiêu đề bài viết</th>
                    <th>Chuyên mục</th>
                    <th>Ngày đăng</th>
                    <th width="100">Bình luận</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($res && mysqli_num_rows($res) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>

                            <td>
                                <a href="../index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" target="_blank" style="text-decoration: none; color: #333; font-weight: 500;">
                                    <?= htmlspecialchars(mb_strimwidth($row['tieude'], 0, 60, '...')) ?>
                                </a>
                                <br>
                                <small style="color: #999;">#<?= $row['id'] ?></small>
                            </td>

                            <td>
                                <?php if (!empty($row['parent_name'])): ?>
                                    <span style="font-size: 11px; color: #888;"><?= htmlspecialchars($row['parent_name']) ?> ></span><br>
                                    <span class="status-badge" style="background: #eef2f5; color: #444;">
                                        <?= htmlspecialchars($row['cat_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge" style="background: #e3f2fd; color: #0d47a1;">
                                        <?= htmlspecialchars($row['cat_name'] ?? 'Chưa phân loại') ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td><?= date('d/m/Y', strtotime($row['ngaydang'])) ?></td>

                            <td style="text-align: center;">
                                <?php if ($row['total_comments'] > 0): ?>
                                    <span style="display:inline-block; padding: 4px 10px; background: #ffebee; color: #c62828; border-radius: 20px; font-weight: bold;">
                                        <?= $row['total_comments'] ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #ccc;">0</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['total_comments'] > 0): ?>
                                    <a class="btn btn-view"
                                        href="index.php?mod=binhluan&act=list_chitiet&news_id=<?= $row['id'] ?>">
                                        💬 Chi tiết
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>Trống</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-table-cell" style="text-align: center; padding: 40px;">
                            <?php if ($isEditor): ?>
                                Không có bài viết nào thuộc danh mục bạn quản lý (ID: <?= $current_user_id ?>).
                            <?php else: ?>
                                Không tìm thấy dữ liệu.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>