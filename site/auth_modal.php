<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Web_tintuc/site/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Document</title>
</head>

<body>
    <div id="auth-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAuthModal()">&times;</span>

            <div class="auth-tabs">
                <div class="tab-item active" onclick="switchTab('login')">Đăng nhập</div>
                <div class="tab-item" onclick="switchTab('register')">Đăng ký</div>
            </div>

            <div class="social-login">
                <p>Đăng nhập với tài khoản</p>
                <div class="social-icons">
                    <button class="s-btn google"><i class="fab fa-google"></i></button>
                    <button class="s-btn facebook"><i class="fab fa-facebook-f"></i></button>
                    <button class="s-btn apple"><i class="fab fa-apple"></i></button>
                </div>
            </div>

            <form id="login-form" action="/Web_tintuc/site/pages/dangnhap.php" method="POST" class="auth-form active">
                <div class="input-group">
                    <label>TÊN ĐĂNG NHẬP *</label>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <label>MẬT KHẨU *</label>
                    <div class="password-field">
                        <input type="password" name="password" class="pass-input" placeholder="Nhập mật khẩu" required>
                        <i class="far fa-eye toggle-password"></i>
                    </div>
                </div>
                <button type="submit" name="btn_login" class="btn-submit">Đăng nhập</button>

            </form>

            <form id="register-form" action="/Web_tintuc/site/pages/dangky.php" method="POST" class="auth-form">

                <div class="input-group">
                    <label>HỌ VÀ TÊN *</label>
                    <input type="text" name="hoten" placeholder="Nhập họ và tên" required>
                </div>

                <div class="input-group">
                    <label>TÊN ĐĂNG NHẬP *</label>
                    <input type="text" name="username" placeholder="Viết liền không dấu (VD: user123)" required>
                </div>

                <div class="input-group">
                    <label>EMAIL (TÙY CHỌN)</label>
                    <input type="email" name="email" placeholder="Ví dụ: abc@gmail.com">
                </div>

                <div class="input-group">
                    <label>MẬT KHẨU *</label>
                    <div class="password-field">
                        <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                        <i class="far fa-eye toggle-password"></i>
                    </div>
                </div>

                <button type="submit" name="btn_register" class="btn-submit">Đăng ký</button>
                <p class="auth-note">
                    Bằng cách đăng ký tài khoản, bạn đồng ý với <a href="#">Điều khoản sử dụng</a> của chúng tôi.
                </p>
            </form>
        </div>
    </div>
</body>

</html>

<script>
    // 1. Hàm mở Modal
    function openAuthModal(mode = 'login') {
        document.getElementById('auth-modal').style.display = 'flex';
        switchTab(mode);
    }

    // 2. Hàm đóng Modal
    function closeAuthModal() {
        document.getElementById('auth-modal').style.display = 'none';
    }

    // 3. Hàm chuyển Tab (Login <-> Register)
    function switchTab(tabName) {
        // Reset active tab
        const tabs = document.querySelectorAll('.tab-item');
        tabs.forEach(t => t.classList.remove('active'));

        // Reset active form
        document.getElementById('login-form').classList.remove('active');
        document.getElementById('register-form').classList.remove('active');

        if (tabName === 'login') {
            tabs[0].classList.add('active');
            document.getElementById('login-form').classList.add('active');
        } else {
            tabs[1].classList.add('active');
            document.getElementById('register-form').classList.add('active');
        }
    }

    // 4. Đóng modal khi bấm ra vùng đen bên ngoài
    window.onclick = function(event) {
        let modal = document.getElementById('auth-modal');
        if (event.target == modal) {
            closeAuthModal();
        }
    }

    // 5. CHỨC NĂNG ẨN/HIỆN MẬT KHẨU (Mới thêm)
    document.querySelectorAll('.toggle-password').forEach(item => {
        item.addEventListener('click', function() {
            // Tìm ô input nằm ngay trước icon con mắt
            let input = this.previousElementSibling;

            if (input.type === "password") {
                input.type = "text"; // Hiện mật khẩu
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash'); // Đổi icon thành mắt gạch chéo
            } else {
                input.type = "password"; // Ẩn mật khẩu
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye'); // Đổi icon về mắt thường
            }
        });
    });
</script>