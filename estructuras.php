<?php
session_start();
include "./include/login.php";
include './include/db_query.php';

$tabla=$_GET['tabla'];

$secundarias=db_tablas_hijas($tabla);

$tabla_ant="";
$clave_ant="";

$sal="";
$sal.="<H3>TABLAS HIJAS<H3>";
$sal.="<table>";
foreach( $secundarias as $sec )
{
	//print_r($sec);
	//die();
	if( $tabla_ant!=$sec['TABLA_HIJA'] OR $tabla_ant==NULL )
	{
		$sal_tabla=$tabla_ant=$sec['TABLA_HIJA'];
	}
	else	$sal_tabla="";

	if( $clave_ant!=$sec['CONSTRAINT_NAME'] OR $clave_ant==NULL )
	{
		$sal_clave=$clave_ant=$sec['CONSTRAINT_NAME'];
	}
	else	$sal_clave="";

	$sal.="<tr>
		<td><a href='estructuras.php?tabla=".$sec['TABLA_HIJA']."'>".$sal_tabla."</a></td>
		<td>".$sal_clave."</td>
		<td>".$sec['COLUMNA_HIJA']."</td>
		<td>".$sec['COLUMNA_PADRE']."</td>
		<td>".$sec['DELETE_RULE']."</td>
		</tr>";
}
$sal.="</table><br><br>";

$secundarias=array();
$secundarias=db_tablas_padres($tabla);

$tabla_ant="";
$sal.="<H3>TABLAS PADRE<H3>";
$sal.="<table>";
foreach( $secundarias as $sec )
{
	//print_r($sec);
	//die();
	if( $tabla_ant!=$sec['TABLA_PADRE'] OR $tabla_ant==NULL )
	{
		$sal_tabla=$tabla_ant=$sec['TABLA_PADRE'];
	}
	else	$sal_tabla="";

	if( $clave_ant!=$sec['CLAVE_HIJA'] OR $clave_ant==NULL )
	{
		$sal_clave=$clave_ant=$sec['CLAVE_HIJA'];
	}
	else	$sal_clave="";

	$sal.="<tr>
		<td><a href='estructuras.php?tabla=".$tabla."'>".$sal_tabla."</a></td>
		<td>".$sal_clave."</td>
		<td>".$sec['COLUMNA_PADRE']."</td>
		<td>".$sec['COLUMNA_HIJA']."</td>
		<td>".$sec['CLAVE_PADRE']."</td>
		</tr>";
}
$sal.="</table>";


echo $sal;



function db_tablas()
{

$connect=new oci_Logon();

$db_query = new DB_Query( "SELECT table_name FROM all_tables WHERE owner='MUNI'
				and table_name NOT LIKE 'REP_%'
				and table_name NOT LIKE 'TMP_%'
				and table_name NOT LIKE '%HIST%'
				ORDER BY table_name");

$rows = $db_query->do_query();

echo "<table>";
foreach( $rows as $r )
{
	echo "<tr>
		<td>".$r['TABLE_NAME']."</td>
		</tr>";
}
echo "</table>";
}

function db_tablas_hijas( $tabla )
{

$db_query = new DB_Query( 
	"select b.table_name tabla_hija,b.column_name columna_hija,
         b.position,
         c.table_name tabla_padre,c.column_name columna_padre,
         a.constraint_name,
         a.delete_rule
    	from all_cons_columns b,
         all_cons_columns c,
         all_constraints a
	where b.constraint_name = a.constraint_name
	     and a.owner           = b.owner
	     and b.position        = c.position
	     and c.constraint_name = a.r_constraint_name
	     and c.owner           = a.r_owner
	     and a.constraint_type = 'R'
	     and c.owner='MUNI' 
	     AND c.table_name like '%".$tabla."'
	order by b.table_name,a.constraint_name,b.column_name,b.position" );

$rows = $db_query->do_query();
return $rows;
}

function db_tablas_padres( $tabla )
{

$sql="select a.r_constraint_name clave_padre,a.constraint_name clave_hija,
    		c.table_name as tabla_padre,c.column_name as columna_padre,
		b.column_name as columna_hija
    from
       all_constraints a,
       all_cons_columns b,
       all_cons_columns c
    where
    a.constraint_name = b.constraint_name and
    a.owner           = b.owner and
    a.r_constraint_name = c.constraint_name and
    a.r_owner           = c.owner and
    b.position=c.position and
    a.owner='MUNI' AND
    a.table_name='".$tabla."' and
    a.constraint_type ='R'
    ORDER BY c.table_name,b.constraint_name,c.position";

$db_query = new DB_Query( $sql );

$rows = $db_query->do_query();
return $rows;
}

?>
