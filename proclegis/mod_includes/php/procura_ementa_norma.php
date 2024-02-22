<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$norma = $_POST['norma'];

$sql = "SELECT * FROM cadastro_normas_juridicas WHERE id = :id";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':id', $norma);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		echo $result['ementa'];
	}
}
else
{
	echo "Ementa não encontrada";
}
?>