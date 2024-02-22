<?php
session_start (); 
$pagina_link = 'relatorios_agenda';
if(isset($_GET['age_id'])){$sol_id = $_GET['age_id'];}
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
    $page = "Relatórios &raquo; <a href='relatorios_agenda.php?pagina=relatorios_agenda".$autenticacao."'>Agenda</a> ";
	$agi_id = $_GET['agi_id'];
	
	$filtro = $_REQUEST['filtro'];
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
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente )
				ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
			ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
			LEFT JOIN ( agenda_gerenciar_usuario 
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario )
			ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
			WHERE ".$empresa_query." AND ".$cliente_query." AND ".$tecnico_query." AND ".$forma_atendimento_query." AND ".$data_query." AND ".$categoria_query." AND ".$filtro_query." 
			GROUP BY agi_data  
			";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "relatorios_agenda")
    {
        echo "
			<div class='titulo'> $page  </div>			
            <div class='filtro'>
                <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='relatorios_agenda.php?pagina=relatorios_agenda".$autenticacao."&filtro=1'>
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
				<input name='fil_tecnico' id='fil_tecnico' value='$fil_tecnico' placeholder='Técnico'>
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
				<input type='text' name='fil_data_inicio' id='fil_data_inicio' placeholder='Data Início' value='".implode('/',array_reverse(explode('-',$fil_data_inicio)))."' onkeypress='return mascaraData(this,event);'>
				<input type='text' name='fil_data_fim' id='fil_data_fim' placeholder='Data Fim' value='".implode('/',array_reverse(explode('-',$fil_data_fim)))."' onkeypress='return mascaraData(this,event);'>
				<select name='fil_categoria' id='fil_categoria' >
					<option value='$fil_categoria' >$fil_categoria_n</option>
					"; 
					$sql = " SELECT * FROM aux_categoria_solicitacao ORDER BY cas_id";
					$stmt_cat = $PDO->prepare($sql);
					$stmt_cat->execute();
					while($result_cat = $stmt_cat->fetch())
					{
						echo "<option value='".$result_cat['cas_id']."'>".$result_cat['cas_descricao']."</option>";
					}
					echo "
				</select>
				<select name='fil_forma_atendimento' id='fil_forma_atendimento' >
					<option value='$fil_forma_atendimento'>$fil_forma_atendimento_n</option>
					"; 
					$sql = "SELECT * FROM aux_formas_atendimento 
							ORDER BY fat_descricao ASC";
					$stmt_fat = $PDO->prepare($sql);
					$stmt_fat->execute();
					while($result_fat = $stmt_fat->fetch())
					{
						echo "<option value='".$result_fat['fat_id']."'>".$result_fat['fat_descricao']."</option>";
					}
					echo "
				</select>
				<input type='submit' value='Filtrar'> 
                </form>            
			</div>			
			";
			if($filtro == 1)
			{
				echo "<br><img class='hand' title='Imprimir Relatório' style='float:right; margin:0 5px;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('relatorio_agenda_imprimir.php?fil_cliente=$fil_cliente&fil_tecnico=$fil_tecnico&fil_categoria=$fil_categoria&fil_forma_atendimento=$fil_forma_atendimento&fil_data_inicio=$fil_data_inicio&fil_data_fim=$fil_data_fim$autenticacao');>";
			}
			echo "
			
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>$periodo</td>
				</tr>";
				$c=0;
				while($result = $stmt->fetch())
				{
					$sol_id 			= $result['sol_id'];
					$ano 				= $result['ano'];
					$emp_tipo 			= $result['emp_tipo'];
					$emp_nome_razao 	= $result['emp_nome_razao'];
					$emp_fantasia 		= $result['emp_fantasia'];
					$con_objeto 		= $result['con_objeto'];
					$sol_tipo 			= $result['sol_tipo'];
					$solicitante 		= $result['ctt_nome'].$result['usu_nome'];
					$sol_data 			= implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
					$sol_hora 			= substr($result['sol_data_cadastro'],11,5);
					
					$emp_logo 			= $result['emp_logo'];
					if($emp_logo == '')
					{
						$emp_logo = '../imagens/nophoto.png';
					}
					
					
					// SOLICITACOES
					$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes
							LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
							LEFT JOIN cliente_solicitacoes_agenda ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
							LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
							WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
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
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$sol_id').toolbar({content: '#user-options-$sol_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$sol_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_editar&sol_id=$sol_id$autenticacao'><img border='0' src='../imagens/icon-editar.png' ></a>						
					</div>
					";
					echo "<tr class='$c1'>
							  <td>
							  	<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span> - ".implode("/",array_reverse(explode("-",$result['agi_data'])))." - ".$result['agi_horario']."
								<div style='float:right;'>$categoria</div> <br>
								<b>Solicitação:</b> $solic <br>
								<b>Técnico(s): $usuarios</b> <br>
								<b>Veículo(s): $carros</b> <br>
								<b>Forma de Atendimento:</b> ".$result['fat_descricao']." <br>
								<b>Status:</b> ".$sts_status." <br><br>
								".nl2br($result['agi_descricao'])."
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
    if($pagina == 'agenda_gerenciar_itens_adicionar')
    {
        echo "	
		<form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&action=adicionar&age_id=$age_id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
			  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>			  
			</ul>
			
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<br>
								<label>Data:</label> <input name='agi_data' id='agi_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'>
								<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
								<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' placeholder='Horário' >
								<p><label>Forma de Atendimento:</label> 
										<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
										<option value=''>Forma de Atendimento</option>
										"; 
										$sql = "SELECT * FROM aux_formas_atendimento 
												ORDER BY fat_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
										}
										echo "
									</select>
								<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'></textarea>
								<p><label>Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
									
							</td>
						</tr>
					</table>
			  	</div>
				<div id='tecnicos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_tecnico'>
								<div class='bloco_tecnico'>
									<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
									<br><label>Técnico:</label> 
										<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
										<option value=''>Técnico</option>
										"; 
										$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
										}
										echo "
									</select>
									<label>Responsável?</label>	
									   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
									   		<option value=''>Responsável?</option>
											<option value='Sim'>Sim</option>
											<option value='Não'>Não</option>
									   </select>
									<p>
									<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
									<br><br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
			 	<div id='veiculos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_veiculo'>
								<div class='bloco_veiculo'>
									<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
									<br><label>Veículo:</label> 
										<select name='veiculo[1][agc_carro]' id='agc_carro'>
										<option value=''>Veículo</option>
										"; 
										$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
										}
										echo "
									</select>
									<p>
									<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
									<br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>				
			</div>   			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
        ";
    }
    
    if($pagina == 'agenda_gerenciar_itens_editar')
    {
        $sql = "SELECT * FROM agenda_gerenciar_itens 
				LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
				WHERE agi_id = :agi_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':agi_id', $agi_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$agi_data 					= implode("/",array_reverse(explode("-",$result['agi_data'])));
			$agi_horario_inicial		= $result['agi_horario_inicial'];
			$agi_horario 				= $result['agi_horario'];
			$agi_forma_atendimento 		= $result['agi_forma_atendimento'];
			$fat_descricao 				= $result['fat_descricao'];
			$ser_descricao		 		= $result['ser_descricao'];
			$agi_descricao		 		= $result['agi_descricao'];
			$agi_anexo		 			= $result['agi_anexo'];
			$agi_status			 		= $result['agi_status'];
			echo "
            <form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&action=editar&age_id=$age_id&agi_id=$agi_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
				  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
				  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>			  
				</ul>
				
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br>
									<label>Data:</label> <input name='agi_data' id='agi_data' value='$agi_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'>
									<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' value='$agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
									<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' value='$agi_horario' placeholder='Horário' >
									<p><label>Forma de Atendimento:</label> 
											<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
											<option value='$agi_forma_atendimento'>$fat_descricao</option>
											"; 
											$sql = "SELECT * FROM aux_formas_atendimento 
													ORDER BY fat_descricao ASC";
											$stmt = $PDO->prepare($sql);
											$stmt->execute();
											while($result = $stmt->fetch())
											{
												echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
											}
											echo "
										</select>
									<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'>$agi_descricao</textarea>
									<p><label>Anexo:</label> ";if($agi_anexo != ''){ echo "<a href='$agi_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp; 
									<p><label>Alterar Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
								</td>
							</tr>
						</table>
					</div>
					<div id='tecnicos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_tecnico'>
									";
									$sql = "SELECT * FROM agenda_gerenciar_usuario 
											LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario	
											WHERE agu_agenda_item = :agu_agenda_item";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':agu_agenda_item', $agi_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_tecnico'>
												<input type='hidden' name='tecnico[$x][agu_id]' id='agu_id' value='".$result['agu_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Técnico:</label> 
													<select name='tecnico[$x][agu_usuario]' id='agu_usuario'  class='agu_usuario'>
													<option value='".$result['agu_usuario']."'>".$result['usu_nome']."</option>
													"; 
													$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
													$stmt_gestor = $PDO->prepare($sql);
													$stmt_gestor->execute();
													while($result_gestor = $stmt_gestor->fetch())
													{
														echo "<option value='".$result_gestor['usu_id']."'>".$result_gestor['usu_nome']."</option>";
													}
													echo "
												</select>
												<label>Responsável?</label>	
												   <select name='tecnico[$x][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
														<option value='".$result['agu_responsavel']."'>".$result['agu_responsavel']."</option>
														<option value='Sim'>Sim</option>
														<option value='Não'>Não</option>
												   </select>
												<p><img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remTecnico' title='Remover' class='botao_dinamico'><br><br>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_tecnico'>
											<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
											<br><label>Técnico:</label> 
												<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
												<option value=''>Técnico</option>
												"; 
												$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
												}
												echo "
											</select>
											<label>Responsável?</label>	
											   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
													<option value=''>Responsável?</option>
													<option value='Sim'>Sim</option>
													<option value='Não'>Não</option>
											   </select>
											<p>
											<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
											<br><br>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='veiculos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_veiculo'>
									";
									$sql = "SELECT * FROM agenda_gerenciar_carro
											LEFT JOIN cadastro_carros ON cadastro_carros.car_id = agenda_gerenciar_carro.agc_carro	
											WHERE agc_agenda_item = :agc_agenda_item";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':agc_agenda_item', $agi_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_veiculo'>
												<input type='hidden' name='veiculo[$x][agc_id]' id='agc_id' value='".$result['agc_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Tipo:</label> 
													<select name='veiculo[$x][agc_carro]' id='agc_carro' >
													<option value='".$result['agc_carro']."'>".$result['car_descricao']."</option>
													"; 
													$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
													$stmt = $PDO->prepare($sql);
													$stmt->execute();
													while($result = $stmt->fetch())
													{
														echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
													}
													echo "
												</select>
												<p>
												<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remVeiculo' title='Remover' class='botao_dinamico'>
												<br>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_veiculo'>
											<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
											<br><label>Veículo:</label> 
												<select name='veiculo[1][agc_carro]' id='agc_carro'>
												<option value=''>Veículo</option>
												"; 
												$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
												}
												echo "
											</select>
											<p>
											<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
											<br>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>		
				</div>    
				<center>
				<div id='erro' align='center'>&nbsp;</div>
				<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
				</center>
			</form>
            ";
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
