<?php
session_start (); 
$pagina_link = 'aux_modalidades';
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
    $page = "Auxiliares &raquo; <a href='aux_modalidades.php?pagina=aux_modalidades".$autenticacao."'>Modalidades</a>";
	$mod_id = $_GET['mod_id'];
	$mod_descricao = trim($_POST['mod_descricao']);
	$mod_status = $_POST['mod_status'];
	$dados = array(
		'mod_descricao' 	=> $mod_descricao,
		'mod_status' 		=> $mod_status
	);
	if($action == "adicionar")
    {
        $sql = "INSERT INTO aux_modalidades SET ".bindFields($dados);
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
        $sql = "UPDATE aux_modalidades SET ".bindFields($dados)." WHERE mod_id = :mod_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['mod_id'] =  $mod_id;
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
        $sql = "DELETE FROM aux_modalidades WHERE mod_id = :mod_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':mod_id',$mod_id);
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
    if($action == 'ativar')
    {
        $sql = "UPDATE aux_modalidades SET mod_status = :mod_status WHERE mod_id = :mod_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':mod_status',1);
        $stmt->bindParam(':mod_id',$mod_id);
        $stmt->execute();
    }
    if($action == 'desativar')
    {
        $sql = "UPDATE aux_modalidades SET mod_status = :mod_status WHERE mod_id = :mod_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':mod_status',0);
        $stmt->bindParam(':mod_id',$mod_id);
        $stmt->execute();
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM aux_modalidades 
            ORDER BY mod_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "aux_modalidades")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Nova Modalidade' type='button' onclick=javascript:window.location.href='aux_modalidades.php?pagina=aux_modalidades_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_tabela'>Descrição</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$mod_id 	= $result['mod_id'];
					$mod_descricao 	= $result['mod_descricao'];
					$mod_status = $result['mod_status'];
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$mod_id').toolbar({content: '#user-options-$mod_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$mod_id' class='toolbar-icons' style='display: none;'>
						";
						if($mod_status == 1)
						{
							echo "<a title='Desativar' href='aux_modalidades.php?pagina=aux_modalidades&action=desativar&mod_id=$mod_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						else
						{
							echo "<a title='Ativar' href='aux_modalidades.php?pagina=aux_modalidades&action=ativar&mod_id=$mod_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						echo "
						<a title='Editar' href='aux_modalidades.php?pagina=aux_modalidades_editar&mod_id=$mod_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(	
								'Deseja realmente excluir a modalidade <b>$mod_descricao</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'aux_modalidades.php?pagina=aux_modalidades&action=excluir&mod_id=$mod_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td>$mod_descricao</td>
							  <td align=center>";
							  if($mod_status == 1)
							  {
								echo "<img border='0' src='../imagens/icon-ativo.png' width='15' height='15'>";
							  }
							  else
							  {
								echo "<img border='0' src='../imagens/icon-inativo.png' width='15' height='15'>";
							  }
							  echo "
							  </td>
							  <td align=center><div id='normal-button-$mod_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=aux_modalidades".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM aux_modalidades ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhuma modalidade cadastrada.";
		}
    }
    if($pagina == 'aux_modalidades_adicionar')
    {
        echo "	
        <form name='form_aux_modalidades' id='form_aux_modalidades' enctype='multipart/form-data' method='post' action='aux_modalidades.php?pagina=aux_modalidades&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0' width='100%'>
                <tr>
                    <td align='left'>
						<div class='quadro'>
							<div class='formtitulo'>Dados Gerais</div>
							<label>Modalidade:</label> <input name='mod_descricao' id='mod_descricao' placeholder='Modalidade'>
							<p><label>Status:</label> <input type='radio' name='mod_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        	<input type='radio' name='mod_status' value='0'> Inativo<br>
                        <br><br>
						</div>
                        <center>
						<div id='erro' align='center'>&nbsp;</div>
                        <input type='button' id='bt_aux_modalidades' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_modalidades.php?pagina=aux_modalidades".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'aux_modalidades_editar')
    {
        $sql = "SELECT * FROM aux_modalidades 
				WHERE mod_id = :mod_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':mod_id', $mod_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
            $mod_descricao 		= $result['mod_descricao'];
			$mod_status 		= $result['mod_status'];
            echo "
            <form name='form_aux_modalidades' id='form_aux_modalidades' enctype='multipart/form-data' method='post' action='aux_modalidades.php?pagina=aux_modalidades&action=editar&mod_id=$mod_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro'>
								<div class='formtitulo'>Dados Gerais</div>
								<p><label>Modalidade:</label> <input name='mod_descricao' id='mod_descricao' value='$mod_descricao' placeholder='Modalidade'>
								<p><label>Status:</label>";
								if($mod_status == 1)
								{
									echo "<input type='radio' name='mod_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										  <input type='radio' name='mod_status' value='0'> Inativo
										 ";
								}
								else
								{
									echo "<input type='radio' name='mod_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										  <input type='radio' name='mod_status' value='0' checked> Inativo
										 ";
								}
								echo "
							<br><br>
							</div>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_aux_modalidades' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_modalidades.php?pagina=aux_modalidades$autenticacao'; value='Cancelar'/></center>
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