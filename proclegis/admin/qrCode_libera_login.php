<?php

// Include the qrlib file 
include '../../core/mod_includes/php/funcoes.php'; 
include '../../core/mod_includes/php/phpqrcode/qrlib.php'; 

$hash = $_GET['hash'];

$chave = substr($hash,-8);
$texto = base64_decode(str_replace($chave,"",$hash));
$string         = desencriptar($texto, base64_encode($chave));
$s = explode("|",$string);    
$login_id    = $s[0];
$sis_url     = $s[1];
$cli_url     = $s[2];



if($sis_url != "")
{
    $_SESSION['sistema_url'] = $sis_url;
}
if($cli_url != "")
{
    $_SESSION['cliente_url'] = $cli_url;
}
include '../../core/mod_includes/php/connect.php'; 

$sql = "UPDATE cadastro_usuarios_qrcode_login SET 
        libera_login 	= :libera_login
        WHERE id = :id ";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':id',$login_id);
$stmt->bindValue(':libera_login',1);
if($stmt->execute())
{
    echo "<script>alert('Login liberado com sucesso!');</script>";
}else{$erro=1; $err = $stmt->errorInfo();}

?>