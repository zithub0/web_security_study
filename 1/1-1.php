<?php
// 1.1 Stored XSS

// 파일 기반 게시글 저장
$filename = "comments.txt";

// 댓글 저장 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comment = $_POST["comment"];
    file_put_contents($filename, $comment . "\n", FILE_APPEND);
}

// 댓글 출력
$comments = file_exists($filename) ? file($filename) : [];
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Stored XSS Example</title></head>
<body>
<h2>댓글 남기기 (Stored XSS)</h2>
<form method="post">
    <textarea name="comment" rows="4" cols="40"></textarea><br>
    <input type="submit" value="제출">
</form>

<h3>댓글 목록</h3>
<?php foreach ($comments as $c): ?>
    <div><?= $c ?></div>
<?php endforeach; ?>
</body>
</html>