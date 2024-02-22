<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$uf = $_POST['uf'];
$sql = "SELECT * FROM end_municipios WHERE mun_uf = :uf";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':uf', $uf);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['mun_id']."'>".$result['mun_nome']."</option>";
	}
}
else
{
	echo "<option value=''>Selecione UF</option>";
}
?>