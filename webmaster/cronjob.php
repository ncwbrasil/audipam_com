<?php
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam | Gerenciador de Sistemas</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../core/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../core/mod_includes/js/jquery-1.8.3.min.js"></script>
<!-- TOOLBAR -->
<link href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<script type="text/javascript" src="../core/mod_includes/js/funcoes.js"></script>
</head>
<body>
<?php	
require_once("../core/mod_includes/php/ctracker.php");
include		('../core/mod_includes/php/connect_sistema.php');
include		('../core/mod_includes/php/funcoes-jquery.php');
include		('../core/mod_includes/php/funcoes.php');
?>

<?php


$hoje = date("Y-m-d", strtotime("- 2 days"));
$null = '';

### VENCE A FATURA - INI ###
$sql_vence_fatura = "SELECT * FROM financeiro_faturas 
					  LEFT JOIN ( cadastro_servicos 
					 	LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = cadastro_servicos.ser_cliente
						LEFT JOIN aux_tipo_servico ON aux_tipo_servico.tps_id = cadastro_servicos.ser_tipo_servico)
					 ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
					 LEFT JOIN financeiro_tramitacao h1 ON h1.ftr_fatura = financeiro_faturas.fat_id 
					 WHERE h1.ftr_id = (SELECT MAX(h2.ftr_id) FROM financeiro_tramitacao h2 where h2.ftr_fatura = h1.ftr_fatura) AND
						   fat_data_vencimento < :fat_data_vencimento AND (fat_data_pagamento IS NULL OR fat_data_pagamento = :fat_data_pagamento) AND ( ftr_status = :ftr_status1 OR ftr_status = :ftr_status4 ) ";
$stmt_vence_fatura = $PDO->prepare($sql_vence_fatura);
$stmt_vence_fatura->bindParam(":fat_data_vencimento",$hoje);
$stmt_vence_fatura->bindParam(":fat_data_pagamento",$null);
$ftr_status1 = "Em aberto";
$ftr_status4 = "Enviado";
$stmt_vence_fatura->bindParam(":ftr_status1",$ftr_status1);
$stmt_vence_fatura->bindParam(":ftr_status4",$ftr_status4);
$stmt_vence_fatura->execute();
$rows_vence_fatura = $stmt_vence_fatura->rowCount();
if($rows_vence_fatura > 0){
	while($result_vence_fatura = $stmt_vence_fatura->fetch())
	{
		$sql_vence = "INSERT INTO financeiro_tramitacao (ftr_fatura, ftr_status, ftr_usuario) VALUES (:ftr_fatura, :ftr_status, :ftr_usuario) ";
		$stmt_vence = $PDO->prepare($sql_vence);
		$stmt_vence->bindParam(":ftr_fatura",$result_vence_fatura['fat_id']);
		$ftr_status = "Vencido";
		$stmt_vence->bindValue(":ftr_status",$ftr_status);
		$stmt_vence->bindValue(":ftr_usuario",1);
		$stmt_vence->execute();


		// SELECIONA USUARIOS PARA ALERTAS
		$sql = "SELECT * FROM cadastro_usuarios
				LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
				WHERE set_nome = :set_nome1 OR set_nome = :set_nome2 ";
		$stmt_ale = $PDO->prepare($sql);	
		$set_nome1 = "Diretoria";
		$set_nome2 = "Administrador";			
		$stmt_ale->bindParam(':set_nome1',$set_nome1);          			      
		$stmt_ale->bindParam(':set_nome2',$set_nome2);          			      
		if($stmt_ale->execute())
		{
			while($result_ale = $stmt_ale->fetch())
			{
				$users[] = $result_ale['usu_id'];										
			}
			// WEB ALERTA
			$fat_id = $result_vence_fatura['fat_id'];
			$ale_descricao = "A fatura <span class='bold'>".$fat_id."</span> venceu :(";
			$ale_link = "financeiro_faturas/view/?fil_fat_id=".$fat_id."";		
			$destinatario = implode(",",$users);  
			alertaWeb($PDO, 1, $destinatario, $ale_descricao, $ale_link);
		}
		
		
		$faturas 	.= $result_vence_fatura['fat_id']."<br>";
		$clientes 	.= $result_vence_fatura['cli_nome']." (".$result_nova_fatura['cli_nome_fotografado'].") <br>";
		$servicos 	.= $result_vence_fatura['tps_nome']."<br>";
		$datas_venc	.= implode("/",array_reverse(explode("-",$result_vence_fatura['fat_data_vencimento'])))."<br>";
		$valores 	.= "R$ ".number_format($result_vence_fatura['fat_valor'],2,",",".")."<br>";
		
		
	}
	
	include('../core/mail/envia_faturas_vencidas.php');
}
### VENCE A FATURA - FIM ###

