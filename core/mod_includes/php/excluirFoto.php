<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

// WEB ALERTA
$fot_id = $_POST['fot_id'];
$sql = "DELETE FROM social_agenda_fotos                                
        WHERE fot_id = :fot_id ";
$stmt_foto = $PDO->prepare($sql);            
$stmt_foto->bindParam(':fot_id', $fot_id);
if($stmt_foto->execute())
{
    echo "true";
}
else
{
    echo "false";
}

?>