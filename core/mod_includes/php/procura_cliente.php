<?php
include('connect_sistema.php');
?>

<?php
$busca = str_replace(".","",str_replace("-","",$_POST['busca']));
$sql = "SELECT * FROM cadastro_clientes 
		WHERE (cli_nome LIKE :busca1 OR cli_nome_fotografado LIKE :busca2 OR REPLACE(REPLACE(cli_cpf, '.', ''), '-', '') LIKE :busca3) AND
			  cli_cliente = :cli_cliente
		ORDER BY cli_nome ASC";
$stmt = $PDO->prepare($sql);
$busca = '%'.$busca.'%';
$stmt->bindParam(':busca1', $busca);
$stmt->bindParam(':busca2', $busca);
$stmt->bindParam(':busca3', $busca);
$stmt->bindValue(':cli_cliente', 1);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		
		echo "<input id='campo' value='&raquo; ".$result['cli_nome']." (".$result['cli_nome_fotografado'].")' name='campo' onclick='carregaBuscaCliente(this.value,\"".$result['cli_id']."\");'><br>";		
	}
	
}
else
{
	echo "<script> jQuery('#suggestions').hide();</script>"; 
	echo "";
}
?>