<?php
// 취약한 페이지: Reflected XSS
// Rule ID: CUSTOM-REFLECTED-XSS
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