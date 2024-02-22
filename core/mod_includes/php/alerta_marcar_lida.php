<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 
$ale_id = $_POST['ale_id'];
$sql = "UPDATE social_alertas SET
		ale_lida = :ale_lida 
		WHERE ale_id = :ale_id
		";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(':ale_lida',1);
$stmt->bindParam(':ale_id',$ale_id);
if($stmt->execute())
{
	echo "Ok";
}



?> 