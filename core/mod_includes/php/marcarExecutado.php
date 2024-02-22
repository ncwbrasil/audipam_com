<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$ins_id 			= $_POST['ins_id'];

$dados = array_filter(array(
	'ins_executado' 		=> 1
));


$sql = "UPDATE growth_instagram SET ".bindFields($dados)." WHERE ins_id = :ins_id ";
$stmt = $PDO->prepare($sql);	
$dados['ins_id'] =  $ins_id;
if($stmt->execute($dados))
{		
	echo "true";
}				
else
{
	echo "false";
}



?>