<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

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
    $fat_id = $_POST['fat_id'];
    $ale_descricao = "Fatura <span class='bold'>".$fat_id."</span> liberada para cobrança";
    $ale_link = "financeiro_faturas/view/?fil_fat_id=".$fat_id."";    
    $destinatario = implode(",",$users);  
    echo alertaWeb($PDO, $_SESSION['usuario_id'], $destinatario, $ale_descricao, $ale_link);
}



?>