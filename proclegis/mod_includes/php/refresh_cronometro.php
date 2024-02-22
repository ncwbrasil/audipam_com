<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$sessao = $_POST['sessao'];


$sql = "SELECT * FROM cadastro_sessoes_plenarias_cronometro 
		WHERE sessao = :sessao";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':sessao', $sessao);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		$crono = "Discurso: ".$result['discurso_tmp']."<br>";
		$crono .= "Aparte: ".$result['aparte_tmp']."<br>";
		$crono .= "Questão de Ordem: ".$result['ordem_tmp']."<br>";
		$crono .= "Considerações Finais: ".$result['consideracoes_tmp']."<br>";
	}
	echo $crono;
}
else
{
	
}
?>