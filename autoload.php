<?php
/*

    PSR-4 compliant autoload function
    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md

    Any class reference that begins with the prefix "PlusAmp\" will be automatically loaded from the /classes/ directory,
    as long as there is a PHP file with a matching path.
    For example, PlusAmp\Controllers\ControllerIfc will resolve to /classes/Controllers/ControllerIfc.php


*/

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Podcast\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/classes/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    } else {
        error_log("Could not resolve $class as $file");
    }
});

?>