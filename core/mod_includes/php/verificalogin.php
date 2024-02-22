<div id='janela' class='janela' style='display:none;'> </div>
<div id='janelaAcao' class='janelaAcao' style='display:none;'> </div>

<?php 

if (!isset($_SESSION['webmaster']['audipam'] ) || !isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_login']))
{	
	
	sec_session_start();
	$_SESSION = array();// Zera todos os valores da sessão
	$params = session_get_cookie_params();// Pega os parâmetros da sessão 
	setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);// Deleta o cookie atual.
	session_destroy();// Destrói a sessão
	echo "
	<SCRIPT language='JavaScript'>
		abreMask(
		'<img src=../core/imagens/x.png> 1Sua sessão expirou ou você não tem permissão para acessar esta área.<br>Por favor faça Login novamente.<br><br>'+
		'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'login/$cli_url\';>' );
	</SCRIPT>
	 ";
	exit;
}
else
{
	
	$sql = "SELECT * FROM cadastro_usuarios
			LEFT JOIN log_login_usuarios h1 ON h1.log_usuario = cadastro_usuarios.usu_id 
			WHERE h1.log_id = (SELECT MAX(h2.log_id) FROM log_login_usuarios h2 where h2.log_usuario = h1.log_usuario) AND
				  usu_email = :email AND usu_status = :status";
	$stmt = $PDO->prepare( $sql );
	$stmt->bindParam( ':email', $_SESSION['usuario_login']);
	$stmt->bindValue( ':status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
	
		if($_SESSION['webmaster']['audipam']  != $stmt->fetch(PDO::FETCH_OBJ)->log_hash)
		{
			sec_session_start();
			$_SESSION = array();// Zera todos os valores da sessão
			$params = session_get_cookie_params();// Pega os parâmetros da sessão 
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);// Deleta o cookie atual.
			session_destroy();// Destrói a sessão
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../core/imagens/x.png> 6Você não tem permissão para acessar esta área.<br>Por favor faça Login.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'login/$cli_url\';>' );
			</SCRIPT>
			 ";
		}
	}
	else
	{
		sec_session_start();
		$_SESSION = array();// Zera todos os valores da sessão
		$params = session_get_cookie_params();// Pega os parâmetros da sessão 
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);// Deleta o cookie atual.
		session_destroy();// Destrói a sessão
		echo "
		<SCRIPT language='JavaScript'>
			abreMask(
			'<img src=../core/imagens/x.png> 5Você não tem permissão para acessar esta área.<br>Por favor faça Login.<br><br>'+
			'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'login/$cli_url\';>' );
		</SCRIPT>
		 ";
	}
}



?>
