<?php
class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = [
        'username', 'email', 'password_hash', 'failed_attempts', 'locked_until',
        'reset_token_hash', 'reset_expires', 'api_token_hash', 'api_token_expires',
    ];

    public static function findByEmail(string $email): ?array
    {
        return self::db()->query('SELECT * FROM users WHERE email = ?', [$email])->fetch() ?: null;
    }

    /** Q12: minutes of lock remaining, or 0 if not locked. */
    public static function lockMinutesLeft(array $user): int
    {
        if (empty($user['locked_until'])) return 0;
        $left = strtotime($user['locked_until']) - time();
        return $left > 0 ? (int)ceil($left / 60) : 0;
    }

    /** Q12: count a failed login; lock the account after N failures. */
    public static function recordFailure(array $user): void
    {
        $n = (int)$user['failed_attempts'] + 1;
        $data = ['failed_attempts' => $n];
        if ($n >= (int)config('security.max_attempts')) {
            $data = ['failed_attempts' => 0,
                     'locked_until' => date('Y-m-d H:i:s', time() + config('security.lock_minutes') * 60)];
        }
        self::update((int)$user['id'], $data);
    }

    public static function clearFailures(int $id): void
    {
        self::update($id, ['failed_attempts' => 0, 'locked_until' => null]);
    }

    /** Q13: only the SHA-256 hash of the reset token is stored in the DB. */
    public static function createResetToken(int $id): string
    {
        $token = bin2hex(random_bytes(32));
        self::update($id, [
            'reset_token_hash' => hash('sha256', $token),
            'reset_expires'    => date('Y-m-d H:i:s', time() + config('security.reset_minutes') * 60),
        ]);
        return $token;
    }

    public static function findByResetToken(string $token): ?array
    {
        return self::db()->query(
            'SELECT * FROM users WHERE reset_token_hash = ? AND reset_expires > ?',
            [hash('sha256', $token), date('Y-m-d H:i:s')]
        )->fetch() ?: null;
    }

    /** Q8: API bearer token (hash stored, expiry enforced). */
    public static function createApiToken(int $id): array
    {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + config('security.api_token_hours') * 3600);
        self::update($id, ['api_token_hash' => hash('sha256', $token), 'api_token_expires' => $expires]);
        return [$token, $expires];
    }

    public static function findByApiToken(string $token): ?array
    {
        return self::db()->query(
            'SELECT * FROM users WHERE api_token_hash = ? AND api_token_expires > ?',
            [hash('sha256', $token), date('Y-m-d H:i:s')]
        )->fetch() ?: null;
    }
}
