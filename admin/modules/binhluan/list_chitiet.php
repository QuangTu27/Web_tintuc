<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

if (!isset($_GET['news_id'])) {
    echo "<p>❌ Chưa chọn bài viết để xem bình luận</p>";
    exit;
}

$news_id = (int)$_GET['news_id'];

$sql_news = "SELECT tieude FROM tbl_news WHERE id = $news_id";
$res_news = mysqli_query($conn, $sql_news);
$news = mysqli_fetch_assoc($res_news);

$sql = "SELECT *
        FROM tbl_comments
        WHERE news_id = $news_id
        ORDER BY parent_id ASC, create_at DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="admin-content">
    <a href="index.php?mod=binhluan&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>
    <h2 class="admin-title">
        <span class="sub-title">
            Bài viết: <?= htmlspecialchars($news['tieude']) ?>
        </span>
    </h2>

    <div class="table-scroll">

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người bình luận</th>
                    <th>Nội dung</th>
                    <th>Loại</th>
                    <th>Ngày bình</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($res) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr class="<?= $row['parent_id'] ? 'comment-child' : '' ?>">
                            <td><?= $row['id'] ?></td>

                            <td>
                                <?= $row['ten_nguoi_binh'] ?: 'Ẩn danh' ?>
                                <?php if ($row['user_id']): ?>
                                    <br>
                                    <small>User ID: <?= $row['user_id'] ?></small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['noidung']) ?>
                            </td>

                            <td>
                                <?= $row['parent_id'] ? 'Trả lời' : 'Gốc' ?>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($row['create_at'])) ?>
                            </td>

                            <td>
                                <a class="btn btn-delete"
                                    href="index.php?mod=binhluan&act=delete&id=<?= $row['id'] ?>&news_id=<?= $news_id ?>"
                                    onclick="return confirm('Xoá bình luận này?')">
                                    🗑️ Xoá
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Chưa có bình luận nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>