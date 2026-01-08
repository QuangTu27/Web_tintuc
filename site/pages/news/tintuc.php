<?php
$sql_all = "SELECT * FROM tbl_news WHERE trangthai = 'da_dang' ORDER BY id DESC";
$query_all = mysqli_query($conn, $sql_all);
?>
<div class="container">
    <h2 class="section-title">TẤT CẢ TIN TỨC</h2>
    <div class="news-grid-system">
        <?php while($row = mysqli_fetch_array($query_all)){ ?>
        <div class="card-item">
            <a href="index.php?p=chitiet_tintuc&id=<?php echo $row['id']; ?>">
                <div class="img-wrap"><img src="images/<?php echo $row['hinhanh']; ?>"></div>
                <div class="card-body">
                    <h4><?php echo $row['tieude']; ?></h4>
                    <p><?php echo substr($row['tomtat'], 0, 100); ?>...</p>
                </div>
            </a>
        </div>
        <?php } ?>
    </div>
</div>