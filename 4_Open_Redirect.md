# 4. 오픈 리다이렉트

## 개요

- 오픈 리다이렉트
    - 정의
        - 일반적인 웹 애플리케이션에서는 사용자의 요청에 따라 임의의 외부 URL로 리다이렉션(redirect)할 수 있도록 허용
        - 하지만 **적절한 검증 없이** 외부 URL을 그대로 처리하는 경우 발생하는 보안 취약점
        - redirect는 보통 로그인 이후 특정 페이지로 이동시킬 때 사용합니다. 주로 로그아웃 후 홈화면으로 돌려보내는 상황에서 자주 사용
    - 발생 원인
        - 사용자 입력값(URL)을 검증 없이 그대로 리다이렉트에 사용
            - 암호화되지 않은 HTTP로 세션 ID가 전송되면, 공격자가 네트워크 상에서 스니핑 동작 가능
        - 로그인 후 리디렉션, 이메일 링크 등에서 return, redirect, next 매개변수를 통해 동작
        - 리디렉션 대상이 외부 도메인으로 설정되어 검증 없이 모두 허용하는 경
            
            
        
- 공격 종류
    - http 기반 동작
        - 일반적으로 http에서는 헤더값을 기반으로 동작, 혹은 입력 받은 매개변수 값으로 동작
        - 예시 1) header("Location: http://redirect_url.com");
        - 예시 2) header("Location: ".$_GET['url']);
    - html이나 자바 스크립 기반 동작
        - HTML 태그나 자바스크립트 코드에 따른 동작
        - 예시 1) <meta http-equiv="refresh" content="0; url='http://redirect_url.com/'" />
        - 예시 2) window.location.href = "http://redirect_url.com";

- 취약점 영향
    - 신뢰받는 도메인을 이용한 URL을 공격자가 생성하여 사용자를 악성 사이트로 유도하여 추가 피해가 가
    - 피싱 페이지를 통해 로그인 정보, 세션 쿠키, 개인 정보 등을 가로챌 수 있음

---

## 공격 시라니오

1. http 기반 동작
    - 참고 사항
        - 사용자가 4-1.php 페이지 구성
        - 다음과 같이 리다이렉트 주소가 포함된 요청
    - 참고 파일
        - 4-1. php
    - 공격 시나리오
        1. 해당 코드에는 window.location 코드로 인하여 리다이렉트를 진행
        
        ```php
        <!DOCTYPE html>
        <html>
        <head>
          <title>Redirecting...</title>
          <script>
            // URL에서 redirect 파라미터를 읽어온 뒤 해당 위치로 이동
            const params = new URLSearchParams(window.location.search);
            const redirectUrl = params.get("redirect");
        
            if (redirectUrl) {
              window.location.href = redirectUrl;
            } else {
              document.write("No redirect URL specified.");
            }
          </script>
        </head>
        <body>
        </body>
        </html>
        
        ```
        
        1. redirect 값에 대한 검증 없이 외부 URL로 이동 가능
        
        ```php
        https://4-1.html?redirect=https://attacker.com/phishing
        
        ```
        
        1. 피싱 등의 2차 피해 가능
        
2. html이나 자바 스크립 기반 동작
    - 참고 사항
        - 사용자가 4-2.php 페이지 구성
        - 다음과 같이 리다이렉트 주소가 포함된 요청
    - 참고 파일
        - 4-2. php
    - 공격 시나리오
        1. 해당 코드에는 redirect  매개변수로 리다이렉트를 진행
        
        ```php
        <?php
        // GET 파라미터로 전달된 redirect 주소를 그대로 사용
        $redirect = $_GET['redirect'] ?? '/home.php';
        
        // 검증 없이 외부 주소 포함 가능
        header("Location: $redirect");
        exit;
        ?>
        ```
        
        1. redirect 값에 대한 검증 없이 외부 URL로 이동 가능
        
        ```php
        https://4-2.php?redirect=https://attacker.com
        
        ```
        
        1. 피싱 등의 2차 피 가능

---

## 대책

- 리다이렉션 대상 URL을 **화이트리스트**로 제한하거나 외부 URL일 경우 차단