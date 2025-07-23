<?php
session_set_cookie_params([
    'samesite' => 'Strict',  // SameSite=Strict 설정
    'secure' => true,        // HTTPS 환경일 경우에만 쿠키 전송
    'httponly' => true       // 자바스크립트 접근 차단 (XSS 방지)
]);
.
.
.

session_start();
.
.
.

if (!isset($_SESSION['user'])) {
    die("Unauthorized");
}

// JSON POST 요청 처리
$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['email'])) {
    echo "이메일이 '" . htmlspecialchars($input['email']) . "' 로 변경되었습니다.";
} else {
    echo "이메일이 지정되지 않았습니다.";
}
?>