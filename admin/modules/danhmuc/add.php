<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Xử lý khi submit form
if (isset($_POST['btn_add'])) {
    $name = trim($_POST['name']);

    // Kiểm tra tên danh mục có rỗng không
    if (empty($name)) {
        $error = "Tên danh mục không được để trống";
    } else {
        // Kiểm tra tên danh mục đã tồn tại chưa
        $check = "SELECT * FROM tbl_categories WHERE name='$name'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Tên danh mục đã tồn tại";
        } else {
            // Thêm danh mục mới
            $sql = "INSERT INTO tbl_categories(name) VALUES ('$name')";
            mysqli_query($conn, $sql);
            header('Location: index.php?mod=danhmuc&act=list');
            exit();
        }
    }
}
?>
<div>
    <h2>
        <a href="index.php?mod=danhmuc&act=list">
            <i class="btn_back"></i> Quay lại
        </a>
    </h2>
</div>
<h2>Thêm danh mục</h2>
<?php if (isset($error)) { ?>
    <p style="color:red"><?= $error ?></p>
<?php } ?>
<form method="post">
    <table cellpadding="5">
        <tr>
            <td>Tên danh mục</td>
            <td><input type="text" name="name" required></td>
        </tr>

        <tr>
            <td></td>
            <td><button type="submit" name="btn_add">Thêm danh mục</button></td>
        </tr>
    </table>