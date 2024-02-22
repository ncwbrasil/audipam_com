<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if($acao == 'listar_cidades'){
	$cli_uf = $_GET['uf_id'];

	$sql = "SELECT * FROM cadastro_clientes
	LEFT JOIN end_uf ON end_uf.uf_id = cadastro_clientes.cli_uf
	WHERE cli_uf = :cli_uf
	GROUP BY cli_nome";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':cli_uf', $cli_uf);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows>0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dados['dados'][$i]['cli_nome'] = $result['cli_nome']; 
			$dados['dados'][$i]['cli_url'] = $result['cli_url']; 
		}
		echo JSON::encode($dados);
	}
	else
	{
		echo "false";
	}
}

if($acao == 'listar_estados'){

	$sql = "SELECT * FROM end_uf
	Right JOIN cadastro_clientes ON cadastro_clientes.cli_uf = end_uf.uf_id";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':cli_sigla', $cli_sigla);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows>0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dados['dados'][$i]['uf_sigla']= $result['uf_sigla'];
			$dados['dados'][$i]['uf_id'] = $result['uf_id']; 
		}
		echo JSON::encode($dados);
	}
	else
	{
		echo "false";
	}
}

if($acao =='cadastro_usuario'){
	$usu_nome = $_POST['usu_nome']; 
	$usu_email = $_POST['usu_email'];
	$usu_senha = hash('sha512', $_POST['usu_senha']);
	$usu_cidade = $_POST['usu_cidade']; 
	$usu_token = $_POST['usu_token']; 

	$sql_usu = "SELECT * FROM app_usuarios WHERE usu_email = :usu_email";
	$stmt_usu = $PDO->prepare($sql_usu);
	$stmt_usu->bindParam(':usu_email', $usu_email);
	$stmt_usu->execute();
	$rows_usu = $stmt_usu->rowCount();
	if($rows_usu>0){
		$sql ="UPDATE app_usuarios SET
		usu_nome = :usu_nome, 
		usu_senha = :usu_senha, 
		usu_cidade = :usu_cidade, 
		usu_token = :usu_token 
		WHERE usu_email = :usu_email"; 
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_nome', $usu_nome);
		$stmt->bindParam(':usu_senha', $usu_senha);
		$stmt->bindParam(':usu_cidade', $usu_cidade);
		$stmt->bindParam(':usu_token', $usu_token);
		$stmt->bindParam(':usu_email', $usu_email);
		if ($stmt->execute()) {
			echo"true"; 
		}
		else {
			echo "false"; 
		}

	}
	else {
		$sql ="INSERT INTO app_usuarios SET
		usu_nome = :usu_nome, 
		usu_email = :usu_email, 
		usu_senha = :usu_senha, 
		usu_cidade = :usu_cidade, 
		usu_token = :usu_token"; 
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_nome', $usu_nome);
		$stmt->bindParam(':usu_email', $usu_email);
		$stmt->bindParam(':usu_senha', $usu_senha);
		$stmt->bindParam(':usu_cidade', $usu_cidade);
		$stmt->bindParam(':usu_token', $usu_token);
		if ($stmt->execute()) {
			echo"true"; 
		}
		else {
			echo "false"; 
		}
	}
}

if($acao =='alterarCidade'){
	$usu_cidade = $_GET['usu_cidade']; 

	$sql ="UPDATE app_usuarios SET usu_cidade = :usu_cidade"; 
	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':usu_cidade', $usu_cidade);
	if ($stmt->execute()) {
		echo"true"; 
	}
	else {
		echo "false"; 
	}
}


?>
