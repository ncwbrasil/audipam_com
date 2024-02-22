<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$sessao = $_POST['sessao'];


// MATERIA E RESULTADO
$sql = "SELECT *, cadastro_sessoes_plenarias_exp_materias.id as id 
				, cadastro_materias.numero as numero
				, cadastro_sessoes_plenarias_exp_materias.observacao as observacao
				, cadastro_sessoes_plenarias_exp_materias_leitura.observacao as observacao_leitura
				, cadastro_sessoes_plenarias_exp_materias_votacao.observacao as observacao_votacao
		FROM cadastro_sessoes_plenarias_exp_materias 
		LEFT JOIN cadastro_sessoes_plenarias_exp_materias_leitura ON cadastro_sessoes_plenarias_exp_materias_leitura.materia_exp = cadastro_sessoes_plenarias_exp_materias.id                     
		LEFT JOIN ( cadastro_sessoes_plenarias_exp_materias_votacao 
			LEFT JOIN aux_sessoes_plenarias_tipo_resultado ON aux_sessoes_plenarias_tipo_resultado.id = cadastro_sessoes_plenarias_exp_materias_votacao.resultado)
		ON cadastro_sessoes_plenarias_exp_materias_votacao.materia_exp = cadastro_sessoes_plenarias_exp_materias.id                     
		LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_exp_materias.tipo_materia                     
		LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_exp_materias.materia                     
		LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_exp_materias.sessao_plenaria                     
		WHERE cadastro_sessoes_plenarias_exp_materias.ativo = :ativo AND 
				cadastro_sessoes_plenarias_exp_materias.sessao_plenaria = :sessao_plenaria AND
				( cadastro_sessoes_plenarias_exp_materias.status = :status OR cadastro_sessoes_plenarias_exp_materias.status = :status2)
		ORDER BY cadastro_sessoes_plenarias_exp_materias.id DESC
		LIMIT 0, 1
	";
$stmt = $PDO_PROCLEGIS->prepare($sql);                                                    
$stmt->bindParam(':sessao_plenaria', 	$sessao);    
$status = "Aberta para votação";
$stmt->bindParam(':status', 	$status);
$status2 = "Matéria votada";
$stmt->bindParam(':status2', 	$status2);
$stmt->bindValue(':ativo', 	1);
$stmt->execute();
$rows = $stmt->rowCount();
if ($rows > 0)
{
	while($result = $stmt->fetch())
	{
		echo $result['nome']." Nº ".$result['numero']." de ".$result['ano']."<br>";
		echo $result['ementa']."<br>";		
	}              
}
else
{
	echo "Não há matérias abertas para votação no momento.";              
}
?>