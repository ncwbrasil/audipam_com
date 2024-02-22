<?php
session_start (); 
$pagina_link = 'relatorios_contratos';
if(isset($_GET['con_id'])){$sol_id = $_GET['con_id'];}

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
    $page = "Relatórios &raquo; <a href='relatorios_contratos.php?pagina=relatorios_contratos".$autenticacao."'>Contratos</a> ";
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
		$empresa_query = " con_contratado = '".$fil_empresa."' ";
		$sql_fat = "SELECT * FROM cadastro_empresas  WHERE emp_id = :fil_empresa ";
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
		$cliente_query = " con_contratante = '".$fil_cliente."' ";
		$sql_cli = "SELECT * FROM cadastro_empresas WHERE emp_id = :fil_cliente ";
		$stmt_cli = $PDO->prepare($sql_cli);
		$stmt_cli->bindParam(":fil_cliente",$fil_cliente);
		$stmt_cli->execute();
		$result_cli = $stmt_cli->fetch();
		$fil_cliente_n = $result_cli['emp_fantasia'];
	}
	
	$fil_dia_nf = $_REQUEST['fil_dia_nf'];
	if($fil_dia_nf == '')
	{
		$nf_query = " 1 = 1 ";
	}
	else
	{
		$nf_query = " con_dia_emissao_nf = '".str_pad($fil_dia_nf,2,"0",STR_PAD_LEFT)."' ";
	}
	
	$fil_data_inicio_enc = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio_enc'])));
	$fil_data_fim_enc = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim_enc'])));
	if($fil_data_inicio_enc == '' && $fil_data_fim_enc == '')
	{
		$data_query = " 1 = 1 ";
	}
	elseif($fil_data_inicio_enc != '' && $fil_data_fim_enc == '')
	{
		$data_query = " con_final_vig >= '$fil_data_inicio_enc' ";
	}
	elseif($fil_data_inicio_enc == '' && $fil_data_fim_enc != '')
	{
		$data_query = " con_final_vig <= '$fil_data_fim_enc 23:59:59' ";
	}
	elseif($fil_data_inicio_enc != '' && $fil_data_fim_enc != '')
	{
		$data_query = " con_final_vig BETWEEN '$fil_data_inicio_enc' AND '$fil_data_fim_enc 23:59:59' ";
	}
	$fil_con_status = $_REQUEST['fil_con_status'];
	if($fil_con_status == '')
	{
		$status_query = " 1 = 1 ";
		$fil_con_status_n = "Status";
	}
	else
	{
		$status_query = " con_status = '".$fil_con_status."' ";
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
	
    $sql = "SELECT * FROM cadastro_contratos
			LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_contratos.con_contratante
			LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
			WHERE ".$empresa_query." AND ".$cliente_query."  AND ".$nf_query."  AND ".$data_query." AND ".$status_query." AND ".$filtro_query." 
			GROUP BY con_id  
			ORDER BY con_final_vig ASC
			";
  	$stmt = $PDO->prepare($sql);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "relatorios_contratos")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='relatorios_contratos.php?pagina=relatorios_contratos".$autenticacao."&filtro=1'>
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
					echo "<option value='".$result_cat['emp_id']."'>".$result_cat['emp_fantasia']."</option>";
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
				<option value=''>Todos</option>
			</select>
			<input name='fil_dia_nf' id='fil_dia_nf' value='$fil_dia_nf' placeholder='Dia Emissão NF'>
			<input type='text' name='fil_data_inicio_enc' id='fil_data_inicio_enc' placeholder='Data Encerramento Início' value='".implode('/',array_reverse(explode('-',$fil_data_inicio_enc)))."' onkeypress='return mascaraData(this,event);'>
			<input type='text' name='fil_data_fim_enc' id='fil_data_fim_enc' placeholder='Data Encerramento Fim' value='".implode('/',array_reverse(explode('-',$fil_data_fim_enc)))."' onkeypress='return mascaraData(this,event);'>
			<select name='fil_con_status' id='fil_con_status'>
				<option value='$fil_con_status'>$fil_con_status_n</option>
				<option value='Vigente'>Vigente</option>
				<option value='Cancelado'>Cancelado</option>
				<option value='Finalizado'>Finalizado</option>
				<option value='Suspenso'>Suspenso</option>
				<option value=''>Todos</option>
			</select>
			<input type='submit' value='Filtrar' > 
			</form>
			<div id='erro'> </div>
			";
			if($filtro == 1)
			{
				echo "<img class='hand' title='Imprimir Relatório' style='float:right; margin:0 5px;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('relatorio_contratos_imprimir.php?fil_cliente=$fil_cliente&fil_dia_nf=$fil_dia_nf&fil_con_status=$fil_con_status&fil_data_inicio_enc=$fil_data_inicio_enc&fil_data_fim_enc=$fil_data_fim_enc$autenticacao');>";
			}
			echo "
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
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "<tr class='$c1'>
							  <td>
							  	<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span> <br>
								<b>Objeto do Contrato:</b> ".$result['con_objeto']."<br>
								<b>Contrato:</b> ".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")"."<br>
								<b>Encerramento:</b> ".implode("/",array_reverse(explode("-",$result['con_final_vig'])))." <br>
								<b>Dia Emissão NF:</b> ".$result['con_dia_emissao_nf']."<br>
								<b>Periodicidade:</b> ".$result['con_periodicidade']."<br>
								<b>Valor Global:</b> R$ ".number_format($result['con_valor_global_atual'],2,',','.')." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Valor Mensal Vigente:</b> R$ ".number_format($result['con_valor_unitario_atual'],2,',','.')."<br>
								<b>Status:</b> ".$result['con_status']."<br><p>
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
