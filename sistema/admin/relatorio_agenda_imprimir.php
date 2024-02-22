<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
$fil_empresa = $_REQUEST['fil_empresa'];
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
$fil_tecnico = $_REQUEST['fil_tecnico'];
if($fil_tecnico == '')
{
	$tecnico_query = " 1 = 1 ";
	$fil_tecnico_n = "Técnico";
}
else
{
	$tecnico_query = " usu_nome LIKE '%".$fil_tecnico."%' ";
}
$fil_categoria = $_REQUEST['fil_categoria'];
if($fil_categoria == '')
{
	$categoria_query = " 1 = 1 ";
	$fil_categoria_n = "Categoria";
}
else
{
	$categoria_query = " sol_categoria = '".$fil_categoria."' ";
	$sql_cat = "SELECT * FROM aux_categoria_solicitacao WHERE cas_id = :fil_categoria ";
	$stmt_cat = $PDO->prepare($sql_cat);
	$stmt_cat->bindParam(":fil_categoria",$fil_categoria);
	$stmt_cat->execute();
	$result_cat = $stmt_cat->fetch();
	$fil_categoria_n = $result_cat['cas_descricao'];
}
$fil_forma_atendimento = $_REQUEST['fil_forma_atendimento'];
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
}
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
if($fil_data_inicio != '' && $fil_data_fim != '')
{
	$periodo = "Período: ".$_REQUEST['fil_data_inicio']." a ".$_REQUEST['fil_data_fim']."";
}
$sql = "SELECT * FROM agenda_gerenciar_itens 
			LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
			LEFT JOIN (cliente_solicitacoes_agenda 
				LEFT JOIN ( cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente )
				ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
			ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
			LEFT JOIN ( agenda_gerenciar_usuario 
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario )
			ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
			WHERE ".$empresa_query." AND ".$cliente_query." AND ".$tecnico_query." AND ".$forma_atendimento_query." AND ".$categoria_query." AND ".$data_query." 
			GROUP BY agi_id ";
$stmt = $PDO->prepare($sql);	
$stmt->bindParam(':sol_id', $sol_id);
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
.right {float:right;}
.left {float:left;}

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
		<div class='titulo'>Relatório de Agenda</span> </div>
		<div>
			<table class='bordatabela' cellpadding='10' cellspacing='0' width='100%'>
							 	<tr>
									<td class='titulo_first'>$periodo</td>
								</tr>
								";
								if($rows > 0)
								{
									while($result = $stmt->fetch())
									{
										// SOLICITACOES
										$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes
												LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
												LEFT JOIN cliente_solicitacoes_agenda ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
												LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
												WHERE     h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
														  soa_agenda_item = :agi_id";
										$stmt_solic = $PDO->prepare($sql);
										$stmt_solic->bindParam(':agi_id',$result['agi_id']);
										$stmt_solic->execute();
										$rows_solic = $stmt_solic->rowCount();
										$solic="";
										$sts_status="";
										if($rows_solic > 0)
										{
											$ultimo_solic = $stmt_solic->fetchAll(PDO::FETCH_COLUMN);
											$ultimo_solic = end($ultimo_solic);
											$stmt_solic->execute();
											while($result_solic = $stmt_solic->fetch())
											{
												$categoria = "<img src='".$result_solic['cas_icone']."' width='30' valign='middle'> ".$result_solic['cas_descricao']."";
												$atual = $result_solic['sol_id'];
												$atual2 = "".$result_solic['ano'].".".$result_solic['sol_id']."";
												$solic .= $atual2;
												if($ultimo_solic !== $atual)
												{
													$solic .= ", ";
												}	
												$sts_status			= $result_solic['sts_status'];
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
											}					
										}	
										
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
												if($ultimo_carro !== $atual)
												{
													$carros .= ", ";
												}	
											}					
										}	
										if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
										echo "
										<tr class='$c1'>
											<td>
											<table width='1000'>
											<tr>
											<td>
												<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span>
											</td>
											<td align='right'>
												$categoria
											</td>
											</tr>
											</table>
											
								<b>Data:</b> ".implode("/",array_reverse(explode("-",$result['agi_data'])))." - ".$result['agi_horario']." <br>
								<b>Solicitação:</b> $solic <br>
												<b>Técnico(s): $usuarios</b> <br>
												<b>Veículo(s): $carros</b> <br>
												<b>Forma de Atendimento:</b> ".$result['fat_descricao']." <br>
												<b>Status:</b> ".$sts_status." <br><br>
												".nl2br($result['agi_descricao'])."
											</td>											
										</tr>
										";
									}
								}
								echo "
							</table>
		</div>
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
// $mpdf->SetTitle('Audipam | Relatório de Atividades');
// $mpdf->useOddEven = false;
// $mpdf->SetHTMLHeader('<div class="topo"><img src=../imagens/logo_branco.png width="300"><br><br></div>'); 
// $mpdf->SetHTMLFooter('');

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
// $mpdf->WriteHTML(utf8_decode("$html"));
// //$mpdf->AddPage();

// $mpdf->Output('Orçamento_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');
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