<?php
// 1.3 Stored XSS (대책 htmlspecialchars 적용)

$filename = "comments.txt";

// 댓글 저장 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comment = $_POST["comment"];
    // 이스케이프 없이 저장 (원형 유지)
    file_put_contents($filename, $comment . "\n", FILE_APPEND);
}

// 댓글 읽기
$comments = file_exists($filename) ? file($filename) : [];
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Stored XSS - 방어된 예제</title></head>
<body>
<h2>댓글 남기기 (방어된 Stored XSS)</h2>
<form method="post">
    <textarea name="comment" rows="4" cols="40"></textarea><br>
    <input type="submit" value="제출">
</form>

<h3>댓글 목록</h3>
<?php foreach ($comments as $c): ?>
    <div><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
</body>
</html>