<?php
// Session đã được start ở index.php bên ngoài
if(!isset($_SESSION['user_id'])){
    header('Location: index.php?p=dangnhap');
    exit();
}

$user_id = $_SESSION['user_id'];
$news_id = $_GET['news_id'];

// Kiểm tra xem đã lưu chưa
$check = mysqli_query($conn, "SELECT * FROM tbl_bookmarks WHERE user_id = '$user_id' AND news_id = '$news_id'");

if(mysqli_num_rows($check) == 0){
    $sql = "INSERT INTO tbl_bookmarks(user_id, news_id) VALUES ('$user_id', '$news_id')";
    mysqli_query($conn, $sql);
    echo "<script>alert('Đã lưu vào danh sách yêu thích!'); window.history.back();</script>";
} else {
    echo "<script>alert('Mày đã lưu bài này trước đó rồi!'); window.history.back();</script>";
}
?>