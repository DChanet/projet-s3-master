<?php

spl_autoload_register(function($className) {
    $class_file = __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . mb_strtolower($className) . ".class.php";
    
    if (file_exists($class_file)) {
        require_once $class_file;
        return true;
    }
    else {
        throw new Exception("Pas trouvé $className");
        //return false;
    }
});
