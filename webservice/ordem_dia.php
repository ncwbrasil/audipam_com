<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if($acao == 'apresentaOrdem'){
	$id = $_GET['id'];

	$sql = "SELECT *, cadastro_sessoes_plenarias_od_materias.id as id 
	, cadastro_materias.numero as numero
	FROM cadastro_sessoes_plenarias_od_materias 
	LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_od_materias.tipo_materia                     
	LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_od_materias.materia                     
	LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_od_materias.sessao_plenaria                     
	WHERE  cadastro_sessoes_plenarias_od_materias.sessao_plenaria = :sessao_plenaria		
	ORDER BY cadastro_sessoes_plenarias_od_materias.ordem ASC ";
	$stmt = $PDO_PROCLEGIS->prepare($sql);            
	$stmt->bindParam(':sessao_plenaria', 	$id); 
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$id_od_materias = $result['id'];
			$tipo_materia = $result['tipo_materia'];
			$sigla = $result['sigla'];
			$nome = $result['nome'];
			$materia = $result['materia'];
			$numero = $result['numero'];
			$ano = $result['ano'];
		
			$ordem = $result['ordem'];
			$tipo_votacao = $result['tipo_votacao'];
			$observacao = $result['observacao'];

			// AUTORES
			$autor=array();
			$sql = "SELECT *
					FROM cadastro_materias_autoria
					LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
					WHERE cadastro_materias_autoria.materia = :materia	";
			$stmt_aut = $PDO_PROCLEGIS->prepare($sql);                                
			$stmt_aut->bindParam(':materia', 	$materia);                                
			$stmt_aut->execute();
			$rows_aut = $stmt_aut->rowCount();
			if($rows_aut > 0)
			{
				while($result_aut = $stmt_aut->fetch())
				{
					$autor[] = $result_aut['nome'];
				}
			}

			$osp_conteudo[$i]="
				<p><span class='subtitulo'>".$result['nome']." Nº ".$result['numero']." de ".$result['ano']."</span> <br>
				<span class='subtitulo'>Autor(es):</span> ".implode(", ",$autor)."</br>
				<span class='subtitulo'>Tipo de Votação:</span> $tipo_materia </br>
				<span class='subtitulo'>Observação:</span> $observacao </br>
			";

		}
		$dados['dados'][0]['osp_conteudo'] = $osp_conteudo;	
		echo JSON::encode($dados);
	}	
	else {
		echo "false";
	}
}


?>