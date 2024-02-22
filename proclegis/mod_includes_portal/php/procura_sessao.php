<?php
session_start(); 
include_once("connect.php");
$legislatura = $_POST['legislatura'];

$sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim, numero 
		FROM aux_mesa_diretora_sessoes 
		WHERE legislatura = :legislatura
		ORDER BY numero DESC ";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':legislatura', $legislatura);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Selecione a Sessão Legislativa</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>".$result['numero']." (".$result['data_inicio'].")</option>";
	}
}
else
{
	echo "<option value=''>Nenhuma sessão legislativa cadastrada.";
}
?>