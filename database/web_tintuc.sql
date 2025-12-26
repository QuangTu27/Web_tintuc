-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 23, 2025 lúc 04:30 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_tintuc`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_ads`
--

CREATE TABLE `tbl_ads` (
  `id` int(11) NOT NULL,
  `ten_quangcao` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `link_lien_ket` varchar(255) DEFAULT '#',
  `vitri` enum('banner_top','sidebar_left','sidebar_right') DEFAULT 'sidebar_right',
  `trangthai` enum('hien','an') DEFAULT 'hien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_ads`
--

INSERT INTO `tbl_ads` (`id`, `ten_quangcao`, `hinh_anh`, `link_lien_ket`, `vitri`, `trangthai`) VALUES
(1, 'Quảng cáo Shopee', 'ads1.jpg', 'https://shopee.vn', 'sidebar_right', 'hien'),
(2, 'Tuyển sinh Đại học', 'ads2.jpg', 'https://dh.edu.vn', 'banner_top', 'hien');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_bookmarks`
--

CREATE TABLE `tbl_bookmarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `ngay_luu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_bookmarks`
--

INSERT INTO `tbl_bookmarks` (`id`, `user_id`, `news_id`, `ngay_luu`) VALUES
(1, 1, 1, '2025-12-19 08:46:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_categories`
--

CREATE TABLE `tbl_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_categories`
--

INSERT INTO `tbl_categories` (`id`, `name`) VALUES
(1, 'Thời sự'),
(2, 'Giải trí'),
(3, 'Thể thao'),
(4, 'Công nghệ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_comments`
--

CREATE TABLE `tbl_comments` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ten_nguoi_binh` varchar(100) NOT NULL,
  `noidung` text NOT NULL,
  `ngaybinh` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_comments`
--

INSERT INTO `tbl_comments` (`id`, `news_id`, `user_id`, `ten_nguoi_binh`, `noidung`, `ngaybinh`) VALUES
(1, 1, 1, 'Admin', 'Bài viết rất hay!', '2025-12-10 23:25:09'),
(2, 3, 1, 'Admin', 'Tuyệt vời! Việt Nam cố lên!', '2025-12-10 23:25:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_news`
--

CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL,
  `tieude` varchar(255) NOT NULL,
  `noidung` longtext DEFAULT NULL,
  `hinhanh` varchar(255) DEFAULT NULL,
  `tomtat` text DEFAULT NULL,
  `ngaydang` datetime DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_news`
--

INSERT INTO `tbl_news` (`id`, `tieude`, `noidung`, `hinhanh`, `tomtat`, `ngaydang`, `category_id`) VALUES
(1, 'Việt Nam đạt kỷ lục xuất khẩu', 'Nội dung bài viết mẫu...', 'news1.jpg', NULL, '2025-12-10 23:25:09', 1),
(2, 'Ca sĩ A ra mắt MV mới', 'Nội dung bài viết mẫu...', 'news2.jpg', NULL, '2025-12-10 23:25:09', 2),
(3, 'Đội tuyển Việt Nam thắng đậm', 'Nội dung bài viết mẫu...', 'news3.jpg', NULL, '2025-12-10 23:25:09', 3),
(4, 'Ra mắt smartphone mới', 'Nội dung bài viết mẫu...', 'news4.jpg', NULL, '2025-12-10 23:25:09', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hoten` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `username`, `password`, `hoten`, `email`, `role`) VALUES
(1, 'admin', '123456', 'Quản trị', 'admin@gmail.com', 'admin'),
(8, 'qtu', '123', 'Quang Tú', 'quangtuphung899@gmail.com', 'user');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbl_ads`
--
ALTER TABLE `tbl_ads`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tbl_bookmarks`
--
ALTER TABLE `tbl_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_save` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Chỉ mục cho bảng `tbl_categories`
--
ALTER TABLE `tbl_categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tbl_comments`
--
ALTER TABLE `tbl_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `tbl_news`
--
ALTER TABLE `tbl_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tbl_ads`
--
ALTER TABLE `tbl_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tbl_bookmarks`
--
ALTER TABLE `tbl_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tbl_categories`
--
ALTER TABLE `tbl_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tbl_comments`
--
ALTER TABLE `tbl_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tbl_news`
--
ALTER TABLE `tbl_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tbl_bookmarks`
--
ALTER TABLE `tbl_bookmarks`
  ADD CONSTRAINT `tbl_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_bookmarks_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `tbl_news` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tbl_comments`
--
ALTER TABLE `tbl_comments`
  ADD CONSTRAINT `tbl_comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `tbl_news` (`id`),
  ADD CONSTRAINT `tbl_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`);

--
-- Các ràng buộc cho bảng `tbl_news`
--
ALTER TABLE `tbl_news`
  ADD CONSTRAINT `tbl_news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_categories` (`id`);
COMMIT;




-- 1. Cập nhật bảng Users (Role mới, Avatar, Ngày tạo)
ALTER TABLE `tbl_users` 
MODIFY COLUMN `role` ENUM('user', 'admin', 'moderator', 'contributor', 'reporter', 'journalist') DEFAULT 'user',
ADD COLUMN `avatar` VARCHAR(255) DEFAULT 'default_avatar.jpg' AFTER `email`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `avatar`;

-- 2. Cập nhật bảng News (Trạng thái duyệt bài, Lượt xem)
ALTER TABLE `tbl_news` 
ADD COLUMN `view_count` INT(11) DEFAULT 0 AFTER `category_id`,
ADD COLUMN `trang_thai` ENUM('ban_nhap', 'cho_duyet', 'da_dang', 'bi_tu_choi') DEFAULT 'cho_duyet' AFTER `view_count`;

-- 3. Cập nhật bảng Comments (Reply lồng nhau, Trạng thái ẩn/hiện, Ngày bình luận)
ALTER TABLE `tbl_comments`
ADD COLUMN `parent_id` INT(11) DEFAULT NULL AFTER `user_id`,
ADD COLUMN `status` TINYINT(1) DEFAULT 1 COMMENT '1: hiện, 0: ẩn' AFTER `noidung`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`;

-- Thêm khóa ngoại cho chức năng Reply
ALTER TABLE `tbl_comments` 
ADD CONSTRAINT `fk_comment_parent` FOREIGN KEY (`parent_id`) REFERENCES `tbl_comments`(`id`) ON DELETE CASCADE;

-- 4. Tạo bảng Likes (Mỗi người 1 like/bài, không lo spam)
CREATE TABLE IF NOT EXISTS `tbl_likes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `news_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_news_like` (`user_id`, `news_id`),
  CONSTRAINT `fk_like_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_like_news` FOREIGN KEY (`news_id`) REFERENCES `tbl_news` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
