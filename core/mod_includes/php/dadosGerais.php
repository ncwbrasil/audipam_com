<?php

// if($cli_url == ""){$cli_url = $_SESSION[$sis_url]['cliente_url'];}
// if($cli_url == ""){$cli_url = $_SESSION['cliente_url'];}
// if($_SESSION['cliente_url'] == ""){$_SESSION['cliente_url'] = $cli_url;}
// if($_SESSION['sistema_url'] == ""){$_SESSION['sistema_url'] = $sis_url;}

if($_SESSION['webmaster']['audipam'] || $_SESSION['cliente_url'] != "")
{

	# PEGA NOME SISTEMA E CLIENTE #
	$sql = "SELECT * FROM cadastro_clientes
			INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
			WHERE cli_url = :cli_url AND sis_url = :sis_url AND cli_status = :cli_status";
	$stmt = $PDO->prepare( $sql );
	$stmt->bindParam( ':cli_url', $_SESSION['cliente_url']);
	$stmt->bindParam( ':sis_url', $_SESSION['sistema_url']);
	$stmt->bindValue( ':cli_status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();	

	if($rows > 0)
	{
		$result = $stmt->fetch();	
		$sis_nome 	= $result['sis_nome'];	
		$sis_logo 	= $result['sis_logo'];	
		$sis_dominio = $result['sis_dominio'];	
		$cli_id 	= $result['cli_id'];	
		$cli_nome 	= $result['cli_nome'];
		$cli_foto 	= $result['cli_foto'];		
	}
	else
	{	
		echo "Página não encontrada :(";
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
	}
}
else
{
	# PEGA NOME SISTEMA E CLIENTE #
	$sql = "SELECT * FROM cadastro_clientes
			INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
			WHERE cli_url = :cli_url AND sis_url = :sis_url";
	$stmt = $PDO->prepare( $sql );
	$stmt->bindParam( ':cli_url', $cli_url);
	$stmt->bindParam( ':sis_url', $sis_url);
	$stmt->bindValue( ':cli_status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();	

	if($rows > 0)
	{
		$result = $stmt->fetch();	
		$sis_nome 	= $result['sis_nome'];	
		$sis_logo 	= $result['sis_logo'];	
		$sis_dominio = $result['sis_dominio'];	
		$cli_id 	= $result['cli_id'];	
		$cli_nome 	= $result['cli_nome'];
		$cli_foto 	= $result['cli_foto'];		
	}
	else
	{	
		echo "Página não encontrada :(";
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
		exit;
	}
}


?>