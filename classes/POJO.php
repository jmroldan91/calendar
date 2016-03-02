<?php

abstract class POJO {

    public function set($values, $init=0) {   
        $i=0;
        foreach($this as $key => $value) {
            $this->$key = $values[$i+$init];
            $i++;
        }
    }
    
    public function read(){
        foreach($this as $key => $value) {  
            $this->$key = Request::req($key);
        }
    }

    public function toJSON(){
        $str="";
        foreach ($this as $key => $value) {            
            $str.='"'.$key.'" : '.json_encode($value).', ';
        }
        return "{".substr($str,0,-2)."}";
    }
    
    public function toArray(){
        $array = array();
        foreach ($this as $key => $value) {            
            $array[$key] = $value;
        }
        return $array;
    } 
    
    public function __toString() {
        $str="";
        foreach ($this as $key => $value) {            
            $str.='"'.$key.'" : "'.$value.'", ';
        }
        return substr($str,0,-2);
    }

}