### CRIA NOVA FATURA COM BASE NO DIA DE VENCIMENTO - INI ###
	$dia_venc = date("d", strtotime("+ 30 days"));
	$dia_mes_venc = date("m", strtotime("+ 30 days"));
	$dia_ano_venc = date("Y", strtotime("+ 30 days"));

	/* CALCULA VIRADA ANO */
	$mes_venc = date("m", strtotime("+ 1 month"));
	if($mes_venc == 1)
	{
		$ano_venc = date("Y", strtotime("+ 1 year"));
	}
	else
	{
		$ano_venc = date("Y");
	}

	/* ANO-MES, PARA VERIFICAR SE JÁ FOI CRIADO UMA FATURA DO MÊS DE VENCIMENTO */
	$ano_mes_venc = date("Y-m", strtotime("+ 30 days"));


	/* SELECIONA TODOS SERVICOS COM DIA DE VENCIMENTO ENTRE OS PROXIMOS 30 DIAS */
	$sql_nova_fatura = "SELECT * FROM cadastro_servicos 
						LEFT JOIN aux_tipo_servico ON aux_tipo_servico.tps_id = cadastro_servicos.ser_tipo_servico
						LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = cadastro_servicos.ser_cliente
						LEFT JOIN financeiro_faturas ON financeiro_faturas.fat_servico = cadastro_servicos.ser_id
						WHERE ser_dia_vencimento <= :ser_dia_vencimento AND ser_iniciado = :ser_iniciado AND ( ser_data_fim IS NULL OR ser_data_fim = :ser_data_fim) AND ser_status = :ser_status
						GROUP BY ser_id";
	$stmt_nota_fatura = $PDO->prepare($sql_nova_fatura);
	$stmt_nota_fatura->bindParam(":ser_dia_vencimento",$dia_venc);	
	$stmt_nota_fatura->bindParam(":ser_data_fim",$null);
	$ser_iniciado = "Sim";
	$stmt_nota_fatura->bindParam(":ser_iniciado",$ser_iniciado);
	$ser_status = "Ativo";
	$stmt_nota_fatura->bindValue(":ser_status",$ser_status);	
	$stmt_nota_fatura->execute();
	$rows_nova_fatura = $stmt_nota_fatura->rowCount();
	if($rows_nova_fatura > 0)
	{
		$faturas=$clientes=$servicos=$datas_venc=$valores="";
		$m=0;
		while($result_nova_fatura = $stmt_nota_fatura->fetch())
		{
			$ser_id = $result_nova_fatura['ser_id'];
			/* SELECIONA TODAS AS FATURAS QUE FORAM GERADAS REFERENTE AO MÊS DE VENCIMENTO */
			
			if($result_nova_fatura['ser_periodicidade'] == "Mensal")
			{
				$sql_verifica_existente = "SELECT * FROM financeiro_faturas
										   LEFT JOIN cadastro_servicos ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
										   WHERE DATE_FORMAT(fat_data_vencimento,'%Y-%m') =  :fat_data_vencimento AND fat_servico = :fat_servico
										   ORDER BY fat_id DESC
 										   LIMIT :limit_inicial, :limit_final";
			}
			elseif($result_nova_fatura['ser_periodicidade'] == "Bimestral")
			{
				$sql_verifica_existente = "SELECT * FROM financeiro_faturas
										   LEFT JOIN cadastro_servicos ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
										   WHERE DATE_FORMAT(DATE_ADD(fat_data_vencimento, INTERVAL 2 MONTH),'%Y-%m') >  :fat_data_vencimento AND fat_servico = :fat_servico
										   ORDER BY fat_id DESC
 										   LIMIT :limit_inicial, :limit_final	
											";
			}
			elseif($result_nova_fatura['ser_periodicidade'] == "Trimestral")
			{
				$sql_verifica_existente = "SELECT * FROM financeiro_faturas
										   LEFT JOIN cadastro_servicos ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
										   WHERE DATE_FORMAT(DATE_ADD(fat_data_vencimento, INTERVAL 3 MONTH),'%Y-%m') >  :fat_data_vencimento AND fat_servico = :fat_servico
										   ORDER BY fat_id DESC
 										   LIMIT :limit_inicial, :limit_final	
											";
			}
			elseif($result_nova_fatura['ser_periodicidade'] == "Semestral")
			{
				$sql_verifica_existente = "SELECT * FROM financeiro_faturas
										   LEFT JOIN cadastro_servicos ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
										   WHERE DATE_FORMAT(DATE_ADD(fat_data_vencimento, INTERVAL 6 MONTH),'%Y-%m') >  :fat_data_vencimento AND fat_servico = :fat_servico 
										   ORDER BY fat_id DESC
 										   LIMIT :limit_inicial, :limit_final";
			}
			elseif($result_nova_fatura['ser_periodicidade'] == "Anual")
			{
				$sql_verifica_existente = "SELECT * FROM financeiro_faturas
										   LEFT JOIN cadastro_servicos ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
										   WHERE DATE_FORMAT(DATE_ADD(fat_data_vencimento, INTERVAL 12 MONTH),'%Y-%m') >  :fat_data_vencimento AND fat_servico = :fat_servico 
										   ORDER BY fat_id DESC
 										   LIMIT :limit_inicial, :limit_final";
			}
			
			$stmt_verifica_existente = $PDO->prepare($sql_verifica_existente);
			$stmt_verifica_existente->bindParam(":fat_data_vencimento",$ano_mes_venc);
			$stmt_verifica_existente->bindParam(":fat_servico",$ser_id);
			$stmt_verifica_existente->bindValue(":limit_inicial",0);
			$stmt_verifica_existente->bindValue(":limit_final",1);
			$stmt_verifica_existente->execute();
			$rows_verifica_existente = $stmt_verifica_existente->rowCount();
			
			if($rows_verifica_existente > 0)
			{
			
			}
			else
			{
				
				$m++;
				$data_vencimento = strtotime($dia_ano_venc."-".$dia_mes_venc."-".$result_nova_fatura['ser_dia_vencimento']);
				$proximoDiaUtil = diaUtil($data_vencimento);
				$fat_data_vencimento = date('Y-m-d',$proximoDiaUtil);
				/* INSERE A PROXIMA FATURA */
				$sql_fatura = "INSERT INTO financeiro_faturas (
							  fat_servico, 
							  fat_forma_pagamento, 
							  fat_valor, 
							  fat_data_vencimento ) 
							  VALUES (
							  :fat_servico, 
							  :fat_forma_pagamento, 
							  :fat_valor, 
							  :fat_data_vencimento
							  ) ";
				$stmt_fatura = $PDO->prepare($sql_fatura);
				$stmt_fatura->bindParam(":fat_servico",$ser_id);
				$stmt_fatura->bindParam(":fat_forma_pagamento",$result_nova_fatura['ser_forma_pagamento']);
				$stmt_fatura->bindParam(":fat_valor",$result_nova_fatura['ser_valor']);
				$stmt_fatura->bindParam(":fat_data_vencimento",$fat_data_vencimento);				
				if($stmt_fatura->execute())
				{
					$fat_id = $PDO->lastInsertId();
					/* INSERE O STATUS DA PROXIMA FATURA */
					$sql_financeiro_tramitacao = "INSERT INTO financeiro_tramitacao (ftr_fatura, ftr_status, ftr_usuario) VALUES (:ftr_fatura, :ftr_status, :ftr_usuario)";
					$stmt_financeiro_tramitacao = $PDO->prepare($sql_financeiro_tramitacao);
					$stmt_financeiro_tramitacao->bindParam(":ftr_fatura",$fat_id);
					$ftr_status = "Em aberto";
					$stmt_financeiro_tramitacao->bindValue(":ftr_status",$ftr_status);
					$stmt_financeiro_tramitacao->bindValue(":ftr_usuario",1);
					$stmt_financeiro_tramitacao->execute();

					$faturas 	.= $fat_id."<br>";
					$clientes 	.= $result_nova_fatura['cli_nome']." (".$result_nova_fatura['cli_nome_fotografado'].") <br>";
					$servicos 	.= $result_nova_fatura['tps_nome']."<br>";
					$datas_venc	.= implode("/",array_reverse(explode("-",$fat_data_vencimento)))."<br>";
					$valores 	.= "R$ ".number_format($result_nova_fatura['ser_valor'],2,",",".")."<br>";

					
				}				
			}			
		}
		if($m > 0)
		{
			// WEB ALERTA
			$ale_descricao = "Novas faturas foram criadas. <br><br>
			<table cellpadding=3>
			<tr>
				<td>ID</td>
				<td>Cliente</td>
				<td>Serviço</td>
				<td>Data Venc.</td>
				<td>Valor</td>
			</tr>
			<tr>
				<td>".$faturas."</td>
				<td>".$clientes."</td>
				<td>".$servicos."</td>
				<td>".$datas_venc."</td>
				<td>".$valores."</td>
			</tr>
			</table>
			";

			// SELECIONA USUARIOS PARA ALERTAS
			$sql = "SELECT * FROM cadastro_usuarios
					LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
					WHERE set_nome = :set_nome1 OR set_nome = :set_nome2 ";
			$stmt_ale = $PDO->prepare($sql);	
			$set_nome1 = "Diretoria";
			$set_nome2 = "Administrador";			
			$stmt_ale->bindParam(':set_nome1',$set_nome1);          			      
			$stmt_ale->bindParam(':set_nome2',$set_nome2);          			      
			if($stmt_ale->execute())
			{
				while($result_ale = $stmt_ale->fetch())
				{
					$users[] = $result_ale['usu_id'];										
				}
				// WEB ALERTA
				$ale_link = "social_alerta";			
				$destinatario = implode(",",$users);  
				alertaWeb($PDO, 1, $destinatario, $ale_descricao, $ale_link);
			}
			

			// EMAIL
			include('../core/mail/envia_faturas_criadas.php');
		}
	}

