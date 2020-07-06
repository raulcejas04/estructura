<?PHP 
/**
 * Archivo de configuracion 
 * Modulo logon.php
 */
/**
 * Configuracion DB ORACLE
 */
//define("dbName"		,"MUNIAV");
//informatica/infor#2016@localhost:1522/TRIMU

define("dbName"		,"(DESCRIPTION=( ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP) (HOST=localhost) (PORT=1522)))( CONNECT_DATA=(SID=TRIMU) ))");
define("dbUser"		,"informatica");
define("dbPass"		,"infor#2016");

/**
 * Configuracion Logon -> See: band.conf.php
 */
/* 0 para no banear */
define("TRYTIMES" 	,"0");
define("LOGED"    	,"/");
define("BANNED"    	,"/baned.htm");
/**
 * Configuracion de LOGUEO Para el modulo logon
 */
define("DOLOG"		,false);
?>
