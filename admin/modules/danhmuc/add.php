<?php
// Kết nối CSDL
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');
if ($_SESSION['admin_role'] !== 'admin') {
    die('Bạn không có quyền thao tác danh mục');
}
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
<div class="admin-container">
    <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>

    <h2 class="admin-title">Thêm danh mục</h2>

    <?php if (isset($error)) { ?>
        <p style="color:red; margin-bottom:15px;">
            <?= $error ?>
        </p>
    <?php } ?>

    <form method="post" class="admin-form">

        <div class="form-group">
            <label>Tên danh mục</label>
            <input type="text" name="name" required>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_add" class="btn btn-OK">
                Thêm danh mục
            </button>

            <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
                Huỷ
            </a>
        </div>

    </form>
</div>