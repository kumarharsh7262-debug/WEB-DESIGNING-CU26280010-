<?php
// Central configuration. Secrets come from environment variables (never commit real ones).
$root = dirname(__DIR__);

return [
    'app' => [
        'name'     => 'MyProject',
        'base_url' => getenv('APP_URL') ?: 'https://myproject.local',
        'env'      => getenv('APP_ENV') ?: 'production',
        'timezone' => 'UTC',
    ],
    // Q20: the app connects with a LIMITED user (see database/mysql_user.sql), not root.
    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'name'    => getenv('DB_NAME') ?: 'myproject_db',
        'user'    => getenv('DB_USER') ?: 'myproject_app',
        'pass'    => getenv('DB_PASS') ?: 'ChangeMe_Str0ng!Pass',
        'charset' => 'utf8mb4',
    ],
    'mail' => [   // Q14: PHPMailer / SMTP
        'host'      => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port'      => (int)(getenv('SMTP_PORT') ?: 587),
        'user'      => getenv('SMTP_USER') ?: '',
        'pass'      => getenv('SMTP_PASS') ?: '',
        'from'      => getenv('SMTP_FROM') ?: 'no-reply@myproject.local',
        'from_name' => 'MyProject',
    ],
    'security' => [
        'max_attempts'    => 5,    // Q12
        'lock_minutes'    => 15,   // Q12
        'reset_minutes'   => 60,   // Q13
        'api_token_hours' => 24,   // Q8
    ],
    'upload' => [     // Q15
        'max_size' => 2 * 1024 * 1024,
        'allowed'  => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'],
    ],
    'paths' => [
        'root'    => $root,
        'uploads' => $root . '/storage/uploads',   // OUTSIDE the public web root
        'logs'    => $root . '/storage/logs',
    ],
];
