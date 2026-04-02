<?php
/**
 * Front Controller – All requests route through here.
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer autoload (for DomPDF etc.)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load configuration
require_once __DIR__ . '/config.php';

// Autoloader: load core/ and models/ classes
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/core/' . $class . '.php',
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load helpers
require_once BASE_PATH . '/core/Helpers.php';

// Start session
Session::start();

// Get the URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// Initialize and dispatch router
$router = new Router();
require_once BASE_PATH . '/routes.php';
$router->dispatch($url);
