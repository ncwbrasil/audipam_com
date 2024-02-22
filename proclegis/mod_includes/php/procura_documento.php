<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_documento = $_POST['tipo_documento'];

$sql = "SELECT * FROM docadm_documentos 
		WHERE tipo = :tipo AND ( restrito = 'Não' OR ( restrito = 'Sim' AND cadastrado_por = ".$_SESSION['usuario_id'].") )
		ORDER BY ano DESC, numero DESC";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':tipo', $tipo_documento);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Selecione o documento</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>Nº ".$result['numero']." de ".$result['ano']."</option>";
	}
}
else
{
	echo "<option value=''>Nenhum documento cadastrado para este tipo. Cadastre em Administrativo -> Documentos</option>";
}
?>