<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_norma = $_POST['tipo_norma'];

$sql = "SELECT * FROM cadastro_normas_juridicas WHERE tipo = :tipo ORDER BY numero ASC";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':tipo', $tipo_norma);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Selecione a norma jurídica</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>Nº ".$result['numero']." de ".$result['ano']."</option>";
	}
}
else
{
	echo "<option value=''>Nenhuma norma jurídica cadastrada para este tipo. Cadastre em Cadastro -> Normas Jurídicas</option>";
}
?>