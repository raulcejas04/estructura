<?php 
/**
 * Identificacion de usuarios DGR Chubut.
 * Modulo LOGON.PHP
 */
 //define ("CONFDIR" ,"../../conf");
 require_once ('logon.conf.php');
 
class oci_Logon {
    var
    $MAXTRYS, $band, $USR, $PASS, $DB, $DBCON,$captcha;

    function oci_Logon() {
    	//global $smarty;
        $this->MAXTRYS  = TRYTIMES;
		$this->USR = dbUser;
		$this->PASS = dbPass;
		$this->DB = dbName;
       
		//die(oci_connect('FWBASE','fwbase',$this->DB,"AL32UTF8"));
	   
        //die("USER: ".$this->USR ."PASS: ".$this->PASS ."DB: ".$this->DB);
        //"WE8ISO8859P15"
		if (!$this -> DBCON  = oci_connect($this->USR,$this->PASS,$this->DB,"AL32UTF8")){	
		//if (!$this -> DBCON  = oci_connect('fwbase','fwbase','FWBASE',"AL32UTF8")){	
           // $e = oci_error();
			//trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);	  
		    die("Error al tratar de conectarse a la Base de Datos.");
		    //die("Error al tratar de conectarse a la Base de Datos.".$this->USR.'-'.$this->PASS.'-'.$this->DB);
		  //$smarty -> display ("login/down.tpl");
           //exit;
		}
		
		//SIL $query = "BEGIN www.W_ACCESO.SET_PUBLIC_ROLES; END;";
		//SIL $stmt = OCIParse($this->DBCON, $query);
		//SIL ociexecute($stmt);

		// API
		//$query = 'SELECT instance_name, host_name, version from v$instance';
		//$stmt = ociparse($this->DBCON, $query);
		//ociexecute ($stmt);
		//ocifetchstatement($stmt, $posiciones, 0, -1, OCI_FETCHSTATEMENT_BY_ROW | OCI_ASSOC); 
		//foreach ($posiciones as $pos) {
		//	echo ($pos['INSTANCE_NAME'] . ' - ' . $pos['HOST_NAME'] . ' - ' . $pos['VERSION']);
		//}
		//exit;

		/* 
		 *  --- REGIONALIZACION DE CONECCION ------
		 *  
		 *  - NLS_NUMERIC_CHARACTERS = 'dg'
		 * 		The characters d and g represent the decimal character and group separator, respectively. 
		 * 		They must be different single-byte characters.
		 *  - NLS_TERRITORY
		 *  - NLS_LANGUAGE 
		 */
		
		$query = "alter session set NLS_LANGUAGE=spanish  NLS_NUMERIC_CHARACTERS = '.,' NLS_DATE_FORMAT = 'DD/MM/YYYY'";
		
		$stmt = OCIParse($this->DBCON,$query);
		ociexecute ($stmt);		
    } 

    function isLogged() {
        return (isset($_SESSION['W3S_LOGED']) and ($_SESSION['W3S_LOGED'] == true));
    } 

