<?php
session_start (); 
$pagina_link = 'aux_enunciados';
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
    $page = "Auxiliares &raquo; <a href='aux_enunciados.php?pagina=aux_enunciados".$autenticacao."'>Enunciados</a>";
	$enu_id = $_GET['enu_id'];
	$enu_assunto = trim($_POST['enu_assunto']);
	$enu_tema 	 = trim($_POST['enu_tema']);
	$enu_parecer = trim($_POST['enu_parecer']);
	$dados = array(
		'enu_assunto' 	=> $enu_assunto,
		'enu_tema' 		=> $enu_tema,
		'enu_parecer' 	=> $enu_parecer
	);
	if($action == "adicionar")
    {
        $sql = "INSERT INTO aux_enunciados SET ".bindFields($dados);
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
    
    if($action == 'editar')
    {
        $sql = "UPDATE aux_enunciados SET ".bindFields($dados)." WHERE enu_id = :enu_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['enu_id'] =  $enu_id;
		if($stmt->execute($dados))
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Dados alterados com sucesso.<br><br>'+
                '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao alterar dados.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
            </SCRIPT>
            ";
        }
    }
    
    if($action == 'excluir')
    {
        $sql = "DELETE FROM aux_enunciados WHERE enu_id = :enu_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':enu_id',$enu_id);
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
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM aux_enunciados 
            ORDER BY enu_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "aux_enunciados")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Novo Enunciado' type='button' onclick=javascript:window.location.href='aux_enunciados.php?pagina=aux_enunciados_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_tabela'>Assunto</td>
					<td class='titulo_tabela'>Tema</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$enu_id 		= $result['enu_id'];
					$enu_assunto 	= $result['enu_assunto'];
					$enu_tema		= $result['enu_tema'];
					$enu_parecer 	= $result['enu_parecer'];
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$enu_id').toolbar({content: '#user-options-$enu_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$enu_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='aux_enunciados.php?pagina=aux_enunciados_editar&enu_id=$enu_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(	
								'Deseja realmente excluir o enunciado <b>$enu_assunto</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'aux_enunciados.php?pagina=aux_enunciados&action=excluir&enu_id=$enu_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td>$enu_assunto</td>
							  <td>$enu_tema</td>
							  <td align=center><div id='normal-button-$enu_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=aux_enunciados".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM aux_enunciados ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhum enunciado cadastrado.";
		}
    }
    if($pagina == 'aux_enunciados_adicionar')
    {
        echo "	
        <form name='form_aux_enunciados' id='form_aux_enunciados' enctype='multipart/form-data' method='post' action='aux_enunciados.php?pagina=aux_enunciados&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0' width='100%'>
                <tr>
                    <td align='left'>
						<div class='quadro'>
							<div class='formtitulo'>Dados Gerais</div>
							<p><label>Enunciado:</label> <input name='enu_assunto' id='enu_assunto' placeholder='Enunciado'>
							<p><label>Tema:</label> <input name='enu_tema' id='enu_tema' placeholder='Tema'>
							<p><label>Parecer:</label> <textarea name='enu_parecer' id='enu_parecer' placeholder='Parecer'></textarea>
                        <br><br>
						</div>
                        <center>
						<div id='erro' align='center'>&nbsp;</div>
                        <input type='button' id='bt_aux_enunciados' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_enunciados.php?pagina=aux_enunciados".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'aux_enunciados_editar')
    {
        $sql = "SELECT * FROM aux_enunciados 
				WHERE enu_id = :enu_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':enu_id', $enu_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
            $enu_assunto 		= $result['enu_assunto'];
			$enu_tema	 		= $result['enu_tema'];
			$enu_parecer 		= $result['enu_parecer'];
            echo "
            <form name='form_aux_enunciados' id='form_aux_enunciados' enctype='multipart/form-data' method='post' action='aux_enunciados.php?pagina=aux_enunciados&action=editar&enu_id=$enu_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro'>
								<div class='formtitulo'>Dados Gerais</div>
								<p><label>Enunciado:</label> <input name='enu_assunto' id='enu_assunto' value='$enu_assunto' placeholder='Enunciado'>
								<p><label>Tema:</label> <input name='enu_tema' id='enu_tema' value='$enu_tema' placeholder='Tema'>
								<p><label>Parecer:</label> <textarea name='enu_parecer' id='enu_parecer' placeholder='Enunciado'>$enu_parecer</textarea>								
							<br><br>
							</div>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_aux_enunciados' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_enunciados.php?pagina=aux_enunciados$autenticacao'; value='Cancelar'/></center>
                            </center>
						</td>
					</tr>
				</table>
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