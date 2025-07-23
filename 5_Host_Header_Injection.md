# 5. Host Header Injection

## 개요

- 오픈 리다이렉트
    - 정의
        - 웹 애플리케이션이 HTTP 요청의 Host 헤더 값을 신뢰하고 검증 없이 사용할때 발생하는 취약
    - 발생 원인
        - 클라이언트로부터 전달된 Host 헤더 값을 신뢰하고 이를 필터링 없이 사용하는 경우 발생
        - Host헤더 혹 X-Forwarded-Host 헤더에서 발생
            
            
        
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
    - CSRF와 동일하게 인증 없이 상대 시스템에서 제어 가능한 설정 가능
        - 민간함 정보를 출력
        - 서버에 임의 코드 실행
        - 데이터 변조

---

## 공격 시라니오

1. 

---

## 대책

- 서버에서 허용된 Host만 처리 (화이트리스트 적용)
- Host 헤더를 입력값 검증