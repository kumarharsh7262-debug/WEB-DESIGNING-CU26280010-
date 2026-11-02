<?php
/** Read config with dot notation: config('db.host') */
function config(string $key, $default = null)
{
    static $cfg = null;
    $cfg ??= require ROOT . '/config/config.php';
    $val = $cfg;
    foreach (explode('.', $key) as $part) {
        if (!is_array($val) || !array_key_exists($part, $val)) return $default;
        $val = $val[$part];
    }
    return $val;
}

/** Escape output (XSS protection) */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    return '/' . ltrim($path, '/');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}
