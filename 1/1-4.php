<?php
// 1-4 Reflected XSS 헤더(대책 : 응답의 문자 인코딩 지정)

//변경 사항
header("Content-Type: text/html; charset=UTF-8");

?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Reflected XSS Example</title></head>
<body>
<h2>검색 (Reflected XSS)</h2>
<form method="get">
    <input type="text" name="q">
    <input type="submit" value="검색">
</form>

<?php if (isset($_GET['q'])): ?>
    <p>검색어: <?= $_GET['q'] ?></p>
<?php endif; ?>
</body>
</html>