<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$lea_id 			= $_POST['lea_id'];
$status 			= $_POST['status'];
$usuario 			= $_POST['usuario'];


$sql = "INSERT INTO cadastro_leads_status SET
		pst_lead = :lea_id,
		pst_status = :pst_status,
		pst_usuario = :pst_usuario
		";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':lea_id',$lea_id);
$stmt->bindParam(':pst_status',$status);
$stmt->bindParam(':pst_usuario',$usuario);
if($stmt->execute())
{
	echo "true";
}
else
{
	echo "false";
}



?>