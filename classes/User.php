<?php

class User extends POJO {
    protected $email, $pass, $alias, $fechaAlta, $activo, $administrador, $personal;
    
    function __construct($email=null, $pass=null, $alias=null, $fechaAlta=null, $act=0, $admin=0, $staff=0) {
        $this->email = $email;
        $this->pass = sha1($pass);
        $this->alias = $alias;
        if($this->alias == null){
            $this->alias = $email;
        }
        $this->fechaAlta = $fechaAlta;
        if($this->fechaAlta == null){
            $this->fechaAlta = date_create()->format('Y-m-d H:i:s');
        }        
        $this->activo = $act;
        $this->administrador = $admin;
        $this->personal = $staff;
    }
    
    function getEmail() {
        return $this->email;
    }

    function getPass() {
        return $this->pass;
    }

    function getAlias() {
        return $this->alias;
    }

    function getFechaAlta() {
        return $this->fechaAlta;
    }

    function getActivo() {
        return $this->activo;
    }

    function getAdministrador() {
        return $this->administrador;
    }

    function getPersonal() {
        return $this->personal;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPass($pass) {
        $this->pass = $pass;
    }

    function setAlias($alias) {
        $this->alias = $alias;
    }

    function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;
    }

    function setActivo($act) {
        $this->activo = $act;
    }

    function setAdministrador($admin) {
        $this->administrador = $admin;
    }

    function setPersonal($staff) {
        $this->personal = $staff;
    }



}
