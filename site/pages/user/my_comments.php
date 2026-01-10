<?php
// BẮT BUỘC ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert-box'>⚠️ Vui lòng đăng nhập để xem lịch sử bình luận.</div>";
    return;
}

$user_id = $_SESSION['user_id'];

// --- XỬ LÝ XÓA BÌNH LUẬN ---
if (isset($_GET['del_cmt'])) {
    $del_id = intval($_GET['del_cmt']);
    // Chỉ xóa nếu bình luận đó thuộc về user đang đăng nhập
    $sql_check = "SELECT id FROM tbl_comments WHERE id = $del_id AND user_id = $user_id";
    if (mysqli_num_rows(mysqli_query($conn, $sql_check)) > 0) {
        mysqli_query($conn, "DELETE FROM tbl_comments WHERE id = $del_id");
        echo "<script>alert('Đã xóa bình luận thành công!'); window.location.href='index.php?p=thongtincanhan&act=my_comments';</script>";
    } else {
        echo "<script>alert('Lỗi: Bạn không có quyền xóa bình luận này!');</script>";
    }
}

// --- LẤY DANH SÁCH BÌNH LUẬN ---
// JOIN bảng comments với news để lấy thông tin bài viết
$sql = "SELECT c.*, n.tieude, n.hinhanh, n.id as news_id
        FROM tbl_comments c
        JOIN tbl_news n ON c.news_id = n.id
        WHERE c.user_id = $user_id
        ORDER BY c.ngaybinh DESC";
$query = mysqli_query($conn, $sql);
?>

<style>
    .news-list-vertical {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .news-item-row {
        display: flex;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        transition: 0.2s;
        text-decoration: none;
        color: #333;
        position: relative;
    }

    .news-item-row:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #17a2b8;
        /* Màu xanh cyan cho comment */
    }

    .row-thumb {
        width: 200px;
        height: 140px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .row-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s;
    }


    .row-body {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .row-body h4 {
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
        color: #333;
    }

    .row-body h4:hover {
        color: #007bff;
    }

    /* Style riêng cho phần nội dung bình luận */
    .my-comment-content {
        background: #f1f8ff;
        padding: 10px;
        border-radius: 6px;
        font-size: 14px;
        color: #444;
        font-style: italic;
        border-left: 3px solid #007bff;
        margin-bottom: 10px;
    }

    .row-meta {
        font-size: 12px;
        color: #888;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    /* Hiện */
    .status-hidden {
        background: #fff3cd;
        color: #856404;
    }

    /* Ẩn */

    .btn-delete-cmt {
        color: #dc3545;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #dc3545;
        padding: 3px 10px;
        border-radius: 4px;
        transition: 0.2s;
    }

    .btn-delete-cmt:hover {
        background: #dc3545;
        color: #fff;
    }

    @media (max-width: 600px) {
        .news-item-row {
            flex-direction: column;
        }

        .row-thumb {
            width: 100%;
            height: 160px;
        }
    }
</style>

<div style="margin-top: 10px; margin-bottom: 50px;">
    <h3 style="border-left: 4px solid #17a2b8; padding-left: 10px; margin-bottom: 20px; color: #333;">
        Ý KIẾN CỦA BẠN
    </h3>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <div class="news-list-vertical">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>

                <div class="news-item-row">
                    <a href="index.php?p=chitiet_tintuc&id=<?= $row['news_id'] ?>" class="row-thumb">
                        <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'">
                    </a>

                    <div class="row-body">
                        <a href="index.php?p=chitiet_tintuc&id=<?= $row['news_id'] ?>" style="text-decoration:none;">
                            <h4>Bài viết: <?= htmlspecialchars($row['tieude']) ?></h4>
                        </a>

                        <div class="my-comment-content">
                            "<?= htmlspecialchars($row['noidung']) ?>"
                        </div>

                        <div class="row-meta">
                            <div>
                                <span>📅 <?= date('d/m/Y H:i', strtotime($row['ngaybinh'])) ?></span>
                                <span style="margin: 0 5px;">|</span>
                                <?php if ($row['status'] == 1): ?>
                                    <span class="status-badge status-active">✅ Đã duyệt</span>
                                <?php else: ?>
                                    <span class="status-badge status-hidden">⏳ Chờ duyệt</span>
                                <?php endif; ?>
                            </div>

                            <a href="index.php?p=thongtincanhan&act=my_comments&del_cmt=<?= $row['id'] ?>"
                                onclick="return confirm('Bạn muốn xóa bình luận này?')"
                                class="btn-delete-cmt">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">
            <i class="far fa-comments" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
            <p style="color: #666;">Bạn chưa bình luận vào bài viết nào.</p>
        </div>
    <?php endif; ?>
</div>