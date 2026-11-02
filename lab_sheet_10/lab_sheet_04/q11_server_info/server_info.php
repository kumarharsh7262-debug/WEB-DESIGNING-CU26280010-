<?php
// Q11 – Inspect server environment using $_SERVER
$keys = [
  'SERVER_SOFTWARE',
  'SERVER_NAME',
  'SERVER_ADDR',
  'SERVER_PORT',
  'SERVER_PROTOCOL',
  'DOCUMENT_ROOT',
  'SCRIPT_FILENAME',
  'SCRIPT_NAME',
  'REQUEST_METHOD',
  'REQUEST_URI',
  'QUERY_STRING',
  'HTTP_HOST',
  'HTTP_USER_AGENT',
  'HTTP_ACCEPT_LANGUAGE',
  'REMOTE_ADDR',
  'REMOTE_PORT',
  'REQUEST_TIME',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Server Information</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background: #f4f6f8
    }

    table {
      border-collapse: collapse;
      width: 100%;
      background: #fff
    }

    th,
    td {
      padding: 8px 12px;
      border: 1px solid #ddd;
      text-align: left;
      word-break: break-all
    }

    th {
      background: #37474f;
      color: #fff;
      width: 30%
    }
  </style>
</head>

<body>
  <h2>Apache / PHP Environment</h2>
  <p>Web server: <b><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'n/a') ?></b> |
    PHP: <b><?= PHP_VERSION ?></b> | SAPI: <b><?= php_sapi_name() ?></b> | OS: <b><?= PHP_OS ?></b></p>
  <table>
    <tr>
      <th>$_SERVER key</th>
      <th>Value</th>
    </tr>
    <?php foreach ($keys as $k): ?>
      <tr>
        <td><?= $k ?></td>
        <td>
          <?= htmlspecialchars($k === 'REQUEST_TIME' ? date('Y-m-d H:i:s', $_SERVER[$k]) : (string) ($_SERVER[$k] ?? 'n/a')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php if (function_exists('apache_get_modules')): ?>
    <h3>Loaded Apache modules</h3>
    <p><?= htmlspecialchars(implode(', ', apache_get_modules())) ?></p>
  <?php endif; ?>
</body>

</html>