<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if ($acao == 'listarProposicao'){

	$id = $_GET['id']; 

	if($id == ''){
		$sql = "SELECT *, cadastro_proposicoes.id as id
		, cadastro_proposicoes.observacao as descricao
		, aux_proposicoes_tipos.descricao as tipo 
		FROM cadastro_proposicoes 
		LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
		ORDER BY cadastro_proposicoes.id DESC";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			for($i = 0; $i < $stmt->rowCount(); $i++){
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				$id = $result['id'];                            
				$tipo = $result['tipo'];
				$numero = $result['numero']; 
				if($result['data_envio'] == '' ){
					$data_envio ='Sem data Prevista'; 
				}else {
					$data_envio = date('d/m/Y', strtotime(($result['data_envio']))); 
				}
				if($result['data_recebimento'] == ''){
					$data_recebimento ='Sem data Prevista'; 
				}else {
					$data_recebimento = date('d/m/Y', strtotime(($result['data_recebimento']))); 
				}

				$pp_previa = "<p><span class='subtitulo'>$tipo nº $numero</span> <br>
					<b>Data de Envio:</b> $data_envio <br>
					<b>Data de Recebimento:</b> $data_recebimento";
				$dados['dados'][$i]['pp_previa'] = $pp_previa;
				$dados['dados'][$i]['pp_id'] = $id;

			}
			echo JSON::encode($dados);
		}
		else
		{
			echo "false";
		}
	}
	else {	
		$sql = "SELECT *, cadastro_proposicoes.id as id
		, cadastro_proposicoes.observacao as descricao
		, aux_proposicoes_tipos.descricao as tipo 
		FROM cadastro_proposicoes 
		LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
		WHERE cadastro_proposicoes.autor = :autor
		ORDER BY cadastro_proposicoes.id DESC";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->bindValue(':autor', $id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			for($i = 0; $i < $stmt->rowCount(); $i++){
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				$id = $result['id'];                            
				$tipo = $result['tipo'];
				$numero = $result['numero']; 

				if($result['data_envio'] == '' ){
					$data_envio ='Sem data Prevista'; 
				}else {
					$data_envio = date('d/m/Y', strtotime(($result['data_envio']))); 
				}
				if($result['data_recebimento'] == ''){
					$data_recebimento ='Sem data Prevista'; 
				}else {
					$data_recebimento = date('d/m/Y', strtotime(($result['data_recebimento']))); 
				}

				$pp_previa = "<p><span class='subtitulo'>$tipo nº $numero</span> <br>
					<b>Data de Envio:</b> $data_envio <br>
					<b>Data de Recebimento:</b> $data_recebimento";
				$dados['dados'][$i]['pp_previa'] = $pp_previa;
				$dados['dados'][$i]['pp_id'] = $id;

			}
			echo JSON::encode($dados);
		}
		else
		{
			echo "false";
		}

	}
}

if($acao == 'apresentarProposicao'){
	$id = $_GET['id'];
	$sql = "SELECT *, cadastro_proposicoes.observacao as descricao
	, aux_proposicoes_tipos.descricao as tipo_descricao
	, aux_materias_tipos.nome as nome_materia 
	, aux_materias_tipos.sigla as sigla_materia 
	FROM cadastro_proposicoes 
	LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_proposicoes.tipo_materia
	LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo                     
	WHERE cadastro_proposicoes.id = :id ";
	$stmt = $PDO_PROCLEGIS->prepare($sql);            
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		$result = $stmt->fetch();                                                 
		$tipo    = $result['tipo'];
		$tipo_descricao    = $result['tipo_descricao'];
		$numero         = $result['numero'];
		$descricao         = $result['descricao'];
		$observacao         = $result['observacao'];
		$texto_original         =  str_replace("../", "", $result['texto_original']); 
		$tipo_materia       = $result['tipo_materia'];
		$nome_materia       = $result['nome_materia'];
		$sigla_materia       = $result['sigla_materia'];
		$numero_materia         = $result['numero_materia'];
		$ano_materia         = $result['ano_materia'];
		$data_envio         = $result['data_envio'];

		if($result['data_envio'] == '' ){
			$data_envio ='Sem data Prevista'; 
		}else {
			$data_envio = date('d/m/Y', strtotime(($result['data_envio']))); 
		}
		if($result['data_recebimento'] == ''){
			$data_recebimento ='Sem data Prevista'; 
		}else {
			$data_recebimento = date('d/m/Y', strtotime(($result['data_recebimento']))); 
		}

		if($texto_original != ''){ $documento = "<p><b>Texto Original:</b> <a href='https://audipam.com.br/proclegis/$texto_original' target='_blank'><i class='fas fa-file-alt'></i></a> </p>";}
		if($nome_materia) { $materia = "<p><b>Matéria Vinculada:</b> $nome_materia nº $numero_materia de $ano_materia</p>";}

		$pp_descricao="<p class='titulo verde'>$tipo_descricao - $numero</p>
				<p><b>Data de Envio:</b> $data_envio <br>
				<b>Data de Recebimento:</b> $data_recebimento </p>
				<p>$descricao</p>
				<p><b>Observação:</b> $observacao</p>
				$documento
				$materia";

		$dados['dados'][0]['pp_descricao'] = $pp_descricao;
		$dados['dados'][0]['pp_id'] = $result['id']; 
		$dados['dados'][0]['pp_latitude'] = $result['latitude'];
		$dados['dados'][0]['pp_longitude'] = $result['longitude'];

	
		echo JSON::encode($dados);
			
	}else {
		echo "false";
	}
}

if ($acao == 'tipoProposicao') {
	$sql = "SELECT * FROM aux_proposicoes_tipos ORDER BY descricao";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$dados['dados'][$i]['tp_id'] = $result['id'];
			$dados['dados'][$i]['tp_descricao'] = $result['descricao'];
		}
	}else {
		echo "false"; 
	}
	echo JSON::encode($dados);
}

?>
