<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách danh mục
$sql = "SELECT * FROM tbl_categories ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/Web_tintuc/admin/css/style_admin.css">
    <title>Danh sách danh mục</title>
</head>

<body>
    <h2>DANH SÁCH DANH MỤC</h2>

    <p>
        <a href="index.php?mod=danhmuc&act=add">➕ Thêm danh mục</a>
    </p>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Thao tác</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td>
                        <a href="index.php?mod=danhmuc&act=edit&id=<?= $row['id'] ?>">✏️ Sửa</a> |
                        <a href="index.php?mod=danhmuc&act=delete&id=<?= $row['id'] ?>"
                            onclick="return confirm('Bạn có chắc muốn xoá danh mục này?')">
                            🗑️ Xoá
                        </a>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='3'>Chưa có danh mục nào</td></tr>";
        }
        ?>
    </table>