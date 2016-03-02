<?php

class Utils {
    
    static function redirect($dest = null, $end=TRUE){
        if($dest===null){
            $dest = './index.php'; 
        }
        header('Location: '.$dest);
        if($end){
            exit();        
        }
    }
    
    static function queryToArray($query){
        $result = array();
        $campos = explode("&", $query);
        foreach ($campos as $item) {
            $campo = explode("=", $item);
            for($i=0;$i<count($campo);$i++){
                $result[$campo[$i]] = $campo[++$i];
            }
        }
        return $result;
    }
    
    static function ArrayToQuery($array){
        $result = "";
        foreach ($array as $key => $value) {
            $result .= $key . '=' . $value . '&';
        }
        return substr($result, 0,-1);
    }
        
    static function strNormailze($goodName){
	if (strlen($goodName) > 30) {
            $goodName =  substr($name, 0, 30);
        }        
        $goodName = str_replace("'", '', $goodName);
        $goodName = str_replace('"', '', $goodName);
        $goodName = str_replace('/', ' ', $goodName);
        $goodName = str_replace('á', 'a', $goodName);
        $goodName = str_replace('é', 'e', $goodName);
        $goodName = str_replace('í', 'i', $goodName);
        $goodName = str_replace('ó', 'o', $goodName);
        $goodName = str_replace('ú', 'u', $goodName);
        
        return $goodName;
    }
    
    static function getManager($table, $db){
        switch ($table){
            case 'address': return new ManageAddress($db);
            case 'category': return new ManageCategory($db);
            case 'message': return new ManageMessage($db);
            case 'product': return new ManageProduct($db);
            case 'sale': return new ManageSale($db);
            case 'user': return new ManageUser($db);
        }
    }
    
    static function getObject($table){
        switch ($table){
            case 'address': return new Address(); 
            case 'category': return new Category();
            case 'message': return new Message();
            case 'product': return new Product();
            case 'sale': return new Sale();
            case 'user': return new User();
        }
    }
    
    static function translateTable($string){
        switch ($string){
            case 'address': return 'Direcciones';
            case 'category': return 'Categoria';
            case 'message': return 'Mensajes';
            case 'product': return 'Productos';
            case 'sale': return 'Ventas';
            case 'user': return 'Usuarios';
        }
    }
    
    static function getHTMLtable($table, QueryString $querystring, $arrayitems = []){
        switch ($table){
            case 'address': return Render::AddressHtmlTable($arrayitems, $querystring);
            case 'category': return Render::categoryHtmlTable($arrayitems, $querystring);
            case 'message': return Render::messageHtmlTable($arrayitems, $querystring);
            case 'product': return Render::productHtmlTable($arrayitems, $querystring);
            case 'sale': return Render::saleHtmlTable($arrayitems, $querystring);
            case 'user': return Render::userHtmlTable($arrayitems, $querystring);
        }
    }
    
    static function getHTMLForm($table, QueryString $querystring){
        switch ($table){
            case 'address': return Render::addressHtmlForm($querystring);
            case 'category': return Render::categoryHtmlForm($querystring);
            case 'message': return Render::disableHtmlForm($querystring);
            case 'product': return Render::productHtmlForm($querystring);
            case 'sale': return Render::disableHtmlForm($querystring);
            case 'user': return Render::userHtmlForm($querystring);
        }
    }
}

