<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$lea_id 			= $_POST['lea_id'];
$tas_tipo 	= $_POST['tas_tipo'];
$tas_data = reverteData($_POST['tas_data']);
$tas_hora 	= $_POST['tas_hora'];
$tas_observacao 	= $_POST['tas_observacao'];
$tas_responsavel 	= $_POST['tas_responsavel'];

$dados = array_filter(array(
	'tas_lead' 				=> $lea_id,
	'tas_tipo'				=> $tas_tipo,
	'tas_data' 				=> $tas_data,
	'tas_hora' 				=> $tas_hora,
	'tas_observacao' 		=> $tas_observacao,
	'tas_responsavel' 		=> $tas_responsavel	
));


$sql = "INSERT INTO cadastro_leads_tasks SET ".bindFields($dados);
$stmt = $PDO->prepare($sql);	

if($stmt->execute($dados))
{		

	// SELECIONA USUARIOS PARA ALERTAS
	$sql = "SELECT * FROM cadastro_usuarios
		LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
		WHERE usu_id = :usu_id ";
	$stmt_ale = $PDO->prepare($sql);				
	$stmt_ale->bindParam(':usu_id',$tas_responsavel);          			      
	if($stmt_ale->execute())
	{
		while($result_ale = $stmt_ale->fetch())
		{
			$users[] = $result_ale['usu_id'];										
		}
		// WEB ALERTA                
		$ale_descricao = "Você tem uma nova tarefa: <span class='bold'>".$tas_tipo."</span>.";
		$ale_link = "cadastro_leads/view";
		$destinatario = implode(",",$users);  
		alertaWeb($PDO, $_SESSION['usuario_id'], $destinatario, $ale_descricao, $ale_link);
	}    


	// WEB ALERTA                
	// $ale_descricao = "A fatura <span class='bold'>".$fat_id."</span> no valor de <span class='bold verde'>R$ ".$_POST['fat_valor_pago']."</span> foi paga!";
	// $ale_link = "financeiro_faturas/exib/$fat_id";
	// $setores = array("'Diretoria'","'Administrador'"); 
	// $destinatario = implode(",",$setores);  
	// alertaWeb($PDO, $_SESSION['usuario_id'], $destinatario, $ale_descricao, $ale_link);

	echo "true";	
}
else
{
	echo "false";
}


?>