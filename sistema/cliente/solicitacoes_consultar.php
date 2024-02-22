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
<!-- ABAS -->
<link rel="stylesheet" href="../mod_includes/js/abas/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../mod_includes/js/abas/bootstrap.js"></script>
<!-- ABAS -->
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<!--   TimeLine   -->
<link rel="stylesheet" href="../mod_includes/js/timeline/style-timeline.css"> <!-- Resource style -->
<script src="../mod_includes/js/timeline/modernizr.js"></script> <!-- Modernizr -->
<script src="../mod_includes/js/timeline/main.js"></script> <!-- Modernizr -->
<!--   TimeLine   -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogincliente.php');

?>
<div class='lateral'>
	<?php include("mod_menu/menu.php");?>
</div>
<div class='barra'> 
<div class='msg'>
	<?php //include("mod_menu/barra.php");?>
</div>
</div>
<div class='centro'>
	<div class='box'>
    <?php
	$page = "<a href='solicitacoes_consultar.php?pagina=solicitacoes_consultar".$autenticacao."'>Consultar Solicitações</a>";
	$sol_id 				= $_GET['sol_id'];
	$sol_contrato 			= $_POST['sol_contrato'];
	$sol_item_contrato 		= $_POST['sol_item_contrato'];
	$sol_breve_historico 	= $_POST['sol_breve_historico'];
	$sol_memorial 			= $_POST['sol_memorial'];
	$dados = array_filter(array(
		'sol_cliente' 			=> $_SESSION['cliente_id'],
		'sol_contato' 			=> $_SESSION['contato_id'],
		'sol_contrato' 			=> $sol_contrato,
		'sol_item_contrato' 	=> $sol_item_contrato,
		'sol_breve_historico' 	=> $sol_breve_historico,
		'sol_memorial' 			=> $sol_memorial
	));
	
	if($action == "adicionar")
    {
        $sql = "INSERT INTO cliente_solicitacoes SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			$sol_id = $PDO->lastInsertId();
			
			// STATUS SOLICITACAO
			$sql = "INSERT INTO cliente_status_solicitacoes  SET sts_solicitacao = :sts_solicitacao, sts_status = :sts_status ";
			$stmt = $PDO->prepare($sql);	
			$stmt->bindParam(':sts_solicitacao',$sol_id);
			$stmt->bindValue(':sts_status',1);
			if($stmt->execute()){}else{ $erro=1; $err = $stmt->errorInfo();}
        }
		else{ $erro=1; $err = $stmt->errorInfo();}
		
      	if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";
		}
		else
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/x.png> Erro ao efetuar cadastro.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
			</SCRIPT>
				"; 
		}
    }
    
	
	$num_por_pagina = 20;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes 
			INNER JOIN ( cadastro_contratos 
				INNER JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
				INNER JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico )
			ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
			INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
			LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
			WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
				  ges_contato = :ges_contato
			GROUP BY sol_id
			ORDER BY sol_data_cadastro DESC
			LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':ges_contato', 	$_SESSION['contato_id']);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
	if($pagina == "solicitacoes_consultar")
	{
		echo "<div class='titulo'> $page </div>";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Protocolo</td>
					<td class='titulo_tabela'>Contrato</td>
					<td class='titulo_tabela'>Item do Contrato</td>
					<td class='titulo_tabela' align='center'>Data da Cadastro</td>
					<td class='titulo_last' align='center'>Status</td>

				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$sol_id 			= $result['sol_id'];
					$ano 				= $result['ano'];
					$con_numero_processo= $result['con_numero_processo'];
					$con_ano_processo 	= $result['con_ano_processo'];
					$mod_descricao 		= $result['mod_descricao'];
					$ite_descricao		= $result['ite_descricao'];
					$ite_tipo 			= $result['ite_tipo'];
					$sol_data = implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
					$sol_hora = substr($result['sol_data_cadastro'],11,5);
					$sts_status 			= $result['sts_status'];
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
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "<tr class='$c1'>
							  <td><a href='solicitacoes_consultar.php?pagina=solicitacoes_consultar_exibir&sol_id=$sol_id$autenticacao' style='font-size:16px;'>$ano.$sol_id</a></td>
							  <td>$con_numero_processo/$con_ano_processo<br><span class='detalhe'>$mod_descricao</span></td>
							  <td>$ite_descricao<br><span class='detalhe'>$ite_tipo</span></td>
							  <td align=center>$sol_data<br><span class='detalhe'>às $sol_hora</span></td>
							  <td align=center>$sts_status</td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=solicitacoes_consultar".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM cliente_solicitacoes 
						INNER JOIN ( cadastro_contratos 
							INNER JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
							INNER JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id )
						ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
						INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
						LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
						WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND 
							  ges_contato = :ges_contato
						GROUP BY sol_id
						ORDER BY sol_data_cadastro DESC";
				$stmt = $PDO->prepare($cnt);
				$stmt->bindParam(':ges_contato', $_SESSION['contato_id']);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br><br>Não há nenhuma solicitação registrada.";
		}
	}
	
	if($pagina == "solicitacoes_consultar_exibir")
	{
		?>
        <script language="javascript">
		jQuery(document).ready(function()
		{
			jQuery('html, body').animate({scrollTop:$(document).height()-$(window).height()}, 1500);
		});
		</script>
        <?php
		
		$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes 
				INNER JOIN ( cadastro_contratos 
					INNER JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
					INNER JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
					LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
					LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico )
				ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
				INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
				LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
				WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
					  ges_contato = :ges_contato AND sol_id = :sol_id
				GROUP BY sol_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':ges_contato', $_SESSION['contato_id']);
		$stmt->bindParam(':sol_id', $sol_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			
			$result = $stmt->fetch();
          	$sol_id 			= $result['sol_id'];
			$ano 				= $result['ano'];
			$con_numero_processo= $result['con_numero_processo'];
			$con_ano_processo 	= $result['con_ano_processo'];
			$mod_descricao 		= $result['mod_descricao'];
			$ite_descricao		= $result['ite_descricao'];
			$ite_tipo 			= $result['ite_tipo'];
			$sol_data = implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
			$sol_hora = substr($result['sol_data_cadastro'],11,5);
			$sol_breve_historico 	= nl2br($result['sol_breve_historico']);
			$sol_memorial 			= nl2br($result['sol_memorial']);
			$sol_anexo	 			= $result['sol_anexo'];
			$sts_status 			= $result['sts_status'];
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
            <form name='form_cadastro_empresas' id='form_cadastro_empresas' enctype='multipart/form-data' method='post' action='cadastro_empresas.php?pagina=cadastro_empresas&action=editar&emp_id=$emp_id$autenticacao'>
                <div class='titulo'> $page &raquo; Exibir </div>
				<img class='hand' style='float:right;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('solicitacao_imprimir.php?sol_id=$sol_id$autenticacao');>
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' 	href='#historico'>Histórico da Solicitação</a></li>
				  <li><a data-toggle='tab' 					href='#dados'>Dados da Solicitação</a></li>
				</ul>
				<div class='tab-content'>
					<div id='historico' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
								";
								$sql = "SELECT * FROM cliente_solicitacoes 
										INNER JOIN ( cadastro_contratos 
											INNER JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
											INNER JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id 
											LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
											LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico)
										ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
										INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
										LEFT JOIN cliente_status_solicitacoes ON cliente_status_solicitacoes.sts_solicitacao = cliente_solicitacoes.sol_id 
										WHERE ges_contato = :ges_contato AND
											  sol_id = :sol_id AND sts_status <> 5
										GROUP BY sts_id
										ORDER BY sts_data ASC";
								$stmt = $PDO->prepare($sql);	
								$stmt->bindParam(':ges_contato', $_SESSION['contato_id']);
								$stmt->bindParam(':sol_id', $sol_id);
								$stmt->execute();
								$rows = $stmt->rowCount();
								if($rows > 0)
								{
									echo "<section id='cd-timeline' class='cd-container'>";
									while($result = $stmt->fetch())
									{
										$sol_id 		= $result['sol_id'];
										$sts_observacao = $result['sts_observacao'];
										$sts_data		= implode("/",array_reverse(explode("-",substr($result['sts_data'],0,10))));
										$sts_hora		= substr($result['sts_data'],11,5);
										$sts_status 	= $result['sts_status'];
										$sts_anexo	 	= $result['sts_anexo'];
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
										<div class='cd-timeline-block'>
											<div class='cd-timeline-img cd-location'>
												<img src='../imagens/cd-icon-location.svg' alt='Location'>
											</div> <!-- cd-timeline-img -->
								
											<div class='cd-timeline-content'>
												<h2></h2>
												<p><b>Status:</b> ".$sts_status."
												<p><b>Observações:</b> ".$sts_observacao."
												";if($sts_anexo != ''){ echo "<p><b>Anexo:</b> <a href='$sts_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp;						
												<span class='cd-date'>".$sts_data."<br>às ".$sts_hora."</span>
											</div> <!-- cd-timeline-content -->
										</div> <!-- cd-timeline-block -->
										
										";
									}
									echo "</section>";
								}
								echo "
								</td>
							</tr>
						</table>
					</div>
					<div id='dados' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br>
									<label>Protocolo:</label> 				<div class='sol_exibir'>$ano.$sol_id</div>
									<label>Data da Solicitação:</label>  	<div class='sol_exibir'>$sol_data às $sol_hora</div>
									<label>Status Atual:</label>  			<div class='sol_exibir'>$sts_status</div>
									<label>Contrato:</label>  				<div class='sol_exibir'>$con_numero_processo/$con_ano_processo ($mod_descricao)</div>
									<label>Item do Contrato:</label>  		<div class='sol_exibir'>$ite_descricao ($ite_tipo)</div>
									<label>Breve Histórico:</label>  		<div class='sol_exibir'>$sol_breve_historico</div>
									<label>Memorial:</label>  				<div class='sol_exibir'>$sol_memorial</div>
									";if($sol_anexo != ''){ echo "<label>Anexo:</label> <div class='sol_exibir'><a href='$sol_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a></div>";} echo " &nbsp;							
								</td>
							</tr>
						</table>						
					</div>
				</div>
				<br>
				<center>				
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_consultar.php?pagina=solicitacoes_consultar$autenticacao'; value='Voltar'/></center>
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
