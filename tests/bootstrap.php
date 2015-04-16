<?php
date_default_timezone_set('UTC');


spl_autoload_register(function ($class) {
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
