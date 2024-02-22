<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
$sol_id = $_GET['sol_id'];
$sql = "SELECT *, Year(sol_data_cadastro) as ano,s1.ser_descricao as s1,s2.ser_descricao as s2 FROM cliente_solicitacoes 
		LEFT JOIN ( cadastro_contratos 
			LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
			LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
			LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
			LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
		ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
		LEFT JOIN aux_servicos as s2 ON s2.ser_id = cliente_solicitacoes.sol_servico
		LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
		LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
		LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
		LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
		WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
			  sol_id = :sol_id
		GROUP BY sol_id";
$stmt = $PDO->prepare($sql);	
$stmt->bindParam(':sol_id', $sol_id);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{
	$result = $stmt->fetch();
	$sol_id 			= $result['sol_id'];
	$ano 				= $result['ano'];
	$con_numero_processo= $result['con_numero_processo'];if($con_numero_processo != ''){$con_numero_processo .= "/";}
	$con_ano_processo 	= $result['con_ano_processo'];
	$mod_descricao 		= $result['mod_descricao'];if($mod_descricao != ''){$mod_descricao = "(".$mod_descricao.")";}
	$ite_descricao		= $result['ite_descricao'];
	$ite_tipo 			= $result['ite_tipo'];if($ite_tipo != ''){$ite_tipo = "(".$ite_tipo.")";}
	$sol_tipo 			= $result['sol_tipo'];
	$ser_descricao		= $result['s1'].$result['s2'];
	$emp_tipo 			= $result['emp_tipo'];
	$emp_nome_razao 	= $result['emp_nome_razao'];
	$emp_fantasia 		= $result['emp_fantasia'];
	$solicitante		= $result['ctt_nome'].$result['usu_nome'];
	$sol_tipo 			= $result['sol_tipo'];
	$sol_data = implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
	$sol_hora = substr($result['sol_data_cadastro'],11,5);
	$sol_breve_historico 	= nl2br($result['sol_breve_historico']);
	$sol_memorial 			= nl2br($result['sol_memorial']);
	$sol_anexo	 			= $result['sol_anexo'];
	$sts_status 			= $result['sts_status'];
	
	$ctt_email	 			= $result['ctt_email'];
	$ctt_nome	 			= $result['ctt_nome'];
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
		<div class='titulo'>Solicitação</span> </div>
		<div class='label'>Protocolo:</div> 			<div class='info'>$ano.$sol_id</div>
		<div class='label'>Tipo de Solicitação:</div> 	<div class='info'>$sol_tipo</div>
		<div class='label'>Cliente/Empresa:</div>       <div class='info'>
														";
															if($emp_tipo == 'PJ')
														  {
															  echo "$emp_fantasia (<span class='detalhe'>$emp_nome_razao</span>)";
														  }
														  else
														  {
															  if($emp_fantasia != '')
															  {
																echo "$emp_fantasia (<span class='detalhe'>$emp_nome_razao</span>)";
															  }
															  else
															  {
																echo "$emp_nome_razao";
															  }
														  } 
														echo " </div>
		<div class='label'>Solicitante:</div>  			<div class='info'>$solicitante</div>
		<div class='label'>Data da Solicitação:</div>  	<div class='info'>$sol_data às $sol_hora</div>
		<div class='label'>Status Atual:</div>  		<div class='info'>$sts_status</div>
		<div class='label'>Contrato:</div>  			<div class='info'>$con_numero_processo$con_ano_processo $mod_descricao &nbsp;</div>
		<div class='label'>Item do Contrato:</div>  	<div class='info'>$ite_descricao $ite_tipo &nbsp;</div>
		<div class='label'>Serviço:</div>  				<div class='info'>$ser_descricao &nbsp;</div>
		<div class='label'>Breve Histórico:</div>  		<div class='info'>$sol_breve_historico</div>
		<div class='label'>Memorial:</div>  			<div class='info'>$sol_memorial</div>
		
		<div class='titulo'>Histórico</span> </div>
		<div>
			<table class='bordatabela' cellpadding='10' cellspacing='0' width='100%'>
							 	<tr>
									<td class='titulo_first'>Data</td>
									<td class='titulo_tabela'>Status</td>
									<td class='titulo_tabela'>Descrição</td>
									<td class='titulo_last'>Usuário</td>
								</tr>
								";
								$sql = "SELECT * FROM cliente_solicitacoes 
										LEFT JOIN ( cadastro_contratos 
											LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
											LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id 
											LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
											LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico)
										ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
										LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
										LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
										LEFT JOIN (cliente_status_solicitacoes 
											LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_status_solicitacoes.sts_usuario)
										ON cliente_status_solicitacoes.sts_solicitacao = cliente_solicitacoes.sol_id 
										WHERE sol_id = :sol_id
										GROUP BY sts_id
										ORDER BY sts_data ASC";
								$stmt = $PDO->prepare($sql);	
								$stmt->bindParam(':sol_id', $sol_id);
								$stmt->execute();
								$rows = $stmt->rowCount();
								if($rows > 0)
								{
									while($result = $stmt->fetch())
									{
										if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
										$sts_observacao = nl2br($result['sts_observacao']);
										$sts_data		= implode("/",array_reverse(explode("-",substr($result['sts_data'],0,10))));
										$sts_hora		= substr($result['sts_data'],11,5);
										$sts_id	 		= $result['sts_id'];
										$sts_status 	= $result['sts_status'];
										$sts_usuario 	= $result['sts_usuario'];
										$sts_anexo		= $result['sts_anexo'];
										$usu_nome 		= $result['usu_nome'];
										$ctt_nome 		= $result['ctt_nome'];
										if($sts_usuario == '')
										{
											$usuario = $ctt_nome;
										}
										else
										{
											$usuario = $usu_nome;
										}
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
										echo "
										<tr class='$c1'>
											<td>".$sts_data."<br><span class='detalhe'>às ".$sts_hora."</span></td>
											<td>".$sts_status."</td>
											<td>".$sts_observacao."</td>
											<td>".$usuario."</td>											
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

define('MPDF_PATH', '../mod_includes/js/mpdf/');
include(MPDF_PATH.'mpdf.php');
$mpdf = new mPDF(
 '',    // mode - default ''
 'A4',    // format - A4, for example, default ''
 0,     // font size - default 0
 '',    // default font family
 10,    // margin_left
 10,    // margin right
 30,     // margin top
 23,    // margin bottom
 5,     // margin header
 5,     // margin footer
 'P');  // L - landscape, P - portrait);
$mpdf->SetTitle('Audipam | Imprimir Solicitação');
$mpdf->useOddEven = false;
$mpdf->SetHTMLHeader('<div class="topo"><img src=../imagens/logo_branco.png width="300"><br><br></div>'); 
$mpdf->SetHTMLFooter('');

$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='UTF-8';
$mpdf->WriteHTML(utf8_decode("$html"));
//$mpdf->AddPage();

$mpdf->Output('Orçamento_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');
exit();
?>