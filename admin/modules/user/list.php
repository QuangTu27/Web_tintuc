<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách user
$sql = "SELECT * FROM tbl_users ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/Web_tintuc/admin/css/style_admin.css">
    <title>Danh sách người dùng</title>

</head>

<body>

    <h2>DANH SÁCH NGƯỜI DÙNG</h2>

    <p>
        <a href="index.php?mod=user&act=add">➕ Thêm người dùng</a>
    </p>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Quyền</th>
            <th>Thao tác</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['hoten'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['role'] ?></td>
                    <td>
                        <a href="index.php?mod=user&act=edit&id=<?= $row['id'] ?>">✏️ Sửa</a> |
                        <a href="index.php?mod=user&act=delete&id=<?= $row['id'] ?>"
                            onclick="return confirm('Bạn có chắc muốn xoá user này?')">
                            🗑️ Xoá
                        </a>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='7'>Chưa có người dùng nào</td></tr>";
        }
        ?>
    </table>

</body>

</html>