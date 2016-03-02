<?php
class ManageTask extends ManagePOJO{
    protected $table='task';
    
    public function get($id) {
        $params = [];
        $params['id'] = $id;
        $r = $this->db->query($this->table, '*', $params);
        if($r!=-1){
            $row = $this->db->getRow();
            $task = new Task();
            $task->set($row);
            return $task;
        }else{
            return null;
        }
    }
    
    function getList($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $limit = ($page-1)*$nrpp . ',' . $nrpp;
        $this->db->query($this->table, '*', $params, $order, $limit);
        $r = [];
        while($row=$this->db->getRow()){
            $tmp = new Task();
            $tmp->set($row);
            $r[] = $tmp;
        }
        return $r;
    }
    
    function getListJSON($page = "1", $nrpp = Constant::_NRPP, $order = "1", $params = []) {
        $list = $this->getList($page, $nrpp, $order, $params);
        $r = "";
        foreach ($list as $key =>$value) {
            $r .= $value->toJSON() . ",";
        }
        $r = "[" . substr($r, 0,-1) . "]";
        return $r;
    }
    
    function getDaysList($m) {
        $this->db->send('select day(taskdate) from task where month(taskdate) = ' . $m);
        $r = [];
        while($row=$this->db->getRow()){
            $r[] = $row[0];
        }
        return $r;
    }
    
    function  createTable(){
        $sql = "create table if not exists `task` ( "
             . " id int not null auto_increment,"
             . " idUser varchar(80) not null,"
             . " title varchar(40) not null,"
             . " content varchar(100),"
             . " taskdate datetime not null,"
             . " primary key (id)"
             . ") engine=INNODB";
     return $this->db->send($sql);
    } 
}