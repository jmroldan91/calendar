<?php
abstract class ManagePOJO {      
    protected $db = null;
    
    function __construct(DataBase $db) {
        $this->db = $db;
    }
    
    function insert(POJO $pojo){
        return $this->db->insert($this->table, $pojo->toArray(), true);
    }
    
    function set(POJO $pojo, $pkid){
        $paramsWhere = array();
        $paramsWhere['id'] = $pkid;        
        return $this->db->update($this->table, $pojo->toArray(), $paramsWhere);
    }
            
    function delete($id){
        $params = array();
        $params['id'] = $id;
        $result = $this->db->delete($this->table, $params);    
        return $result;
    }
    
    function erase(POJO $pojo){
        return $this->delete($pojo->getID());
    }    
    
    public function getNumReg($params = array()){
        return $this->db->count($this->table, $params);
    }
    
    function getListJSON($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $list = $this->getList($page, $nrpp, $order, $params);
        foreach ($list as $value) {
            $r .= $value->toJSON() . ",";
        }
        $r = "[" . substr($r, 0,-1) . "]";
        return $r;
    }
    
    abstract function get($id);
    abstract function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $where='1=1');
}