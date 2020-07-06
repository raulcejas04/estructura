<?php
class procedimientos
{
public $sal;

function procedimientos( $titulo,$id_menu, $principal )
{
$principales=db_principal_procedimientos( $id_menu, $principal );

$sal_proc.="\n<table id='basic' border=1>\n";

$i=1;
$sal_proc.="<tr data-node-id='1'><td>1</td><td>".$id_menu."-".$titulo."-".$principal."<td></tr>\n";


foreach( $principales as $pri )
{
	$k=1;

	if( $pri['tipo']=='PROC' )
	{
		$j++;
		$nodo="1.".$j;
		$sal_proc.="<tr data-node-id='".$nodo."' data-node-pid='1'><td>".$nodo."</td><td style='color:red'>".$pri['funcion']."->".$pri['nombre']."</td></tr>\n";
		$sal_proc.=$this->pant_proc_hijos( $pri['nombre'], $nodo, $k );
		$sal_proc.=$this->pant_tablas_hijas( $pri['nombre'], $nodo, $k );
	}

	if( $pri['tipo']=='VALID' )
	{
		$j++;
		$nodo="1.".$j;
		$sal_proc.="<tr data-node-id='".$nodo."' data-node-pid='1'><td>".$nodo."</td><td style='color:red'>".$pri[funcion]."->".$pri['nombre']."</td></tr>\n";
		$sal_proc.=$this->pant_proc_hijos( $pri['nombre'], $nodo, $k );
		$sal_proc.=$this->pant_tablas_hijas( $pri['nombre'], $nodo, $k );
	}

	if( $pri['tipo']=='RAROS' )
	{
		$j++;
		$nodo="1.".$j;
		$sal_proc.="<tr data-node-id='".$nodo."' data-node-pid='1'><td>".$nodo."</td><td style='color:red'>".$pri[funcion]."->".$pri['nombre']."</td></tr>\n";
		$sal_proc.=$this->pant_proc_hijos( $pri['nombre'], $nodo, $k );
		$sal_proc.=$this->pant_tablas_hijas( $pri['nombre'], $nodo, $k );
	}
}
$sal_proc.="</table>";
$this->sal=$sal_proc;
}//function

function pant_proc_hijos( $procedimiento, $nodo, &$k )
{

if( strlen($nodo)>10 )
	return;

$proc_hijos=db_procedimientos_hijos( $procedimiento );

foreach($proc_hijos as $proc )
{
	$nodo_hijo=$nodo.".".$k;

	$sal.="<tr data-node-id='".$nodo_hijo."' data-node-pid='".$nodo."'><td>".$nodo_hijo."</td><td style='color:red'>".$proc['hijo']."-".$proc['tipo_hijo']."</td></tr>\n";

	$proc_nietos=db_procedimientos_hijos( $proc['hijo'] );
	$tabla_nietos=db_tablas_hijas( $proc['hijo'] );
	if( sizeof($proc_nietos)>0 OR sizeof($tabla_nietos)>0)
	{
		$l=1;
		$sal.=$this->pant_proc_hijos( $proc['hijo'],$nodo_hijo,$l);
		$sal.=$this->pant_tablas_hijas( $proc['hijo'],$nodo_hijo,$l);
	}
	$k++;
}
return $sal;
}

function pant_tablas_hijas($procedimiento, $nodo, &$k)
{
if( strlen($nodo)>10 )
	return;

$tablas_hijas=db_tablas_hijas( $procedimiento );

foreach($tablas_hijas as $tabla )
{
	$nodo_hijo=$nodo.".".$k;

	$sal.="<tr data-node-id='".$nodo_hijo."' data-node-pid='".$nodo."'><td>".$nodo_hijo."</td><td style='color:blue'>".$tabla['tabla']."<font color='orange'>".$tabla['crud']."</font></td></tr>\n";

	$k++;
}
return $sal;
}//function

}//class
?>
