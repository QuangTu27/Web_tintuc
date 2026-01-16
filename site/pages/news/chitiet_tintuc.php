<?php

/**
 * FILE: site/pages/news/chitiet_tintuc.php
 * Đã tích hợp hệ thống Like & Comment AJAX
 */

// 1. Đảm bảo kết nối DB luôn chạy
if (!isset($conn)) {
    $path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
    if (file_exists($path_connect)) {
        include_once $path_connect;
    }
}

// 2. Lấy ID và ép kiểu
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$row = null;

if ($id > 0 && isset($conn)) {
    // Tăng lượt xem
    mysqli_query($conn, "UPDATE tbl_news SET view_count = view_count + 1 WHERE id = $id");

    // Truy vấn lấy chi tiết + danh mục
    $sql = "SELECT n.*, 
                   c1.name as cat_name, c1.id as cat_id,
                   c2.name as parent_name, c2.id as parent_id
            FROM tbl_news n 
            LEFT JOIN tbl_categories c1 ON n.category_id = c1.id 
            LEFT JOIN tbl_categories c2 ON c1.parent_id = c2.id 
            WHERE n.id = $id LIMIT 1";

    $query = mysqli_query($conn, $sql);
    if ($query) {
        $row = mysqli_fetch_array($query);
    }
}

// 3. Nếu ID không tồn tại
if (!$row) {
    echo "<div style='padding:100px 20px; text-align:center; background:#f9f9f9;'>
            <h2 style='color:#d9534f;'>⚠️ Bài viết không tồn tại!</h2>
            <a href='index.php'>← Quay lại trang chủ</a>
          </div>";
    return;
}

// LOGIC LƯU TIN ĐÃ XEM
if ($id > 0 && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $cookie_name = 'viewed_news_' . $uid;
    $viewed_news = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];
    if (($key = array_search($id, $viewed_news)) !== false) unset($viewed_news[$key]);
    array_unshift($viewed_news, $id);
    $viewed_news = array_slice($viewed_news, 0, 12);
    setcookie($cookie_name, json_encode($viewed_news), time() + (86400 * 30), "/");
}
?>

