<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>College Web</title>
</head>

<body style="font-family:Arial,sans-serif;text-align:center;margin-top:15vh">
  <h1>Welcome to collegeweb.local</h1>
  <p>Virtual host is working.</p>
  <p>Host header: <b><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') ?></b></p>
  <p>Document root: <code><?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? '') ?></code></p>
</body>

</html>