<?php
// Q16 – Store and read a theme-color preference in a cookie
$allowed = ['#ffffff' => 'White', '#e3f2fd' => 'Blue', '#e8f5e9' => 'Green', '#212121' => 'Dark'];

// Set cookie BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['reset'])) {
    setcookie('theme_color', '', time() - 3600, '/');
  } elseif (isset($allowed[$_POST['color'] ?? ''])) {
    setcookie('theme_color', $_POST['color'], [
      'expires' => time() + 30 * 24 * 3600, // 30 days
      'path' => '/',
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
  }
  header('Location: ' . $_SERVER['PHP_SELF']); // reload so the cookie is readable
  exit;
}

$color = $_COOKIE['theme_color'] ?? '#ffffff';
if (!isset($allowed[$color])) {
  $color = '#ffffff';
}
$text = ($color === '#212121') ? '#fff' : '#000';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Theme Preference</title>
</head>

<body style="font-family:Arial,sans-serif;margin:40px;background:<?= $color ?>;color:<?= $text ?>">
  <h2>Theme Preference (Cookie)</h2>
  <p>Current theme: <b><?= htmlspecialchars($allowed[$color]) ?></b>
    <?= isset($_COOKIE['theme_color']) ? '(read from cookie)' : '(default – no cookie yet)' ?>
  </p>
  <form method="post">
    <select name="color">
      <?php foreach ($allowed as $hex => $label): ?>
        <option value="<?= $hex ?>" <?= $hex === $color ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Save</button>
    <button type="submit" name="reset" value="1">Reset</button>
  </form>
  <p>The choice persists for 30 days, even after closing the browser.</p>
</body>

</html>