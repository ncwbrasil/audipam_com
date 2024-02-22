<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$legislatura = $_POST['legislatura'];

$sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim, numero 
		FROM aux_mesa_diretora_sessoes WHERE legislatura = :legislatura";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':legislatura', $legislatura);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Sessão Legislativa</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>".$result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")</option>";
	}
}
else
{
	echo "<option value=''>Nenhuma sesseão legislativa cadastrada. Cadastre em Auxiliares -> Módulo Mesa Diretora</option>";
}
?>