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