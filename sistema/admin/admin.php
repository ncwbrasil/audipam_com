<?php
session_start (); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script src="../mod_includes/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../mod_includes/js/tooltip.js" type="text/javascript" ></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
<script src= 'https://cdn.zingchart.com/2.3.0/zingchart.min.js'></script>
<script> ZC.MODULESDIR = 'https://cdn.zingchart.com/2.3.0/modules/';
  		 ZC.LICENSE = ['569d52cefae586f634c54f86dc99e6a9','ee6b7db5b51705a13dc2339db3edaf6d'];
</script>
<!--<script src="https://cdn.zingchart.com/zingchart.min.js"></script>
<script src="https://cdn.zingchart.com/zingchart-core.min.js"></script>
<script>zingchart.MODULESDIR="https://cdn.zingchart.com/modules/";</script>-->
</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');

?>
<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
<div class='msg'>
	<?php include("../mod_menu/barra.php");?>
</div>
</div>
<div class='centro'>
	<div class='box dashboard'>
    
    	<!-- PROGRAMACAO -->
		<?php	
		echo '
		<div class="bloco">
			<p class="titulo">Últimos Acessos</p>
			';
			// $sql = "SELECT *, count(log_usuario) as c FROM admin_log_login 
			// 		INNER JOIN admin_usuarios ON admin_usuarios.usu_id = admin_log_login.log_usuario
			// 		group by log_usuario having c = 1
			// 		ORDER BY log_id DESC												
			// 		LIMIT 0, 5";
			$sql = "SELECT * FROM admin_usuarios 
					INNER JOIN admin_log_login h1 ON h1.log_usuario = admin_usuarios.usu_id		
					WHERE h1.log_id = (SELECT MAX(h2.log_id) FROM admin_log_login h2 where h2.log_usuario = h1.log_usuario) 
					ORDER BY log_id DESC												
					LIMIT 0, 5";
			$stmt = $PDO->prepare($sql);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					// SOLICITACOES
					$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes
							LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
							WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
								sts_usuario = :sts_usuario
							GROUP BY sol_id 
							ORDER BY sts_id DESC
							LIMIT 0,1
							";
					$stmt_solic = $PDO->prepare($sql);
					$stmt_solic->bindParam(':sts_usuario',$result['log_usuario']);
					$stmt_solic->execute();
					$rows_solic = $stmt_solic->rowCount();
					if($rows_solic > 0)
					{							
						while($result_solic = $stmt_solic->fetch())
						{
							switch($result_solic['sts_status'])
							{
								case 1 : $status = "Registrado";break;
								case 2 : $status = "Em Análise";break;
								case 3 : $status = "<span class='laranja'>Agendado</span>";break;
								case 4 : $status = "<span class='azul'>Em Execução</span>";break;
								case 5 : $status = "<span class='vermelho'>Cancelado</span>";break;
								case 6 : $status = "Em Homologação";break;
								case 7 : $status = "<span class='verde'>Concluído</span>";break;
							}
							$sts_data = implode("/",array_reverse(explode("-",substr($result_solic['sts_data'],0,10))));
							$sts_data .= " às ".substr($result_solic['sts_data'],11,5);
							$ultima_mov = "<a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=".$result_solic["sol_id"].$autenticacao."'>".$result_solic["ano"].".".$result_solic["sol_id"]."</a> | ".$status." | ".$sts_data."";
						}
					}
					echo "
					<tr>
					<td>
						<div style='background:url(".$result['usu_foto']."); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
					</td>						
					<td>
						".implode("/",array_reverse(explode("-",substr($result['log_data'],0,10))))."<br>".
						substr($result['log_data'],11,5)."
					</td>
					<td>
						".$ultima_mov."
					</td>
					</tr>
					";
				}

				echo '
				</table>
				';
			}
			echo '				
		</div>
		
		';


		?>
		<div class='bloco' style='min-height:400px;'>
			<p class='titulo'>Minhas Próximas Tarefas</p>
			<?php
			$hoje = date("Y-m-d");
			$sql = "SELECT * FROM agenda_gerenciar_itens 
					LEFT JOIN agenda_gerenciar_usuario ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
					LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
					LEFT JOIN (cliente_solicitacoes_agenda 
						LEFT JOIN ( cliente_solicitacoes 
							LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
							LEFT JOIN (cadastro_empresas 
								LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf)
							ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
							LEFT JOIN ( cadastro_contratos 
								LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
							ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato )
						ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao)
					ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
					WHERE agu_usuario = :agu_usuario AND agi_data >= :agi_data
					GROUP BY agi_id 
					ORDER BY agi_data ASC
					LIMIT 0, 5";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':agu_usuario',$_SESSION['usuario_id']);
			$stmt->bindParam(':agi_data',$hoje);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					// SOLICITACOES
					$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes
							LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
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
								soa_agenda_item = :agi_id
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
							$ano			= $result_solic['ano'];
							$user		= $result_solic['ctt_nome'].$result_solic['usu_nome'];
							$sts_data		= implode("/",array_reverse(explode("-",substr($result_solic['sts_data'],0,10))));
							$sts_hora		= substr($result_solic['sts_data'],11,5);
							$data_hora = $sts_data." às ".$sts_hora;							
							$obs = trim(str_replace('"',"´",preg_replace('/\s+/', " ",str_replace(array("\r\n", "\r", "\n"), "|",str_replace("\t", " ",$result_solic['sts_observacao'])))));
							
							
							
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
					
					
					
					if($result['emp_nome_razao'] == "")
					{
						$titulo = "Solicitação Interna";
					}
					elseif($result['emp_nome_razao'] != "")
					{
						$titulo = $result['emp_nome_razao']."/".$result['uf_sigla'];
					}
					$descricao = $result['agi_descricao'];
					$cas_icone 			= $result['cas_icone'];
					$cas_descricao 		= $result['cas_descricao'];
					$fil_status = $result['sts_status'];
					switch($fil_status)
					{
						case 1 : $fil_status_n = "Registrado";break;
						case 2 : $fil_status_n = "Em Análise";break;
						case 3 : $fil_status_n = "<span class='laranja'>Agendado</span>";break;
						case 4 : $fil_status_n = "<span class='azul'>Em Execução</span>";break;
						case 5 : $fil_status_n = "<span class='vermelho'>Cancelado</span>";break;
						case 6 : $fil_status_n = "Em Homologação";break;
						case 7 : $fil_status_n = "<span class='verde'>Concluído</span>";break;
					}

					echo "
					<tr>
					<td>
						<div style='background:url(".$result['emp_logo']."); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
					</td>
					<td align='left'>
						<b>$titulo</b> <br>
						".truncate($result['agi_descricao'],55)." <img src='../imagens/icon-exibir.png' width='20' onmouseover=\"toolTip('".$descricao."');\" onmouseout=\"toolTip();\">
					</td>
					<td align=center><img src='".$cas_icone."' width='30' title='".$cas_descricao."'></td>
					<td>
						<!--".implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))))." <br>-->
						$solic<br>
						".$status."
					</td>
					<td>
						".implode("/",array_reverse(explode("-",$result['agi_data'])))."
					</td>
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma tarefa programada";
			}
			?>
		</div>
			

        <!-- CONTRATOS -->
		<div class='bloco'>
        	<p class='titulo'>Próximos Contratos à Vencer</p>
            <?php
			$hoje = date("Y-m-d");
			$sql = "SELECT * FROM cadastro_contratos 
					LEFT JOIN (cadastro_empresas 
								LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf) 
					ON cadastro_empresas.emp_id = cadastro_contratos.con_contratante
					LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
					LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico
					WHERE con_status = :con_status AND con_final_vig >= :con_final_vig
					ORDER BY con_final_vig ASC
					LIMIT 0, 5";
			$stmt = $PDO->prepare($sql);
			$con_status = "Vigente";
			$stmt->bindParam(':con_status',$con_status);
			$stmt->bindParam(':con_final_vig',$hoje);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					$descricao = $result['con_objeto'];
					echo "
					<tr>
					<td>
						<div style='background:url(".$result['emp_logo']."); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
					</td>
					<td align='left'>
						<b>".$result['emp_nome_razao']."/".$result['uf_sigla']."</b> <br>
						".truncate($descricao,55)." <img src='../imagens/icon-exibir.png' width='20' onmouseover=\"toolTip('".$descricao."');\" onmouseout=\"toolTip();\">
					</td>
					<td>
						".implode("/",array_reverse(explode("-",$result['con_final_vig'])))."
					</td>
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma tarefa programada";
			}
			?>
        </div>
        
        
        <!-- SOLICITACOES -->
		<div class='bloco'>
        	<p class='titulo'>Últimas Solicitações Cadastradas Pendentes</p>
            <?php
			$hoje = date("Y-m-d");
			$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes 
					LEFT JOIN ( cadastro_contratos 
						LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
						LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
						LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
						LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico )
					ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
					LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
					LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
					LEFT JOIN (cadastro_empresas 
								LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf)
					ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
					LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
					WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
						  ( sts_status = :sts_status1 OR sts_status = :sts_status2 OR sts_status = :sts_status3 ) 
					GROUP BY sol_id
					ORDER BY sol_id DESC
					LIMIT 0, 5
			";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':agi_data',$hoje);
			$stmt->bindValue(':sts_status1',1);
			$stmt->bindValue(':sts_status2',2);
			$stmt->bindValue(':sts_status3',3);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					$descricao = $result['sol_memorial'];
					$fil_status = $result['sts_status'];
					
					// TECNICOS
					$sql = "SELECT usu_foto FROM agenda_gerenciar_usuario
							INNER JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario
							WHERE usu_id = :sts_usuario";
					$stmt_usuarios = $PDO->prepare($sql);
					$stmt_usuarios->bindParam(':sts_usuario',$result['sts_usuario']);
					$stmt_usuarios->execute();
					$rows_usuarios = $stmt_usuarios->rowCount();
					$usuarios="";
					if($rows_usuarios > 0)
					{
						while($result_usuarios = $stmt_usuarios->fetch())
						{
							$usu_foto = $result_usuarios['usu_foto'];
							
						}					
					}	
					switch($fil_status)
					{
						case 1 : $fil_status_n = "Registrado";break;
						case 2 : $fil_status_n = "Em Análise";break;
						case 3 : $fil_status_n = "<span class='laranja'>Agendado</span>";break;
						case 4 : $fil_status_n = "<span class='azul'>Em Execução</span>";break;
						case 5 : $fil_status_n = "<span class='vermelho'>Cancelado</span>";break;
						case 6 : $fil_status_n = "Em Homologação";break;
						case 7 : $fil_status_n = "<span class='verde'>Concluído</span>";break;
					}
					echo "
					<tr>
					<td>
						<div style='background:url(".$result['emp_logo']."); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
					</td>
					<td align='left'>
						<b>".$result['emp_nome_razao']."/".$result['uf_sigla']."</b> <br>
						".truncate($descricao,55)." <img src='../imagens/icon-exibir.png' width='20' onmouseover=\"toolTip('".$descricao."');\" onmouseout=\"toolTip();\">
					</td>
					<td>
						<!--".implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))))." <br>-->
						<a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=".$result['sol_id']."$autenticacao' >".$result['ano'].".".$result['sol_id']."</a><br>
						".$fil_status_n."
					</td>
					<td>
						<div style='background:url($usu_foto); height: 50px; width:50px; background-size:100%; object-fit:cover; border-radius:100px;'></div>
					</td>	
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma solicitação cadastrada";
			}
			?>
        </div>
        
        <!-- PARECERES -->
		<div class='bloco'>
        	<p class='titulo'>Últimos Pareceres Cadastrados</p>
            <?php
			$hoje = date("Y-m-d");
			$sql = "SELECT * FROM cadastro_pareceres 
					LEFT JOIN aux_assuntos_pareceres ON aux_assuntos_pareceres.ass_id = cadastro_pareceres.par_assunto
					ORDER BY par_id DESC
					LIMIT 0, 5
			";
			$stmt = $PDO->prepare($sql);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					$descricao = $result['par_parecer'];
					
					echo "
					<tr>
					
					<td align='left'>
						<b>".$result['ass_descricao']."</b> <br>
						".truncate($descricao,160)." <img src='../imagens/icon-exibir.png' width='16' onmouseover=\"toolTip('".str_replace('"','',str_replace("'","",str_replace("\r", ' ', str_replace("\n", ' ', $descricao))))."');\" onmouseout=\"toolTip();\">
					</td>
					<td>
						
					</td>
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma solicitação cadastrada";
			}
			?>
        </div>
        
        <!-- FORMACAO -->
		<div class='bloco'>
        	<p class='titulo'>Próximas certificações a vencer</p>
            <?php
			$hoje = date("Y-m-d");
			$sql = "SELECT for_entidade, for_formacao, usu_foto, for_data_vcto FROM admin_usuarios_formacao 
					LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = admin_usuarios_formacao.for_usuario
					UNION ALL 
					SELECT for_entidade, for_formacao, emp_logo, for_data_vcto FROM cadastro_empresas_formacao 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_formacao.for_empresa
					ORDER BY for_data_vcto ASC
					LIMIT 0, 5";
			$stmt = $PDO->prepare($sql);						
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
				';
				while($result = $stmt->fetch())
				{
					echo "
					<tr>
					<td>
						".$result['for_entidade']."<br>
						".$result['for_formacao']."
					</td>
					<td>
						".implode("/",array_reverse(explode("-",$result['for_data_vcto'])))."
					</td>
					<td>
						<div style='background:url(".$result['usu_foto']."); height: 50px; width:50px; object-fit:cover; background-size:100%;  border-radius:100px;'></div>
					</td>
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma tarefa programada";
			}
			?>
        </div>
        
		<!-- HORAS CONTRATOS  -->
		<div class='bloco' style='width:100%;'>
        	<p class='titulo'>Horas por contrato no mês atual</p>
            <?php
			$hoje = date("Y-m-d");
			$sql = "SELECT * FROM cadastro_contratos 
					LEFT JOIN (cadastro_empresas 
								LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf) 
					ON cadastro_empresas.emp_id = cadastro_contratos.con_contratante
					LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
					LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
					LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico
					WHERE con_status = :con_status AND con_final_vig >= :con_final_vig AND ite_unidade = :ite_unidade
					ORDER BY con_final_vig ASC
					LIMIT 0, 5";
			$stmt = $PDO->prepare($sql);
			$con_status = "Vigente";
			$ite_unidade = "Hora/Trabalho";
			$stmt->bindParam(':con_status',$con_status);
			$stmt->bindParam(':con_final_vig',$hoje);
			$stmt->bindParam(':ite_unidade',$ite_unidade);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{	
				echo '
				<table width="95%" align="center" class="dash" cellpadding="10" cellspacing="0"> 
					<tr>
						<td colspan="2">Cliente/Contrato</td>
						<td>Horas contratadas</td>
						<td>Horas executadas</td>
				';
				while($result = $stmt->fetch())
				{
					$descricao = $result['con_objeto'];
					$data = date("Y-m");

					$sql = "SELECT * FROM agenda_gerenciar_itens 
							LEFT JOIN (cliente_solicitacoes_agenda 
								LEFT JOIN (cliente_solicitacoes 
									LEFT JOIN (cadastro_contratos 
										LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id)
									ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato)
								ON cliente_solicitacoes.sol_id = cliente_solicitacoes_agenda.soa_solicitacao) 
							ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id
							WHERE con_id = :con_id AND DATE_FORMAT(agi_data,'%Y-%m') = '".$data."'
							";
										
					$stmt_horas = $PDO->prepare($sql);
					$stmt_horas->bindParam(':con_id',$result['con_id']);					
					$stmt_horas->execute();
					$rows_horas = $stmt_horas->rowCount();
					$horas_exec = "";					
					if($rows_horas > 0)
					{
						$carga_horaria=$inicial=$final=$separador=$hora=$diferenca="";
						while($result_horas = $stmt_horas->fetch())
						{
							$hora = $result_horas['agi_horario'];							
							$separador = explode(" às ",$hora);
							
							if($separador[0] != "" && $separador[1] != "")
							{
								$inicial = strtotime($separador[0]);
								$final = strtotime($separador[1]);
								$diferenca = $final - $inicial;
								$diferenca = strtotime(date("Y-m-d"))+$diferenca;
								//$carga_horaria = date("H\hi",$diferenca);
								$carga_horaria = date("H",$diferenca);
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
									//$carga_horaria = date("H\hi",$diferenca);
									$carga_horaria = date("H",$diferenca);
								}
								else
								{
									//$carga_horaria = "Erro ao calcular carga horária";
								}
								
							}												
							$horas_exec += $carga_horaria;
						}
					}

					echo "
					<tr>
					<td>
						<div style='background:url(".$result['emp_logo']."); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
					</td>
					<td align='left'>
						<b>".$result['emp_nome_razao']."/".$result['uf_sigla']."</b> <br>
						".truncate($descricao,155)." <img src='../imagens/icon-exibir.png' width='20' onmouseover=\"toolTip('".$descricao."');\" onmouseout=\"toolTip();\">
					</td>
					<td>
						".$result['ite_quantidade']."
					</td>
					<td>
						".$horas_exec."
					</td>
					</tr>
					";
				}
			
			echo "
			</table>";
			}
			else
			{
				echo "Não há nenhuma tarefa programada";
			}
			?>
        </div>

        <!-- GRAFICOS BLOCO 01 - INICIO -->
        <!-- <div id='myChart01'>
        	<?php 			
			# FILTRO
			$ano = date("Y");
			$mes = date("m");
			
			$fil_mes = $_REQUEST['fil_mes'];
			if($fil_mes == '')
			{
				//$mes = $mes;				
			} 
			else
			{
				$mes = $fil_mes;				
			} 
			switch ($mes)
			{
				case 1: $mes_n = "Janeiro"; break;
				case 2: $mes_n = "Fevereiro"; break;
				case 3: $mes_n = "Março"; break;
				case 4: $mes_n = "Abril"; break;
				case 5: $mes_n = "Maio"; break;
				case 6: $mes_n = "Junho"; break;
				case 7: $mes_n = "Julho"; break;
				case 8: $mes_n = "Agosto"; break;
				case 9: $mes_n = "Setembro"; break;
				case 10: $mes_n = "Outubro"; break;
				case 11: $mes_n = "Novembro"; break;
				case 12: $mes_n = "Dezembro"; break;
			}
			$fil_ano = $_REQUEST['fil_ano'];
			if($fil_ano == '')
			{
				$ano = date("Y");				
			} 
			else
			{
				$ano = $fil_ano;									
			} 
			$data_query = $ano."-".$mes;
			echo "
			<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='admin.php?".$autenticacao."'>
			<select name='fil_mes' id='fil_mes'>
                <option value=''>".$mes_n."</option>
                <option value='01'>Janeiro</option>
                <option value='02'>Fevereiro</option>
                <option value='03'>Março</option>
                <option value='04'>Abril</option>
                <option value='05'>Maio</option>
                <option value='06'>Junho</option>
                <option value='07'>Julho</option>
                <option value='08'>Agosto</option>
                <option value='09'>Setembro</option>
                <option value='10'>Outubro</option>
                <option value='11'>Novembro</option>
                <option value='12'>Dezembro</option>
            </select>
            <select name='fil_ano' id='fil_ano'>
                <option value='$ano'>".$ano."</option>
                <option value='".date("Y")."'>".date("Y")."</option>
                <option value='".(date("Y")-1)."'>".(date("Y")-1)."</option>               
            </select>
			<input type='submit' value='Filtrar'> 
			</form>
			</div>
			";
			#
			
			# QUERY PARA GRÁFICO 01 (Solicitações por cliente)
			$sql = "SELECT * FROM (
					
					SELECT emp_nome_razao, COUNT(sol_id) as qtd FROM cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
					WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
						  DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data
					GROUP BY emp_nome_razao
					ORDER BY qtd DESC 
					LIMIT 0, 5) as t
					ORDER BY qtd ASC";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':data', $data_query);
			$stmt->execute();
			$rows = $stmt->rowCount();
			
			$leg_g1 = array();
			$qtd_g1 = array();		
			while($result = $stmt->fetch())
			{
				$emp_nome_razao = $result['emp_nome_razao'];
				$leg_g1[] 		= $emp_nome_razao;
				$qtd_g1[] 		= $result['qtd'];
			}
			$leg_g1 = json_encode($leg_g1);
			$qtd_g1 = implode(",",array_values($qtd_g1));
			# 
		
			# QUERY PARA GRÁFICO 02 (Solicitações por status)
			$sql = "SELECT sts_status, COUNT(sts_status) as qtd FROM cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
					LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
					WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
						  DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data
					GROUP BY sts_status
					ORDER BY sts_status";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':data', $data_query);
			$stmt->execute();
			$rows = $stmt->rowCount();
			
			$leg_g2 = array();
			$qtd_g2 = array();		
			while($result = $stmt->fetch())
			{
				$status = $result['sts_status'];
				switch($status)
				{
					case 1 : $status = "Registrado";break;
					case 2 : $status = "Em Análise";break;
					case 3 : $status = "Agendado";break;
					case 4 : $status = "Em Execução";break;
					case 5 : $status = "Cancelado";break;
					case 6 : $status = "Em Homologação";break;
					case 7 : $status = "Concluído";break;
				}
				$leg_g2[] 	= $status;
				$qtd_g2[] 	= $result['qtd'];
			}
			$leg_g2 = json_encode($leg_g2);
			$qtd_g2 = implode(",",array_values($qtd_g2));
			# 
			?>			
        </div> -->
       	<!-- GRAFICOS BLOCO 01 - FIM -->
        
        <br /><br />
        
        
        
        <!-- GRAFICOS BLOCO 02 - INICIO -->
        <!-- <div id='myChart02'>
        	<?php 
			# FILTRO
			$ano = date("Y");
			$mes = date("m");
			
			$fil_meses = $_REQUEST['fil_meses'];
			if($fil_meses == '')
			{
				$meses = "06";	
				$inicio = date("Y-m",strtotime("-".$meses." month"));
				$fim =	date("Y-m");
			} 
			else
			{
				$meses = $fil_meses;
				$inicio = date("Y-m",strtotime("-".$meses." month"));
				$fim =	date("Y-m");	
			} 
			
			
			echo "
			<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='admin.php?".$autenticacao."'>
            <select name='fil_meses' id='fil_meses'>
                <option value='$meses'>".$meses."</option>
                <option value='03'>03</option>
                <option value='06'>06</option>  
				<option value='12'>12</option>               
            </select>
			<input type='submit' value='Filtrar'> 
			</form>
			</div>
			";	
			#
			
			# QUERY PARA GRÁFICO 03 E 05 (Solicitações totais e por tipo)
			$sql = "SELECT DATE_FORMAT(sol_data_cadastro,'%Y-%m') as data, COUNT(sol_id) as qtd FROM cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
					WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') BETWEEN :inicio AND :fim
					GROUP BY DATE_FORMAT(sol_data_cadastro,'%Y-%m')
					ORDER BY DATE_FORMAT(sol_data_cadastro,'%Y-%m') ASC";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':inicio', $inicio);
			$stmt->bindParam(':fim', $fim);
			$stmt->execute();
			$rows = $stmt->rowCount();
			
			$leg_g3 = array();
			$qtd_g3 = array();	
			$qtd_ext = array();	
			$qtd_int = array();		
			while($result = $stmt->fetch())
			{
				$data = $result['data'];
				$data = substr($data, -2);
				switch ($data)
				{
					case 1: $data = "Jan"; break;
					case 2: $data = "Fev"; break;
					case 3: $data = "Mar"; break;
					case 4: $data = "Abr"; break;
					case 5: $data = "Mai"; break;
					case 6: $data = "Jun"; break;
					case 7: $data = "Jul"; break;
					case 8: $data = "Ago"; break;
					case 9: $data = "Set"; break;
					case 10: $data = "Out"; break;
					case 11: $data = "Nov"; break;
					case 12: $data = "Dez"; break;
				}
				$leg_g3[] 	= $data;
				$qtd_g3[] 	= $result['qtd'];
				
				$sql = "SELECT COALESCE(COUNT(sol_id),0) as qtd FROM cliente_solicitacoes 
						WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data  AND sol_tipo = :sol_tipo	
						";
				$stmt_ext = $PDO->prepare($sql);
				$sol_tipo = "Externa";
				$stmt_ext->bindParam(':data', $result['data']);
				$stmt_ext->bindParam(':sol_tipo', $sol_tipo);
				$stmt_ext->execute();
				$qtd_ext[] 	= $stmt_ext->fetch(PDO::FETCH_OBJ)->qtd;
				
				$sql = "SELECT COALESCE(COUNT(sol_id),0) as qtd FROM cliente_solicitacoes 
						WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data  AND sol_tipo = :sol_tipo	
						";
				$stmt_int = $PDO->prepare($sql);
				$sol_tipo = "Interna";
				$stmt_int->bindParam(':data', $result['data']);
				$stmt_int->bindParam(':sol_tipo', $sol_tipo);
				$stmt_int->execute();
				$qtd_int[] 	= $stmt_int->fetch(PDO::FETCH_OBJ)->qtd;
			}
			$leg_g3 = json_encode($leg_g3);
			$qtd_g3 = implode(",",array_values($qtd_g3));
			$qtd_ext = implode(",",array_values($qtd_ext ));
			$qtd_int = implode(",",array_values($qtd_int));
			# 			
			
			# QUERY PARA GRÁFICO 04 (Pizza)
			$sql = "SELECT DATE_FORMAT(sol_data_cadastro,'%Y-%m') as data, COUNT(sol_id) as qtd FROM cliente_solicitacoes 
					LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
					LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
					WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data
					GROUP BY DATE_FORMAT(sol_data_cadastro,'%Y-%m')
					ORDER BY DATE_FORMAT(sol_data_cadastro,'%Y-%m') ASC";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':data', date("Y-m"));
			$stmt->execute();
			$rows = $stmt->rowCount();
			
			$leg_pizza = array();
			$total_this = array();	
			$qtd_ext_this = array();	
			$qtd_int_this = array();		
			while($result = $stmt->fetch())
			{
				$data = $result['data'];
				$data = substr($data, -2);
				switch ($data)
				{
					case 1: $data = "Janeiro"; break;
					case 2: $data = "Fevereiro"; break;
					case 3: $data = "Março"; break;
					case 4: $data = "Abril"; break;
					case 5: $data = "Maio"; break;
					case 6: $data = "Junjo"; break;
					case 7: $data = "Julho"; break;
					case 8: $data = "Agosto"; break;
					case 9: $data = "Setembro"; break;
					case 10: $data = "Outubro"; break;
					case 11: $data = "Novembro"; break;
					case 12: $data = "Dezembro"; break;
				}
				$leg_pizza[] 	= $data;
				$total_this[] 	= $result['qtd'];
				
				$sql = "SELECT COALESCE(COUNT(sol_id),0) as qtd FROM cliente_solicitacoes 
						WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data  AND sol_tipo = :sol_tipo	
						";
				$stmt_ext = $PDO->prepare($sql);
				$sol_tipo = "Externa";
				$stmt_ext->bindParam(':data', $result['data']);
				$stmt_ext->bindParam(':sol_tipo', $sol_tipo);
				$stmt_ext->execute();
				$qtd_ext_this[] 	= $stmt_ext->fetch(PDO::FETCH_OBJ)->qtd;
				
				$sql = "SELECT COALESCE(COUNT(sol_id),0) as qtd FROM cliente_solicitacoes 
						WHERE DATE_FORMAT(sol_data_cadastro,'%Y-%m') = :data  AND sol_tipo = :sol_tipo	
						";
				$stmt_int = $PDO->prepare($sql);
				$sol_tipo = "Interna";
				$stmt_int->bindParam(':data', $result['data']);
				$stmt_int->bindParam(':sol_tipo', $sol_tipo);
				$stmt_int->execute();
				$qtd_int_this[] 	= $stmt_int->fetch(PDO::FETCH_OBJ)->qtd;
			}
			$leg_pizza = implode(",",array_values($leg_pizza));
			$total_this = implode(",",array_values($total_this));
			$qtd_ext_this = implode(",",array_values($qtd_ext_this));
			$qtd_int_this = implode(",",array_values($qtd_int_this));
			#
			
			?>		
        </div> -->
        <!-- GRAFICOS BLOCO 02 - FIM -->
        
        <BR /><BR />
        
                
        <?php
		include("../mod_includes/php/charts.php");
		?>

	    <div id='interna'>
        <table width='100%'>
            <tr>
                <td align='justify' valign='top' >
                    Explicação das legendas:<br>
                    <ul>
                    <img src='../imagens/icon-ativo.png' style='padding:5px 6px;' valign='middle'> Ativado<br />
                    <img src='../imagens/icon-inativo.png' style='padding:5px 6px' valign='middle'> Desativado<br />
                    <img src='../imagens/icon-ativa-desativa.png' valign='middle'> Faz a troca de Ativado para Desativado e vice-versa<br />
                    <img src='../imagens/icon-exibir.png' valign='middle'> Exibir<br />
                    <img src='../imagens/icon-editar.png' valign='middle'> Editar<br />
                    <img src='../imagens/icon-excluir.png' valign='middle'> Excluir<br />
                    <img src='../imagens/icon-pago.png' valign='middle'> Dar baixa em fatura<br />
                    <img src='../imagens/icon-obs.png' valign='middle'> Observações da fatura<br />
                    <img src='../imagens/icon-contato.png' valign='middle'> Contatos do cliente<br />
                    </ul>
                </td>
            </tr>
        </table>
        
        
        <?php
		/*
        <!-- GRAFICOS BLOCO 02 - INICIO -->
        <div id='myChart02'>
        	<?php 
			# FILTRO
			$ano = date("Y");
			$mes = date("m");
			
			$fil_meses = $_REQUEST['fil_meses'];
			if($fil_meses == '')
			{
				$meses = "06";	
				$inicio = date("Y-m",strtotime("-".$meses." month"));
				$fim =	date("Y-m");
			} 
			else
			{
				$meses = $fil_meses;
				$inicio = date("Y-m",strtotime("-".$meses." month"));
				$fim =	date("Y-m");	
			} 
			
			
			echo "
			<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='admin.php?".$autenticacao."'>
            <select name='fil_meses' id='fil_meses'>
                <option value='$meses'>".$meses."</option>
                <option value='03'>03</option>
                <option value='06'>06</option>  
				<option value='12'>12</option>               
            </select>
			<input type='submit' value='Filtrar'> 
			</form>
			</div>
			";	
			#
			
					
			?>		
        </div>
        <!-- GRAFICOS BLOCO 02 - FIM -->
        
        <br /><br />
		 </div>*/
		
		?>
        
   
    </div>
</div>
<script src='../mod_includes/js/w8/scripts.js'></script>
<?php
include('../mod_rodape/rodape.php');
?>