### CRIA NOVA FATURA COM BASE NO DIA DE VENCIMENTO - FIM ###

### VENCE O SERVICO QUE POSSUI DATA FINAL - INI ###
$sql_vence_servico = "SELECT * FROM cadastro_servicos 
					  LEFT JOIN aux_tipo_servico ON aux_tipo_servico.tps_id = cadastro_servicos.ser_tipo_servico
					  LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = cadastro_servicos.ser_cliente
					 WHERE ser_data_fim < :ser_data_fim AND ser_status = :ser_status ";
$stmt_vence_servico = $PDO->prepare($sql_vence_servico);
$stmt_vence_servico->bindParam(":ser_data_fim",$hoje);
$ser_status = "Ativo";
$stmt_vence_servico->bindValue(":ser_status",$ser_status);
$stmt_vence_servico->execute();
$rows_vence_servico = $stmt_vence_servico->rowCount();
if($rows_vence_servico > 0)
{
	$faturas=$clientes=$servicos=$datas_venc=$valores="";
	while($result_vence_servico = $stmt_vence_servico->fetch())
	{
		$sql_vence = "UPDATE cadastro_servicos SET 
					  ser_status = :ser_status
					  WHERE ser_id = :ser_id
						";
		$stmt_vence = $PDO->prepare($sql_vence);
		$stmt_vence->bindParam(":ser_id",$result_vence_servico['ser_id']);
		$ser_status = "Suspenso";
		$stmt_vence->bindValue(":ser_status",$ser_status);		
		$stmt_vence->execute();

		$ser_id 		.= $result_vence_servico['ser_id']."<br>";
		$clientes 		.= $result_vence_servico['cli_nome']." (".$result_vence_servico['cli_nome_fotografado'].") <br>";
		$servicos 		.= $result_vence_servico['tps_nome']."<br>";
		$data_inicio	.= implode("/",array_reverse(explode("-",$result_vence_servico['ser_data_inicio'])))."<br>";
		$data_fim		.= implode("/",array_reverse(explode("-",$result_vence_servico['ser_data_fim'])))."<br>";
		$valores 		.= "R$ ".number_format($result_vence_servico['ser_valor'],2,",",".")."<br>";
	}
	// WEB ALERTA
	$ale_descricao = "Novos serviços vencidos. <br><br>
	<table cellpadding=3>
	<tr>
		<td>Serviço</td>
		<td>Cliente</td>		
		<td>Data final</td>
		<td>Valor</td>
	</tr>
	<tr>
		<td>".$servicos."</td>
		<td>".$clientes."</td>		
		<td>".$data_fim."</td>
		<td>".$valores."</td>
	</tr>
	</table>
	";
	// SELECIONA USUARIOS PARA ALERTAS
	$sql = "SELECT * FROM cadastro_usuarios
			LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
			WHERE set_nome = :set_nome1 OR set_nome = :set_nome2 ";
	$stmt_ale = $PDO->prepare($sql);	
	$set_nome1 = "Diretoria";
	$set_nome2 = "Administrador";			
	$stmt_ale->bindParam(':set_nome1',$set_nome1);          			      
	$stmt_ale->bindParam(':set_nome2',$set_nome2);          			      
	if($stmt_ale->execute())
	{
		while($result_ale = $stmt_ale->fetch())
		{
			$users[] = $result_ale['usu_id'];										
		}
		// WEB ALERTA
		$ale_link = "social_alerta";	
		$destinatario = implode(",",$users);  
		alertaWeb($PDO, 1, $destinatario, $ale_descricao, $ale_link);
	}
	


	include('../core/mail/envia_servicos_vencidos.php');
}
		
