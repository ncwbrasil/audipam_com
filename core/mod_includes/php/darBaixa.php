<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$fat_id 			= $_POST['fat_id'];
$fat_valor_pago 	= str_replace(",",".",str_replace(".","",$_POST['fat_valor_pago']));
$fat_data_pagamento = reverteData($_POST['fat_data_pagamento']);
$ftr_observacao 	= $_POST['ftr_observacao'];

$dados = array_filter(array(
	'fat_valor_pago' 		=> $fat_valor_pago,
	'fat_data_pagamento' 	=> $fat_data_pagamento	
));


$sql = "UPDATE financeiro_faturas SET ".bindFields($dados)." WHERE fat_id = :fat_id ";
$stmt = $PDO->prepare($sql);	
$dados['fat_id'] =  $fat_id;
if($stmt->execute($dados))
{		
	$sql_financeiro_tramitacao = "INSERT INTO financeiro_tramitacao (ftr_fatura, ftr_status, ftr_observacao, ftr_usuario) 
									VALUES (:fat_id, :ftr_status, :ftr_observacao, :ftr_usuario)";
	$stmt_financeiro_tramitacao = $PDO->prepare($sql_financeiro_tramitacao);
	$stmt_financeiro_tramitacao->bindParam(':fat_id',$fat_id);
	$ftr_status = "Pago";
	$stmt_financeiro_tramitacao->bindParam(':ftr_status',$ftr_status);
	$stmt_financeiro_tramitacao->bindParam(':ftr_observacao',$ftr_observacao);
	$stmt_financeiro_tramitacao->bindParam(':ftr_usuario',$_SESSION['usuario_id']);
	if($stmt_financeiro_tramitacao->execute()){ }else {$erro=1;}
	
	if($erro != 1)
	{                   
		// SELECIONA USUARIOS PARA ALERTAS
		$sql = "SELECT * FROM cadastro_usuarios
				LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
				WHERE set_nome = :set_nome1 OR set_nome = :set_nome2 ";
		$stmt_ale = $PDO->prepare($sql);	
		$set_nome1 = "Diretoria";
		$set_nome2 = "Administrador";			
		$stmt_ale->bindParam(':set_nome1',$set_nome1);          			      
		$stmt_ale->bindParam(':set_nome2',$set_nome2);          			      
		if($stmt_ale->execute())
		{
			while($result_ale = $stmt_ale->fetch())
			{
				$users[] = $result_ale['usu_id'];										
			}
			// WEB ALERTA                
			$ale_descricao = "A fatura <span class='bold'>".$fat_id."</span> no valor de <span class='bold verde'>R$ ".$_POST['fat_valor_pago']."</span> foi paga!";
			$ale_link = "financeiro_faturas/exib/$fat_id";		 
			$destinatario = implode(",",$users);  
			alertaWeb($PDO, $_SESSION['usuario_id'], $destinatario, $ale_descricao, $ale_link);
		}

		

		echo "true";
	}				
	else
	{
		echo "false";
	}
}
else
{
	echo "false";
}


?>