<?php

$login_id = $_POST['login_id'];
$sis_url = $_POST['sis_url'];
$cli_url = $_POST['cli_url'];
if($sis_url != "")
{
    $_SESSION['sistema_url'] = $sis_url;
}
if($cli_url != "")
{
    $_SESSION['cliente_url'] = $cli_url;
}

require_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start();
include('../../../core/mod_includes/php/connect.php'); 


$sql = "SELECT * FROM cadastro_usuarios_qrcode_login
		WHERE id = :id
		";

$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindValue(':id', $login_id);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		if($result['libera_login'] == 1)
		{
			echo "true";
		}
		else
		{
			echo "false"; 
		}
	}
	
}
else
{
	echo "false"; 
}
?>