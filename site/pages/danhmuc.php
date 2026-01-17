<?php
// 1. KẾT NỐI & LẤY ID
if (!isset($conn)) include_once($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. LẤY THÔNG TIN DANH MỤC HIỆN TẠI
$sql_cat = "SELECT * FROM tbl_categories WHERE id = $id";
$res_cat = mysqli_query($conn, $sql_cat);
$current_cat = mysqli_fetch_assoc($res_cat);

if (!$current_cat) {
    echo "<div class='container' style='padding:50px; text-align:center;'><h3>❌ Danh mục không tồn tại!</h3></div>";
    return;
}

// --- LOGIC XỬ LÝ HEADER DANH MỤC ---
$parent_id = 0;
$parent_name = "";
$parent_link_id = 0;
$sub_categories = [];

if ($current_cat['parent_id'] == 0) {
    // A. Nếu đang xem Danh mục CHA
    $parent_id = $id;
    $parent_name = $current_cat['name'];
    $parent_link_id = $id;
    $sql_sub = "SELECT * FROM tbl_categories WHERE parent_id = $id ORDER BY id ASC";
} else {
    // B. Nếu đang xem Danh mục CON
    $parent_id = $current_cat['parent_id'];
    $sql_parent = "SELECT * FROM tbl_categories WHERE id = $parent_id";
    $res_parent = mysqli_query($conn, $sql_parent);
    $row_parent = mysqli_fetch_assoc($res_parent);

    $parent_name = $row_parent['name'];
    $parent_link_id = $row_parent['id'];
    $sql_sub = "SELECT * FROM tbl_categories WHERE parent_id = $parent_id ORDER BY id ASC";
}

$res_sub = mysqli_query($conn, $sql_sub);
while ($sub = mysqli_fetch_assoc($res_sub)) {
    $sub_categories[] = $sub;
}

// 3. LOGIC LẤY TIN
if ($current_cat['parent_id'] == 0) {
    $list_cat_ids = [$id];
    foreach ($sub_categories as $sub) {
        $list_cat_ids[] = $sub['id'];
    }
    $str_ids = implode(',', $list_cat_ids);
} else {
    $str_ids = $id;
}

// 4. PHÂN TRANG & QUERY TIN
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(id) as total FROM tbl_news WHERE category_id IN ($str_ids) AND trangthai='da_dang'";
$res_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($res_count);
$total_records = $row_count['total'];
$total_pages = ceil($total_records / $limit);

$sql_news = "SELECT * FROM tbl_news 
             WHERE category_id IN ($str_ids) AND trangthai='da_dang' 
             ORDER BY ngaydang DESC 
             LIMIT $start, $limit";
$query_news = mysqli_query($conn, $sql_news);

// 6. TIN XEM NHIỀU SIDEBAR
$sql_top = "SELECT * FROM tbl_news WHERE trangthai='da_dang' ORDER BY view_count DESC LIMIT 5";
$query_top = mysqli_query($conn, $sql_top);
?>

<link rel="stylesheet" href="site/css/category.css?v=<?= time() ?>">

<div class="container" style="margin-top: 20px;">
    <div class="cat-header-wrapper">
        <h1 class="cat-title-large">
            <a href="index.php?p=danhmuc&id=<?= $parent_link_id ?>" title="<?= $parent_name ?>">
                <?= htmlspecialchars($parent_name) ?>
            </a>
        </h1>

        <?php if (!empty($sub_categories)): ?>
            <ul class="cat-sub-nav">
                <?php foreach ($sub_categories as $sub):
                    $isActive = ($sub['id'] == $id) ? 'active' : '';
                ?>
                    <li>
                        <a href="index.php?p=danhmuc&id=<?= $sub['id'] ?>" class="<?= $isActive ?>">
                            <?= htmlspecialchars($sub['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="container main-wrapper" style="display: flex; gap: 30px; margin-top: 20px; max-width: 1200px; align-items: flex-start;">

    <div class="content-area" style="flex: 2;">

        <?php if ($total_records > 0): ?>
            <div class="category-news-list">
                <?php while ($row = mysqli_fetch_assoc($query_news)): ?>
                    <div class="cat-news-item">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>" class="cat-thumb">
                            <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'">
                        </a>
                        <div class="cat-info">
                            <h3>
                                <a href="index.php?p=chitiet_tintuc&id=<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['tieude']) ?>
                                </a>
                            </h3>
                            <p class="cat-sapo">
                                <?= mb_substr(strip_tags($row['tomtat']), 0, 150, 'UTF-8') ?>...
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    if ($page > 1) echo '<a href="index.php?p=danhmuc&id=' . $id . '&page=' . ($page - 1) . '">«</a>';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active = ($i == $page) ? 'active' : '';
                        echo '<a href="index.php?p=danhmuc&id=' . $id . '&page=' . $i . '" class="' . $active . '">' . $i . '</a>';
                    }
                    if ($page < $total_pages) echo '<a href="index.php?p=danhmuc&id=' . $id . '&page=' . ($page + 1) . '">»</a>';
                    ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="padding: 30px; background: #f9f9f9; text-align: center; border-radius: 8px;">
                <p>📭 Chưa có bài viết nào.</p>
            </div>
        <?php endif; ?>
    </div>

    <aside style="flex: 0 0 300px; max-width: 300px; position: sticky; top: 20px;">
        <div style="border: 1px solid #eee; border-radius: 4px; overflow: hidden;">
            <h3 style="background: #f7f7f7; color: #333; padding: 10px 15px; margin: 0; font-size: 14px; text-transform: uppercase; border-bottom: 1px solid #eee; font-weight: bold;">
                Đọc nhiều
            </h3>
            <div style="padding: 15px; background: #fff;">
                <?php while ($top = mysqli_fetch_assoc($query_top)): ?>
                    <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                        <img src="images/news/<?= $top['hinhanh'] ?>" style="width: 70px; height: 50px; object-fit: cover; flex-shrink: 0;" onerror="this.src='images/default_news.jpg'">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $top['id'] ?>" style="font-size: 13px; text-decoration: none; color: #333; font-weight: 500; line-height: 1.4;">
                            <?= mb_substr($top['tieude'], 0, 50, 'UTF-8') ?>...
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </aside>

</div>