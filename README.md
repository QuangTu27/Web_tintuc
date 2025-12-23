# Hướng dẫn tạo Database và các bảng trên MySQL

Tài liệu này dùng để **thống nhất cấu trúc database** cho tất cả thành viên trong nhóm.

## Yêu cầu
- Đã cài đặt **XAMPP**
- Có file `web_tintuc.sql`

---

## Các bước thực hiện

### Bước 1: Tải file database
- Tải file: `web_tintuc.sql` (đã được cung cấp trong folder database)

---

### Bước 2: Khởi động XAMPP
- Mở **XAMPP Control Panel**
- Chạy:
  - `Apache`
  - `MySQL`
- Mở trình duyệt và truy cập: http://localhost/phpmyadmin

---

### Bước 3: Tạo cơ sở dữ liệu mới
- Chọn **Mới (New)** ở góc trên bên trái
- Nhập tên cơ sở dữ liệu: web_tintuc
- Bấm **Tạo (Create)**

---

### Bước 4: Import cấu trúc database
- Chọn database `web_tintuc`
- Chọn tab **Nhập (Import)**
- Chọn file `web_tintuc.sql`
- Bấm **Nhập (Go)**

---

## Ghi chú
- Không tự ý sửa cấu trúc bảng (tên bảng, cột, kiểu dữ liệu)
- Mục đích: **đảm bảo tất cả thành viên dùng chung cấu trúc database**
- Nếu cần thay đổi, phải thống nhất trong nhóm trước

---

✔️ Sau khi hoàn thành các bước trên, database đã sẵn sàng để sử dụng.


