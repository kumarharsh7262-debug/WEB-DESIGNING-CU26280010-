<?php
// Q7 – Display all students in a styled HTML table
$pdo = require __DIR__ . '/../config/db.php';
$students = $pdo->query('SELECT id, name, email, enrollment_date FROM students ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Students</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background: #f4f6f8
    }

    table {
      border-collapse: collapse;
      width: 100%;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .1)
    }

    th {
      background: #1976d2;
      color: #fff;
      text-align: left
    }

    th,
    td {
      padding: 10px 14px;
      border-bottom: 1px solid #e0e0e0
    }

    tr:nth-child(even) {
      background: #f9f9f9
    }

    tr:hover {
      background: #e3f2fd
    }

    .del {
      color: #c62828;
      text-decoration: none
    }
  </style>
</head>

<body>
  <h2>Students (<?= count($students) ?>)</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Enrollment Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$students): ?>
        <tr>
          <td colspan="5">No records found.</td>
        </tr>
      <?php else:
        foreach ($students as $s): ?>
          <tr>
            <td><?= (int) $s['id'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['enrollment_date']) ?></td>
            <td><a class="del" href="../q09_delete/delete.php?id=<?= (int) $s['id'] ?>"
                onclick="return confirm('Delete this student?')">Delete</a></td>
          </tr>
        <?php endforeach; endif; ?>
    </tbody>
  </table>
  <p><a href="../q06_insert/insert.php">+ Add student</a> | <a href="../q08_update/update.php">Update email</a></p>
</body>

</html>