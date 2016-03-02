<?php

class Request {
    private static function read($param, $filtar=true, $indice=null){
        if(is_array($param)){
            if($indice===null){
                $array = array();
                foreach ($param as $value){
                    $r = self::clean($value, $filtar);
                    if($r==""){
                        $r = null;
                    }
                    $array[] = $r;
                }
                return $array;
            }else{
                if(isset($param[$indice])){
                    $r = self::clean($param[$indice], $filtrar);
                    if($r==""){
                        return null;
                    }
                    return $r;
                }
            }
        }else{
            $r = self::clean($param, $filtar);
            if($r == ""){
                $r = null;
            }
            return $r; 
        }
    }
    
    private static function clean($param, $filtar=true){
        if($filtar){
            $param = htmlspecialchars($param);
        }
        return trim($param);
    }

    static function get($nom, $filtar=true, $indice=null){
        if(isset($_GET[$nom])){
            return self::read($_GET[$nom], $filtar, $indice);
        }
        return null;
    }
    
    static function post($nom, $filtar=true, $indice=null){
        if(isset($_POST[$nom])){
           return self::read($_POST[$nom], $filtar, $indice);
        }
        return null;
    }
    
    static function req($nom, $filtar=true, $indice=null){
        $valor=self::post($nom, $filtar, $indice);
        if($valor!==null){
            return $valor;
        }        
        return self::get($nom, $filtar, $indice=null);
    }
}
