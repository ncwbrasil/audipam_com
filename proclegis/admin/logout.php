<?php 

include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Processo Legislativo</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.png">
<link href="../../core/css/style.css" rel="stylesheet" type="text/css" />
<script src="../../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
</head>
<body>
<?php
$callback = $_SESSION['cliente_url'];
unset($_SESSION['audipam']['webmaster']);
unset($_SESSION['proclegis']);
unset($_SESSION['usuario_name']);
unset($_SESSION['usuario_id']);
unset($_SESSION['usuario_login']);
unset($_SESSION['setor_nome']);
unset($_SESSION['setor_id']);
unset($_SESSION['autor_id']);
unset($_SESSION['cliente_id']);
unset($_SESSION['cliente_url']);
unset($_SESSION['sistema_url']);
session_unset();
session_destroy();
session_write_close();
echo "<SCRIPT LANGUAGE='JavaScript'>
		window.location.href = 'login/".$callback."';
</SCRIPT>";
?>
</body>
</html>