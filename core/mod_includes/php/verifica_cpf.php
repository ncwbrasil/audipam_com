<?php
include('connect.php');
$usu_cpf = $_POST['usu_cpf'];
$sql = "SELECT * FROM cadastro_usuarios WHERE usu_cpf = :usu_cpf ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':usu_cpf', $usu_cpf);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0) 
{
	echo "true";
} 
?>