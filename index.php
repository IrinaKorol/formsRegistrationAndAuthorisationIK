<?php

// ini_set('display_errors', 1);
define('ROOT', dirname(__FILE__)); // создание константы ROOT, где dirname(__FILE__) - полный путь к файлу на диске

include_once(ROOT . '/router.php');
index();
?>