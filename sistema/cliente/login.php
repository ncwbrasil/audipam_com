<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');

?>
<div class='lateral'>
	<?php include("mod_menu/menu_login.php");?>
</div>
<div class='barra'> 
   
</div>
<div class='centro'>
	<div class='box'>
        <form name='form_login' id='form_login' enctype='multipart/form-data' method='post' autocomplete='off' action='envialogin.php'>
            <div class='titulo'> Acesso Restrito - Ambiente Cliente </div>
            <div id='interna'>
            <table align='center' class='margemtab' cellspacing='10'>
                <tr>
                    <td>
                        <span class='textopeq'>Digite seu usuário e senha para acessar o sistema.</span><br>
                    </td>
                </tr>
                <tr>
                    <td align='center'>
                        <input name='login' id='login' placeholder='Login' size='20'>
                    </td>
                </tr>
                <tr >
                    <td align='center'>
                        <input type='password' name='senha' id='senha' placeholder='Senha' size='20'>
                    </td>
                </tr>
                <tr >
                    <td  align='center' height='30' valign='bottom'>
                        <input type='submit' id='bt_login' value=' Entrar no Sistema ' name='B1'>
                    </td>
                </tr>
            </table>
            </div>
        </form>
  	</div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
