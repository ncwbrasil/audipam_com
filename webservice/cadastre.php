<?php
include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");
$PDO->exec("SET CHARACTER SET utf8");
$PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

$acao = $_GET['action']; 

if($acao == 'listar_cidades'){

	$sql = "SELECT cli_nome, cli_id, cli_url, cli_foto FROM cadastro_clientes";
	$stmt = $PDO->prepare($sql);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows>0)
	{
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$dados['dados'][]= $result;
		}
		echo JSON::encode($dados);
	}
	else
	{
		echo "false";
	}
}
?>