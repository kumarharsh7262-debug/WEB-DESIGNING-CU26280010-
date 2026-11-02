<?php
/**
 * Shared helper functions.
 */

const UPLOAD_DIR      = __DIR__ . '/../uploads/';
const MAX_UPLOAD_SIZE = 2 * 1024 * 1024; // 2 MB
const REMEMBER_DAYS   = 30;

// ---- Task 4: start the session -------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ---- Task 16: XSS protection ---------------------------------------------
/** Escape any user-generated value before printing it into HTML. */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url)
{
    header('Location: ' . $url);
    exit;
}

// ---- CSRF protection for all state-changing POST requests ----------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(403);
        exit('Invalid or missing CSRF token.');
    }
}

// ---- Flash messages -------------------------------------------------------
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [$type, $message];
}

function flash_render(): string
{
    $html = '';
    foreach ($_SESSION['flash'] ?? [] as [$type, $msg]) {
        $html .= '<div class="alert ' . e($type) . '">' . e($msg) . '</div>';
    }
    unset($_SESSION['flash']);
    return $html;
}

// ---- Authentication helpers ----------------------------------------------
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

/** Task 5: redirect unauthenticated visitors to the login page. */
function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true); // prevent session fixation
    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
}

// ---- Task 17: Remember Me cookie -----------------------------------------
function set_remember_cookie(mysqli $conn, int $userId): void
{
    $token = bin2hex(random_bytes(32));
    $hash  = hash('sha256', $token);

    $stmt = $conn->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
    $stmt->bind_param('si', $hash, $userId);
    $stmt->execute();

    setcookie('remember', $userId . ':' . $token, [
        'expires'  => time() + REMEMBER_DAYS * 86400,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_remember_cookie(): void
{
    setcookie('remember', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    unset($_COOKIE['remember']);
}

function try_remember_login(mysqli $conn): void
{
    if (is_logged_in() || empty($_COOKIE['remember']) || !is_string($_COOKIE['remember'])) {
        return;
    }
    if (!preg_match('/^(\d+):([a-f0-9]{64})$/', $_COOKIE['remember'], $m)) {
        clear_remember_cookie();
        return;
    }
    $id = (int)$m[1];
    $stmt = $conn->prepare('SELECT id, name, remember_token FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && $user['remember_token'] && hash_equals($user['remember_token'], hash('sha256', $m[2]))) {
        login_user($user);
    } else {
        clear_remember_cookie();
    }
}

// ---- Prepared-statement helper -------------------------------------------
/** Prepare, bind (if params given) and execute a query. */
function db_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// ---- Cart helper ----------------------------------------------------------
function cart_count(): int
{
    return array_sum($_SESSION['cart'] ?? []);
}

// ---- Task 14: image upload ------------------------------------------------
/**
 * Validate and store an uploaded product image.
 * Returns the new unique filename, or null if nothing was uploaded / on error.
 * $error is filled with a message when validation fails.
 */
function handle_image_upload(array $file, ?string &$error = null): ?string
{
    $error = null;

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null; // no file chosen: not an error
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed (error code ' . (int)$file['error'] . ').';
        return null;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $error = 'Image is too large. Maximum size is 2 MB.';
        return null;
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        $error = 'Invalid upload.';
        return null;
    }

    // Check the real MIME type from file contents, not the client-supplied one
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        $error = 'Only JPG, PNG, GIF or WEBP images are allowed.';
        return null;
    }

    // Unique, unguessable name; extension comes from the verified MIME type
    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        $error = 'Could not save the uploaded file.';
        return null;
    }
    return $name;
}

function delete_image_file(?string $name): void
{
    if ($name && preg_match('/^[A-Za-z0-9_.-]+$/', $name) && is_file(UPLOAD_DIR . $name)) {
        unlink(UPLOAD_DIR . $name);
    }
}
