<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if($acao == 'listar'){

	$sql = "SELECT * FROM portal_noticias";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$noticia="<p class='data'>".date('d/m/Y H:i', strtotime($result['nt_data_cadastro']))."</p>
				<p class='titulo'>".$result['nt_titulo']."</p>
				<p>".mb_strimwidth($result['nt_descricao'], 0, 150, "...")."</p>";
			$foto = str_replace("../", "https://audipam.com.br/proclegis/", $result['nt_imagem']);

			$dados['dados'][$i]['nt_previa'] = $noticia;
			$dados['dados'][$i]['nt_imagem'] = $foto;
			$dados['dados'][$i]['nt_id'] = $result['nt_id'];
		}
		echo JSON::encode($dados);
	}
	else {
		echo 'false'; 
	}
}

if($acao == 'apresenta'){

	$nt_id = $_GET['nt_id'];
	$sql = "SELECT * FROM portal_noticias WHERE nt_id = :nt_id";
	$stmt = $PDO_PROCLEGIS->prepare($sql);
	$stmt->bindValue('nt_id', $nt_id);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		for($i = 0; $i < $stmt->rowCount(); $i++){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			$foto = str_replace("../", "https://audipam.com.br/proclegis/", $result['nt_imagem']);

			$noticia="<p class='data'>".date('d/m/Y H:i', strtotime($result['nt_data_cadastro']))."</p>
					<p class='subtitulo'>".$result['nt_titulo']."</p>
					<p>
						".$result['nt_descricao']."
					</p>"; 

			$dados['dados'][$i]['nt_descricao'] = $noticia;
			$dados['dados'][$i]['nt_imagem'] = $foto;
			$dados['dados'][$i]['nt_id'] = $result['nt_id'];
		}
		echo JSON::encode($dados);
	}
	else {
		echo 'false'; 
	}
}

?>