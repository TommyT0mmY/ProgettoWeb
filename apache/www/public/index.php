<?php 
declare(strict_types=1);

// Basic PSR-4 autoloader
spl_autoload_register(function($classname) {
    $prefix = 'Unibostu\\';
    $base_dir = __DIR__ . '/../app/';
    $prefix_len = strlen($prefix);
    // Check prefix
    if (strncmp($prefix, $classname, $prefix_len) !== 0) {
        return;
    }
    $relative_classname = substr($classname, $prefix_len);
    $file = $base_dir . str_replace('\\', '/', $relative_classname) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$app = new \Unibostu\Core\App();
$app->run();
