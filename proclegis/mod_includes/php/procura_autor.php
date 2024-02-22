<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_autor = $_POST['tipo_autor'];

$sql = "SELECT * FROM aux_autoria_autores WHERE tipo_autor = :tipo_autor";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':tipo_autor', $tipo_autor);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value='' selected>Selecione o autor</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>".$result['nome']."</option>";
	}
}
else
{
	echo "<option value='' selected>Não há autores cadastrados</option>";
	
}
?>