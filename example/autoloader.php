<?php

set_time_limit(0);

set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR .
        "lib". PATH_SEPARATOR .
        get_include_path());

spl_autoload_register(function ($class) {
    $thisClass = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);

    $baseDir = __DIR__;

    if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
        $baseDir = substr($baseDir, 0, -strlen($thisClass));
    }

    $className = ltrim($className, '\\');
    $fileName = $baseDir;
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($fileName)) {
        require $fileName;
    }
});



//Some functions to help samples


function getRandLat() {
    return rand(-83, 83) + (mt_rand() / 10000000000);
}

function getRandLng() {
    return rand(-178, 179) + (mt_rand() / 10000000000);
}