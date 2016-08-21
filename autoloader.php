<?php
/**
 * Created by PhpStorm.
 * User: Michał Kowalik <maf.michal@gmail.com>
 * Date: 21.08.16 11:42
 */

spl_autoload_register(function($class){

    if (!preg_match('#^' . preg_quote('org\majkel\dbase\\') . '(.+)$#', $class, $m)) {
        return;
    }

    $classPath = "{$m[1]}.php";

    if (DIRECTORY_SEPARATOR !== '\\') {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
    }

    include_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $classPath;

});
