<?php

session_start();


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