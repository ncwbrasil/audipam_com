<?php
ob_start();	
include_once("url.php");
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
include_once("../../core/mod_includes/php/dadosGerais.php");
include_once('../mod_includes/php/funcoes-jquery.php');

if(isset($_SESSION['webmaster']['audipam']) && $cli_url != '')
{
	
	$sql = "SELECT * FROM cadastro_clientes
          LEFT JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
          WHERE sis_nome = :sis_nome AND cli_url = :cli_url AND cli_status = :cli_status";
	$stmt = $PDO->prepare( $sql );
	$sis_nome	= "Processo Legislativo / Leis";
	$stmt->bindParam( ':sis_nome', $sis_nome);
	$stmt->bindParam( ':cli_url', $cli_url);
	$stmt->bindValue( ':cli_status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		$result = $stmt->fetch();
		$_SESSION['cliente_id'] 	= $result['cli_id'];
		$_SESSION['cliente_name'] 	= $result['cli_nome'];
		$_SESSION['cliente_url'] 	= $cli_url;		
		$_SESSION['sistema_url'] 	= $result['sis_url'];		
		header("location: ../dashboard");		
	}
}
if($cli_url == "")
{
	exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : "";
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Processo Legislativo</title>
<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../../core/imagens/favicon.png">
<link href="../mod_includes/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../core/mod_includes/js/jquery-2.1.4.js"></script>
</head>
<body class='login'>
<div id='login'>
	<div class="logo">
		<img src='../<?php echo $cli_foto;?>'  alt="Logo" />
    </div>
	<div class='box'>
        <form name='form_login' id='form_login' enctype='multipart/form-data' method='post' autocomplete='off' action='login/<?php echo $cli_url;?>/send'>
            <p class="titulo">ACESSO RESTRITO</p>
            <input name='email'  id='email' class='login' placeholder='Email' value='<?php echo $_POST['email'];?>'><p>
            <input type='password' name='senha'  id='senha' class='login' value='<?php echo $_POST['senha'];?>' placeholder='Senha'><p>
			<div class='mensagem'></div><p>  
			<div class='qrcode' style='text-align:left;'></div><p>
            <input type='submit' value=' Entrar no Sistema ' id='bt-login' class='login'>
        </form>
  	</div>
    <p>
    
</div>
<?php

if($action == "send")
{
	
    if(isset($_POST['email']) &&  isset($_POST['senha']))
	{
		$email = $_POST['email'];
		$senha = hash('sha512',$_POST['senha']);

		$result = selectLoginUsuario($PDO_PROCLEGIS, $email, $senha);

		if (!empty($result))
		{
			$usu_status 		= $result[0]['usu_status'];
			$usu_id 			= $result[0]['usu_id'];
			$usu_setor 			= $result[0]['usu_setor'];
			$set_nome 			= $result[0]['set_nome'];
			$usu_nome 			= $result[0]['usu_nome'];
			$autor 				= $result[0]['id'];

			# PEGA NOME SISTEMA E CLIENTE #
			$sql = "SELECT * FROM cadastro_clientes
					INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
					WHERE cli_url = :cli_url ";
			$stmt = $PDO->prepare( $sql );
			$stmt->bindParam( ':cli_url', $cli_url);
			$stmt->execute();
			$rows = $stmt->rowCount();	

			if($rows > 0)
			{
				$result = $stmt->fetch();	
				$sis_url 	= $result['sis_url'];	
				$cli_url 	= $result['cli_url'];	
				$cli_id 	= $result['cli_id'];
			}
			
			if ($usu_status == 0)
			{
			
				?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Seu usuário está desativado, por favor contate o administrador do sistema.");
				</script>
				<?php 
					
				exit;
			}
			else
			{
				
				//Cria registro de aguardando QR Code para finalizar o login
				$sql = "INSERT INTO cadastro_usuarios_qrcode_login SET usuario = :usuario ";
				$stmt = $PDO_PROCLEGIS->prepare( $sql );
				$stmt->bindParam( ':usuario', $usu_id );
				if($stmt->execute())
				{
					$login_id = $PDO_PROCLEGIS->lastInsertId();

					# GERA QRCODE #
					?>	
					<script>
					jQuery('#bt-login').hide();
					jQuery('.qrcode').html("Para finalizar a autenticação, siga os seguintes passos: <p>"+
										   "1. Abra a câmera do seu celular <br>"+
										   "2. Aponte para o <b>QR Code</b> abaixo <br>"+
										   "3. Abra o link que aparecerá na notificação <br>"+
										   "<center><img width='230' src='qrcode.php?login_id=<?php echo $login_id;?>&cli_url=<?php echo $cli_url;?>&sis_url=<?php echo $sis_url;?>'></center>");
					
					jQuery('#email').hide();
					jQuery('#senha').hide();
					
					setInterval(function() {
						
						jQuery.post("../mod_includes/php/check_login.php",
						{
							login_id:"<?php echo $login_id;?>",
							cli_url:"<?php echo $cli_url;?>",
							sis_url:"<?php echo $sis_url;?>"
							
						},
						function(valor) // Carrega o resultado acima para o campo catadm
						{		
							if(valor.indexOf("true") > -1)
							{
								$('#form_login').attr('action', 'login/<?php echo $cli_url;?>/ok');
								$('#form_login').submit();
							}
							else
							{

							}
											
							
						});
					
					}, 1000);
					</script>
					<?php

					
					


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
				
				?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Login ou senha incorretos.");
				</script>
				<?php
			}
			
		}
	}
	else
	{
		?>
        <script>
            mensagem("X","<i class='fa fa-exclamation-circle'></i> Requisição incorreta.");
        </script>
        <?php 
	}
}
if($action == "ok")
{
	
    if(isset($_POST['email']) &&  isset($_POST['senha']))
	{
		$email = $_POST['email'];
		$senha = hash('sha512',$_POST['senha']);

		$result = selectLoginUsuario($PDO_PROCLEGIS, $email, $senha);

		if (!empty($result))
		{
			$usu_status 		= $result[0]['usu_status'];
			$usu_id 			= $result[0]['usu_id'];
			$usu_setor 			= $result[0]['usu_setor'];
			$set_nome 			= $result[0]['set_nome'];
			$usu_nome 			= $result[0]['usu_nome'];
			$autor 				= $result[0]['id'];

			# PEGA NOME SISTEMA E CLIENTE #
			$sql = "SELECT * FROM cadastro_clientes
					INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
					WHERE cli_url = :cli_url ";
			$stmt = $PDO->prepare( $sql );
			$stmt->bindParam( ':cli_url', $cli_url);
			$stmt->execute();
			$rows = $stmt->rowCount();	

			if($rows > 0)
			{
				$result = $stmt->fetch();	
				$sis_url 	= $result['sis_url'];	
				$cli_url 	= $result['cli_url'];	
				$cli_id 	= $result['cli_id'];
			}
			
			if ($usu_status == 0)
			{
			
				?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Seu usuário está desativado, por favor contate o administrador do sistema.");
				</script>
				<?php 
					
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
				$_SESSION['cliente_id'] 	= $cli_id;
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
					header("location: ../../dashboard");				
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
				
				?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Login ou senha incorretos.");
				</script>
				<?php
			}
			
		}
	}
	else
	{
		?>
        <script>
            mensagem("X","<i class='fa fa-exclamation-circle'></i> Requisição incorreta.");
        </script>
        <?php 
	}
}
?>
</body>
</html>
