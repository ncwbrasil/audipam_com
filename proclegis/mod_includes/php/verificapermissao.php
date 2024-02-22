<?php 

if(isset($_SESSION['webmaster']['audipam']))
{
	$permissoes = array();
	$permissoes['view'] 	= 1;
	$permissoes['add'] 		= 1;
	$permissoes['edit'] 	= 1;
	$permissoes['excluir'] 	= 1;
}
else
{
	$sql = "SELECT * FROM cadastro_clientes WHERE cli_id = :cli_id AND cli_status = :cli_status ";
	$stmt = $PDO->prepare($sql);
	$stmt->bindParam( ':cli_id', $_SESSION['cliente_id']);
	$stmt->bindValue( ':cli_status', 1);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
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
			'<img src=imagens/x.png> 1Você não tem permissão para acessar esta área.<br>Por favor faça Login.<br><br>'+
			'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'login/$cli_url\';>' );
		</SCRIPT>
		 ";
		exit;
	}
	$sql = "SELECT * FROM admin_setores_permissoes
			INNER JOIN ( admin_submodulos 
				INNER JOIN admin_modulos 
				ON admin_modulos.mod_id = admin_submodulos.sub_modulo )
			ON admin_submodulos.sub_id = admin_setores_permissoes.sep_submodulo
			INNER JOIN ( admin_setores 
				INNER JOIN cadastro_usuarios 
				ON cadastro_usuarios.usu_setor = admin_setores.set_id )
			ON admin_setores.set_id = admin_setores_permissoes.sep_setor
			WHERE sep_setor = :setor AND sub_link = :sub_link AND usu_email = :email AND usu_status = :status ";
	$stmt = $PDO_PROCLEGIS->prepare( $sql );
	
	$stmt->bindParam( ':setor', 	$_SESSION['setor_id'] );
	$stmt->bindParam( ':sub_link', 	$pagina_link );
	$stmt->bindParam( ':email', $_SESSION['usuario_login']);
	$stmt->bindValue( ':status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		$permissoes = array();
		$result = $stmt->fetch();
		$permissoes['view'] 	= $result['sep_consultar'];
		$permissoes['add'] 		= $result['sep_adicionar'];
		$permissoes['edit'] 	= $result['sep_editar'];
		$permissoes['excluir'] 	= $result['sep_excluir'];
		log_operacao(0, $PDO_PROCLEGIS);
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
			'<img src=../core/imagens/x.png> 2Você não tem permissão para acessar esta área.<br>Por favor faça Login.<br><br>'+
			'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'login/$cli_url\';>' );
		</SCRIPT>
			";
		exit;	
	}
}
?>
