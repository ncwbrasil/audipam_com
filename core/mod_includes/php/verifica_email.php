<?php
include('connect.php');
$usu_email = $_POST['usu_email'];
$sql = "SELECT * FROM cadastro_usuarios WHERE usu_email = :usu_email ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':usu_email', $usu_email);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0) 
{
	echo "true";
} 
?>