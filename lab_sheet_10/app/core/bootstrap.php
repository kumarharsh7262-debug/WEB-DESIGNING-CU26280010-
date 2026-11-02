<?php
// Bootstraps the app: helpers, autoloader, error handling (Q16), session (Q10).
require_once __DIR__ . '/helpers.php';

// Composer (PHPMailer) if installed
if (is_file(ROOT . '/vendor/autoload.php')) {
    require_once ROOT . '/vendor/autoload.php';
}

// Simple PSR-0 style autoloader for app classes
spl_autoload_register(function (string $class) {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = ROOT . "/app/$dir/$class.php";
        if (is_file($file)) { require_once $file; return; }
    }
});

date_default_timezone_set(config('app.timezone', 'UTC'));

/* ---------- Q16: hide errors from visitors, log them to a file ---------- */
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', config('paths.logs') . '/php_error.log');
error_reporting(E_ALL);

function render_500(): void
{
    if (!headers_sent()) {
        http_response_code(500);
        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal server error']);
            return;
        }
    }
    require ROOT . '/app/views/errors/500.php';   // standalone page, no DB needed
}

set_exception_handler(function (Throwable $e) {
    error_log(sprintf("Uncaught %s: %s in %s:%d\n%s",
        get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
    render_500();
});

register_shutdown_function(function () {       // fatal errors
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        render_500();
    }
});

Session::start();
