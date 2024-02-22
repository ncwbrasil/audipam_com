<?php
session_start ();
require_once("../mod_includes/php/ctracker.php");

include('../mod_includes/php/connect.php');
function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
	{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

include("../mod_includes/php/class.ipdetails.php");
$ip = getIp();
$ipdetails = new ipdetails($ip); 
$ipdetails->scan();
$pais = $ipdetails->get_countrycode();
$regiao = $ipdetails->get_region();
$cidade = $ipdetails->get_city();
include('../mod_includes/php/caracter_especial.php');

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="author" content="Gustavo Costa">
<meta http-equiv="Content-Language" content="pt-br">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Audipam - Sistema Administrativo Integrado</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
</head>

<div id='janela' class='janela' style='display:none;'> </div>
<div id='janelaAcao' class='janelaAcao' style='display:none;'> </div>
<?php

include('../mod_includes/php/funcoes-jquery.php');

$login = $_POST['login'];
$senha = md5($_POST['senha']);
$sql = "SELECT * FROM admin_usuarios 
		INNER JOIN admin_setores ON admin_setores.set_id = admin_usuarios.usu_setor
		WHERE usu_login = :login AND usu_senha = :senha";
$stmt = $PDO->prepare( $sql );
$stmt->bindParam( ':login', $login );
$stmt->bindParam( ':senha', $senha );
$stmt->execute();
$rows = $stmt->rowCount();

if ($rows > 0)
{
	while ($field = $stmt->fetch()) 
	{     
		$status 	= $field['usu_status'];
		$usu_id 	= $field['usu_id'];
		$n 			= $field['usu_nome'];
		$s 			= $field['usu_setor'];
		$s_n 		= $field['set_nome'];
		$s_id 		= $field['set_id'];
		$homologador 		= $field['usu_homologador'];
	}
	if ($status == 0)
	{
		echo "&nbsp;
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/x.png> Seu usuário está desativado, por favor contate o administrador do sistema.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
			</SCRIPT>
			";
	}
	else
	{		
	   	$_SESSION['audipam_sistema'] 	= $login.md5($n);
	   	$_SESSION['n'] 					= $n;
	   	$_SESSION['setor'] 				= $s;
	   	$_SESSION['setor_nome'] 		= $s_n;
	   	$_SESSION['usuario_id'] 		= $usu_id;
		$_SESSION['setor_id'] 			= $s_id;
		$_SESSION['homologador'] 		= $homologador;
		$sql = " INSERT INTO admin_log_login (log_usuario, log_hash, log_ip, log_cidade, log_regiao, log_pais) VALUES (:id, :hash, :ip, :cidade, :regiao, :pais)";
		$stmt = $PDO->prepare( $sql );
		$stmt->bindParam( ':id', 		$usu_id );
		$stmt->bindParam( ':hash', 		$_SESSION['audipam_sistema']);
		$stmt->bindParam( ':ip', 		$ip );
		$stmt->bindParam( ':cidade', 	$cidade );
		$stmt->bindParam( ':regiao', 	$regiao );
		$stmt->bindParam( ':pais', 		$pais );
		if($stmt->execute())
		{
			echo "<script language='JavaScript'>self.location = 'admin.php?login=$login&n=$n'</script>";
		}
	}

}
else
{
  	$_SESSION['audipam_sistema'] = 'N';
   	$sql = " INSERT INTO admin_log_login (log_ip, log_observacao, log_cidade, log_regiao, log_pais) VALUES (:ip, :observacao, :cidade, :regiao, :pais)";
	$stmt = $PDO->prepare( $sql );
	$observacao = "Falha login: $login | $senha";
	$stmt->bindParam( ':ip', 			$ip );
	$stmt->bindParam( ':observacao', 	$observacao);
	$stmt->bindParam( ':cidade', 		$cidade );
	$stmt->bindParam( ':regiao', 		$regiao );
	$stmt->bindParam( ':pais', 			$pais );
	$stmt->execute();
	
  	echo "&nbsp;
   		<SCRIPT language='JavaScript'>
			abreMask(
			'<img src=../imagens/x.png> Login ou senha incorreta.<br>Por favor tente novamente.<br><br>'+
			'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
		</SCRIPT>
   		";
}

?>