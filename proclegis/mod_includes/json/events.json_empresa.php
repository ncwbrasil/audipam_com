<?php
header('Content-type: text/json');
require_once("../php/ctracker.php");
include('../php/connect.php');
date_default_timezone_set('America/Sao_Paulo');

$age_id = $_GET['age_id'];

$sql = "SELECT * FROM agenda_gerenciar_itens WHERE agi_agenda = :age_id  AND agi_data >= '2020-01-01' ";
$stmt_ultimo = $PDO->prepare($sql);
//$stmt_ultimo->bindValue(':agi_status',1);
$stmt_ultimo->bindParam(':age_id',$age_id);
$stmt_ultimo->execute();
$ultimo_registro = $stmt_ultimo->fetchAll(PDO::FETCH_COLUMN);
$ultimo_registro = end($ultimo_registro);

$sql = "SELECT * FROM agenda_gerenciar_itens 
		LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
		LEFT JOIN (cliente_solicitacoes_agenda 
			LEFT JOIN ( cliente_solicitacoes 
				LEFT JOIN (cadastro_empresas 
					LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf)
				ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
				LEFT JOIN ( cadastro_contratos 
					LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
				ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato )
			ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
		ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
		WHERE agi_agenda = :age_id AND agi_data >= '2020-01-01' 
		ORDER BY agi_id ASC
		 ";
		
$stmt = $PDO->prepare($sql);
//$stmt->bindValue(':agi_status',1);
$stmt->bindParam(':age_id',$age_id);
$stmt->execute();
echo '[';
while($result = $stmt->fetch())
{
	// TECNICOS
	$sql = "SELECT usu_nome FROM agenda_gerenciar_usuario
			 INNER JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario
			 WHERE agu_agenda_item = :agi_id";
	$stmt_usuarios = $PDO->prepare($sql);
	$stmt_usuarios->bindParam(':agi_id',$result['agi_id']);
	$stmt_usuarios->execute();
	$rows_usuarios = $stmt_usuarios->rowCount();
	$usuarios="";
	if($rows_usuarios > 0)
	{
		$ultimo_usuario = $stmt_usuarios->fetchAll(PDO::FETCH_COLUMN);
		$ultimo_usuario = end($ultimo_usuario);
		$stmt_usuarios->execute();
		while($result_usuarios = $stmt_usuarios->fetch())
		{
			$atual = $result_usuarios['usu_nome'];
			$usuarios .= $atual;
			if($ultimo_usuario != $atual)
			{
				$usuarios .= ", ";
			}	
		}					
	}	
	
	// CARROS
	$sql = "SELECT car_descricao FROM agenda_gerenciar_carro
			 LEFT JOIN cadastro_carros ON cadastro_carros.car_id = agenda_gerenciar_carro.agc_carro
			 WHERE agc_agenda_item = :agi_id";
	$stmt_carros = $PDO->prepare($sql);
	$stmt_carros->bindParam(':agi_id',$result['agi_id']);
	$stmt_carros->execute();
	$rows_carros = $stmt_carros->rowCount();
	$carros="";
	if($rows_carros > 0)
	{
		$ultimo_carro = $stmt_carros->fetchAll(PDO::FETCH_COLUMN);
		$ultimo_carro = end($ultimo_carro);
		$stmt_carros->execute();
		while($result_carros = $stmt_carros->fetch())
		{
			$atual = $result_carros['car_descricao'];
			$carros .= $atual;
			if($ultimo_carro != $atual)
			{
				$carros .= ", ";
			}	
		}					
	}	
	
	// SOLICITACOES
	$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes
			LEFT JOIN ( cadastro_contratos 
				LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
				LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
			ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
			
			LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
			LEFT JOIN cliente_solicitacoes_agenda ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
			LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
			WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
				  soa_agenda_item = :agi_id AND sol_id 
			GROUP BY sol_id 
			";
	$stmt_solic = $PDO->prepare($sql);
	$stmt_solic->bindParam(':agi_id',$result['agi_id']);
	$stmt_solic->execute();
	$rows_solic = $stmt_solic->rowCount();
	$solic="";
	$status="";
	$user="";
	$obs="";
	if($rows_solic > 0)
	{
		$ultimo_solic = $stmt_solic->fetchAll(PDO::FETCH_COLUMN);
		$ultimo_solic = end($ultimo_solic);
		$stmt_solic->execute();
		while($result_solic = $stmt_solic->fetch())
		{
			$atual = $result_solic['sol_id'];			
			$status			= $result_solic['sts_status'];
			$user		= $result_solic['ctt_nome'].$result_solic['usu_nome'];
			$sts_data		= implode("/",array_reverse(explode("-",substr($result_solic['sts_data'],0,10))));
			$sts_hora		= substr($result_solic['sts_data'],11,5);
			$data_hora = $sts_data." às ".$sts_hora;							
			$obs = trim(
					str_replace(
						'"',"´",preg_replace(
							'/\s+/', " ",str_replace(
									array("\r\n", "\r", "\n"), "|",str_replace(
										"\t", " ",str_replace('\\',"/",$result_solic['sts_observacao']))))));
			
			
			
			$tool = str_replace(" ","&nbsp;",$solicitante);
			switch($status)
			{
				case 1 : $status = "Registrado";break;
				case 2 : $status = "Em Análise";break;
				case 3 : $status = "<span class='laranja'>Agendado</span>";break;
				case 4 : $status = "<span class='azul'>Em Execução</span>";break;
				case 5 : $status = "<span class='vermelho'>Cancelado</span>";break;
				case 6 : $status = "Em Homologação";break;
				case 7 : $status = "<span class='verde'>Concluído</span>";break;
			}
			//$status = $status." <a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=".$result_solic['sol_id']."$autenticacao'><img src='../imagens/icon-exibir_agenda.png' onmouseover=toolTip('<b>Usuário</b>');  onmouseout='toolTip();'></a>";
			
			$atual2 = "<a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=".$result_solic['sol_id']."$autenticacao'>".$result_solic['ano'].".".$result_solic['sol_id']." <img src='../imagens/icon-exibir_agenda.png'></a>";
			$solic .= $atual2;
			if($ultimo_solic != $atual)
			{
				$solic .= ", ";
			}	
		}					
	}	
	
	// SERVIDORES
	$sql = "SELECT ctt_nome FROM agenda_gerenciar_servidores
			 INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = agenda_gerenciar_servidores.ags_servidor
			 WHERE ags_agenda_item = :agi_id";
	$stmt_servidores = $PDO->prepare($sql);
	$stmt_servidores->bindParam(':agi_id',$result['agi_id']);
	$stmt_servidores->execute();
	$rows_servidores = $stmt_servidores->rowCount();
	$servidores="";
	if($rows_servidores > 0)
	{
		$ultimo_servidor = $stmt_servidores->fetchAll(PDO::FETCH_COLUMN);
		$ultimo_servidor = end($ultimo_servidor);
		$stmt_servidores->execute();
		while($result_servidores = $stmt_servidores->fetch())
		{
			$atual = $result_servidores['ctt_nome'];
			$servidores .= $atual;
			if($ultimo_servidor != $atual)
			{
				$servidores .= ", ";
			}	
		}					
	}	
	
	if($result['emp_nome_razao'] == "")
	{
		$titulo = "Solicitação Interna";
	}
	elseif($result['emp_nome_razao'] != "")
	{
		$titulo = $result['emp_nome_razao']."/".$result['uf_sigla'];
	}
	if($ultimo_registro != $result['agi_id'])
	{	
	
		echo '	{ "date": "'.$result['agi_data'].' '.$result['agi_horario_inicial'].':00", "hour": "'.$result['agi_horario'].'", "gerenciar": "'.$result['agi_id'].'", "anexo": "'.$result['agi_anexo'].'", "envolvidos": "'.$usuarios.'", "servidores": "'.$servidores.'", "carros": "'.$carros.'","solicitacoes": "'.$solic.'", "publico": "'.$result['agi_publico'].'", "comquem": "'.$result['agi_comquem'].'", "logo": "'.$result['emp_logo'].'", "valor": "'.$result['agi_valor'].'", "cliente": "'.$titulo.'", "url": "agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens_editar&age_id='.$age_id.'&agi_id='.$result['agi_id'].'&login='.$login.'&n='.$n.'", "forma_atendimento": "'.$result['fat_descricao'].'", "type": "demo", "title": "'.$result['agi_titulo'].'", "description": "'.$result['agi_descricao'].'", "servico": "'.$result['ser_descricao'].'", "status": "'.$status.'", "user": "'.$user.'", "obs": "'.$obs.'", "data_hora": "'.$data_hora.'" },';			
	}
	else
	{	
		echo '	{ "date": "'.$result['agi_data'].' '.$result['agi_horario_inicial'].':00", "hour": "'.$result['agi_horario'].'", "gerenciar": "'.$result['agi_id'].'", "anexo": "'.$result['agi_anexo'].'", "envolvidos": "'.$usuarios.'", "servidores": "'.$servidores.'", "carros": "'.$carros.'","solicitacoes": "'.$solic.'", "publico": "'.$result['agi_publico'].'", "comquem": "'.$result['agi_comquem'].'", "logo": "'.$result['emp_logo'].'", "valor": "'.$result['agi_valor'].'", "cliente": "'.$titulo.'", "url": "agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens_editar&age_id='.$age_id.'&agi_id='.$result['agi_id'].'&login='.$login.'&n='.$n.'", "forma_atendimento": "'.$result['fat_descricao'].'", "type": "demo", "title": "'.$result['agi_titulo'].'", "description": "'.$result['agi_descricao'].'", "servico": "'.$result['ser_descricao'].'", "status": "'.$status.'", "user": "'.$user.'", "obs": "'.$obs.'", "data_hora": "'.$data_hora.'" }';			
	}
}
	
echo ']';
//echo '	{ "date": "'; echo $initTime+3600000; echo '", "type": "demo", "title": "Project '; echo $i; echo ' demo", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.", "url": "http://www.event2.com/" }';

//echo '[';
//	echo '	{ "date": "2015-11-03 16:30:10", "hour": "09:00 as 12:00", "espaco": "", "publico": "Pequenos de 3 a 6 anos", "comquem": "Brincadores", "valor": "R$100,00", "type": "meeting", "title": "Dança Adulto", "description": "Lorem Ipsum dolor set" },';
//	echo '	{ "date": "2015-11-03 17:30:10", "hour": "09:00 as 10:00", "espaco": "Sala Pequena", "publico": "Pequeninos de 8 a 30 meses", "comquem": "Brincadores", "valor": "R$150,00", "type": "demo", "title": "Sensorial Baby", "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat." }';
//echo ']';
?>
