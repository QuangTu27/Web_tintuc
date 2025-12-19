<?php
session_start();
// Gọi file kết nối (Lùi ra 1 cấp thư mục để tìm connect.php)
include '../connect.php'; 

// Kiểm tra nếu đã login rồi thì đẩy thẳng vào trang dashboard
if (isset($_SESSION['admin_login'])) {
    header('location: index.php');
    exit();
}

// Xử lý khi người dùng ấn nút Đăng nhập
if (isset($_POST['btn_login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // LƯU Ý QUAN TRỌNG:
    // 1. Tên bảng phải là 'tbl_users' (như trong hình bạn gửi)
    // 2. Cột role phải so sánh với chữ 'admin'
    $sql = "SELECT * FROM tbl_users WHERE username = '$u' AND password = '$p' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Đăng nhập thành công -> Lưu session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_name'] = $row['hoten'];
        $_SESSION['admin_id'] = $row['id'];
        
        // Chuyển hướng vào trang quản trị chính
        header('location: index.php'); 
    } else {
        $error = "Sai tài khoản, mật khẩu hoặc bạn không phải Admin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background: #f0f2f5; 
            font-family: Arial, sans-serif; 
        }
        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            width: 350px; 
            text-align: center; 
        }
        h2 { 
            margin-bottom: 20px; 
            color: #333; 
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        button { width: 100%; 
            padding: 12px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
        }
        button:hover { 
            background: #0056b3; 
        }
        .error { 
            color: red; 
            background: #ffe6e6; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 15px; 
            font-size: 14px; 
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>QUẢN TRỊ VIÊN</h2>
        
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="btn_login">ĐĂNG NHẬP</button>
        </form>
        
        <p style="margin-top: 15px; font-size: 13px;">
            <a href="../index.php" style="text-decoration: none; color: #666;">← Quay về trang chủ</a>
        </p>
    </div>
</body>
</html>