### VENCE O SERVICO QUE POSSUI DATA FINAL - FIM ###



### CRIA NOVAS DESPESAS BASEADO EM CONTAS À PAGAR - INI ###
	$dia = date("d");
	$mes = date("m");
	$ano = date("Y");

	/* ANO-MES, PARA VERIFICAR SE JÁ FOI CRIADO UMA DESPESA NO MÊS ATUAL */
	$ano_mes_venc = date("Y-m");

	/* SELECIONA TODAS CONTAS À PAGAR ATIVAS */
	if($dia == 18)
	{

		$sql_nova_despesa = "SELECT * FROM financeiro_contas_pagar WHERE cap_status = :cap_status ";
		$stmt_nova_despesa = $PDO->prepare($sql_nova_despesa);
		$stmt_nova_despesa->bindValue(":cap_status",1);
		$stmt_nova_despesa->execute();
		$rows_nova_despesa = $stmt_nova_despesa->rowCount();

		if($rows_nova_despesa > 0)
		{
			while($result_nova_despesa = $stmt_nova_despesa->fetch())
			{
				$cap_id = $result_nova_despesa['cap_id'];
				/* SELECIONA TODAS AS DESPESAS QUE FORAM GERADAS REFERENTE AO MÊS ATUAL E A RESPECTIVA CONTA À PAGAR*/
				$sql_verifica_existente = "SELECT * FROM financeiro_despesas
										   WHERE DATE_FORMAT(des_data_vencimento,'%Y-%m') =  :des_data_vencimento AND des_cap = :des_cap 
										   ";			
				$stmt_verifica_existente = $PDO->prepare($sql_verifica_existente);
				$stmt_verifica_existente->bindParam(":des_data_vencimento",$ano_mes_venc);
				$stmt_verifica_existente->bindParam(":des_cap",$cap_id);
				$stmt_verifica_existente->execute();
				$rows_verifica_existente = $stmt_verifica_existente->rowCount();
				
				if($rows_verifica_existente > 0)
				{
					
				}
				else
				{
					$data_vencimento = $ano."-".$mes."-".$result_nova_despesa['cap_dia_vencimento'];
					/* INSERE A DESPESA */
					$sql_despesa = "INSERT INTO financeiro_despesas (
								  des_cap,
								  des_tipo_despesa,
								  des_descricao,
								  des_valor,
								  des_data_vencimento,								  
								  des_usuario
								  ) VALUES (
								  :des_cap, 
								  :des_tipo_despesa, 
								  :des_descricao, 
								  :des_valor, 
								  :des_data_vencimento,								  
								  :des_usuario
								  ) ";
								  
					$stmt_despesa = $PDO->prepare($sql_despesa);
					$stmt_despesa->bindParam(":des_cap",$cap_id);
					$stmt_despesa->bindParam(":des_tipo_despesa",$result_nova_despesa['cap_tipo_despesa']);
					$stmt_despesa->bindParam(":des_descricao",$result_nova_despesa['cap_descricao']);
					$stmt_despesa->bindParam(":des_valor",$result_nova_despesa['cap_valor']);
					$stmt_despesa->bindParam(":des_data_vencimento",$data_vencimento);					
					$stmt_despesa->bindValue(":des_usuario",1);
					if($stmt_despesa->execute())
					{
						$despesa 	.= $result_nova_despesa['cap_descricao']."<br>";												
						$datas_venc	.= implode("/",array_reverse(explode("-",$data_vencimento)))."<br>";
						$valores 	.= "R$ ".number_format($result_nova_despesa['cap_valor'],2,",",".")."<br>";
					}
				}
			}
			// WEB ALERTA			
			$ale_descricao = "Novas despesas foram criadas. <br><br>
			<table cellpadding=3>
			<tr>
				<td>Despesa</td>
				<td>Data Venc.</td>
				<td>Valor</td>
			</tr>
			<tr>
				<td>".$despesa."</td>
				<td>".$datas_venc."</td>
				<td>".$valores."</td>
			</tr>
			</table>
			";
			// SELECIONA USUARIOS PARA ALERTAS
			$sql = "SELECT * FROM cadastro_usuarios
					LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
					WHERE set_nome = :set_nome1 OR set_nome = :set_nome2 ";
			$stmt_ale = $PDO->prepare($sql);	
			$set_nome1 = "Diretoria";
			$set_nome2 = "Administrador";			
			$stmt_ale->bindParam(':set_nome1',$set_nome1);          			      
			$stmt_ale->bindParam(':set_nome2',$set_nome2);          			      
			if($stmt_ale->execute())
			{
				while($result_ale = $stmt_ale->fetch())
				{
					$users[] = $result_ale['usu_id'];										
				}
				// WEB ALERTA
				$ale_link = "social_alerta";				
				$destinatario = implode(",",$users);  
				alertaWeb($PDO, 1, $destinatario, $ale_descricao, $ale_link);
			}			
		}		
	}
	
### CRIA NOVAS DESPESAS BASEADO EM CONTAS À PAGAR - FIM ###


include('../core/mod_rodape/rodape.php');
?>
</body>
</html>