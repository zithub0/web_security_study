<?php
// GET 파라미터로 전달된 redirect 주소를 그대로 사용
$redirect = $_GET['redirect'] ?? '/home.php';

// 검증 없이 외부 주소 포함 가능
header("Location: $redirect");
exit;
?>