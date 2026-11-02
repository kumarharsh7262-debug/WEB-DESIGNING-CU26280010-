<?php
// Q13 – Secure image upload: validates size, extension and real MIME type
$uploadDir = __DIR__ . '/uploads/';
$maxSize = 2 * 1024 * 1024; // 2 MB
$allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMime = ['image/jpeg', 'image/png', 'image/gif'];
$message = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['image'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $message = '<p style="color:red">Upload failed (error code ' . (int) ($file['error'] ?? -1) . ').</p>';
    } elseif ($file['size'] > $maxSize) {
        $message = '<p style="color:red">File too large. Maximum is 2 MB.</p>';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
            $message = '<p style="color:red">Only JPG, PNG or GIF images are allowed.</p>';
        } else {
            // Random name prevents overwrites and path tricks
            $newName = bin2hex(random_bytes(8)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $message = '<p style="color:green">Uploaded as <b>' . htmlspecialchars($newName) . '</b></p>'
                    . '<img src="uploads/' . htmlspecialchars($newName) . '" style="max-width:250px">';
            } else {
                $message = '<p style="color:red">Could not save the file. Check folder permissions.</p>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Image Upload</title>
</head>

<body style="font-family:Arial,sans-serif;max-width:480px;margin:40px auto">
    <h2>Upload an Image</h2>
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif" required>
        <button type="submit">Upload</button>
    </form>
    <p><small>Allowed: jpg, png, gif &middot; Max 2 MB</small></p>
</body>

</html>