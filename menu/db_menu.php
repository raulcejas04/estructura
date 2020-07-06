<?php

function db_solapas()
{
$cursor=new db_cursor();

//solapas sino tiene padres
$cursor->sql="SELECT a.*
		FROM menu a 
		WHERE a.id_menu_padre=0";
		
$cursor->db_ejecutar();
while( $solapa=$cursor->db_avanzar() )
{
	$solapas[]=$solapa;
	if( $excluir_solapas )
		$excluir_solapas.=",";
	$excluir_solapas.=$solapa['id_menu'];
}

return array( $solapas, $excluir_solapas );
}

function db_arbol( $excluir_solapas )
{
$cursor=new db_cursor();

//tiene hijos ?
$cursor->sql="SELECT a.id_menu,a.id_menu_padre,a.d_titulo,a.d_url
		FROM menu a LEFT JOIN menu b ON b.id_menu_padre=a.id_menu
		WHERE a.id_menu NOT IN (".$excluir_solapas.") AND
		b.id_menu IS NULL";
$cursor->sql="SELECT a.id_menu,a.id_menu_padre,a.d_titulo,a.d_url
		FROM menu a 
		WHERE a.id_menu NOT IN (".$excluir_solapas.")";

$cursor->db_ejecutar();
while( $hoja=$cursor->db_avanzar() )
{
	$arbol[]=$hoja;
}
return $arbol;
}


function db_principal_procedimientos( $id_menu, $principal )
{
$cursor=new db_cursor();

//tiene hijos ?
$cursor->sql="SELECT n_orden,'' as funcion,
			UPPER(SUBSTRING(d_procedure,POSITION('.' IN d_procedure)+1)) as nombre,
			'PROC' as tipo
		FROM maestro
		WHERE id_menu=$id_menu AND LENGTH(TRIM(d_procedure))>0
		UNION ALL
		SELECT n_orden_2 as n_orden,'' as funcion,UPPER(SUBSTRING(d_validacion,POSITION('.' IN d_procedure)+1)) as nombre,'VALID' as tipo
		FROM maestro
		WHERE id_menu=$id_menu  AND LENGTH(TRIM(d_validacion))>0
		UNION ALL
		SELECT 0 as n_orden,funcion,UPPER(procedimiento) as nombre,'RAROS' as tipo
		FROM procedimientos_raros
		WHERE principal='$principal'";

$cursor->db_ejecutar();
while( $proc=$cursor->db_avanzar() )
{
	$procedimientos[]=$proc;
}
return $procedimientos;
}


function db_procedimientos_hijos( $procedimiento )
{
$cursor=new db_cursor();

//tiene hijos ?
$cursor->sql="SELECT DISTINCT hijo,tipo_hijo
		FROM proc_total
		WHERE
		padre='$procedimiento'";

$cursor->db_ejecutar();
while( $proc=$cursor->db_avanzar() )
{
	$procedimientos[]=$proc;
}
return $procedimientos;
}


function db_tablas_hijas( $procedimiento )
{
$cursor=new db_cursor();

//tiene hijos ?
/*$cursor->sql="SELECT DISTINCT p.tabla,p.tipo_tabla,c.crud
		FROM proc_tables p 
			LEFT JOIN proc_tabla_crud c ON UPPER(TRIM(p.procedimiento))=UPPER(TRIM(c.procedimiento)) AND 
				UPPER(TRIM(p.tabla))=UPPER(TRIM(c.tabla))
		WHERE
		UPPER(TRIM(p.procedimiento))=UPPER(TRIM('$procedimiento'))*/



$cursor->sql="SELECT DISTINCT b.tabla,'' as tipo,b.sentencia as crud
		FROM proc_tabla_body b
		WHERE
		UPPER(procedimiento)=UPPER('".$procedimiento."')
		UNION
		SELECT DISTINCT p.tabla,p.tipo_tabla,c.crud
		FROM proc_tables p 
			LEFT JOIN proc_tabla_crud c ON UPPER(TRIM(p.procedimiento))=UPPER(TRIM(c.procedimiento)) AND 
				UPPER(TRIM(p.tabla))=UPPER(TRIM(c.tabla))
		WHERE
		UPPER(TRIM(p.procedimiento))=UPPER(TRIM('$procedimiento'))";
//echo $cursor->sql."<br>";
$cursor->db_ejecutar();
while( $proc=$cursor->db_avanzar() )
{
	$procedimientos[]=$proc;
}
return $procedimientos;
}

?>
