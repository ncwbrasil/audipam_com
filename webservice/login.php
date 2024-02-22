<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$usuario = $_GET['login'];
$senha = hash('sha512',$_GET['senha']);
$token = $_GET['token']; 

$sql = "SELECT * FROM app_usuarios  
WHERE usu_email = :usu_email AND usu_senha = :usu_senha";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':usu_email', $usuario);
$stmt->bindParam(':usu_senha', $senha);
$stmt->execute();
$rows = $stmt->rowCount();

if($rows>0)
{   
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($token != $result['usu_token']){
		$sql_t = "UPDATE app_usuarios SET
		usu_token = :usu_token
		WHERE usu_email = :usu_email "; 
		$stmt_t = $PDO->prepare($sql_t);
		$stmt_t->bindParam(':usu_token', $token);
		$stmt_t->bindParam(':usu_email', $usuario);
		$stmt_t->execute();
	}

	$dados['dados'][]= $result; 
	echo JSON::encode($dados);
}
else
{
	echo "false";
}
?>