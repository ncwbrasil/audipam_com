<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 


$tas_id 	= $_POST['tas_id'];



$sql = "UPDATE cadastro_leads_tasks SET tas_concluido = :tas_concluido 
		WHERE tas_id = :tas_id ";
$stmt = $PDO->prepare($sql);	
$stmt->bindParam(":tas_id",$tas_id);
$stmt->bindValue(":tas_concluido",1);
if($stmt->execute($dados))
{		

	echo "true";	
}
else
{
	echo "false";
}


?>