<?php
// Q4: FRONT CONTROLLER - every request enters here (see public/.htaccess).
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
require ROOT . '/app/core/bootstrap.php';

$router = new Router();
require ROOT . '/config/routes.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/'
);
