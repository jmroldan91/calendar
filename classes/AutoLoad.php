<?php
function autocargador($clase) {
    $path = $_SERVER['DOCUMENT_ROOT'].'/classes/' . $clase . '.php';
    if(file_exists($path)){
        require_once $path;
    }
}    

spl_autoload_register('autocargador');
