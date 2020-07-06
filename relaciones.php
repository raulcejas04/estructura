<?php
session_start();
include "./include/conectar.php";
include "./include/terminar.php";
include "./menu/pant_proc.php";
include "./menu/db_menu.php";

/*print_r($_GET );*/

$id_menu=$_GET['id_menu'];
$d_titulo=$_GET['d_titulo'];
$d_url=$_GET['d_url'];


$pant=new procedimientos( $d_titulo,$id_menu, $d_url );
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<script src="dist/jquery-simple-tree-table.js"></script>
</head>
</body>
<a href='./index.php'>MENU</a>
  <!--<button type="button" id="expander">expand</button>
  <button type="button" id="collapser">collapse</button>
  <button type="button" id="open1">open 1</button>
  <button type="button" id="close1">close 1</button>
-->
<?
echo $pant->sal;
?>
  <script>
  $('#basic').simpleTreeTable();
  </script>
</body>
</html>
