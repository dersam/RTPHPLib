<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */

spl_autoload_register(function ($class) {
    $path = explode('\\', $class);
    $file = __DIR__.'/src/'. implode('/', $path) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
