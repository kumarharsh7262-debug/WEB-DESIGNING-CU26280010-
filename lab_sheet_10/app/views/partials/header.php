<?php /** @var string $title */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | <?= e(config('app.name')) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require ROOT . '/app/views/partials/nav.php'; ?>
<main class="container">
<?php foreach (Session::pullFlash() as $type => $messages): ?>
  <?php foreach ($messages as $m): ?>
    <div class="alert alert-<?= e($type) ?>"><?= e($m) ?></div>
  <?php endforeach; ?>
<?php endforeach; ?>
