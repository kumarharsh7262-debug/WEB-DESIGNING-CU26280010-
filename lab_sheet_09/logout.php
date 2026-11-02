<?php
/** Task 6: Secure logout - clear variables, delete session cookie, destroy session */
require_once __DIR__ . '/includes/init.php';

// Logout must be a POST with a valid CSRF token so other sites can't log users out
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_verify();

// Invalidate the Remember Me token in the database and its cookie
if (is_logged_in()) {
    db_query($conn, 'UPDATE users SET remember_token = NULL WHERE id = ?', 'i', [(int)$_SESSION['user_id']]);
}
clear_remember_cookie();

// 1. Clear all session variables
$_SESSION = [];

// 2. Remove the session cookie from the browser
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?: 'Lax',
    ]);
}

// 3. Destroy the session on the server
session_destroy();

// Start a fresh session only to carry the flash message
session_start();
flash('success', 'You have been logged out.');
redirect('login.php');
