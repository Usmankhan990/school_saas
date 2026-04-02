<?php
/**
 * Router script for PHP built-in development server.
 * Usage: php -S localhost:8000 server.php
 *
 * This replaces .htaccess rewrite rules for the dev server.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists on disk (CSS, JS, images, etc.), serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP built-in server handle static files
}

// Otherwise, route everything through index.php
$_GET['url'] = ltrim($uri, '/');
require_once __DIR__ . '/index.php';
