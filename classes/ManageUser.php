<?php

class ManageUser extends ManagePOJO{
    protected $table='user';
    
    public function get($email) {
        $params = [];
        $params['email'] = $email;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $user = new User();
            $user->set($row);
            return $user;
        }else{
            return null;
        }
    }
    public function getByAlias($alias) {
        $params = [];
        $params['alias'] = $alias;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $user = new User();
            $user->set($row);
            return $user;
        }else{
            return null;
        }
    }
    
    function set(User $user, $email){
        $paramsWhere = array();
        $paramsWhere['email'] = $email;    
        $paramsSet = $user->toArray();
        unset($paramsSet['pass']);
        return $this->db->update($this->table, $paramsSet, $paramsWhere);
    }

    function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $limit = ($page-1)*$nrpp . ',' . $nrpp;
        $this->db->query($this->table, '*', $params, $order, $limit);
        $r = [];
        while($row=$this->db->getRow()){
            $tmp = new User();
            $tmp->set($row);
            $r[] = $tmp;
        }
        return $r;
    }

    function newUserGuest($email, $pass){
        $user = new User($email, $pass);
        return $this->insert($user);
    }
    
    function newUserStaff($email, $pass, $act){
        $user = new User($email, $pass, $act);
        return $this->insert($user);
    }
    
    function newUserAdmin($email, $pass, $act, $admin, $staff){
        $user = new User($email, $pass, $act, $admin, $staff);
        return $this->insert($user);
    }
    
    function getHash($email){
        return sha1($email . Constant::_SEMILLA);
    }

    function googleUserRegister(User $user){
        $user->setActivo('1');
        return $this->insert($user);
    }
    
    
    function getLevel(User $user = null){
        if($user !== null && $user->getActivo() == '1'){
            if($user->getAdministrador() == '1'){
                return '3';  //Full access
            }
            else if($user->getPersonal() == '1'){
                return '2'; //staff access
            }
            else{
                return '1'; //user access
            }
        }else{
            return '0'; //register access
        }
    }
    
    function validate(User $user){
        $validaEmail = $this->get($user->getEmail());
        $validaAlias = $this->getByAlias($user->getAlias());
        if($user->getPass()==null){
            return -2; //Contraseña en blanco
        }
        if($validaEmail->getEmail()!=null){
            return -1; //El email ya existe
        }
        if($validaAlias->getAlias()!=null){
            return 0; //El alias ya existe
        }
        return 1;
    }

    
    function authenticate_google_OAuthtoken(){
        $id_token = Request::req('token');
        $url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='.$id_token;
        $conexion = curl_init();
        curl_setopt($conexion, CURLOPT_URL, $url);
        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($conexion);
        curl_close($conexion);
        return $r;//aquí vienen todos los datos
    }
    
    function  createTable(){
        $sql = "create table if not exists `user` ( "
             . " email varchar(80) not null,"
             . " pass varchar(40) not null,"
             . " alias varchar(80) UNIQUE,"
             . " fechaAlta datetime not null,"
             . " activo tinyint not null default 0,"
             . " administrador tinyint not null default 0,"
             . " personal tinyint not null default 0,"
             . " primary key (email)"
             . ") engine=INNODB";
     return $this->db->send($sql);
    } 
}
