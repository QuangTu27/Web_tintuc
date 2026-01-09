<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php');

// =================================================================
// 1. LẤY DỮ LIỆU DANH MỤC & XỬ LÝ ĐA CẤP
// =================================================================
$sql_cat = "SELECT * FROM tbl_categories ORDER BY id ASC";
$res_cat = mysqli_query($conn, $sql_cat);

// Đưa tất cả vào mảng để xử lý
$menuItems = [];
while ($row = mysqli_fetch_assoc($res_cat)) {
    $menuItems[] = $row;
}

// Hàm hỗ trợ lấy danh mục con
function getSubCategories($items, $parentId)
{
    $subs = [];
    foreach ($items as $item) {
        if ($item['parent_id'] == $parentId) {
            $subs[] = $item;
        }
    }
    return $subs;
}

// Xử lý thông tin User
$avatar = 'default_avatar.png';
$displayName = 'Người dùng';
$username = 'Guest';

if (isset($_SESSION['user_login'])) {
    $u_id = $_SESSION['user_id'];
    // Truy vấn lại thông tin mới nhất
    $sql_user_info = "SELECT avatar, hoten, username FROM tbl_users WHERE id = $u_id";
    $res_user_info = mysqli_query($conn, $sql_user_info);

    if ($res_user_info && mysqli_num_rows($res_user_info) > 0) {
        $u_data = mysqli_fetch_assoc($res_user_info);

        $avatar = !empty($u_data['avatar']) ? $u_data['avatar'] : 'default_avatar.png';
        $displayName = !empty($u_data['hoten']) ? $u_data['hoten'] : $u_data['username'];
        $username = $u_data['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tin tức 24H</title>
    <link rel="stylesheet" href="/Web_tintuc/site/css/main.css">
    <link rel="stylesheet" href="/Web_tintuc/site/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <?php include 'auth_modal.php'; ?>

    <div id="header-placeholder"></div>

    <header id="siteHeader">
        <div class="top-bar">
            <div class="container top-bar-inner">
                <a href="index.php" class="logo-top">TINTUC<span>24H</span></a>

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

                <div class="flex-spacer"></div>

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

                                <li><a href="index.php?p=thongtincanhan&act=general">Thông tin chung</a></li>
                                <li><a href="index.php?p=thongtincanhan&act=my_comments">Ý kiến của bạn</a></li>
                                <li><a href="index.php?p=thongtincanhan&act=bookmark_list">Tin đã lưu</a></li>
                                <li><a href="index.php?p=thongtincanhan&act=tin_da_xem">Tin đã xem</a></li>

                                <li class="divider"></li>

                                <li>
                                    <a href="index.php?act=logout" class="logout-link" onclick="return confirm('Bạn muốn đăng xuất?')">
                                        Thoát <i class="fas fa-sign-out-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="javascript:void(0)" onclick="openAuthModal('login')"><i class="fas fa-user"></i> Đăng nhập</a>
                        <span style="margin: 0 5px;">|</span>
                        <a href="javascript:void(0)" onclick="openAuthModal('register')">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="nav-menu-wrapper">
            <div class="container nav-inner">

                <a href="index.php" class="nav-btn-home">
                    <i class="fas fa-home"></i>
                </a>

                <nav class="nav-categories">
                    <ul>
                        <?php
                        $count = 0;
                        // Chỉ duyệt các danh mục CHA 
                        foreach ($menuItems as $cat):
                            if ($cat['parent_id'] == 0):
                                // Tìm con của danh mục này
                                $childCats = getSubCategories($menuItems, $cat['id']);
                                $hasChild = count($childCats) > 0;

                                if ($count < 9):
                        ?>
                                    <li class="<?= $hasChild ? 'has-child' : '' ?>">
                                        <a href="index.php?p=danhmuc&id=<?= $cat['id'] ?>">
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </a>

                                        <?php if ($hasChild): ?>
                                            <ul class="sub-menu">
                                                <?php foreach ($childCats as $sub): ?>
                                                    <li>
                                                        <a href="index.php?p=danhmuc&id=<?= $sub['id'] ?>">
                                                            <?= htmlspecialchars($sub['name']) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                        <?php
                                endif; // End limit check
                                $count++;
                            endif; // End parent check
                        endforeach;
                        ?>
                    </ul>
                </nav>

                <a href="javascript:void(0)" id="btnOpenMenu" class="nav-btn-all">
                    <i class="fas fa-bars"></i>
                </a>

    </header>

    <div id="full-menu-overlay">
        <div class="container">
            <div class="full-menu-header">
                <h3>Tất cả chuyên mục</h3>
                <span id="btnCloseMenu" class="close-btn"> <i class="fas fa-times"></i></span>
            </div>

            <div class="full-menu-grid">
                <?php
                // Duyệt qua tất cả danh mục Cha
                foreach ($menuItems as $cat):
                    if ($cat['parent_id'] == 0):
                        // Lấy danh mục con
                        $childCats = getSubCategories($menuItems, $cat['id']);
                ?>
                        <div class="menu-column">
                            <a href="index.php?p=danhmuc&id=<?= $cat['id'] ?>" class="parent-link">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>

                            <?php if (count($childCats) > 0): ?>
                                <div class="child-links">
                                    <?php foreach ($childCats as $sub): ?>
                                        <a href="index.php?p=danhmuc&id=<?= $sub['id'] ?>">
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </div>

    <script>
        const btnOpen = document.getElementById('btnOpenMenu');
        const btnClose = document.getElementById('btnCloseMenu');
        const overlay = document.getElementById('full-menu-overlay');
        const body = document.body;

        // Mở menu
        btnOpen.addEventListener('click', function(e) {
            e.preventDefault(); // Chặn chuyển trang
            overlay.classList.add('active');
            body.style.overflow = 'hidden'; // Khóa cuộn trang web
        });

        // Đóng menu
        btnClose.addEventListener('click', function() {
            overlay.classList.remove('active');
            body.style.overflow = ''; // Mở khóa cuộn
        });

        // Click ra ngoài khoảng trắng cũng đóng
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                body.style.overflow = '';
            }
        });
    </script>

    <script>
        const header = document.getElementById('siteHeader');
        const placeholder = document.getElementById('header-placeholder');
        const scrollThreshold = 100;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > scrollThreshold) {
                header.classList.add('is-fixed');
                header.classList.add('hide-top');
                placeholder.style.display = 'block';
            } else {
                header.classList.remove('is-fixed');
                header.classList.remove('hide-top');
                placeholder.style.display = 'none';
            }
        });
    </script>

    <script>
        function updateDateTime() {
            const now = new Date();
            const weekdays = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
            const dayName = weekdays[now.getDay()];
            const date = now.toLocaleDateString('vi-VN');
            document.getElementById('date-time').innerText = `${dayName}, ${date}`;
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        const apiKey = '8356bc5ee72baf34994e81f6bdb35652';
        const city = 'Hanoi';
        fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=vi&appid=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                if (data.cod !== 200) return;
                document.getElementById('location').innerText = data.name;
                document.getElementById('temperature').innerText = Math.round(data.main.temp) + '°C';
                document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${data.weather[0].icon}.png`;
            });
    </script>
</body>

</html>