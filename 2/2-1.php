<?php
session_start();

// 로그인된 사용자만 처리
if (!isset($_SESSION['user'])) {
    die("Unauthorized");
}

if (isset($_GET['new_password'])) {
    // 실제로는 DB에 저장해야 함
    echo "비밀번호가 '" . htmlspecialchars($_GET['new_password']) . "' 로 변경되었습니다.";
} else {
    echo "비밀번호가 지정되지 않았습니다.";
}
?>
