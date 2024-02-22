<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$sessao = $_POST['sessao'];


$sql = "SELECT *, cadastro_sessoes_plenarias_presenca.id as id    
				, cadastro_parlamentares.nome as nome 
				, cadastro_parlamentares.id as id_parlamentar 
				, aux_parlamentares_partidos.sigla as partido                            
		FROM cadastro_sessoes_plenarias_presenca
		LEFT JOIN (cadastro_parlamentares 
			LEFT JOIN ( cadastro_parlamentares_filiacoes 
				LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido )
			ON cadastro_parlamentares_filiacoes.parlamentar = cadastro_parlamentares.id)
		ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_presenca.parlamentar
		LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_presenca.sessao_plenaria                     
		WHERE sessao_plenaria = :sessao_plenaria	
		GROUP BY cadastro_parlamentares.id
		ORDER BY cadastro_parlamentares.nome ASC
		";
$stmt_int = $PDO_PROCLEGIS->prepare($sql);                                                                                                
$stmt_int->bindParam(':sessao_plenaria', 	$sessao);
$stmt_int->execute();
$rows_int = $stmt_int->rowCount();
if($rows_int > 0)
{
	while($result_int = $stmt_int->fetch())
	{

		$sql = "SELECT * FROM cadastro_parlamentares_filiacoes 	
				LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido					
				WHERE parlamentar = :parlamentar AND cadastro_parlamentares_filiacoes.ativo = :ativo		
				ORDER BY data_filiacao DESC
				LIMIT 0,1
				";
		$stmt_part = $PDO_PROCLEGIS->prepare($sql);                                                                                                
		$stmt_part->bindParam(':parlamentar', 	$result_int['id_parlamentar']);
		$stmt_part->bindValue(':ativo', 	1);
		$stmt_part->execute();
		$rows_part = $stmt_part->rowCount();
		if($rows_part > 0)
		{
			while($result_part = $stmt_part->fetch())
			{
				$partido = $result_part['sigla'];
			}
		}

		echo $result_int['nome']."/".$partido."<br>";
	}
}
?>