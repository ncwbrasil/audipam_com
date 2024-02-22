<?php
session_start (); 
$pagina_link = 'relatorios_solicitacoes_categoria';
if(isset($_GET['sol_id'])){$sol_id = $_GET['sol_id'];}
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<!-- ABAS -->
<link rel="stylesheet" href="../mod_includes/js/abas/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../mod_includes/js/abas/bootstrap.js"></script>
<!-- ABAS -->
<script src="../mod_includes/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/eventCalendar.css">
<link rel="stylesheet" href="../css/eventCalendar_theme_responsive.css">


</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
require_once('../mod_includes/php/verificapermissao.php');
?>
<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
    <?php include("../mod_menu/barra.php");?>
</div>
<div class='centro'>
    <div class='box'>
	<?php
    $page = "Relatórios &raquo; <a href='relatorios_solicitacoes_categoria.php?pagina=relatorios_solicitacoes_categoria".$autenticacao."'>Solicitações por Categoria</a> ";
	$agi_id = $_GET['agi_id'];
	
	$filtro = $_REQUEST['filtro'];
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
				}
				$fil_tecnico = $_REQUEST['fil_tecnico'];if($fil_tecnico == ''){$fil_tecnico = "Técnico";}
				$fil_contato = $_REQUEST['fil_contato'];if($fil_contato == ''){$fil_contato = "Contato do cliente";}
				
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
								INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_contratos.con_contratado
								LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
								WHERE con_id = :fil_contrato ";
					$stmt_con = $PDO->prepare($sql_con);
					$stmt_con->bindParam(":fil_contrato",$fil_contrato);
					$stmt_con->execute();
					$result_con = $stmt_con->fetch();
					$fil_contrato_n = $result_con['emp_fantasia']." - ".$result_con['con_numero_processo']."/".$result_con['con_ano_processo']." (".$result_con['mod_descricao'].") - ".$result_con['con_status']." - ".implode("/",array_reverse(explode("-",$result_con['con_final_vig'])))."";
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
				$filtro = $_REQUEST['filtro'];
				if($filtro == '')
				{
					$filtro_query = " 1 = 0 ";
				}
				else
				{
					$filtro_query = " 1 = 1 ";
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
            WHERE ".$empresa_query." AND ".$cliente_query." AND ".$categoria_query."  AND ".$contrato_query." AND ".$filtro_query." AND con_id IS NOT NULL
            GROUP BY emp_id, con_id  
			";		
	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':fil_categoria', 		$fil_categoria);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "relatorios_solicitacoes_categoria")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div style='width:100%; display:table;'>
		<div class='filtro'>
			
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='relatorios_solicitacoes_categoria.php?pagina=relatorios_solicitacoes_categoria".$autenticacao."&filtro=1'>
			<!--<input name='fil_tecnico' id='fil_tecnico' value='$fil_tecnico' placeholder='Técnico'>-->
			<select name='fil_categoria' id='fil_categoria'>
				<option value='$fil_categoria'>$fil_categoria_n</option>
				"; 
				$sql = " SELECT * FROM aux_categoria_solicitacao ORDER BY cas_id";
				$stmt_cat = $PDO->prepare($sql);
				$stmt_cat->execute();
				while($result_cat = $stmt_cat->fetch())
				{
					echo "<option value='".$result_cat['cas_id']."'>".$result_cat['cas_descricao']."</option>";
				}
				echo "			
				<option value='' >Todos</option>										
			</select>
			<select name='fil_empresa' id='fil_empresa' >
				<option value='$fil_empresa' >$fil_empresa_n</option>
				"; 
				$sql = "SELECT * FROM agenda_gerenciar
						INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id =  agenda_gerenciar.age_empresa
						ORDER BY emp_fantasia";
				$stmt_cat = $PDO->prepare($sql);
				$stmt_cat->execute();
				while($result_cat = $stmt_cat->fetch())
				{
					echo "<option value='".$result_cat['age_id']."'>".$result_cat['emp_fantasia']."</option>";
				}
				echo "
			</select>
			<select name='fil_cliente' id='fil_cliente' >
				<option value='$fil_cliente'>$fil_cliente_n</option>
				"; 
				$sql = "SELECT * FROM cadastro_empresas
						WHERE emp_cliente = :emp_cliente 
						ORDER BY emp_nome_razao ASC";
				$stmt_fat = $PDO->prepare($sql);
				$stmt_fat->bindValue(":emp_cliente", 1);
				$stmt_fat->execute();
				while($result_fat = $stmt_fat->fetch())
				{
					echo "<option value='".$result_fat['emp_id']."'>".$result_fat['emp_nome_razao']."</option>";
				}
				echo "
			</select>
			<select name='fil_contrato' id='fil_contrato' >
				<option value='$fil_contrato'>$fil_contrato_n</option>			
			</select>
			
			<input type='text' name='fil_data_inicio' id='fil_data_inicio' placeholder='Data Último Status Início' value='".implode('/',array_reverse(explode('-',$fil_data_inicio)))."' onkeypress='return mascaraData(this,event);'>
			<input type='text' name='fil_data_fim' id='fil_data_fim' placeholder='Data Último Status Fim' value='".implode('/',array_reverse(explode('-',$fil_data_fim)))."' onkeypress='return mascaraData(this,event);'>
			<br>
			Dados para rodapé: <br><select name='fil_tecnico' id='fil_tecnico' >
				<option value='$fil_tecnico'>$fil_tecnico</option>
				"; 
				$sql = "SELECT * FROM admin_usuarios
						ORDER BY usu_nome ASC";
				$stmt_fat = $PDO->prepare($sql);
				$stmt_fat->execute();
				while($result_fat = $stmt_fat->fetch())
				{
					echo "<option value='".$result_fat['usu_nome']."'>".$result_fat['usu_nome']."</option>";
				}
				echo "
			</select>
			<select name='fil_contato' id='fil_contato'>
				<option value='$fil_contato'>$fil_contato</option>			
			</select>
			
			<input type='button' value='Filtrar' id='bt_filtro_relatorio_sol'> 			
			</form>
			<div id='erro'> </div>
			
			";
			if($filtro == 1)
			{
				echo "<img class='hand' title='Imprimir Relatório' style='float:right; margin:0 5px;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('relatorio_solicitacoes_categoria_imprimir.php?fil_cliente=$fil_cliente&fil_categoria=$fil_categoria&fil_empresa=$fil_empresa&fil_contrato=$fil_contrato&fil_tecnico=".str_replace(" ","%20",$fil_tecnico)."&fil_contato=".str_replace(" ","%20",$fil_contato)."&fil_data_inicio=$fil_data_inicio&fil_data_fim=$fil_data_fim$autenticacao');>";
			}
			echo "
		</div>
		</div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'></td>
				</tr>";
				$c=0;
				while($result = $stmt->fetch())
				{
					$con_id = $result['con_id'];
					// TECNICOS
					/*$sql = "SELECT usu_nome FROM agenda_gerenciar_usuario
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
					}	*/
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "<tr class='$c1'>
							  <td>
							  	<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span><br>
								<b>Objeto do Contrato:</b> ".$result['con_objeto']."<br>
								<b>Contrato:</b> ".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")"."<br>
								<b>Período:</b> ".implode("/",array_reverse(explode("-",$fil_data_inicio)))." a ".implode("/",array_reverse(explode("-",$fil_data_fim)))."<p>
								<center><b>Histórico do Pedido</b></center>
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
                                            ".$empresa_query." AND ".$cliente_query." AND ".$categoria_query." AND".$data_query." AND ".$filtro_query." AND con_id = :con_id
                                        GROUP BY sol_categoria										
										";							
								$stmt_cat = $PDO->prepare($sql);								
								$stmt_cat->bindParam(':fil_categoria', 		$fil_categoria);
								$stmt_cat->bindParam(':con_id',$con_id);
								$stmt_cat->execute();
								$rows_cat = $stmt_cat->rowCount();
								if($rows_cat > 0)
								{
									while($result_cat = $stmt_cat->fetch())
									{
										echo "<div style='width:100%; display:table; margin-bottom:10px; text-align:left; background-image: linear-gradient(to left, #024893, white)'> <img src='".$result_cat['cas_icone']."' width='30' valign='middle'> ".$result_cat['cas_descricao']."</div>";
										
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
													".$empresa_query." AND ".$cliente_query." AND ".$contrato_query." AND ".$data_query." AND ".$filtro_query." AND sol_categoria = :sol_categoria AND con_id = :con_id
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
												<div style='width:50%; float:left;'>
													<b>Solicitação:</b> ".$result_sol['ano'].".".$result_sol['sol_id']." - 
													<b>Data do Registro da Solicitação:</b> ".implode("/",array_reverse(explode("-",substr($result_sol['sol_data_cadastro'],0,10))))."
												</div>
												<div style='width:50%; float:right; text-align:right;'>
													<b>Última atualização:</b> ".implode("/",array_reverse(explode("-",substr($result_sol['sts_data'],0,10))))." às  ".implode("/",array_reverse(explode("-",substr($result_sol['sts_data'],11,5))))." - <u>".$sts_status."</u>
												</div>
												<br>
												<div style='width:62%; float:left;'>
													<br><b>Técnicos:</b> ".$usuarios."<br>
													<b>Forma de Atendimento:</b> ".$result_sol['fat_descricao']." <br>
													<b>Carga horária:</b> ".$carga_horaria." <br>
													<b>Breve Histórico:</b> ".$result_sol['sol_breve_historico']."<br>
												</div>
												<div style='width:38%; float:right; text-align:left;'>
													
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
												</div>
												<div style='width:100%; display:table;'>
												<b>Síntese do Atendimento Realizado:</b><br>
												".nl2br($result_sol['sts_observacao'])."
												</div>
												<div style='width:100%; display:table;'>												
												";
												if($result_sol['sts_foto'] != "")
												{
													echo "
													<div style='width:47%; float:left;'><b>Figura 1</b><br>
														<img src='".str_replace("../","https://audipam.adm.br/sistema/",$result_sol['sts_foto'])."' style='width:100%'>
													</div>
													";
												}if($result_sol['sts_foto2'] != "")
												{
													echo "
													<div style='width:47%; float:right;'><b>Figura 2</b><br>
														<img src='".str_replace("../","http://audipam.adm.br/sistema/",$result_sol['sts_foto2'])."' style='width:100%;'>
													</div>
													";
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
								
								
							  </td>
						  </tr>";
				}
				echo "</table>";
				
		}
		elseif($filtro == 1 && $rows == 0)
		{
			echo "<br><br><br><br><br>Não foi encontrado nenhum item com os dados pesquisados.";
		}
		else
		{
			echo "<br><br><br><br><br>";
		}
    }  
	?>
    </div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
<?php
include("../mod_includes/js/jquery.eventCalendar.php");
?>
