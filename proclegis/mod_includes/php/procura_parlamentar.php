<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$periodo = $_POST['periodo'];
$comissao = $_POST['comissao'];

$sql = "SELECT *, cadastro_parlamentares.id as id FROM cadastro_parlamentares 
		LEFT JOIN cadastro_comissoes_composicao ON  cadastro_comissoes_composicao.parlamentar = cadastro_parlamentares.id
		WHERE periodo = :periodo AND comissao = :comissao ";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':periodo', $periodo);
$stmt->bindParam(':comissao', $comissao);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Selecione o parlamentar</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>".$result['nome']."</option>";
	}
}
else
{
	echo "<option value=''>Nenhum parlamentar cadastrado para esta composição. Cadastre em Cadastro -> Comissões, aba \"Composição\"</option>";
}
?>