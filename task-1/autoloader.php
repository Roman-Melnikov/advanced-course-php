<?php
function autoloader($class): void
{
    $search = ['//', '_'];
    $replace = DIRECTORY_SEPARATOR;

    $file = str_replace($search, $replace, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('autoloader');