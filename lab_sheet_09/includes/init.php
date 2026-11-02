<?php
/**
 * Bootstrap file included at the top of every page.
 * (Task 2) require_once guarantees each file loads only once.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

// Task 17: silently log the user back in if a valid "Remember Me" cookie exists
try_remember_login($conn);
