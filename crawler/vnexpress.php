<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/connect.php';

$rssUrl = "https://vnexpress.net/rss/tin-moi-nhat.rss";
$rss = simplexml_load_file($rssUrl);

if (!$rss) die("Không đọc được RSS");

foreach ($rss->channel->item as $item) {

    // ===== DATA =====
    $title = trim((string)$item->title);
    $rawDesc = (string)$item->description;
    $descText = strip_tags($rawDesc);
    $tomtat = mb_substr($descText, 0, 255);
    $date = date('Y-m-d H:i:s', strtotime($item->pubDate));

    // ===== CHỐNG TRÙNG (PREPARED) =====
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM tbl_news WHERE tieude = ?");
    mysqli_stmt_bind_param($checkStmt, "s", $title);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        continue;
    }

    // ===== LẤY ẢNH =====
    preg_match('/<img.*?src="(.*?)"/', $rawDesc, $imgMatch);
    $imgUrl = $imgMatch[1] ?? '';

    $imgName = 'default_news.png';
    if ($imgUrl) {
        $imgName = time() . '_rss.webp';
        @file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/Web_tintuc/images/news/' . $imgName,
            file_get_contents($imgUrl)
        );
    }

    // ===== INSERT =====
    $stmt = mysqli_prepare($conn, "
        INSERT INTO tbl_news
        (tieude, tomtat, noidung, hinhanh, ngaydang, trangthai, author_id, category_id, view_count)
        VALUES (?, ?, ?, ?, ?, 'cho_duyet', 1, 1, 0)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $title,
        $tomtat,
        $descText,
        $imgName,
        $date
    );

    mysqli_stmt_execute($stmt);
}

echo "✅ Crawl VnExpress thành công!";
