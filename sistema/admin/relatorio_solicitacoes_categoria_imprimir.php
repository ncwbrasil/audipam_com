<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
$fil_tecnico = $_REQUEST['fil_tecnico'];
$fil_contato = $_REQUEST['fil_contato'];

$fil_categoria = $_REQUEST['fil_categoria'];
if($fil_categoria == '')
{
	$categoria_query = " 1 = 1 ";
	$fil_categoria_n = "Categoria";
}
else
{
	$categoria_query = " (sol_categoria = :fil_categoria) ";
	switch($fil_categoria)
	{
		case 1 : $fil_categoria_n = "Prazos / Notificações / Calendário de Obrigações";break;
		case 2 : $fil_categoria_n = "Assessoramento / Participação em Licitação";break;
		case 3 : $fil_categoria_n = "Treinamento / Capacitação / Workshop";break;
		case 4 : $fil_categoria_n = "Reunião de Trabalho";break;
		case 5 : $fil_categoria_n = "Gestão de Contratos";break;
		case 6 : $fil_categoria_n = "Pareceres & Estudos Técnicos";break;
		case 7 : $fil_categoria_n = "Atendimento Técnico Presencial";break;
		case 8 : $fil_categoria_n = "Suporte Técnico à Distância";break;
		case 9 : $fil_categoria_n = "Digitalização e Gestão Eletrônica de Documentos";break;
		case 10 : $fil_categoria_n = "Atualização de Base de Dados / Manutenção de Software";break;
		case 11 : $fil_categoria_n = "Prospecção";break;
	}
}
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
	$emp_logo = $result_fat['emp_logo'];
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
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				WHERE con_id = :fil_contrato ";
	$stmt_con = $PDO->prepare($sql_con);
	$stmt_con->bindParam(":fil_contrato",$fil_contrato);
	$stmt_con->execute();
	$result_con = $stmt_con->fetch();
	$fil_contrato_n = $result_con['con_numero_processo']."/".$result_con['con_ano_processo']." (".$result_con['mod_descricao'].") ";
}

$fil_data_inicio = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio'])));
$fil_data_fim = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim'])));
if($fil_data_inicio == '' && $fil_data_fim == '')
{
	$data_query = " 1 = 1 ";
}
elseif($fil_data_inicio != '' && $fil_data_fim == '')
{
	$data_query = " sts_data >= '$fil_data_inicio' ";
}
elseif($fil_data_inicio == '' && $fil_data_fim != '')
{
	$data_query = " sts_data <= '$fil_data_fim 23:59:59' ";
}
elseif($fil_data_inicio != '' && $fil_data_fim != '')
{
	$data_query = " sts_data BETWEEN '$fil_data_inicio' AND '$fil_data_fim 23:59:59' ";
}

