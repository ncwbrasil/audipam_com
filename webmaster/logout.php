<?php 
//include_once("url.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../core/mod_includes/php/connect_sistema.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo include_once("url.php");?>
<title>Audipam | Gerenciador de Sistemas</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.png">
<link href="../core/css/style.css" rel="stylesheet" type="text/css" />
<script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
</head>
<body>
<?php
unset($_SESSION['sweetpics']);
unset($_SESSION['setor']);
session_write_close();
echo "<SCRIPT LANGUAGE='JavaScript'>
		window.location.href = 'login/".$_SESSION['cliente_url']."';
</SCRIPT>";
?>
</body>
</html>