<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_documento = $_POST['tipo_documento'];
$ano = date("Y");
$sql = "SELECT * FROM docadm_documentos 
		WHERE tipo = :tipo AND ano = :ano
		ORDER BY numero DESC
		LIMIT 0,1";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':tipo', $tipo_documento);
$stmt->bindParam(':ano', $ano);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		echo $result['numero']+1;
	}
}
else
{
	echo "1";
}
?>