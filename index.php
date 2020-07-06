<?php
session_start();
include "./include/conectar.php";
include "./include/terminar.php";
include "./menu/pant_menu.php";
include "./menu/db_menu.php";

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>jQuery Dropdown On Hover Plugin Demos</title>
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-dropdownhover.css">
<style>
body { background-color:#eee;}
.container{
white-space: nowrap;
}
.dropdown-inline {
	display: inline-block;
	position: relative;
}
</style>
</head>
<body><div id="jquery-script-menu"></div>
<div class="bs-demo-dropdowns" style="margin-top:150px;">
        <div class="container">
<?php


$menu=new menu();
echo $menu->sal;

?>
<script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/bootstrap-dropdownhover.js"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</div></div></body></html>
