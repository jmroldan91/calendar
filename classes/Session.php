<?php

class Session {
    private static $init = false;
    
    function __construct($name = null) {
        if(!self::$init){
            if($name!=null){
                session_start($name);
            }else{
                session_start();
            }
            self::$init = true;
        }
    }
    
    function get($name){
        if(isset($_SESSION[$name])){
            return $_SESSION[$name];
        }
        return null;
    } 
    
    function set($name, $valor){
        $_SESSION[$name]=$valor;
    }
    
    function delete($name){
        if(isset($_SESSION[$name])){
            unset($_SESSION[$name]);
        }
    }
    
    function destroy(){
        session_destroy();
    }
    
    function isLogged(){
        return $this->get("user")!=='' && $this->get("user")!==null;
    }
}
