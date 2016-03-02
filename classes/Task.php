<?php
class Task extends POJO {
    protected $id, $idUser, $title, $content, $taskdate;
    
    function __construct($id=null, $idUser=null, $title=null, $content=null, $taskdate=null){
        $this->id = $id;
        $this->idUser = $idUser;
        $this->title = $title;
        $this->content = $content;
        $this->taskdate = $taskdate;
    }
    
    function getParam($param){
        return $this->$param;
    }
    
    function setParam($param, $value){
        $this->$param = $value;
    }
    
}