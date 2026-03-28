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
                    Vui lòng <a href="javascript:void(0)" onclick="openAuthModal('login')" style="font-weight: bold; color: #856404;">Đăng nhập</a> để tham gia bình luận.
                </p>
            <?php endif; ?>

            <div id="comment-list">
                <?php
                $q_comm = mysqli_query($conn, "SELECT * FROM tbl_comments WHERE news_id = $id AND status IN (1,2) ORDER BY ngaybinh ASC");
                $all_comments = [];
                while ($row_c = mysqli_fetch_assoc($q_comm)) {
                    $all_comments[] = $row_c;
                }
                
                $parents = [];
                $children = [];
                foreach ($all_comments as $c) {
                    if (empty($c['parent_id'])) {
                        array_unshift($parents, $c);
                    } else {
                        if (!isset($children[$c['parent_id']])) {
                            $children[$c['parent_id']] = [];
                        }
                        $children[$c['parent_id']][] = $c;
                    }
                }

                foreach ($parents as $c): 
                    $thoigian = date('d/m/Y H:i', strtotime($c['ngaybinh']));
                ?>
                    <div class="comment-item" id="comment-<?= $c['id'] ?>" style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f1f1;">
                        <div class="comment-content">
                            <strong style="color: #28a745;"><?= htmlspecialchars($c['ten_nguoi_binh']) ?></strong>
                            <small style="color: #bbb; margin-left: 10px;"><?= $thoigian ?></small>
                            <div class="comment-text" id="text-<?= $c['id'] ?>" style="margin: 8px 0; color: #333;">
                                <?php if($c['status'] == 2): ?>
                                    <em style="color: #999;">Bình luận này đã bị xoá.</em>
                                <?php else: ?>
                                    <?= nl2br(htmlspecialchars($c['noidung'])) ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($c['status'] == 1): ?>
                            <div class="comment-actions" style="font-size: 13px; margin-top: 5px;">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="javascript:void(0)" class="btn-reply" data-id="<?= $c['id'] ?>" style="color: #007bff; text-decoration: none; margin-right: 15px;"><i class="fas fa-reply"></i> Trả lời</a>
                                <?php endif; ?>
                                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                    <a href="javascript:void(0)" class="btn-edit" data-id="<?= $c['id'] ?>" style="color: #888; text-decoration: none; margin-right: 15px;"><i class="fas fa-edit"></i> Sửa</a>
                                    <a href="javascript:void(0)" class="btn-delete" data-id="<?= $c['id'] ?>" style="color: #dc3545; text-decoration: none;"><i class="fas fa-trash"></i> Xoá</a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="edit-form-wrap" id="edit-form-<?= $c['id'] ?>" style="display:none; margin-top: 10px;">
                                <textarea class="edit-content" id="input-edit-<?= $c['id'] ?>" style="width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($c['noidung']) ?></textarea>
                                <div style="margin-top: 5px;">
                                    <button class="btn-save-edit" data-id="<?= $c['id'] ?>" style="background: #28a745; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Lưu</button>
                                    <button class="btn-cancel-edit" data-id="<?= $c['id'] ?>" style="background: #6c757d; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Huỷ</button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="replies" id="replies-<?= $c['id'] ?>" style="margin-left: 40px; margin-top: 15px; border-left: 2px solid #eee; padding-left: 15px;">
                            <?php 
                            if(isset($children[$c['id']])) {
                                foreach ($children[$c['id']] as $child): 
                                    $time_child = date('d/m/Y H:i', strtotime($child['ngaybinh']));
                            ?>
                                <div class="comment-item" id="comment-<?= $child['id'] ?>" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #eee;">
                                    <div class="comment-content">
                                        <strong style="color: #28a745;"><?= htmlspecialchars($child['ten_nguoi_binh']) ?></strong>
                                        <small style="color: #bbb; margin-left: 10px;"><?= $time_child ?></small>
                                        <div class="comment-text" id="text-<?= $child['id'] ?>" style="margin: 8px 0; color: #333;">
                                            <?php if($child['status'] == 2): ?>
                                                <em style="color: #999;">Bình luận này đã bị xoá.</em>
                                            <?php else: ?>
                                                <?= nl2br(htmlspecialchars($child['noidung'])) ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if($child['status'] == 1): ?>
                                        <div class="comment-actions" style="font-size: 13px; margin-top: 5px;">
                                            <?php if(isset($_SESSION['user_id'])): ?>
                                            <a href="javascript:void(0)" class="btn-reply" data-id="<?= $c['id'] ?>" style="color: #007bff; text-decoration: none; margin-right: 15px;"><i class="fas fa-reply"></i> Trả lời</a>
                                            <?php endif; ?>
                                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $child['user_id']): ?>
                                                <a href="javascript:void(0)" class="btn-edit" data-id="<?= $child['id'] ?>" style="color: #888; text-decoration: none; margin-right: 15px;"><i class="fas fa-edit"></i> Sửa</a>
                                                <a href="javascript:void(0)" class="btn-delete" data-id="<?= $child['id'] ?>" style="color: #dc3545; text-decoration: none;"><i class="fas fa-trash"></i> Xoá</a>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="edit-form-wrap" id="edit-form-<?= $child['id'] ?>" style="display:none; margin-top: 10px;">
                                            <textarea class="edit-content" id="input-edit-<?= $child['id'] ?>" style="width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($child['noidung']) ?></textarea>
                                            <div style="margin-top: 5px;">
                                                <button class="btn-save-edit" data-id="<?= $child['id'] ?>" style="background: #28a745; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Lưu</button>
                                                <button class="btn-cancel-edit" data-id="<?= $child['id'] ?>" style="background: #6c757d; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Huỷ</button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </div>
                        
                        <div class="reply-form-wrap" id="reply-form-<?= $c['id'] ?>" style="display:none; margin-top: 15px; margin-left: 40px;">
                            <textarea class="reply-content" id="input-reply-<?= $c['id'] ?>" placeholder="Viết phản hồi..." style="width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                            <div style="margin-top: 5px;">
                                <button class="btn-submit-reply" data-parent="<?= $c['id'] ?>" data-news="<?= $id ?>" style="background: #007bff; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Gửi trả lời</button>
                                <button class="btn-cancel-reply" data-id="<?= $c['id'] ?>" style="background: #6c757d; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;">Huỷ</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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

        // Toggle Reply Form
        $(document).on('click', '.btn-reply', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('.reply-form-wrap').hide();
            $('.edit-form-wrap').hide();
            $('#text-' + id).show(); // Đảm bảo test hiện lại ở chỗ khác
            $('#reply-form-' + id).show();
            $('#input-reply-' + id).focus();
        });

        // Toggle Edit Form
        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('.reply-form-wrap').hide();
            $('.edit-form-wrap').hide();
            
            // Hiện lại tất cả comment text bị ẩn
            $('.comment-text').show();
            
            // Ẩn nội dung comment hiện tại
            $('#text-' + id).hide();
            $('#edit-form-' + id).show();
            $('#input-edit-' + id).focus();
        });

        // Cancel Edit Form
        $(document).on('click', '.btn-cancel-edit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#edit-form-' + id).hide();
            $('#text-' + id).show();
        });

        // Cancel Reply Form
        $(document).on('click', '.btn-cancel-reply', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#reply-form-' + id).hide();
        });

        // Submit Reply
        $(document).on('click', '.btn-submit-reply', function() {
            const parentId = $(this).data('parent');
            const newsId = $(this).data('news');
            const content = $('#input-reply-' + parentId).val();
            
            if (content.trim() === '') {
                alert('Vui lòng nhập nội dung trả lời!');
                return;
            }

            $.post('site/pages/news/comment.php', {
                news_id: newsId,
                parent_id: parentId,
                noidung: content
            }, function(data) {
                try {
                    const res = JSON.parse(data);
                    if (res.status === 'success') {
                        $('#replies-' + parentId).append(res.html);
                        $('#reply-form-' + parentId).hide();
                        $('#input-reply-' + parentId).val('');
                    } else {
                        alert(res.message);
                    }
                } catch (e) {
                    console.error("Lỗi phản hồi:", data);
                }
            });
        });

        // Save Edit
        $(document).on('click', '.btn-save-edit', function() {
            const id = $(this).data('id');
            const content = $('#input-edit-' + id).val();
            
            if (content.trim() === '') {
                alert('Vui lòng nhập nội dung!');
                return;
            }

            $.post('site/pages/news/comment_action.php', {
                action: 'edit',
                comment_id: id,
                noidung: content
            }, function(data) {
                try {
                    const res = JSON.parse(data);
                    if (res.status === 'success') {
                        $('#text-' + id).html(res.noidung).show();
                        $('#edit-form-' + id).hide();
                    } else {
                        alert(res.message);
                    }
                } catch (e) {
                    console.error("Lỗi phản hồi:", data);
                }
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            if (!confirm('Bạn có chắc muốn xoá bình luận này?')) return;
            
            const id = $(this).data('id');
            $.post('site/pages/news/comment_action.php', {
                action: 'delete',
                comment_id: id
            }, function(data) {
                try {
                    const res = JSON.parse(data);
                    if (res.status === 'success') {
                        $('#text-' + id).html('<em style="color:#999;">Bình luận này đã bị xoá.</em>').show();
                        $('#edit-form-' + id).hide();
                        $('#comment-' + id + ' .comment-actions').hide();
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