<?php
class db_cursor
{
public $res;
public $sql;
public $registros;

function db_ejecutar( $archivo=null, $funcion=null, $linea=null, $excepcion=null )
{
global $conexio;

$tt1=time();
$t1=date('H:i:s');
if( !$conexio )
	terminar( null,null, "No se pudo conectar a la base de datos", $this->res);


if( !$this->sql )
	terminar( null, null, "Falta inicializar la sentencia<br>".$error, $this->res );

//print_r($conexio);

$this->res=mysqli_query( $conexio, $this->sql );

if( !$this->res ) 
{

	$error="<br>Error en archivo: $archivo<br>funcion: $funcion<br>linea: $linea<br>Error:".mysqli_error($conexio)."<br>";

	if( $excepcion )
	{
		throw new Exception( $error );
	}
	else
	{
		terminar( $this->sql,null,$error, $this->res );
	}
}
elseif( substr($this->sql,0,strlen("SELECT"))=="SELECT" and strpos($this->sql,"@")===FALSE )
{
	$this->registros=mysqli_num_rows($this->res);
}

$t2=date('H:i:s');
$tt2=time();

return;
}

function db_avanzar( $assoc=0)
{
	return mysqli_fetch_array( $this->res );
}

function db_cantidad()
{
	return $this->registros=mysqli_num_rows($this->res);
}
function db_avanzar_assoc( $assoc=0)
{
	return mysqli_fetch_assoc($this->res);
}

function db_select_base($base)
{
	global $conexio;
	//echo "base $base<br>";

	return mysqli_select_db($conexio, $base);
}

function db_insert_id(&$conexion)
{
return mysqli_insert_id($conexion);
}

function success()
{
if( $this->res )
	return (mysqli_num_rows($this->res)>0);
else	return false;
}
}//class db_sql


function db_avanzar( $res )
{
	return mysqli_fetch_array($res);
}

function terminar( $sql, $programa=null, $comentario=null, $res=null )
{


//echo "progr $programa<br>";

if( isset( $comentario )) $comentario="Error en ".$comentario;
else $comentario="Error de sql ";
if( $programa != null )
{ 
	unset_sesion( $programa );
	if( isset( $_SESSION[programa] )) unset( $_SESSION[programa] );
}

die($comentario.mysqli_error($res)."<br>".$sql);
}


function unset_sesion( $sub_str )
{
//esta funcion elimina las variables de session del programa
//para ello supone que cada programa define las variables con
//el mismo prefijo + "_"
//recibe como parametro el nombre del programa

//devuleve false sino se le paso parametro y true cuando se le
//paso parametro

//echo "entro en unset_sesion<br>";

if( !isset( $sub_str )) return false;
//echo "antes";print_r( $_SESSION ); echo "<br>";

$tam=strlen($sub_str);
$tam_matriz=0;
$tam_matriz=sizeof( $_SESSION );

if( $tam_matriz > 0 )
{
	while( list($clave,$valor)=each( $_SESSION) AND $tam_matriz>0 )
	{
		if( substr( $clave,0,$tam+1 ) == ($sub_str.'_') )
		{
			$valor=$GLOBALS[_SESSION][$clave];
			$GLOBALS[_SESSION][$clave]=NULL;
			unset( $GLOBALS[_SESSION][$clave] );
		}
	}
}
//echo "despues";print_r( $_SESSION ); echo "<br>";
return true;
}


?>
