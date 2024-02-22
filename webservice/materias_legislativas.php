<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action'];

if ($acao == 'listarMaterias') {

	$tipo = $_GET['tipo'];

    $sql = "SELECT *, aux_materias_tipos.nome as tipo_nome,
		aux_materias_tipos.sigla as tipo_sigla,
		cadastro_materias.id as id
		FROM cadastro_materias 
		LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo
		LEFT JOIN cadastro_materias_assuntos ON cadastro_materias_assuntos.materia = cadastro_materias.id
		LEFT JOIN ( cadastro_materias_autoria 
		LEFT JOIN (aux_autoria_autores 
			LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
		ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
		ON cadastro_materias_autoria.materia = cadastro_materias.id  
		WHERE aux_materias_tipos.id = :tipo
		GROUP BY cadastro_materias.id
		ORDER BY cadastro_materias.id DESC
		LIMIT :limite";

	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':limite', 20);
	$stmt->bindValue(':tipo', $tipo);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$id = $result['id'];
			// AUTORES
			$autor = array();
			$sql_aut = "SELECT *
					FROM cadastro_materias_autoria
					LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
					WHERE cadastro_materias_autoria.materia = :materia	";
			$stmt_aut = $PDO_PROCLEGIS->prepare($sql_aut);
			$stmt_aut->bindParam(':materia',     $id);
			$stmt_aut->execute();
			$rows_aut = $stmt_aut->rowCount();
			if ($rows_aut > 0) {
				while ($result_aut = $stmt_aut->fetch()) {
					$autor[] = $result_aut['nome'];
				}
			}			
			
			$ml_previa = "                                      
				<p>	" . $result['tipo_sigla'] . " " . $result['numero'] . " de " . $result['ano'] . " - " . $result['tipo_nome'] . "</p>
				<span class='bold'>Ementa: " . $result['ementa'] . "<br>
				<span class='bold'>Data apresentação: " . reverteData($result['data_apresentacao']) . "<br>
				<span class='bold'>Autor(es): " . implode(", ", $autor) . "<br>
				";

			$dados['dados'][$i]['ml_previa'] = $ml_previa;
			$dados['dados'][$i]['ml_id'] = $id; 
		}


		// print_r($dados); 
		// exit; 
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if ($acao == 'apresentarMateria') {

	$materia = $_GET['id'];
	$sql = "SELECT *, t1.nome as tipo_nome,
	t1.sigla as tipo_sigla,
	t2.sigla as sigla_externa,
	t2.nome as nome_externa,
	t3.sigla as sigla_origem,
	t3.nome as nome_origem,
	cadastro_materias.id as id
	FROM cadastro_materias 
	LEFT JOIN aux_materias_tipos t1 ON t1.id = cadastro_materias.tipo                                         
	LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_materias.tipo_origem_externa                                         
	LEFT JOIN aux_materias_origem t3 ON t3.id = cadastro_materias.local_origem                                         
	WHERE cadastro_materias.id = :id ";
	$stmt = $PDO_PROCLEGIS->prepare($sql);            
	$stmt->bindParam(':id', $materia);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		$result = $stmt->fetch();

		if($result['endereco']!=''){
			$endereco ="<p>Endereço : ".$result['endereco'].",".$result['end_numero']." - ".$result['cidade']."</div>";
		}

		$ml_descricao = "<div id='dados_gerais' class='tab-pane fade in active' >
			<p><span class='titulo verde'>".$result['tipo_nome']." </span> <br>	<span class='subtitulo laranja'> ".$result['numero']." - ".$result['ano']." </span> </p>
			<p>Data Apresetação: ".reverteData($result['data_apresentacao'])." </p>
			<p>Tipo de apresentação: ".$result['apresentacao']." </p>
			<p>Protocolo: ".$result['protocolo']." </p>
			<p>Apelido: ".$result['apelido']." </p>
			<p>Dias prazo: ".$result['dias_prazo']." </p>
			<p>Matéria polêmica? ".$result['materia_polemica']." </p>
			<p>Objeto: ".$result['objeto']." </p>
			<p>Regime de tramitação: ".$result['regime_tramitacao']." </p>
			<p>Em tramitação? ".$result['em_tramitacao']." </p>
			<p>Fim prazo: ".reverteData($result['data_fim_prazo'])." </p>
			<p>Data publicação: ".reverteData($result['data_publicacao'])." </p>
			<p>É complementar? ".$result['complementar']." </p>
			<p>Ementa: ".$result['ementa']." </p>
			$endereco
		</div>";
		
		//ASSUNTOS
		$sql = "SELECT *, cadastro_materias_assuntos.id as id_assuntos                                                  
		FROM cadastro_materias_assuntos 
		LEFT JOIN aux_materias_assuntos ON aux_materias_assuntos.id = cadastro_materias_assuntos.assunto
		WHERE materia = :materia
		ORDER BY cadastro_materias_assuntos.id DESC";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->bindParam(':materia',     $materia);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {
			while ($result = $stmt->fetch()) {
				$id_assuntos = $result['id_assuntos'];
				$descricao = $result['descricao'];
				$assunto = $result['assunto'];
				$ml_assuntos.="<p><b>Assunto: </b> $assunto</p>
					<p><b>Descrição: </b> $descricao</p>
				";
			}
		} else {
			$ml_assuntos =  "Não há assuntos cadastrados no momento.";
		}

		//AUTORIA
		$sql = "SELECT *, cadastro_materias_autoria.id as id_autoria                                                  
		FROM cadastro_materias_autoria 
		LEFT JOIN aux_autoria_tipo_autor ON aux_autoria_tipo_autor.id = cadastro_materias_autoria.tipo_autor
		LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor  
		LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id  =   aux_autoria_autores.parlamentar                               
		WHERE materia = :materia
		ORDER BY cadastro_materias_autoria.id DESC";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->bindParam(':materia',     $materia);
		$stmt->execute();
		$rows = $stmt->rowCount();

		if ($rows > 0) {
		while ($result = $stmt->fetch()) {
			$id_autoria = $result['id_autoria'];
			$tipo_autor = $result['tipo_autor'];
			$descricao = $result['descricao'];
			$autor = $result['autor'];
			$nome = $result['nome'];
			$primeiro_autor = $result['primeiro_autor'];

			$ml_autoria .= "<p><img src='" . str_replace("../", "https://audipam.com.br/proclegis/", $result['foto']) . "' id='foto_autor' width='100px' margin-botton='-10%'> &nbsp;&nbsp;$nome <br><b>Primeiro Autor: </b>$primeiro_autor<p>";
		}

		} else {
			$ml_autoria = "Não há nenhum item cadastrado.";
		}

		//TRAMITASSAO
		$sql = "SELECT *, cadastro_materias_tramitacao.id as id_tramitacao
		, aux_materias_status_tramitacao.nome as nome_status                                                   
		, cadastro_usuarios.usu_nome as nome_responsavel 
		FROM cadastro_materias_tramitacao 
		LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = cadastro_materias_tramitacao.unidade_origem
		LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = cadastro_materias_tramitacao.unidade_destino                                        
		LEFT JOIN aux_materias_status_tramitacao ON aux_materias_status_tramitacao.id = cadastro_materias_tramitacao.status_tramitacao  
		LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_materias_tramitacao.responsavel
		WHERE materia = :materia
		ORDER BY cadastro_materias_tramitacao.data_tramitacao DESC
		";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->bindParam(':materia',     $materia);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {
			while ($result = $stmt->fetch()) {
				$id_tramitacao = $result['id_tramitacao'];
				$unidade_origem = $result['unidade_origem'];

				// PEGA DADOS DA UNIDADE ORIGEM
				$sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
								, aux_materias_orgaos.nome as nome_orgao
								, cadastro_comissoes.sigla as sigla_comissao
								, cadastro_comissoes.nome as nome_comissao
								, cadastro_parlamentares.nome as nome_parlamentar
						FROM aux_materias_unidade_tramitacao
						LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
						LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
						LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
						WHERE aux_materias_unidade_tramitacao.id = :id";
				$stmt_origem = $PDO_PROCLEGIS->prepare($sql);
				$stmt_origem->bindParam(':id',     $unidade_origem);
				if ($stmt_origem->execute()) {
					$result_origem = $stmt_origem->fetch();
					$origem = $result_origem['nome_parlamentar'] . $result_origem['sigla_orgao'] . " " . $result_origem['nome_orgao'] . $result_origem['sigla_comissao'] . " " . $result_origem['nome_comissao'];
				}


				$unidade_destino = $result['unidade_destino'];
				// PEGA DADOS DA UNIDADE DESTINO
				$sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
									, aux_materias_orgaos.nome as nome_orgao
									, cadastro_comissoes.sigla as sigla_comissao
									, cadastro_comissoes.nome as nome_comissao
									, cadastro_parlamentares.nome as nome_parlamentar
						FROM aux_materias_unidade_tramitacao
						LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
						LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
						LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
						WHERE aux_materias_unidade_tramitacao.id = :id";
				$stmt_destino = $PDO_PROCLEGIS->prepare($sql);
				$stmt_destino->bindParam(':id',     $unidade_destino);
				if ($stmt_destino->execute()) {
					$result_destino = $stmt_destino->fetch();
					$destino = $result_destino['nome_parlamentar'] . $result_destino['sigla_orgao'] . " " . $result_destino['nome_orgao'] . $result_destino['sigla_comissao'] . " " . $result_destino['nome_comissao'];
					$ultima_tramitacao = $result_destino['comissao'];
				}

				$data_tramitacao = reverteData($result['data_tramitacao']);
				$hora_tramitacao = substr($result['hora_tramitacao'], 0, 5);
				$data_encaminhamento = reverteData($result['data_encaminhamento']);
				$data_fim_prazo = reverteData($result['data_fim_prazo']);
				$status_tramitacao = $result['status_tramitacao'];
				$nome_status = $result['nome_status'];
				$turno = $result['turno'];
				$urgente = $result['urgente'];
				$texto_acao = $result['texto_acao'];
				$responsavel = $result['responsavel'];
				$nome_responsavel = $result['nome_responsavel'];
				$confirmacao_recebimento = $result['confirmacao_recebimento'];
				$usu_recebimento = $result['usu_recebimento'];
				$anexo = $result['anexo'];
				$paginas = $result['paginas'];
				
				$ml_tramitacao .= "<p><b>Data e Hora:</b> $data_tramitacao às $hora_tramitacao<br><b>Tramitação:</b> $origem <i class='fas fa-long-arrow-alt-right'></i> $destino </span> <br>$nome_status <br>$texto_acao</p>";
			}
		} else {
			$ml_tramitacao =  "Não há nenhum item cadastrado.";
		}

		$dados['dados'][0]['ml_descricao'] = $ml_descricao;
		$dados['dados'][0]['ml_autoria'] = $ml_autoria;
		$dados['dados'][0]['ml_tramitacao'] = $ml_tramitacao;
		$dados['dados'][0]['ml_assuntos'] = $ml_assuntos;
		$dados['dados'][0]['ml_id'] = $materia;
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if($acao == 'cadastrarEmail'){
	$am_materia = $_POST['am_materia']; 
	$am_email = $_POST['am_email']; 
	$sql = "INSERT INTO aux_acompanhar_materia SET am_materia = :am_materia, am_email = :am_email ";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':am_materia', $am_materia);
	$stmt->bindValue(':am_email', $am_email);
	if ($stmt->execute()) {
		echo "true";
	} else {
		echo "false";
	}

}

if($acao == 'tipoMateria'){
	$sql = " SELECT * FROM aux_materias_tipos ORDER BY nome";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->execute();
	$i = 0; 
	while ($result = $stmt->fetch()) {
		$dados['dados'][$i]['ml_tipo'] = $result['id'];
		$dados['dados'][$i]['ml_tipo_nome'] = $result['nome'] ;
		$i++; 
	}
	echo JSON::encode($dados);
}
