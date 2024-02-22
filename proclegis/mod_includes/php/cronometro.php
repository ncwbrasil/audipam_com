<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo = $_POST['tipo'];
$time = $_POST['time'];
$sessao = $_POST['sessao'];

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
$stmt->bindParam(':tempo', $time);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	
}
else
{
	
}
?>