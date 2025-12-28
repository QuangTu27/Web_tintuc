<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$sql = "
    SELECT 
        n.id,
        n.tieude,
        n.ngaydang,
        COUNT(c.id) AS total_comments
    FROM tbl_news n
    LEFT JOIN tbl_comments c ON n.id = c.news_id
    GROUP BY n.id
    ORDER BY n.ngaydang DESC
";
$res = mysqli_query($conn, $sql);
?>

<div class="admin-content">

    <h2 class="admin-title">QUẢN LÝ BÌNH LUẬN THEO BÀI VIẾT</h2>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề bài viết</th>
                    <th>Ngày đăng</th>
                    <th>Tổng bình luận</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($res) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>

                            <td>
                                <?= htmlspecialchars(mb_strimwidth($row['tieude'], 0, 60, '...')) ?>
                            </td>

                            <td>
                                <?= date('d/m/Y', strtotime($row['ngaydang'])) ?>
                            </td>

                            <td>
                                <strong><?= $row['total_comments'] ?></strong>
                            </td>

                            <td>
                                <?php if ($row['total_comments'] > 0): ?>
                                    <a class="btn btn-view"
                                        href="index.php?mod=binhluan&act=list_chitiet&news_id=<?= $row['id'] ?>">
                                        💬 Xem bình luận
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Chưa có bài viết nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>