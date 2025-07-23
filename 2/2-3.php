<?php

.
.
.
session_start();
.
.
.
// 사용자 인증 확인
if (!isset($_SESSION['user'])) {
    die("Unauthorized");
}

// CSRF 토큰이 세션에 없으면 생성
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 본문 파싱
    $input = json_decode(file_get_contents("php://input"), true);

    // CSRF 토큰 확인
    if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die("CSRF token mismatch");
    }

    // 이메일 변경 처리
    if (isset($input['email'])) {
        echo "이메일이 '" . htmlspecialchars($input['email']) . "' 로 변경되었습니다.";
    } else {
        echo "이메일이 지정되지 않았습니다.";
    }
} else {
    // CSRF 토큰 확인용 응답 (예: 프론트엔드가 미리 받아갈 때)
    echo json_encode([
        'csrf_token' => $_SESSION['csrf_token']
    ]);
}
?>