<?php
ob_start();	
//include_once("url.php");
include("../core/mod_includes/php/connect_sistema.php");
include("../core/mod_includes/php/funcoes.php");
include("../core/mod_includes/php/class.ipdetails.php");
sec_session_start();
$ip = getIp();
$ipdetails = new ipdetails($ip); 
$ipdetails->scan();
$pais = $ipdetails->get_countrycode();
$regiao = $ipdetails->get_region();
$cidade = $ipdetails->get_city();
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Language" content="pt-br">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Audipam | Gerenciador de Sistemas</title>
<link href="../core/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../core/mod_includes/js/jquery-2.1.4.js"></script>
</head>
<div id='janela' class='janela' style='display:none;'> </div>

<?php
include("../core/mod_includes/php/funcoes-jquery.php");
if(isset($_POST['email']) &&  isset($_POST['senha']))
{
	$email = $_POST['email'];
	$senha = hash('sha512',$_POST['senha']);

	$result = selectLoginUsuarioWebmaster($PDO, $email, $senha);
	if (!empty($result))
	{
		$usu_status 		= $result[0]['usu_status'];
		$usu_id 			= $result[0]['usu_id'];
		$usu_setor 			= $result[0]['usu_setor'];
		$set_nome 			= $result[0]['set_nome'];
		$usu_nome 			= $result[0]['usu_nome'];
		
		if ($usu_status == 0)
		{
			echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../core/imagens/x.png> Seu usuário está desativado, por favor contate o administrador do sistema.<br><br>'+
					'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
				</SCRIPT>
				";
			exit;
		}
		else
		{
			
			$ip_address 	= $_SERVER['REMOTE_ADDR']; // Pega o endereço IP do usuário. 
            $user_browser 	= $_SERVER['HTTP_USER_AGENT']; // Pega a string de agente do usuário.

			$_SESSION['webmaster']['audipam'] 		= hash('sha512',$senha.$ip_address.$user_browser);
			$_SESSION['usuario_name'] 	= $usu_nome;
			$_SESSION['usuario_id'] 	= $usu_id;
			$_SESSION['usuario_login']	= $email;
			$_SESSION['setor_nome'] 	= $set_nome;
			$_SESSION['setor_id'] 		= $usu_setor;			
			

			

			$dados = array(
				'log_usuario' => $usu_id,
				'log_hash' => $_SESSION['webmaster']['audipam'],
				'log_ip' => $ip,
				'log_cidade' => $cidade,
				'log_regiao' => $regiao,
				'log_pais' => $pais				
			);

			$rows = insertLogUsuario($PDO,$dados);
			
			
			if($rows > 0)
			{				
				header("location: ../dashboard");
			}
		}	
	}
	else
	{
		$_SESSION['webmaster']['audipam']  = 'N';
		$observacao = "Falha login: $email | $senha";

		$dados = array(			
			'log_observacao' => $observacao,
			'log_ip' => $ip,
			'log_cidade' => $cidade,
			'log_regiao' => $regiao,
			'log_pais' => $pais				
		);

	    $rows = insertLogUsuario($PDO,$dados);

		if($rows > 0)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../core/imagens/x.png> Login ou senha incorreta.<br>Por favor tente novamente.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
			</SCRIPT>
			";
		}
		
	}
}
else
{
	echo "
	<SCRIPT language='JavaScript'>
		abreMask(
		'<img src=../core/imagens/x.png> Requisição incorreta.<br><br>'+
		'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
	</SCRIPT>
	";
}
?>