<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action'];

if ($acao == 'listarNormas') {

	$sql = "SELECT *, aux_normas_juridicas_tipos.nome as tipo_nome,
		aux_normas_juridicas_tipos.sigla as tipo_sigla,
		cadastro_normas_juridicas.id as id
		FROM cadastro_normas_juridicas 
		LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas.tipo                     
		ORDER BY cadastro_normas_juridicas.id DESC
		LIMIT :limite";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':limite', 100);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result['data_apresentacao'] == ''){
				$data="Sem data"; 
			}
			else {
				$data = date('d/m/Y', strtotime($result['data_apresentacao']));
			}

			$nj_previa = "                                        
			
				<p class='subtitulo'>" . $result['tipo_sigla'] . " " . $result['numero'] . "/" . $result['ano'] . " - " . $result['tipo_nome'] . "</p>
				<p><b>Ementa:</b> " . $result['ementa'] . "<br>
				<b>Data apresentação:</b> $data <br>
				<b>Autor(es):</b> " . $result['nome'] . "</p>
			";

			$dados['dados'][$i]['nj_previa'] = $nj_previa;
			$dados['dados'][$i]['nj_id'] = $result['id'];
		}


		// print_r($dados); 
		// exit; 
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if ($acao == 'apresentarNorma') {

	$id = $_GET['id']; 
	$sql = "SELECT *, t1.nome as tipo_nome,
	t1.sigla as tipo_sigla,
	t2.sigla as tipo_sigla_materia,
	t2.nome as tipo_nome_materia,
	cadastro_normas_juridicas.id as id,
	t3.numero as numero_materia,
	t3.ano as ano_materia,
	cadastro_normas_juridicas.complementar as complementar,
	cadastro_normas_juridicas.data_publicacao as data_publicacao,
	cadastro_normas_juridicas.texto_original as texto_original,
	cadastro_normas_juridicas.tipo as tipo,
	cadastro_normas_juridicas.numero as numero,
	cadastro_normas_juridicas.ano as ano,
	cadastro_normas_juridicas.data_apresentacao as data_apresentacao,                    
	cadastro_normas_juridicas.ementa as ementa                    
	FROM cadastro_normas_juridicas 
	LEFT JOIN aux_normas_juridicas_tipos t1 ON t1.id = cadastro_normas_juridicas.tipo                                         
	LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_normas_juridicas.tipo_materia                                                                                                        
	LEFT JOIN cadastro_materias t3 ON t3.id = cadastro_normas_juridicas.materia                                                                                                        
	WHERE cadastro_normas_juridicas.id = :id";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		$result = $stmt->fetch();
		
		$nj_descricao= "<div id='dados_gerais' class='tab-pane fade in active' >
				<p class='titulo verde'>".$result['tipo_sigla']." - ".$result['tipo_nome']."</p>
				<p><b>Número: </b> ".$result['numero']."<br>
				<b>Ano: </b>".$result['ano']."<br>
				<b>Data Apresentação: </b>".date('d/m/Y', strtotime($result['data_apresentacao']))." <br>
				<b>Esfera Federação: </b>".$result['esfera']."<br>
				<b>Complementar:</b> ".$result['complementar']."<br>
				<b>Tipo Matéria:</b> ".$result['tipo_sigla_materia']." - ".$result['tipo_nome_materia']." <br>
				<b>Matéria:</b> Nº ".$result['numero_materia']." de ".$result['ano_materia']." <br>
				<b>Data publicação:</b> ".date('d/m/Y', strtotime($result['data_publicacao']))."<br>
				<b>Fim prazo:</b> ".date('d/m/Y', strtotime($result['data_fim_vigencia']))." <br>
				<b>Ementa:</b> ".$result['ementa']." </p>
		</div>";

		$sql2 = "SELECT *, cadastro_normas_juridicas_assuntos.id as id_assuntos                                                  
		FROM cadastro_normas_juridicas_assuntos 
		LEFT JOIN aux_normas_juridicas_assuntos ON aux_normas_juridicas_assuntos.id = cadastro_normas_juridicas_assuntos.assunto
		WHERE norma = :norma ORDER BY cadastro_normas_juridicas_assuntos.id DESC";
		$stmt2 = $PDO_PROCLEGIS->prepare($sql2);    
		$stmt2->bindParam(':norma', 	$id);                                    
		$stmt2->execute();
		$rows2 = $stmt2->rowCount();
		if ($rows > 0)
		{                                        
			while($result2 = $stmt2->fetch()){
				$id_assuntos = $result2['id_assuntos'];
				$descricao = $result2['descricao'];
				$nj_assunto .="<b>$descricao</b><br>";
			}
		}


		$dados['dados'][0]['nj_descricao'] = $nj_descricao;
		$dados['dados'][0]['nj_assunto'] = $nj_assunto;
		$dados['dados'][0]['nj_id'] = $result['id'];
		echo JSON::encode($dados);
	}
	else {
		echo "false";
	}
}
