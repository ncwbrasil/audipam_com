<?php
ob_start();	
include("url.php");
print_r($_SESSION);
include("../../core/mod_includes/php/connect.php");
include("../../core/mod_includes/php/funcoes.php");
include("../../core/mod_includes/php/class.ipdetails.php");
sec_session_start();

include("../../core/mod_includes/php/dadosGerais.php");


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
<title>Processo Legislativo</title>
<link href="../mod_includes/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../core/mod_includes/js/jquery-2.1.4.js"></script>
</head>
<div id='janela' class='janela' style='display:none;'> </div>

<?php
include("../mod_includes/php/funcoes-jquery.php");

if(isset($_POST['email']) &&  isset($_POST['senha']))
{
	$email = $_POST['email'];
	$senha = hash('sha512',$_POST['senha']);

	$result = selectLoginUsuario($PDO_PROCLEGIS, $email, $senha);

	if (!empty($result))
	{
		
		$usu_status 		= $result[0]['usu_status'];
		$usu_cliente 		= $result[0]['usu_cliente'];
		$usu_id 			= $result[0]['usu_id'];
		$usu_setor 			= $result[0]['usu_setor'];
		$set_nome 			= $result[0]['set_nome'];
		$usu_nome 			= $result[0]['usu_nome'];
		$autor 				= $result[0]['id'];
		
		# PEGA NOME SISTEMA E CLIENTE #
		$sql = "SELECT * FROM cadastro_clientes
				INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
				WHERE cli_id = :cli_id ";
		$stmt = $PDO->prepare( $sql );
		$stmt->bindParam( ':cli_id', $usu_cliente);
		$stmt->execute();
		$rows = $stmt->rowCount();	

		if($rows > 0)
		{
			$result = $stmt->fetch();	
			$sis_url 	= $result['sis_url'];	
			$cli_url 	= $result['cli_url'];			
		}
		
		if ($usu_status == 0)
		{
		
			echo "
				<SCRIPT language='JavaScript'>
					abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
					'<img src=../../core/imagens/x.png> Seu usuário está desativado, por favor contate o administrador do sistema.<br><br>'+
					'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
				</SCRIPT>
				";
				
			exit;
		}
		else
		{
			
			$ip_address 	= $_SERVER['REMOTE_ADDR']; // Pega o endereço IP do usuário. 
            $user_browser 	= $_SERVER['HTTP_USER_AGENT']; // Pega a string de agente do usuário.

			$_SESSION['proclegis'] 		= hash('sha512',$senha.$ip_address.$user_browser);
			$_SESSION['usuario_name'] 	= $usu_nome;
			$_SESSION['usuario_id'] 	= $usu_id;
			$_SESSION['usuario_login']	= $email;
			$_SESSION['setor_nome'] 	= $set_nome;
			$_SESSION['setor_id'] 		= $usu_setor;			
			$_SESSION['autor_id'] 		= $autor;			
			$_SESSION['cliente_id'] 	= $usu_cliente;
			$_SESSION['cliente_url'] 	= $cli_url;
			$_SESSION['sistema_url'] 	= $sis_url;
			


			$dados = array(
				'log_usuario' => $usu_id,
				'log_hash' => $_SESSION['proclegis'],
				'log_ip' => $ip,
				'log_cidade' => $cidade,
				'log_regiao' => $regiao,
				'log_pais' => $pais				
			);

			$rows = insertLogUsuario($PDO_PROCLEGIS,$dados);
			
			
			if($rows > 0)
			{								
				header("location: ../dashboard");				
			}
		}	
	}
	else
	{
		
		$_SESSION['proclegis'] = 'N';
		$observacao = "Falha login: $email | $senha";

		$dados = array(			
			'log_observacao' => $observacao,
			'log_ip' => $ip,
			'log_cidade' => $cidade,
			'log_regiao' => $regiao,
			'log_pais' => $pais				
		);

		$rows = insertLogUsuario($PDO_PROCLEGIS,$dados);

		if($rows > 0)
		{
			
			echo "
			<SCRIPT language='JavaScript'>
				abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
				'<img src=../../core/imagens/x.png> Login ou senha incorreta.<br>Por favor tente novamente.<br><br>'+
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
		abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
		'<img src=../../core/imagens/x.png> Requisição incorreta.<br><br>'+
		'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
	</SCRIPT>
	";
}
?>