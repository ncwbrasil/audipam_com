<?php
header('Content-type: text/json');
include('../../../core/mod_includes/php/connect.php');
date_default_timezone_set('America/Sao_Paulo');

$usu_id = $_GET['usu_id'];
$usu_nome = $_GET['usu_nome'];
$data = $_GET['data'];
if($usu_id == "")
{
	$query_user = " 1 = 1 ";
}
else
{
	$query_user = " age_usuario = :age_usuario ";
}
if($usu_nome == '')
{
	$nome_query = " 1 = 1 ";
}
else
{
	$fil_nome1 = $fil_nome2 = "%".$usu_nome."%";
	$nome_query = " (cli_nome LIKE :fil_nome1 OR cli_nome_fotografado LIKE :fil_nome2 ) ";
}



$sql = "SELECT sag_id FROM social_agenda 
		LEFT JOIN ( cadastro_agendas 
			LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_agendas.age_usuario )
		ON cadastro_agendas.age_id = social_agenda.sag_agenda
		LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = social_agenda.sag_cliente
		WHERE $query_user AND $nome_query ";
$stmt_ultimo = $PDO->prepare($sql);
$stmt_ultimo->bindParam(':age_usuario',$usu_id);
$stmt_ultimo->bindParam(':fil_nome1',$fil_nome1);
$stmt_ultimo->bindParam(':fil_nome2',$fil_nome2);
$stmt_ultimo->execute();
$ultimo_registro = $stmt_ultimo->fetchAll(PDO::FETCH_COLUMN);
$ultimo_registro = end($ultimo_registro);

$sql = "SELECT * FROM social_agenda 
		LEFT JOIN ( cadastro_agendas 
			LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_agendas.age_usuario )
		ON cadastro_agendas.age_id = social_agenda.sag_agenda
		LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = social_agenda.sag_cliente
		LEFT JOIN aux_tipo_servico ON aux_tipo_servico.tps_id = social_agenda.sag_tipo_servico
		WHERE $query_user  AND $nome_query
		GROUP BY sag_id		
		";
		
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':age_usuario',$usu_id);
$stmt->bindParam(':fil_nome1',$fil_nome1);
$stmt->bindParam(':fil_nome2',$fil_nome2);
$stmt->execute();
echo '[';
while($result = $stmt->fetch())
{
	
	if($result['usu_foto'] == "")
	{
		$usu_foto = '../core/imagens/perfil.png';
	}
	else
	{
		$usu_foto = $result['usu_foto'];
	}

	
	if($ultimo_registro != $result['sag_id'])
	{	
	
		echo '	{ "date": "'.$result['sag_data'].' '.$result['sag_horario_inicial'].':00", "hour": "'.$result['sag_horario_inicial'].' às '.$result['sag_horario_final'].'", "gerenciar": "'.$result['sag_id'].'", "cliente": "'.$result['cli_nome'].' ('.$result['cli_nome_fotografado'].')", "envolvidos": "'.$result['usu_nome'].'", "logo": "'.$usu_foto.'",  "url": "social_agenda/edit/'.$result["sag_id"].'", "url_fotos": "social_agenda/fotos/'.$result["sag_id"].'", "tipo_servico": "'.$result['tps_nome'].'", "type": "demo", "title": "'.$result['sag_titulo'].'", "cenario": "'.$result['cen_nome'].'", "description": "'.$result['sag_descricao'].'" },';			
	}
	else
	{	
		echo '	{ "date": "'.$result['sag_data'].' '.$result['sag_horario_inicial'].':00", "hour": "'.$result['sag_horario_inicial'].' às '.$result['sag_horario_final'].'", "gerenciar": "'.$result['sag_id'].'", "cliente": "'.$result['cli_nome'].' ('.$result['cli_nome_fotografado'].')", "envolvidos": "'.$result['usu_nome'].'", "logo": "'.$usu_foto.'",  "url": "social_agenda/edit/'.$result["sag_id"].'", "url_fotos": "social_agenda/fotos/'.$result["sag_id"].'",  "tipo_servico": "'.$result['tps_nome'].'", "type": "demo", "title": "'.$result['sag_titulo'].'", "cenario": "'.$result['cen_nome'].'", "description": "'.$result['sag_descricao'].'"}';			
	}
}
	
echo ']';
//echo '	{ "date": "'; echo $initTime+3600000; echo '", "type": "demo", "title": "Project '; echo $i; echo ' demo", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", "url": "http://www.event2.com/" }';

//echo '[';
//	echo '	{ "date": "2015-11-03 16:30:10", "hour": "09:00 as 12:00", "espaco": "", "publico": "Pequenos de 3 a 6 anos", "comquem": "Brincadores", "valor": "R$100,00", "type": "meeting", "title": "Dança Adulto", "description": "Lorem Ipsum dolor set" },';
//	echo '	{ "date": "2015-11-03 17:30:10", "hour": "09:00 as 10:00", "espaco": "Sala Pequena", "publico": "Pequeninos de 8 a 30 meses", "comquem": "Brincadores", "valor": "R$150,00", "type": "demo", "title": "Sensorial Baby", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat." }';
//echo ']';
?>
