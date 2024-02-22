<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$des_id 			= $_POST['des_id'];
$des_valor_pago 	= str_replace(",",".",str_replace(".","",$_POST['des_valor_pago']));
$des_data_pagamento = reverteData($_POST['des_data_pagamento']);

echo $des_id;
$dados = array_filter(array(
	'des_valor_pago' 		=> $des_valor_pago,
	'des_data_pagamento' 	=> $des_data_pagamento	
));


$sql = "UPDATE financeiro_despesas SET ".bindFields($dados)." WHERE des_id = :des_id ";
$stmt = $PDO->prepare($sql);	
$dados['des_id'] =  $des_id;
if($stmt->execute($dados))
{		
	echo "true";	
}
else
{
	echo "false";
}


?>