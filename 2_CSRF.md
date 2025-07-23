# 2. CSRF

## 개요

- 크로스 사이트 스트립트(XSS)
    - 정의
        - 사용자가 인증된 세션을 가진 상태에서, 공격자가 유도한 악의적인 요청이 사용자의 권한으로 전송되어 서버에서 부적절하게 처리되는 취약점
    - 발생 원인
        - 사용자 인증 상태 유지 (세션 쿠키 등) 하는 상태에서 악성 페이지에 접속하여 발생
        - 중요 요청에 대한 검증 부족
            - 서버가 중요한 요청(post, put 등)에 대해 적절한 검증 (예: CSRF 토큰, Referer 검사 등)을 하지 않을 때 발생합니다.
            
- 공격 종류
    - GET 방식 CSRF (HTML Tag 기반)
        - 사용자가 이 HTML이 포함된 페이지를 방문하면, 브라우저가 자동으로 이미지 URL 요청을 보냄
        
        ```jsx
        <img src="https://bank.com/transfer?to=attacker&amount=1000" />
        
        ```
        
    - POST 방식 CSRF (HTML Tag 기반)
        - form 태그와 JavaScript를 이용해 POST 요청 자동 전송
        
        ```jsx
        <form action="https://bank.com/change-password" method="POST">
          <input type="hidden" name="new_pw" value="attackerpass123">
        </form>
        <script>document.forms[0].submit();</script>
        
        ```
        
    - **AJAX 기반 CSRF**
        - JavaScript의 XMLHttpRequest 또는 fetch API를 이용하여 수행하는 방식
        - 사용자가 쿠키를 소유한 상태로 악성 사이트에 접근하면 다음과 같은 fetch API가 수행
        
        ```jsx
        fetch("https://example.com/api/change-email", {
          method: "POST",
          credentials: "include",  // 세션 쿠키 포함!
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: "attacker@example.com" })
        });
        ```
        
        > AJAX
        > 
        > 
        > AJAX (Asynchronous JavaScript and XML)는 웹 애플리케이션이 전체 페이지를 새로 고치지 않고도 서버로부터 데이터를 가져오거나 서버에 데이터를 보낼 수 있도록 해주는 기법, AJAX의 최신 대체 API가 fetch
        > 

- 취약점 영향
    - 인증 없이 상대 시스템에서 제어 가능한 설정 가능
        - 민간함 정보를 출력
        - 서버에 임의 코드 실행
        - 데이터 변조

---

## 공격 시라니오

1. GET 요청 기반 CSRF
    - 참고 사항
        - 피해자(관리자)가 2-1.php 페이지에 로그인한 상태
        - 이후 악성 페이지(2-1-1.html)의 이미지 태그를 통해 비밀번호 변경 요청을 자동 전송
    - 참고 파일
        - 2-1. php
        - 2-1-1.html
    - 공격 시나리오
        1. 사이트에 접속하여 인증 및 세션을 유지한 상태
            
            ```php
            <?php
            .
            .
            .
            
            session_start();
            .
            .
            .
            
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
            
            ```
            
        2. 아래와 같은 HTML 태그를 기반으로 요청이 실행
            
            ```html
            <img src="http://victim.com/change_password.php?new_password=hacked123">
            ```
            
        

1. **AJAX 기반 CSRF**
    - 참고 사항
        - 피해자(관리자)가 2-1.php 페이지에 로그인한 상태
        - 이후 악성 페이지(2-1-1.html)의 이미지 태그를 통해 비밀번호 변경 요청을 자동 전송
    - 참고 파일
        - 2-2. php
        - 2-2-1.html
    - 공격 시나리오
        1. 사이트에 접속하여 인증 및 세션을 유지한 상태
            
            ```php
            <?php
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
            ```
            
        2. 아래와 같은 HTML 태그를 기반으로 요청이 실행
            
            ```html
            <script>
            fetch("http://victim.com/update_email.php", {
              method: "POST",
              credentials: "include", // 쿠키 자동 전송
              headers: {
                "Content-Type": "application/json"
              },
              body: JSON.stringify({ email: "attacker@example.com" })
            });
            </script>
            ```
            
        

---

## 대책

- CSRF 토큰 사용
    - 서버가 품을 렌더링할 때 무작위 토큰(CSRF token)을 함께 생성하여 클라이언트에 전달
    - 서버는 이 토큰이 유효한지 확인, 없거나 틀리면 거부
    - 예제
        - 2-3에 첨부(2. AJAX 기반 CSRF)
        
        ```php
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
        ```
        

- SameSite 쿠키 설정
    - 브라우저가 **다른 사이트에서 온 요청에는 쿠키를 전송하지 않도록 제한**
    - 예제
        - 2-3에 첨부(2. AJAX 기반 CSRF)
            
            ```php
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
            ```
            
    

- **Referer 또는 Origin 헤더 검증**
    - 서버가 요청의 Referer 또는 Origin 헤더를 확인하여, 자신의 도메인에서 온 요청인지 확인