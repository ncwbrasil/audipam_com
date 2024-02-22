<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo = $_POST['tipo'];
$time = $_POST['time'];
$sessao = $_POST['sessao'];


$sql = "SELECT * FROM cadastro_sessoes_plenarias_cronometro 		
		WHERE sessao = :sessao";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':sessao', $sessao);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{

	$result = $stmt->fetch();
	if($tipo == 'discurso')
	{
		$tempo = $result['discurso'];
	}
	elseif($tipo == 'aparte')
	{
		$tempo = $result['aparte'];
	}
	elseif($tipo == 'ordem')
	{
		$tempo = $result['ordem'];
	}
	elseif($tipo == 'consideracoes')
	{
		$tempo = $result['consideracoes'];
	}

}


if($tipo == 'discurso')
{
	$set = "  discurso_tmp = :tempo ";
}
elseif($tipo == 'aparte')
{
	$set = "  aparte_tmp = :tempo ";
}
elseif($tipo == 'ordem')
{
	$set = "  ordem_tmp = :tempo ";
}
elseif($tipo == 'consideracoes')
{
	$set = "  consideracoes_tmp = :tempo ";
}

$sql = "UPDATE cadastro_sessoes_plenarias_cronometro 
		SET ".$set." 
		WHERE sessao = :sessao";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':sessao', $sessao);
$stmt->bindParam(':tempo', $tempo);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	
}
else
{
	
}
echo $tempo;
?>