if($fil_contato == 'Contato do cliente'){$fil_contato = '';}
if($fil_tecnico != 'Técnico' || $fil_contato != '')
{
	$rodape = '<div class="rodape" style="width:100%;">'.date("d/m/Y").'</div><div class="rodape" style="width:45%;float:left;">'.$fil_tecnico.'<br>'.$fil_empresa_n.'</div><div  class="rodape" style="width:45%;float:right;">'.$fil_contato.'<br>'.$fil_cliente_n.'</div>';
}
 $sql = "SELECT * FROM agenda_gerenciar_itens 
            LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
            LEFT JOIN (cliente_solicitacoes_agenda 
                LEFT JOIN ( cliente_solicitacoes 
                    INNER JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
                    LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
                    LEFT JOIN (cadastro_contratos 
                        LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade )
                    ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato )
                ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
            ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
            LEFT JOIN ( agenda_gerenciar_usuario 
                LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario )
            ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
            WHERE ".$empresa_query." AND ".$cliente_query." AND ".$categoria_query."  AND ".$contrato_query." AND con_id IS NOT NULL
            GROUP BY emp_id, con_id  
			
			";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':fil_categoria', 		$fil_categoria);
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
.rodape 		{ font-family:"Calibri"; color:#777; font-size:11px; text-align:center;}
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
							  	<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span><br>
								<b>Objeto do Contrato:</b> ".$result['con_objeto']."<br>
								<b>Contrato:</b> ".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")"."<br>
								<b>Período:</b> ".implode("/",array_reverse(explode("-",$fil_data_inicio)))." a ".implode("/",array_reverse(explode("-",$fil_data_fim)))."<p>
								<p align='center'><b>Histórico do Pedido</b></p>
								<p>
								";
								$sql = "SELECT * FROM cliente_solicitacoes 
                                        INNER JOIN (cliente_solicitacoes_agenda 
                                            INNER JOIN agenda_gerenciar_itens ON agenda_gerenciar_itens.agi_id = cliente_solicitacoes_agenda.soa_agenda_item)
                                        ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
                                        INNER JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
                                        INNER JOIN (cadastro_contratos 
                                            LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade )
                                        ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
                                        LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
                                        WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
                                            ".$empresa_query." AND ".$cliente_query." AND ".$categoria_query." AND".$data_query." AND con_id = :con_id
                                        GROUP BY sol_categoria		
										";
								$stmt_cat = $PDO->prepare($sql);
								$stmt_cat->bindParam(':fil_categoria', 		$fil_categoria);
								$stmt_cat->bindParam(':agi_id', 		$result['agi_id']);
								$stmt_cat->bindParam(':con_id',$result['con_id']);
								$stmt_cat->execute();
								$rows_cat = $stmt_cat->rowCount();
								if($rows_cat > 0)
								{
									while($result_cat = $stmt_cat->fetch())
									{	
										echo "<div style='width:100%; display:table; margin-bottom:10px; text-align:left; background-image: linear-gradient(to right, #024893, white)'> <img src='".$result_cat['cas_icone']."' width='30' valign='middle'> ".$result_cat['cas_descricao']."</div>";
										
										$sql = "SELECT *,Year(sol_data_cadastro) as ano  FROM agenda_gerenciar_itens 
												LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
												LEFT JOIN (cliente_solicitacoes_agenda 
													INNER JOIN ( cliente_solicitacoes 
														INNER JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
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
													".$empresa_query." AND ".$cliente_query." AND ".$contrato_query." AND ".$data_query."  AND sol_categoria = :sol_categoria  AND con_id = :con_id
												GROUP BY sol_id 
												";
										$stmt_sol = $PDO->prepare($sql);
										$stmt_sol->bindParam(':sol_categoria',$result_cat['sol_categoria']);
										$stmt_sol->bindParam(':con_id',$result['con_id']);
										$stmt_sol->execute();
										$rows_sol = $stmt_sol->rowCount();
										if($rows_sol > 0)
										{
											
											while($result_sol = $stmt_sol->fetch())
											{
												$carga_horaria=$inicial=$final=$separador=$hora=$diferenca="";
												$hora = $result_sol['agi_horario'];
												$separador = explode(" às ",$hora);
												
												if($separador[0] != "" && $separador[1] != "")
												{
													$inicial = strtotime($separador[0]);
													$final = strtotime($separador[1]);
													$diferenca = $final - $inicial;
													$diferenca = strtotime(date("Y-m-d"))+$diferenca;
													$carga_horaria = date("H\hi",$diferenca);
												}
												else
												{
													$separador = explode(" as ",$hora);
													if($separador[0] != "" && $separador[1] != "")
													{
														$inicial = strtotime($separador[0]);
														$final = strtotime($separador[1]);
														$diferenca = $final - $inicial;
														$diferenca = strtotime(date("Y-m-d"))+$diferenca;
														$carga_horaria = date("H\hi",$diferenca);
													}
													else
													{
														$carga_horaria = "Erro ao calcular carga horária";
													}
													
												}
												
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
												<table width='100%'>
													<tr>
													<td>
													<b>Solicitação:</b> ".$result_sol['ano'].".".$result_sol['sol_id']." - 
													<b>Data do Registro da Solicitação:</b> ".implode("/",array_reverse(explode("-",substr($result_sol['sol_data_cadastro'],0,10))))."
													</td>
													<td>
													<b>Última atualização:</b> ".implode("/",array_reverse(explode("-",substr($result_sol['sts_data'],0,10))))." às  ".implode("/",array_reverse(explode("-",substr($result_sol['sts_data'],11,5))))." - <u>".$sts_status."</u>
													</td>
													</tr>
													<tr><td> </td><td> </td></tr>
													<tr>
														<td>
												
													<b>Técnicos:</b> ".$usuarios."<br>
													<b>Forma de Atendimento:</b> ".$result_sol['fat_descricao']." <br>
													<b>Carga horária:</b> ".$carga_horaria." <br>
													<b>Breve Histórico:</b> ".$result_sol['sol_breve_historico']."<br>
													</td>
													<td>
													
													";
													if($result_sol['sts_fb_tecnico'] != "")
													{
														echo "
														<b>Homologação</b> <br>
														<b>Técnico:</b> ".$result_sol['sts_fb_tecnico']." <br>
														<b>Servidor:</b> ".$result_sol['sts_fb_servidor']." <br>
														<b>Data:</b> 	".implode("/",array_reverse(explode("-",$result_sol['sts_fb_data'])))." às ".substr($result_sol['sts_fb_hora'],0,5)."<br>
														<b>Forma Atendimento:</b> ".$result_sol['sts_fb_forma_atendimento']."														
														";
													}
													else
													{
														echo "&nbsp;";
													}
													echo "
													</td>
													</tr>
												</table>
												
												<b>Síntese do Atendimento Realizado:</b>
												<p style='text-align:justify; font-size:10px;'>".nl2br($result_sol['sts_observacao'])."</p>
												
												<div style='width:100%; text-align:center;'>
												
												";
												if($result_sol['sts_foto'] != "")
												{
													echo "<div style='width:50%; display:inline-block;'><b>Figura 1</b><br><img src='".$result_sol['sts_foto']."' style='width:90%'></div>";
													//echo "<div style='width:50%; display:inline-block;'><b>Figura 1</b><br><img src='".str_replace("../","https://audipam.adm.br/sistema/",$result_sol['sts_foto'])."' style='width:90%'></div>";
												}if($result_sol['sts_foto2'] != "")
												{
													echo "<div style='width:50%; display:inline-block;'><b>Figura 2</b><br><img src='".$result_sol['sts_foto2']."' style='width:90%;'></div>";
													//echo "<div style='width:50%; display:inline-block;'><b>Figura 2</b><br><img src='".str_replace("../","http://audipam.adm.br/sistema/",$result_sol['sts_foto2'])."' style='width:90%;'></div>";
												}
												echo "
												</div>
												<hr>
												";
												
											}
										}
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
// $mpdf->SetTitle($emp_fantasia_n);
// $mpdf->useOddEven = false;
// $mpdf->SetHTMLHeader('<div class="topo"><img src='.$emp_logo.' width="300"><br><br></div>'); 
// $mpdf->SetHTMLFooter($rodape);

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
// $mpdf->WriteHTML();
// //$mpdf->AddPage();
// // $mpdf->simpleTables = true;
// // $mpdf->packTableData = true;
// $mpdf->Output('Relatorio_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');

/* Carrega a classe DOMPdf */
require_once("../mod_includes/js/dompdf/dompdf_config.inc.php");

/* Cria a instância */
$dompdf = new DOMPDF();

/* Carrega seu HTML */
$dompdf->load_html(utf8_decode("$html"));

/* Renderiza */
$dompdf->render();

/* Exibe */
$dompdf->stream(
    "Relatorio_".str_pad($orc_id,6,'0',STR_PAD_LEFT).".pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => true /* Para download, altere para true */
    )
);

exit();
?>
