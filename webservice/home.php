<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if ($acao == 'listarHome'){
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
                    ORDER BY cadastro_sessoes_plenarias.id DESC
                    LIMIT :num_por_pagina";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue(':num_por_pagina', 4);
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
			$dados['dados']['sessoes'][$i]['sp_conteudo'] = $sp_conteudo;
			$dados['dados']['sessoes'][$i]['sp_id'] = $result['id_sessao'];
		}
	}
	else {
		$dados['dados']['sessoes'][$i]['sp_conteudo'] = "<p class='subtitulo'> Não há sessões cadastradas no momento!";
		$dados['dados']['sessoes'][$i]['sp_id'] = 0;
	}

	$sql_camara = 'SELECT * FROM cadastro_clientes 
	LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_clientes.cli_municipio
	LEFT JOIN end_uf ON end_uf.uf_id = cadastro_clientes.cli_uf
	WHERE cli_url = :cli_url';
	$stmt_camara = $PDO->prepare($sql_camara);
	$stmt_camara->bindValue(':cli_url', $cli_url);
	$stmt_camara->execute();
	$rows_camara = $stmt_camara->rowCount();
	if($rows_camara > 0){			
		$result_camara = $stmt_camara->fetch(PDO::FETCH_ASSOC);
		$dados['dados']['camara'][0]['cli_imagem'] = "https://audipam.com.br/proclegis/".$result_camara['cli_foto'];

		$cli_conteudo = "<p><i class='fas fa-map-marker-alt'></i> ".$result_camara['cli_endereco']." - ".$result_camara['mun_nome']." - ".$result_camara['uf_nome']."<br>
		<i class='fas fa-at'></i>".$result_camara['cli_email']."<br>
		<i class='fas fa-phone'></i>".$result_camara['cli_telefone']."</p>";
		$dados['dados']['camara'][0]['cli_nome'] = $result_camara['cli_nome'];
		$dados['dados']['camara'][0]['cli_conteudo'] = $cli_conteudo;
	}



	$sql_noticias = 'SELECT * FROM portal_noticias WHERE nt_status = :nt_status LIMIT :limite';
	$stmt_noticias = $PDO_PROCLEGIS->prepare($sql_noticias);
	$stmt_noticias->bindValue(':nt_status', 1);
	$stmt_noticias->bindValue(':limite', 4);
	$stmt_noticias->execute();
	$rows_noticias = $stmt_noticias->rowCount();
	if($rows_noticias > 0){			
		
		for($i = 0; $i < $stmt_noticias->rowCount(); $i++){
			$result_noticias = $stmt_noticias->fetch(PDO::FETCH_ASSOC);

			$nt_previa = "<p><span class='subtitulo'>".$result_noticias['nt_titulo']."</span><br><i class='fas fa-calendar-alt'></i> ".date('d/m/Y', strtotime($result_noticias['nt_data_cadastro']))."</p>";

			$dados['dados']['noticias'][$i]['nt_imagem'] = "https://audipam.com.br/proclegis/".$result_noticias['nt_imagem'];
			$dados['dados']['noticias'][$i]['nt_previa'] = $nt_previa;
			$dados['dados']['noticias'][$i]['nt_id'] = $result_noticias['nt_id'];

		}
	}


	$sql_tv = 'SELECT * FROM portal_tv_camara WHERE tv_status = :tv_status ORDER BY tv_id ASC';
	$stmt_tv = $PDO_PROCLEGIS->prepare($sql_tv);
	$stmt_tv->bindValue(':tv_status', 1);
	$stmt_tv->execute();
	$rows_tv = $stmt_tv->rowCount();
	if($rows_tv > 0){			
		
		$result_tv = $stmt_tv->fetch(PDO::FETCH_ASSOC);
		$url = "https://www.youtube.com/embed/".str_replace("https://www.youtube.com/watch?v=", "", str_replace("&ab_channel=C%C3%A2maraMunicipaldeMogidasCruzes", "", $result_tv['tv_url'])); 
		$dados['dados']['tv_camara'][0]['tv_url'] = $url;

	}


	$dados['dados']['paginas'][0]['hm_paginas']= "Sessões Plenárias";
	$dados['dados']['paginas'][0]['hm_link']= "tabs/sessoes-plenarias";
	$dados['dados']['paginas'][0]['hm_icone']= "far fa-comments";

	$dados['dados']['paginas'][1]['hm_paginas']= "Proposição";
	$dados['dados']['paginas'][1]['hm_link']= "tabs/proposicoes";
	$dados['dados']['paginas'][1]['hm_icone']= "far fa-file-alt";

	$dados['dados']['paginas'][2]['hm_paginas']= "Normas Jurídicas";
	$dados['dados']['paginas'][2]['hm_link']= "tabs/normas-juridicas";
	$dados['dados']['paginas'][2]['hm_icone']= "fas fa-balance-scale";

	$dados['dados']['paginas'][3]['hm_paginas']= "Parlamentares"; 
	$dados['dados']['paginas'][3]['hm_link']= "tabs/parlamentares"; 
	$dados['dados']['paginas'][3]['hm_icone']= "fas fa-user-tie"; 

	$dados['dados']['paginas'][4]['hm_paginas']= "Matérias Legislativas"; 
	$dados['dados']['paginas'][4]['hm_link']= "tabs/materias-legislativas"; 
	$dados['dados']['paginas'][4]['hm_icone']= "fas fa-book-open"; 

	// $dados['dados']['paginas'][5]['hm_paginas']= "Mesa Diretora"; 
	// $dados['dados']['paginas'][5]['hm_link']= "tabs/mesa-diretora"; 
	// $dados['dados']['paginas'][5]['hm_icone']= "fas fa-users"; 

	echo JSON::encode($dados);

//	echo "<pre>";
//	print_r($dados); 
}


?>