<div class="container" style="max-width: 900px; margin: 30px auto; padding: 0 15px;">
    <article class="news-detail" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

        <nav style="font-size: 14px; color: #888; margin-bottom: 20px;">
            <?php if (!empty($row['parent_name'])): ?>
                <a href="index.php?p=danhmuc&id=<?= $row['parent_id'] ?>" style="color: #333; text-decoration: none;"><?= htmlspecialchars($row['parent_name']) ?></a>
                <span style="margin: 0 5px;">></span>
            <?php endif; ?>
            <a href="index.php?p=danhmuc&id=<?= $row['cat_id'] ?>" style="color: #333; text-decoration: none; font-weight: 600;"><?= htmlspecialchars($row['cat_name'] ?? 'Tin tức') ?></a>
        </nav>

        <h1 style="font-size: 36px; line-height: 1.3; color: #222; margin-bottom: 20px; font-weight: 800;"><?= htmlspecialchars($row['tieude']) ?></h1>

        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
            <div style="color: #999; font-size: 13px;">
                <span style="margin-right: 15px;">📅 <?= date('d/m/Y - H:i', strtotime($row['ngaydang'])) ?></span>
                <span>👁️ <?= number_format($row['view_count']) ?> lượt xem</span>
            </div>

            <div style="display: flex; gap: 10px;">
                <?php
                $uid = $_SESSION['user_id'] ?? 0;
                $count_like_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_likes WHERE news_id = $id");
                $total_likes = mysqli_fetch_assoc($count_like_res)['total'];
                $is_liked = false;
                if ($uid > 0) {
                    $check_l = mysqli_query($conn, "SELECT id FROM tbl_likes WHERE user_id=$uid AND news_id=$id");
                    $is_liked = (mysqli_num_rows($check_l) > 0);
                }
                ?>
                <button id="btn-like" data-id="<?= $id ?>"
                    style="cursor:pointer; border:1px solid #0a9e54; padding: 6px 15px; border-radius: 4px; background: <?= $is_liked ? '#0a9e54' : '#fff' ?>; color: <?= $is_liked ? '#fff' : '#0a9e54' ?>; font-size: 13px; font-weight: bold;">
                    <i class="<?= $is_liked ? 'fas' : 'far' ?> fa-thumbs-up"></i> Thích (<span id="like-count"><?= $total_likes ?></span>)
                </button>

                <?php if ($uid > 0):
                    $check_save = mysqli_query($conn, "SELECT id FROM tbl_bookmarks WHERE user_id=$uid AND news_id=$id");
                    $is_saved = (mysqli_num_rows($check_save) > 0);
                ?>
                    <a href="index.php?p=bookmark_add&news_id=<?= $id ?>"
                        style="background: <?= $is_saved ? '#e9ecef' : '#ffc107' ?>; border: 1px solid #ddd; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #000; font-size: 13px; font-weight: bold;">
                        <i class="<?= $is_saved ? 'fas fa-check' : 'far fa-bookmark' ?>"></i> <?= $is_saved ? 'Đã lưu' : 'Lưu tin' ?>
                    </a>
                <?php else: ?>
                    <a onclick="alert('Bạn cần đăng nhập để lưu tin!')" style="background: #f8f9fa; border: 1px solid #ddd; padding: 6px 15px; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px;" href="javascript:void(0)">
                        <i class="far fa-bookmark"></i> Lưu tin
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($row['tomtat'])): ?>
            <div class="sapo" style="font-size: 20px; font-weight: 700; line-height: 1.6; color: #444; margin-bottom: 30px; border-left: 5px solid #28a745; padding-left: 20px;">
                <?= nl2br(htmlspecialchars($row['tomtat'])) ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 30px;">
            <img src="images/news/<?= $row['hinhanh'] ?>" onerror="this.src='images/default_news.jpg'" style="max-width: 100%; height: auto; border-radius: 5px;">
        </div>

        <div class="main-content">
            <?= $row['noidung'] ?>
        </div>

        <div class="comment-section" style="margin-top: 50px; border-top: 2px solid #333; padding-top: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="far fa-comments"></i> Bình luận</h3>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="margin-bottom: 30px;">
                    <textarea id="comment-content" placeholder="Chia sẻ ý kiến của bạn..."
                        style="width: 100%; height: 80px; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                    <button id="btn-submit-comment" data-id="<?= $id ?>"
                        style="margin-top: 10px; background: #0a9e54; color: #fff; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Gửi bình luận
                    </button>
                </div>
            <?php else: ?>
                <p style="background: #fff3cd; padding: 15px; border-radius: 4px; border: 1px solid #ffeeba;">
                    Vui lòng <a href="index.php?p=dangnhap" style="font-weight: bold; color: #856404;">Đăng nhập</a> để tham gia bình luận.
                </p>
            <?php endif; ?>

            <div id="comment-list">
                <?php
                $q_comm = mysqli_query($conn, "SELECT * FROM tbl_comments WHERE news_id = $id AND status = 1 ORDER BY ngaybinh DESC");
                while ($c = mysqli_fetch_assoc($q_comm)): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f1f1;">
                        <strong style="color: #28a745;"><?= htmlspecialchars($c['ten_nguoi_binh']) ?></strong>
                        <small style="color: #bbb; margin-left: 10px;"><?= date('d/m/Y H:i', strtotime($c['ngaybinh'])) ?></small>
                        <p style="margin: 8px 0; color: #333;"><?= nl2br(htmlspecialchars($c['noidung'])) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <button id="btn-share"
            style="cursor:pointer; border:1px solid #007bff; padding: 6px 15px; border-radius: 4px; background: #fff; color: #007bff; font-size: 13px; font-weight: bold; display: flex; align-items: center; gap: 5px;">
            <i class="fas fa-share-alt"></i> Chia sẻ
        </button>

        <span id="share-success" style="display: none; color: #28a745; font-size: 12px; font-weight: bold; margin-left: 10px;">
            <i class="fas fa-check"></i> Đã sao chép link!
        </span>
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="font-weight: bold; color: #000;">Nguồn: TINTUC24H</p>
            <a href="javascript:history.back()" style="color: #007bff; text-decoration: none;">← Quay lại trang trước</a>
        </div>
    </article>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        $(document).on('click', '#btn-like', function(e) {
            e.preventDefault();
            const newsId = $(this).data('id');
            const btn = $(this);
            console.log("Đã bấm Like bài viết:", newsId);

            $.post('site/pages/news/like.php', {
                news_id: newsId
            }, function(data) {
                console.log("Server trả về:", data);
                try {
                    const res = JSON.parse(data);
                    if (res.status === 'success') {
                        $('#like-count').text(res.new_count);
                        if (res.action === 'liked') {
                            btn.css({
                                'background': '#0a9e54',
                                'color': '#fff'
                            });
                            btn.find('i').attr('class', 'fas fa-thumbs-up');
                        } else {
                            btn.css({
                                'background': '#fff',
                                'color': '#0a9e54'
                            });
                            btn.find('i').attr('class', 'far fa-thumbs-up');
                        }
                    } else {
                        alert(res.message);
                    }
                } catch (e) {
                    console.error("Lỗi phân tích JSON:", e);
                    alert("Có lỗi xảy ra, kiểm tra Console!");
                }
            });
        });
        $('#btn-submit-comment').click(function() {
            const content = $('#comment-content').val();
            const newsId = $(this).data('id');
            if (content.trim() === '') {
                alert('Vui lòng nhập nội dung!');
                return;
            }

            $.post('site/pages/news/comment.php', {
                news_id: newsId,
                noidung: content
            }, function(data) {
                try {
                    const res = JSON.parse(data);
                    if (res.status === 'success') {
                        $('#comment-list').prepend(res.html);
                        $('#comment-content').val('');
                    } else {
                        alert(res.message);
                    }
                } catch (e) {
                    console.error("Lỗi phản hồi:", data);
                }
            });
        });
    });

    $('#btn-share').click(function() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            $('#share-success').fadeIn();
            $('#btn-share').css({
                'background': '#007bff',
                'color': '#fff'
            });
            setTimeout(function() {
                $('#share-success').fadeOut();
                $('#btn-share').css({
                    'background': '#fff',
                    'color': '#007bff'
                });
            }, 2000);
        }).catch(function(err) {
            alert('Lỗi: Không thể copy tự động!');
        });
    });
</script>