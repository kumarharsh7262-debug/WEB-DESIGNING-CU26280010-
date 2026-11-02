<?php
// Q17 – Export the students table as a downloadable CSV
$pdo = require __DIR__ . '/../config/db.php';

$filename = 'students_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accents correctly

fputcsv($out, ['ID', 'Name', 'Email', 'Enrollment Date']);

$stmt = $pdo->query('SELECT id, name, email, enrollment_date FROM students ORDER BY id');
while ($row = $stmt->fetch()) {
    fputcsv($out, $row);
}
fclose($out);
exit;
