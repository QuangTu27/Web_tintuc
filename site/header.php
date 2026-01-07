<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// Lấy danh sách danh mục cho Menu
$sql_cat = "SELECT * FROM tbl_categories ORDER BY id ASC";
$res_cat = mysqli_query($conn, $sql_cat);

$avatar = !empty($_SESSION['user_avatar'])
    ? $_SESSION['user_avatar']
    : 'default_avatar.png';

$displayName = $_SESSION['user_name'] ?? 'Người dùng';
$username = $_SESSION['user_username'] ?? $displayName;

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tin tức 24H</title>
    <link rel="stylesheet" href="/Web_tintuc/site/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <?php include 'auth_modal.php'; ?>

    <header>
        <div class="top-bar">
            <div class="container">
                <div class="weather-box">
                    <div class="date-location">
                        <div class="city" id="location">Hà Nội</div>
                        <div class="date" id="date-time">--</div>
                    </div>

                    <div class="weather-divider"></div>

                    <div class="weather-detail">
                        <img id="weather-icon" src="" alt="weather icon">
                        <span class="temp" id="temperature">--°C</span>
                    </div>
                </div>

                <div class="user-action">
                    <?php if (isset($_SESSION['user_login'])): ?>

                        <div class="user-profile-box">
                            <div class="profile-toggle">
                                <img src="/Web_tintuc/images/avatars/<?= $avatar ?>?v=<?= time() ?>" class="user-avatar-mini" alt="User">
                                <span class="name-text"><?= htmlspecialchars($displayName) ?></span>
                                <i class="fas fa-caret-down" style="font-size: 12px; color: white;"></i>
                            </div>

                            <ul class="profile-dropdown">
                                <li class="dropdown-header">
                                    <img src="/Web_tintuc/images/avatars/<?= $avatar ?>?v=<?= time() ?>" class="avatar-large">
                                    <strong class="user-name-text"><?= htmlspecialchars($username) ?></strong>
                                </li>

                                <li><a href="index.php?p=thongtincanhan">Thông tin chung</a></li>
                                <li><a href="index.php?p=my_comments">Ý kiến của bạn</a></li>
                                <li><a href="index.php?p=tin_da_luu">Tin đã lưu</a></li>
                                <li><a href="index.php?p=tin_da_xem">Tin đã xem</a></li>

                                <li class="divider"></li>
                                <li>
                                    <a href="index.php?act=logout" class="logout-link" onclick="return confirm('Bạn muốn đăng xuất?')">
                                        Thoát <i class="fas fa-sign-out-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    <?php else: ?>
                        <a href="javascript:void(0)" onclick="openAuthModal('login')">
                            <i class="fas fa-user"></i> Đăng nhập
                        </a>
                        <span style="margin: 0 5px;">|</span>
                        <a href="javascript:void(0)" onclick="openAuthModal('register')">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="container main-header">
            <a href="index.php" class="logo">TINTUC<span style="color:#333">24H</span></a>

            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <?php while ($cat = mysqli_fetch_assoc($res_cat)): ?>
                        <li>
                            <a href="index.php?p=danhmuc&id=<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        let lastScrollTop = 0; // Lưu vị trí cuộn trước đó
        const header = document.getElementById('siteHeader');

        window.addEventListener('scroll', function() {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > 150) {
                // TRƯỜNG HỢP 1: Đang cuộn xuống -> Thu gọn Header
                if (currentScroll > lastScrollTop) {
                    header.classList.add('is-sticky');
                }
                // TRƯỜNG HỢP 2: Đang cuộn lên -> Hiện lại đầy đủ
                else {
                    header.classList.remove('is-sticky');
                }
            } else {
                // Khi ở gần đầu trang -> Luôn hiện đầy đủ
                header.classList.remove('is-sticky');
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Cập nhật vị trí mới
        }, false);
    </script>


    <script>
        /* ====== THỜI GIAN THỰC ====== */
        function updateDateTime() {
            const now = new Date();
            const weekdays = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
            const dayName = weekdays[now.getDay()];

            const date = now.toLocaleDateString('vi-VN');

            // Cập nhật đúng định dạng: Thứ..., ngày/tháng/năm
            document.getElementById('date-time').innerText = `${dayName}, ${date}`;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        /* ====== THỜI TIẾT (OpenWeatherMap) ====== */
        const apiKey = '8356bc5ee72baf34994e81f6bdb35652';
        const city = 'Hanoi';

        fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=vi&appid=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                if (data.cod !== 200) return;

                document.getElementById('location').innerText = data.name;

                document.getElementById('temperature').innerText =
                    Math.round(data.main.temp) + '°C';

                document.getElementById('weather-icon').src =
                    `https://openweathermap.org/img/wn/${data.weather[0].icon}.png`;
            });
    </script>