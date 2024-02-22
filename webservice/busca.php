<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action'];

if ($acao == 'pesquisaPav') {

	$valor = '%' . $_GET['valor'] . '%';
	$sql = "SELECT * FROM cadastro_parlamentares 
	WHERE nome like :nome ORDER BY nome ASC";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':nome', $valor);
	$stmt->execute();
	$rows = $stmt->rowCount();

	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$par_conteudo = "
				<p><b class='subtitulo'>" . $result['nome'] . "</b> <br>
				<span class='mini'>" . $result['apelido'] . "</span></p>
			";
			$foto = str_replace("../", "https://audipam.com.br/proclegis/", $result['foto']);
			$dados['dados'][$i]['par_previa'] = $par_conteudo;
			$dados['dados'][$i]['par_imagem'] = $foto;
			$dados['dados'][$i]['par_id'] = $result['id'];
		}
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if ($acao == 'pesquisaEsp') {

	$valor = explode("/", $_GET['valor']);
	$dt_inicio = $valor[0];
	$dt_fim = $valor[1];

	$sql = "SELECT *, cadastro_sessoes_plenarias.id as id_sessao
	, cadastro_sessoes_plenarias.numero as numero
	, aux_parlamentares_legislaturas.numero as numero_legislatura
	, YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
	, YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
	, aux_mesa_diretora_sessoes.numero as numero_sessao
	, YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
	, YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
	FROM cadastro_sessoes_plenarias 
	LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
	LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
	LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao  
	WHERE data_abertura BETWEEN :dt_inicio AND :dt_fim                   
	ORDER BY cadastro_sessoes_plenarias.id DESC";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':dt_inicio', $dt_inicio);
	$stmt->bindValue(':dt_fim', $dt_fim);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$data = date('d/m/Y', strtotime($result['data_abertura']));
			$hora = date('H:i', $result['hora_abertura']);

			$sp_conteudo = "
					<p><div class='subtitulo'>" . $result['numero'] . "ª " . $result['descricao'] . " da " . $result['numero_sessao'] . " Sessão Legislativa da " . $result['numero_legislatura'] . " Legislatura</div>
					<div class='mini'>" . $result['descricao'] . " - $data às $hora </div></p>
			";
			$dados['dados'][$i]['sp_conteudo'] = $sp_conteudo;
			$dados['dados'][$i]['sp_id'] = $result['id_sessao'];
		}
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if ($acao == 'pesquisaPpt') {
	$valor = '%' . $_GET['valor'] . '%';

	$sql = "SELECT *, cadastro_proposicoes.id as id
	, cadastro_proposicoes.observacao as descricao
	, aux_proposicoes_tipos.descricao as tipo 
	FROM cadastro_proposicoes 
	LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
	WHERE cadastro_proposicoes.tipo LIKE :tipo
	ORDER BY cadastro_proposicoes.id DESC";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':tipo', $valor);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$id = $result['id'];
			$tipo = $result['tipo'];
			$numero = $result['numero'];
			$data_envio = date('d/m/Y', strtotime(($result['data_envio'])));
			$pp_previa = "<p><span class='subtitulo'>$tipo nº $numero</span> <br>
				<b>Data de Envio:</b> $data_envio <br>";
			$dados['dados'][$i]['pp_previa'] = $pp_previa;
			$dados['dados'][$i]['pp_id'] = $id;
		}
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if ($acao == 'pesquisaOdsp') {
	$valor = '%' . $_GET['valor'] . '%';

	$sql = "SELECT *, cadastro_sessoes_plenarias.id as id
	, cadastro_sessoes_plenarias.numero as numero
	, aux_parlamentares_legislaturas.numero as numero_legislatura
	, YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
	, YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
	, aux_mesa_diretora_sessoes.numero as numero_sessao
	, YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
	, YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
	FROM cadastro_sessoes_plenarias 
	LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
	LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
	LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao
	WHERE cadastro_sessoes_plenarias.numero LIKE :numero
	ORDER BY cadastro_sessoes_plenarias.id DESC ";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':numero', $valor);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		for ($i = 0; $i < $stmt->rowCount(); $i++) {
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$data = date('d/m/Y', strtotime(($result['data_abertura'])));

			$odsp_previa = "<p><span class='subtitulo'>".$result['numero']."ª ".$result['descricao']." da ".$result['numero_sessao']." Sessão Legislativa da ".$result['numero_legislatura']." Legislatura</span> <br>
			<span class='bold'>Tipo:</span>  ".$result['descricao']."<p>
			<span class='bold'>Data abertura:</span> $data às ".substr($result['hora_abertura'],0,5)."<p>
			<span class='bold'>Legislatura:</span> ".$result['numero_legislatura']." (".$result['data_inicio_legislatura']." - ".$result['data_fim_legislatura'].")<p>
			<span class='bold'>Sessão:</span>  ".$result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].")<p>";
			$dados['dados'][$i]['odsp_previa'] = $odsp_previa;
			$dados['dados'][$i]['odsp_id'] = $result['id'];
		}
		echo JSON::encode($dados);
	} else {
		echo "false";
	}
}

if( $acao == 'pesquisaMl'){

	$numero = $_GET['numero']; 
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
	WHERE cadastro_materias.numero = :numero AND cadastro_materias.tipo = :tipo
	GROUP BY cadastro_materias.id";

	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':numero', $numero);
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

if($acao == 'pesquisaNj'){
	$norma = $_GET['valor']; 

	$sql = "SELECT *, aux_normas_juridicas_tipos.nome as tipo_nome,
		aux_normas_juridicas_tipos.sigla as tipo_sigla,
		cadastro_normas_juridicas.id as id
		FROM cadastro_normas_juridicas 
		LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas.tipo                     
		WHERE cadastro_normas_juridicas.numero = :norma";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':norma', $norma);
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