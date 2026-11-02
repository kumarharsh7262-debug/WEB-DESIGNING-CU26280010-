<?php
/** Q9: CSRF tokens - generated, stored in the session, validated on every POST form. */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function validate(?string $token): bool
    {
        return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}
