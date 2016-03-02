<?php
class Controller{
    private $view, $iduser, $table, $db, $mng, $campo, $filter, $nrpp, $order, $page, $op, $step, $result, $content, $secLevel;
    
    public function __construct(){
        $this->session = new Session();
        $this->db = new DataBase();
        $this->idUser = Request::req('idUser');
        /*Renderizado*/
        $this->view = Request::req('view');
        if($this->view == null){
            $this->view = "frontend"; //frontend | backend
        }
        /*Manager*/
        $this->table = Request::req('table');
        if($this->table == null){
            $this->table = 'template';
        }
        $this->mng = $this->getManager($this->table);
        /*Busqueda o filtrado */
        $this->campo = Request::req('campo');
        $this->filter = Request::req('filter');
        $this->arrayWhere =[];
        if($this->campo!=null && $this->filter!=""){
            $this->arrayWhere[$this->campo] = "%".$this->filter."%";
        }
        /*Listado paginado*/
        $this->nrpp = Request::req('nrpp');
        if($this->nrpp==null || $this->nrpp==""){
            $this->nrpp= Constant::_NRPP;
        }
        $this->order = Request::req('order');
        if($this->order==null || $this->order==""){
            $this->order = 1;
        }
        $this->page = Request::req('page');
        if($this->page==null || $this->page==""){
            $this->page = 1;
        }
        /* Operaciones */
        $this->op = Request::req('op');
        if($this->op==null || $this->op==""){
            $this->op = 'view';
        }
        $this->step = Request::req('step');
        if($this->step==null || $this->step==""){
            $this->step = '1';
        }
        $this->result = Request::req('result');
        if($this->result==null){
            $this->result = '';
        }
        $this->content = "";
        if($this->session->get('user') != null){
            $mngUser = new ManageUser($this->db);
            $this->secLevel= $mngUser->getLevel($this->session->get('user'));
        }else{
            $this->secLevel=-1;
        }
    }
    
    function getManager($table){
        $manager = "Manage".ucfirst(strtolower($table));
        if(class_exists($manager)){
            return new $manager(new DataBase());
        }else{
            return null;
        }
        
    }
    
    function getObject($table){
        $object = ucfirst(strtolower($table));
        if(class_exists($object)){
            return new $object();
        }else{
            return null;
        }
    }
    
    function checkUser(){
        if($this->secLevel <= 1){
            echo '{ "result" : "Permision denied" }';
            exit();
        }
    }
    
    function load(){
        $user = $this->session->get('user');
        $controller = ucfirst(strtolower($this->table)).'Controller';
        if(class_exists($controller)){
            $ctl = new $controller();
            $ctl->load();
        }else{
            $metodo = $this->op;
            if(method_exists($this, $metodo)){
                $this->$metodo();
            }else{
                $this->view();
            }
        }
    }
    
    function read(){
        $pages = $this->mng->getNumReg($this->arrayWhere);
        echo '{ "resultset" : ' . json_encode($this->mng->getListJSON($this->page, $this->nrpp, $this->order, $this->arrayWhere)) . ', "pages" : '.$pages.', "where" : '.json_encode($this->arrayWhere).' }';
    }
    
    function get(){
        $pkid = Request::req('pkid');
        $obj = $this->mng->get($pkid);
        if($obj != null){
            echo '{ "result" : 1 , "resultset" : '.$obj->toJSON().' }';
        }else{
            echo '{ "result" : -1 }';
        }
    }
    
    function insert(){
        $this->checkUser();
        $object = $this->getObject($this->table);
        $object->read();
        $r = $this->mng->insert($object);
        echo '{ "result" : ' . $r . ' , "obj" : '.$object->toJSON().'}';
    }
    
    function set(){
        $this->checkUser();
        $object = $this->getObject($this->table);
        $object->read();
        $r = $this->mng->set($object, Request::req('pkid'));
        echo '{ "result" : ' . $r . ' }';
    }
    
    function delete(){
        $this->checkUser();
        $r = $this->mng->delete(Request::Req('pkid'));
        echo '{ "result" : ' . $r . ' }';
    }
    
    
    function install(){
        /*Creacion de tablas de la base datos*/
        $manage = new ManageUser($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
        $manage = new ManageTask($this->db);
        $manage->createTable();
        var_dump($this->db->getQueryError());
    }
    
    function login(){
        $login = Request::req('login');
        $pass1 = Request::req('pass');
        $mng = new ManageUser($this->db);
        $user = $mng->getByAlias($login);
        if($user->getEmail() == null){
            $user = $mng->get($login);
        }
        if($user !== null && $user->getPass() === sha1($pass1) && $user->getActivo() == 1){
            $this->session->set('user', $user);
            echo '{ "user" : '.$user->toJson().', "type" : "success" }';
        }else{
            echo '{ "result" : "login", "type" : "error" }';
        }
    }
    
    function loguot(){
        session_destroy();
        echo '{ "result" : "logout", "type" : "success" }';
    }
    
    function checkSession(){
        $user = $this->session->get('user');
        if($user != null){
            echo '{ "user" : '.$user->toJson().', "type" : "success" }';
        }else{
            echo '{ "type" : "close" }';
        }
        
    }
    
    function getDaysWhitTasks(){
        $m = Request::req('m');
        $mng = new ManageTask($this->db);
        echo '{ "days" : '.json_encode($mng->getDaysList($m)).' }';
    }
}
