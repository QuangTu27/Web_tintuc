<?php
session_start();
// Đảm bảo đường dẫn include chính xác
$path_connect = $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';
include_once $path_connect;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
    exit;
}

if (isset($_POST['noidung']) && isset($_POST['news_id'])) {
    $uid = $_SESSION['user_id'];
    $nid = intval($_POST['news_id']);
    $noidung = mysqli_real_escape_string($conn, $_POST['noidung']);
    $parent_id = (isset($_POST['parent_id']) && intval($_POST['parent_id']) > 0) ? intval($_POST['parent_id']) : 'NULL';
    
    // Lấy tên từ Session 'user_name'
    $ten = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Thành viên';

    // Thực hiện chèn vào Database
    $sql = "INSERT INTO tbl_comments (news_id, user_id, parent_id, ten_nguoi_binh, noidung, status) 
            VALUES ($nid, $uid, $parent_id, '$ten', '$noidung', 1)";

    if (mysqli_query($conn, $sql)) {
        $insert_id = mysqli_insert_id($conn);
        $thoigian = date('d/m/Y H:i');
        
        $is_child = ($parent_id !== 'NULL');
        $margin_style = $is_child ? "margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #eee;" : "margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f1f1;";
        $parent_val = $is_child ? $_POST['parent_id'] : $insert_id;

        $html = "<div class='comment-item' id='comment-{$insert_id}' style='{$margin_style}'>
                    <div class='comment-content'>
                        <strong style='color: #28a745;'>" . htmlspecialchars($ten) . "</strong>
                        <small style='color: #bbb; margin-left: 10px;'>" . $thoigian . "</small>
                        <div class='comment-text' id='text-{$insert_id}' style='margin: 8px 0; color: #333;'>" . nl2br(htmlspecialchars($noidung)) . "</div>
                        
                        <div class='comment-actions' style='font-size: 13px; margin-top: 5px;'>
                            <a href='javascript:void(0)' class='btn-reply' data-id='{$parent_val}' style='color: #007bff; text-decoration: none; margin-right: 15px;'><i class='fas fa-reply'></i> Trả lời</a>
                            <a href='javascript:void(0)' class='btn-edit' data-id='{$insert_id}' style='color: #888; text-decoration: none; margin-right: 15px;'><i class='fas fa-edit'></i> Sửa</a>
                            <a href='javascript:void(0)' class='btn-delete' data-id='{$insert_id}' style='color: #dc3545; text-decoration: none;'><i class='fas fa-trash'></i> Xoá</a>
                        </div>
                        
                        <div class='edit-form-wrap' id='edit-form-{$insert_id}' style='display:none; margin-top: 10px;'>
                            <textarea class='edit-content' id='input-edit-{$insert_id}' style='width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;'>" . htmlspecialchars($noidung) . "</textarea>
                            <div style='margin-top: 5px;'>
                                <button class='btn-save-edit' data-id='{$insert_id}' style='background: #28a745; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;'>Lưu</button>
                                <button class='btn-cancel-edit' data-id='{$insert_id}' style='background: #6c757d; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;'>Huỷ</button>
                            </div>
                        </div>
                    </div>";
                    
        if (!$is_child) {
            $html .= "<div class='replies' id='replies-{$insert_id}' style='margin-left: 40px; margin-top: 15px; border-left: 2px solid #eee; padding-left: 15px;'></div>
                      <div class='reply-form-wrap' id='reply-form-{$insert_id}' style='display:none; margin-top: 15px; margin-left: 40px;'>
                          <textarea class='reply-content' id='input-reply-{$insert_id}' placeholder='Viết phản hồi...' style='width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;'></textarea>
                          <div style='margin-top: 5px;'>
                              <button class='btn-submit-reply' data-parent='{$insert_id}' data-news='{$nid}' style='background: #007bff; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;'>Gửi trả lời</button>
                              <button class='btn-cancel-reply' data-id='{$insert_id}' style='background: #6c757d; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 12px;'>Huỷ</button>
                          </div>
                      </div>";
        }
        $html .= "</div>";

        echo json_encode(['status' => 'success', 'html' => $html, 'parent_id' => $parent_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi SQL: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
}