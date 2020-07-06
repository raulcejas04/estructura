<?php
class menu
{
private $solapas;
private $excluir_solapas;
private $arbol;
public $sal;

function menu()
{
list( $this->solapas, $this->excluir_solapas ) = db_solapas();

$this->arbol=db_arbol($this->excluir_solapas);

//print_r($this->arbol);
$this->sal="";

$i=1;
foreach( $this->solapas as $solapa )
{
	$nietos=$this->hijos($solapa['id_menu']);
	if( sizeof($nietos)>0 )
	{

	//echo "id ".$solapa['id_menu']."solapa ".$solapa['d_titulo']."<br>";
	$this->sal.="
<div class='dropdown dropdown-inline'>
   <a href='#' class='btn btn-default dropdown-toggle' data-toggle='dropdown' data-hover='dropdown'>".$solapa['d_titulo']."<span class='caret'></span></a>
\t<ul class='dropdown-menu' role='menu'>\n".$this->arbol($solapa['id_menu'],0 )."
\t</ul>
</div>\n\n";
	}

}

return;
}//function


function arbol( $id_menu, $i )
{

for( $j=1;$j<=$i;$j++ )
{
	$tabu.="\t";
	$pre1.="<blockquote>";
	$pre2.="</blockquote>";
}

$hijos=$this->hijos($id_menu);

foreach($hijos as $hijo )
{
	//echo $pre1."id ".$hijo['id_menu']."solapa ".$hijo['d_titulo']."<br>".$pre2;

	$nietos=$this->hijos($hijo['id_menu']);
	if( sizeof($nietos)>0 )
	{
		$sal.="
{$tabu}<li class='dropdown'>
{$tabu}\t<a href='#'>".$hijo['d_titulo']."<span class='caret'></span></a>
{$tabu}\t<ul class='dropdown-menu dropdownhover-right'>\n".$this->arbol($hijo['id_menu'],$i+1).
"{$tabu}\t</ul>
{$tabu}</li>\n";
	}
	else
	{
		$sal.="{$tabu}<li><a href='./principal.php?id_menu=".$hijo['id_menu']."&d_titulo=".$hijo['d_titulo']."&d_url=".$hijo['d_url']."'>".$hijo['d_titulo']."</a></li>\n";
	}

}
return $sal;
}

function hijos( $id_menu )
{
reset($this->arbol);
foreach( $this->arbol as $hoja )
{
	if( $hoja['id_menu_padre']==$id_menu )
	{
		$hijos[]=$hoja;
	}
}
return $hijos;
}

}//class
?>