    function logIn() {
        global $smarty;
                
		$res = 1;
		if (isset($_POST['login_proc']) and !$this->isLogged()){
				$this->registrarIntento();
				if ((!isset($_POST['user'])) or (! isset($_POST['pass']) or $_POST['user'] == "" or $_POST['pass'] == "")){
					$smarty -> assign  ("login_tip","Ingrese sus datos de acceso.");
	    			return 1;		
				}
		        $user = $_POST['user'];
        		$pass = md5($_POST['pass']);
        		
		        if (!ereg("^([-_]|[[:alnum:]])+$",$user)){
					$smarty -> assign  ("login_tip","Ingrese sus datos de acceso correctamente. ");
	    			return 1;		
				}
        		if ($this->isCaptchaNeeded()){
					if ($this->codeInThisPost()){
        				$value = $this->captcha->validate_submit();
						if ($value!=1){
							$smarty -> assign ("login_tip","Asegurece de indicar correctamente el c&oacute;digo de seguridad.");
							$this->mostrarCaptcha();		
							return 1;
						}
						else {
							 $this->resetTrysCount();	
						}
					}
					$this->mostrarCaptcha();
        		}
        		$redirect = true;
			}
		elseif ($this->isLogged()){
			if (!isset($_SESSION['W3S_USER_LOGED']) or ! isset($_SESSION['W3S_PASS_LOGED'])){
					$smarty -> assign  ("login_tip","Ingrese sus datos de acceso.");
	    			return 1;		
			}
		    $user = $_SESSION['W3S_USER_LOGED'];
	    	$pass = $_SESSION['W3S_PASS_LOGED'];
			$res = 0;
			}
			else{
				return 1;
			}
		
		/** 
		 * Identifica contra tabla 
		 * de usuarios en Oracle.
		 */
	
		$p_res = $res;
		$p_res2 = " -------------------------------------------------------------------------------------------------- -------------------------------------------------------------------------------------------------- -------------------------------------------------------------------------------------------------- -------------------------------------------------------------------------------------------------- --------------------------------------------------------------------------------------------------";			
		$sess = session_id();
		$host = $_SERVER['REMOTE_ADDR'];
		$stmt = OCIParse($this->DBCON, "BEGIN WWW.W_ACCESO.LOGIN (:p_wssid, :p_host, :p_cuit, :p_pass, :p_res,:p_res2);end;");
		ocibindbyname	($stmt,	":p_wssid",		$sess, -1);
		ocibindbyname	($stmt,	":p_host",		$host, -1);
		ocibindbyname	($stmt,	":p_cuit",		$user, -1);
		ocibindbyname	($stmt,	":p_pass",		$pass, -1);
		ocibindbyname	($stmt,	":p_res",		$p_res, -1);								
		ocibindbyname	($stmt,	":p_res2",		$p_res2, -1);											
		ociexecute		($stmt);
		
	    if (($p_res != 1)){
        	$smarty -> assign  ("login_tip","Error de autentificacion. Usuario/clave invalido/s");
			return 1;
	    }
		

	    $_SESSION['W3S_LOGED'] 		= true;
    	$_SESSION['W3S_USER_LOGED'] = $user;
	    $_SESSION['W3S_PASS_LOGED'] = $pass;
	    $_SESSION['W3S_NAME_LOGED'] = "";
	    
	    if (isset($redirect) && $redirect) {
	    	header("Location: " .SITIO_URL. "login/novedades.html");
	    	exit;
	    }
	    return;
} 

function logout() {
    unset ($_SESSION['W3S_LOGED']);
   	unset ($_SESSION['W3S_USER_LOGED']);
    unset ($_SESSION['W3S_PASS_LOGED']);
	$_SESSION = array();
} 

function getUser() {
	return  (isset($_SESSION['W3S_USER_LOGED']))?$_SESSION['W3S_USER_LOGED']:"";
}
function getPass() {
	return  $_SESSION['W3S_PASS_LOGED'];
}
function getName() {
	return  (isset($_SESSION['W3S_USER_LOGED']))?$_SESSION['W3S_USER_LOGED']:"";
}

function getDb (){
	return $this->DB;
}
function getCon (){
	if ($this->DBCON == null){ 
		$this->DBCON  = oci_connect($this->getUser(),$this->getPass(),$this->getDb(), "WE8ISO8859P15");
		$query = "alter session set nls_territory=spain nls_language=spanish";
		$stmt = OCIParse($this->DBCON,$query);
		ociexecute ($stmt);
	}
	return $this->DBCON;
}
	
function refreshPass($p_pas){
	        $_SESSION['W3S_PASS_LOGED'] = $p_pas;
}
function registrarIntento(){
	if (!isset($_SESSION['W3S_logtrys']))
		$_SESSION['W3S_logtrys'] = 0;
	else
		$_SESSION['W3S_logtrys']++;
	if ($this->isCaptchaNeeded ()){
		require (CONFDIR. "/captcha.conf.php");
		require_once (MODDIR.  "/utils/hn_captcha/hn_captcha.class.php");
		$this -> captcha = new hn_captcha($CAPTCHA_INIT);
	}
}
function mostrarCaptcha(){
		global $smarty;
		$smarty -> assign ("login_cap_img", $this->captcha->display_captcha(true));
		$smarty -> assign ("public_key",	$this->captcha ->public_key_input());
		$smarty -> assign ("captcha_bg", 	$this->captcha -> getBgcolor());
		ereg("([[:alnum:]][[:alnum:]])([[:alnum:]][[:alnum:]])([[:alnum:]][[:alnum:]])",$this->captcha -> getBgcolor(),$eRGB);
		$RGB = array();
		for ($i=1;$i < count($eRGB); $i++)
			$RGB[] = dechex (hexdec ($eRGB[$i])-10*$i);
		$smarty -> assign ("captcha_bg1", implode($RGB,""));
		$smarty -> assign ("isKaptchaNeeded","true"); 
} 

function resetTrysCount(){
		$_SESSION['W3S_logtrys'] = 0;
}
function codeInThisPost(){
	return ($_SESSION['W3S_logtrys'] - $this->MAXTRYS)> 0;
}
function isCaptchaNeeded (){
	return ($_SESSION['W3S_logtrys'] > $this->MAXTRYS and $this->getUser()==null);
}
}
?>
