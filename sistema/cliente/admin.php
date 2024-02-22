<?php
session_start (); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogincliente.php');

?>
<div class='lateral'>
	<?php include("mod_menu/menu.php");?>
</div>
<div class='barra'> 
<div class='msg'>
	<?php //include("mod_menu/barra.php");?>
</div>
</div>
<div class='centro'>
	<div class='box dashboard'>
    	<div class='titulo'> Bem vindo ao Sistema de Atendimento da Audipam  </div>
        <br>
            Neste ambiente você poderá:<br>
            <ul>
                <li><b>Fazer solicitações</b></li>
                <li><b>Consultar e acompanhar o andamento de cada solicitação</b></li>
            </ul>        
        </div>
    </div>
    </div>
</div>
<script src='../mod_includes/js/w8/scripts.js'></script>
<?php
include('../mod_rodape/rodape.php');
?>
