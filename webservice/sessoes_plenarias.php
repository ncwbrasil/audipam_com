<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if ($acao == 'listar'){

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
                    ORDER BY cadastro_sessoes_plenarias.id DESC LIMIT 50";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->execute();
	$rows = $stmt->rowCount();	
	if($rows > 0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$data = date('d/m/Y', strtotime($result['data_abertura'])); 
			$hora = date('H:i', $result['hora_abertura']); 
			
			$sp_conteudo = "
				<p><div class='subtitulo'>".$result['numero']."ª ".$result['descricao']." da ".$result['numero_sessao']." Sessão Legislativa da ".$result['numero_legislatura']." Legislatura</div>
				<div class='mini'>".$result['descricao']." - $data às $hora </div></p>
			";
			$dados['dados'][$i]['sp_conteudo'] = $sp_conteudo;
			$dados['dados'][$i]['sp_id'] = $result['id_sessao'];

		}
		echo JSON::encode($dados);
	}
	else
	{
		echo "false";
	}

}

if ($acao == 'apresentaSessao'){

	$id = $_GET['sp_id']; 
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
					WHERE cadastro_sessoes_plenarias.id = :id                     
                    ORDER BY cadastro_sessoes_plenarias.id DESC";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$rows = $stmt->rowCount();	
	if($rows > 0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$abertura = date('d/m/Y', strtotime($result['data_abertura']))." às ".date('H:i', $result['hora_abertura']);
			$encerramento = date('d/m/Y', strtotime($result['data_encerramento']))." às ".date('H:i', $result['hora_encerramento']);

			if($result['pauta']!=''){$pauta = "<b>Pauta</b>: <a href='https://audipam.com.br/proclegis/".$result['pauta']."'><i class='fas fa-file-alt'></i></a><br>";}
			if($result['ata']!=''){$ata = "<b>Ata</b>: <a href='https://audipam.com.br/proclegis/".$result['ata']."'><i class='fas fa-file-alt'></i></a><br>";}
			if($result['anexo']!=''){$anexo = "<b>Anexo</b>: <a href='https://audipam.com.br/proclegis/".$result['anexo']."'><i class='fas fa-file-alt'></i></a><br>";}

			
			$sp_conteudo = "
				<p class='subtitulo verde'>".$result['numero']."ª ".$result['descricao']." da ".$result['numero_sessao']." Sessão Legislativa da ".$result['numero_legislatura']." Legislatura</p>
				<p><b>Abertura</b>: $abertura<br>
				<b>Encerramento</b>: $encerramento <br>
				$pauta 
				$ata 
				$anexo 
				<b>Quorum</b>: ".$result['quorum']."</p>

			";
			$dados['dados'][$i]['sp_conteudo'] = $sp_conteudo;
			$dados['dados'][$i]['sp_id'] = $result['id_sessao'];

		}
		echo JSON::encode($dados);
	}
	else
	{
		echo "false";
	}

}
?>
