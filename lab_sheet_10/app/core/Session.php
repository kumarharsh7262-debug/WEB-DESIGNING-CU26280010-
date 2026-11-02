<?php
/** Q10: secure session configuration. */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;

        ini_set('session.use_strict_mode', '1');   // reject unknown session IDs
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => is_https(),   // cookie only sent over HTTPS
            'httponly' => true,         // not readable by JavaScript
            'samesite' => 'Lax',        // basic CSRF mitigation
        ]);
        session_name('MYPROJECTSESSID');
        session_start();
    }

    public static function regenerate(): void { session_regenerate_id(true); }   // call after login

    public static function get(string $k, $default = null) { return $_SESSION[$k] ?? $default; }
    public static function set(string $k, $v): void { $_SESSION[$k] = $v; }

    public static function flash(string $type, string $msg): void { $_SESSION['_flash'][$type][] = $msg; }
    public static function pullFlash(): array { $f = $_SESSION['_flash'] ?? []; unset($_SESSION['_flash']); return $f; }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
