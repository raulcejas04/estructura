<?php

require_once("login.php");

class Exc_EjecutarConsulta extends Exception {
    
    var $query;
    var $parametros;
    var $usuario;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $_query = '', $_param = null, $_usuario = 'SIN USUARIO',$code=-20000) {
        $this->query = $_query;
        $this->parametros = $_param;
        $this->usuario = $_usuario;
        parent::__construct('Error: '.$message, $code);
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function getParametrosJSON() {
        return json_encode($this->parametros);
    }
    
    public function getQuery() {
        return $this->query;
    }
    
}

class DB_Query {
    
    var $d_query;
    var $logon;
    
    // Constructor
    function DB_Query($_d_query = ""){
        $this->d_query = $_d_query;
        $this->logon  = new oci_Logon();
    }
    
    // Ejecuta la query ingresada. Recibe opcionalmente un array de parámetros de la forma $name => $value
    // donde $name es el nombre del parámetro tal como fue ingresado en la Query, y $value el valor del
    // parámetro. Devuelve una matriz por número de fila devuelta y nombre del campo ingresado. 
    function do_query(&$parametros = null, $fetch_param = OCI_ASSOC,&$parametros_array = null,&$parametros_blob = null) {
         
        unset($sql_statement);
        $sql_statement = oci_parse($this->logon->getCon(),$this->d_query);
        
        if($parametros) foreach($parametros as $name => &$value){
        	if(strlen($value) == 0) $value = null;
			
            oci_bind_by_name($sql_statement, $name, $value, 4000);
        }
		
		if($parametros_array) foreach($parametros_array as $name => &$value){      
            oci_bind_array_by_name($sql_statement, $name, $value, 4000, 4000, SQLT_CHR);
        }
		
		if($parametros_blob) foreach($parametros_blob as $name => &$value){
			$value = oci_new_descriptor($this->logon->getCon(), OCI_D_LOB);
			oci_bind_by_name($sql_statement, $name, $value, -1, SQLT_BLOB);
            
        }
        
        $result = oci_execute($sql_statement, OCI_DEFAULT);
       
        if(!$result) {
        	//die($this->d_query);
            $e = oci_error($sql_statement);
            throw new Exc_EjecutarConsulta($e['message'], $this->d_query, $parametros, $_SESSION['usuario'],getCodError());
        }
        
        $rows = array();
        //die($this->d_query);
        while($row = oci_fetch_array($sql_statement, OCI_RETURN_NULLS + $fetch_param)) {
            $rows[] = $row;
        }
        
        return $rows;
        
    }
    
    function db_commit() {
        oci_commit($this->logon->getCon());
        return $this;
    }
    
    function db_rollback() {
        oci_rollback($this->logon->getCon());
        return $this;
    }
    
    // Getters y Setters
    
    function getQuery() {
        return $this->d_query;
    }
    
    function setQuery($_d_query) {
        $this->d_query = $_d_query;
        return $this;
    }
    
}

function getCodError(){
	$query = new DB_Query('SELECT id_error.nextval id_error FROM dual' );
	try{
		$result = $query->do_query();		
	}catch(Exc_EjecutarConsulta $error) {	   
		throw new Exc_EjecutarFunc($error->getMessage(),'DBQUERY-SOLICITUD DE COD ERROR',$query->getQuery(),$query->getParametros(),$_SESSION['usuario'],401);
	} 
		
	return $result[0]['ID_ERROR'];	
	
}

?>