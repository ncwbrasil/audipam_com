<?php
session_start (); 
$pagina_link = 'maladireta_gerar';
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
    $page = "Mala Direta &raquo; <a href='maladireta_gerar.php?pagina=maladireta_gerar".$autenticacao."'>Gerar</a> ";
	$agi_id = $_GET['agi_id'];
	
	$filtro = $_REQUEST['filtro'];
	$fil_orgao = $_REQUEST['fil_orgao'];
	if($fil_orgao == '')
	{
		$fil_orgao_n = "Destinatários";
	}
	else
	{
		$fil_orgao_n = $fil_orgao;
		
	}

	$fil_empresa = $_REQUEST['fil_empresa'];
	if($fil_empresa == '')
	{
		$fil_empresa_n = "Remetente";
	}
	else
	{
		$fil_empresa_n = $fil_empresa;
		
	}

	$fil_uf = $_REQUEST['fil_uf'];
	if($fil_uf == '')
	{
		$fil_uf_n = "UF";
	}
	else
	{
		$fil_uf_n = $fil_uf;
		
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
	
    if($pagina == "maladireta_gerar")
    {
        echo "
		<div class='titulo'> $page  </div>			
		<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='maladireta_gerar_imprimir.php?pagina=maladireta_gerar".$autenticacao."&filtro=1'>
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
			<select name='fil_orgao' id='fil_orgao' >
				<option value='$fil_orgao' >$fil_orgao_n</option>
				<option value='Prefeitura'>Prefeitura</option>
				<option value='Câmara'>Câmara</option>
				<option value='Autarquia'>Autarquia</option>
				<option value='Fundação'>Fundação</option>
				<option value='Empresa Pública'>Empresa Pública</option>
				<option value='Sociedade de Economia Mista'>Sociedade de Economia Mista</option>
				<option value='Instituto de Previdência'>Instituto de Previdência</option>
				<option value='Consórcio'>Consórcio</option>
				<option value='Agência Reguladora'>Agência Reguladora</option>
				<option value='Conselho Profissional'>Conselho Profissional</option>
				<option value='Sindicato'>Sindicato</option>
				<option value='Entidade de Classe'>Entidade de Classe</option>
				<option value=''>Todos</option>
			</select>				
			<select name='fil_uf' id='fil_uf' >
				<option value='$fil_uf' >$fil_uf_n</option>
				"; 
				$sql = "SELECT * FROM end_uf
						ORDER BY uf_sigla";
				$stmt_cat = $PDO->prepare($sql);
				$stmt_cat->execute();
				while($result_cat = $stmt_cat->fetch())
				{
					echo "<option value='".$result_cat['uf_id']."'>".$result_cat['uf_sigla']."</option>";
				}
				echo "
				<option value=''>Todos</option>
			</select>				
			<input type='submit' value='Gerar Mala Direta'  formtarget='_blank'> 
			</form>            
		</div>			
		";
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
