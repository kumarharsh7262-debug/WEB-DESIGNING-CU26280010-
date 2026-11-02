<?php
/** Q11: validate + sanitize input with filter_var(). Every method returns the clean value or null. */
class Validator
{
    public static function email($v): ?string
    {
        $v = trim((string)$v);
        $r = filter_var($v, FILTER_VALIDATE_EMAIL);
        return ($r !== false && strlen($r) <= 100) ? strtolower($r) : null;
    }

    public static function url($v): ?string
    {
        $v = trim((string)$v);
        $r = filter_var($v, FILTER_VALIDATE_URL);
        if ($r === false || !in_array(parse_url($r, PHP_URL_SCHEME), ['http', 'https'], true)) return null;
        return strlen($r) <= 255 ? $r : null;
    }

    public static function int($v, ?int $min = null, ?int $max = null): ?int
    {
        $opts = [];
        if ($min !== null) $opts['min_range'] = $min;
        if ($max !== null) $opts['max_range'] = $max;
        $r = filter_var($v, FILTER_VALIDATE_INT, ['options' => $opts]);
        return $r === false ? null : $r;
    }

    public static function float($v, float $min = 0): ?float
    {
        $r = filter_var($v, FILTER_VALIDATE_FLOAT);
        return ($r === false || $r < $min) ? null : (float)$r;
    }

    /** Strip control chars + HTML tags, trim, enforce length. Output is still escaped with e() in views. */
    public static function string($v, int $min = 1, int $max = 255): ?string
    {
        $v = filter_var((string)$v, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
        $v = trim(strip_tags((string)$v));
        $len = mb_strlen($v);
        return ($len >= $min && $len <= $max) ? $v : null;
    }
}
