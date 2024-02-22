<?php
session_start (); 
$pagina_link = 'agenda_gerenciar';
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
    $page = "Cadastros &raquo; <a href='agenda_gerenciar.php?pagina=agenda_gerenciar".$autenticacao."'>Agendas</a>";
	$age_id = $_GET['age_id'];
	$age_empresa = $_POST['age_empresa'];
	
	$dados = array_filter(array(
		'age_empresa' 			=> $age_empresa
	));
	if($action == "adicionar")
    {
        $sql = "INSERT INTO agenda_gerenciar SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
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
    
    if($action == 'excluir')
    {
       	$sql = "DELETE FROM agenda_gerenciar WHERE age_id = :age_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':age_id',$age_id);
        if($stmt->execute())
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Exclusão realizada com sucesso<br><br>'+
                '<input value=\' OK \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao realizar exclusão.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
	
    $num_por_pagina = 20;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM agenda_gerenciar 
			LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = agenda_gerenciar.age_empresa
			ORDER BY emp_fantasia ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "agenda_gerenciar")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Nova Agenda' type='button' onclick=javascript:window.location.href='agenda_gerenciar.php?pagina=agenda_gerenciar_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Empresa</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$age_id 			= $result['age_id'];
					$emp_nome_razao		= $result['emp_nome_razao'];
					$emp_fantasia		= $result['emp_fantasia'];
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$age_id').toolbar({content: '#user-options-$age_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$age_id' class='toolbar-icons' style='display: none;'>
						<a href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id$autenticacao'><img border='0' src='../imagens/icon-agenda2.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir a agenda da empresa <b>$emp_fantasia</b>?<br><b>Atenção:</b> todos itens da agenda serão excluídos permanentemente.<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'agenda_gerenciar.php?pagina=agenda_gerenciar&action=excluir&age_id=$age_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png' ></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
								<td><a href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id$autenticacao'>$emp_fantasia</a><br><span class='detalhe'>$emp_nome_razao</span></td>
							  	<td align=center><div id='normal-button-$age_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=agenda_gerenciar".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM agenda_gerenciar ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br><br>Não há nenhuma agenda cadastrada.";
		}
    }
    if($pagina == 'agenda_gerenciar_adicionar')
    {
        echo "	
		<form name='form_agenda_gerenciar' id='form_agenda_gerenciar' enctype='multipart/form-data' method='post' action='agenda_gerenciar.php?pagina=agenda_gerenciar&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
					<table align='center' cellspacing='0' width='100%'>
						<tr>
							<td align='left'>
								<br>
								<label>Empresa:</label> 
								<div class='suggestion' style='width:80%;'>
									<input name='age_empresa' id='age_empresa' placeholder='ID' autocomplete='off'  type='hidden' />
									<input style='width:100%;' name='age_empresa_nome' id='age_empresa_nome' type='text' placeholder='Contratante: Digite o nome ou CNPJ/CPF' autocomplete='off' />
									<div class='suggestionsBox' id='suggestions' style='display: none;'>
										<div class='suggestionList' id='autoSuggestionsList'>
											&nbsp;
										</div>
									</div>
								</div>
								<p>
								<br><br>
								<center>
								<div id='erro' align='center'>&nbsp;</div>
								<input type='button' id='bt_agenda_gerenciar' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
								<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar.php?pagina=agenda_gerenciar".$autenticacao."'; value='Cancelar'/></center>
								</center>
							</td>
						</tr>
					</table>
				</div>
			</div>   
        </form>
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