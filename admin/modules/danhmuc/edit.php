<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');


if (!isset($_GET['id'])) {
    header("Location: /Web_tintuc/admin/index.php?mod=danhmuc&act=list");
    exit;
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM tbl_categories WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?mod=danhmuc&act=list");
    exit;
}

$category = mysqli_fetch_assoc($result);

/* =========================
   3. XỬ LÝ SUBMIT UPDATE
========================= */
if (isset($_POST['btn_update'])) {
    $name = trim($_POST['name']);

    $sqlUpdate = "
            UPDATE tbl_categories 
            SET name='$name'
            WHERE id=$id
        ";

    mysqli_query($conn, $sqlUpdate);
    header("Location: index.php?mod=danhmuc&act=list");
    exit;
}
?>

<div class="admin-container">

    <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
        ⬅ Quay lại
    </a>
    <h2 class="admin-title">
        Cập nhật danh mục
    </h2>

    <form method="post" class="admin-form">
        <div class="form-group">
            <label>Tên danh mục</label>
            <input type="text" name="name" value="<?= $category['name'] ?>" required>
        </div>

        <div class="btn-group-center">
            <button type="submit" name="btn_update" class="btn btn-OK">
                💾 Cập nhật
            </button>
            <a href="index.php?mod=danhmuc&act=list" class="btn btn-Cancel">
                Huỷ
            </a>
        </div>

    </form>

</div>