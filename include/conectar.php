<?php
$conexio = mysqli_connect('localhost','raul','raul','trimu'); 
if(!conexio) 
{
	die( "error conexion ".mysqli_connect_errno()."-".mysqli_connect_error()."<br>" );
}
?>
