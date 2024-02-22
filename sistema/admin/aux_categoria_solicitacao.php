<?php
session_start (); 
$pagina_link = 'aux_categoria_solicitacao';
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
    $page = "Auxiliares &raquo; <a href='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao".$autenticacao."'>Categorias</a>";
	$cas_id = $_GET['cas_id'];
	$cas_descricao = trim($_POST['cas_descricao']);
	$dados = array(
		'cas_descricao' 	=> $cas_descricao
	);
	if($action == "adicionar")
    {
        $sql = "INSERT INTO aux_categoria_solicitacao SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			require_once '../mod_includes/php/lib/WideImage.php';
			$cas_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/categorias_icone/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["icone"]))
					{
						$nomeArquivo 	= $files["name"]["icone"];
						$nomeTemporario = $files["tmp_name"]["icone"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$cas_icone	= $caminho;
						$cas_icone .= "icon_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($cas_icone));
						$imnfo = getimagesize($cas_icone);
						$img_w = $imnfo[0];	  // largura
						$img_h = $imnfo[1];	  // altura
						if($img_w > 500 || $img_h > 500)
						{
							$image = WideImage::load($cas_icone);
							$image = $image->resize(500, 500);
							$image->saveToFile($cas_icone);
						}
						$sql = "UPDATE aux_categoria_solicitacao SET 
								cas_icone 	 = :cas_icone
								WHERE cas_id = :cas_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':cas_icone',$cas_icone);
						$stmt->bindParam(':cas_id',$cas_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
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
        $sql = "UPDATE aux_categoria_solicitacao SET ".bindFields($dados)." WHERE cas_id = :cas_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['cas_id'] =  $cas_id;
		if($stmt->execute($dados))
        {
			require_once '../mod_includes/php/lib/WideImage.php';
			//UPLOAD ARQUIVOS
			$caminho = "../admin/categorias_icone/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["icone"]))
					{
						$nomeArquivo 	= $files["name"]["icone"];
						$nomeTemporario = $files["tmp_name"]["icone"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$cas_icone	= $caminho;
						$cas_icone .= "icon_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($cas_icone));
						$imnfo = getimagesize($cas_icone);
						$img_w = $imnfo[0];	  // largura
						$img_h = $imnfo[1];	  // altura
						if($img_w > 500 || $img_h > 500)
						{
							$image = WideImage::load($cas_icone);
							$image = $image->resize(500, 500);
							$image->saveToFile($cas_icone);
						}
						$sql = "UPDATE aux_categoria_solicitacao SET 
								cas_icone 	 = :cas_icone
								WHERE cas_id = :cas_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':cas_icone',$cas_icone);
						$stmt->bindParam(':cas_id',$cas_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
				
			
			//
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
        $sql = "DELETE FROM aux_categoria_solicitacao WHERE cas_id = :cas_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':cas_id',$cas_id);
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
        $sql = "UPDATE aux_categoria_solicitacao SET cas_status = :cas_status WHERE cas_id = :cas_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':cas_status',1);
        $stmt->bindParam(':cas_id',$cas_id);
        $stmt->execute();
    }
    if($action == 'desativar')
    {
        $sql = "UPDATE aux_categoria_solicitacao SET cas_status = :cas_status WHERE cas_id = :cas_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':cas_status',0);
        $stmt->bindParam(':cas_id',$cas_id);
        $stmt->execute();
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM aux_categoria_solicitacao 
            ORDER BY cas_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "aux_categoria_solicitacao")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Nova Categoria' type='button' onclick=javascript:window.location.href='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_tabela'>Descrição</td>
					<td class='titulo_tabela' align='center'>Ícone</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$cas_id 	= $result['cas_id'];
					$cas_descricao 	= $result['cas_descricao'];
					$cas_status = $result['cas_status'];
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$cas_id').toolbar({content: '#user-options-$cas_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$cas_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao_editar&cas_id=$cas_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(	
								'Deseja realmente excluir a categoria <b>$cas_descricao</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao&action=excluir&cas_id=$cas_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td>$cas_descricao</td>
							  <td align=center><img border='0' src='".$result['cas_icone']."' width='30' height='30'></td>
							  <td align=center><div id='normal-button-$cas_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=aux_categoria_solicitacao".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM aux_categoria_solicitacao ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhuma categoria cadastrada.";
		}
    }
    if($pagina == 'aux_categoria_solicitacao_adicionar')
    {
        echo "	
        <form name='form_aux_categoria_solicitacao' id='form_aux_categoria_solicitacao' enctype='multipart/form-data' method='post' action='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0' width='100%'>
                <tr>
                    <td align='left'>
						<div class='quadro'>
							<div class='formtitulo'>Dados Gerais</div>
							<label>Categoria:</label> <input name='cas_descricao' id='cas_descricao' placeholder='Categoria'>
							<p><label>Ícone:</label> <input type='file' name='cas_icone[icone]' id='cas_icone'> 
                        <br><br>
						</div>
                        <center>
						<div id='erro' align='center'>&nbsp;</div>
                        <input type='button' id='bt_aux_categoria_solicitacao' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'aux_categoria_solicitacao_editar')
    {
        $sql = "SELECT * FROM aux_categoria_solicitacao 
				WHERE cas_id = :cas_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':cas_id', $cas_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
            $cas_descricao 		= $result['cas_descricao'];
			$cas_icone 			= $result['cas_icone'];
            echo "
            <form name='form_aux_categoria_solicitacao' id='form_aux_categoria_solicitacao' enctype='multipart/form-data' method='post' action='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao&action=editar&cas_id=$cas_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro'>
								<div class='formtitulo'>Dados Gerais</div>
								<p><label>Categoria:</label> <input name='cas_descricao' id='cas_descricao' value='$cas_descricao' placeholder='Categoria'>
								<p><label>Ícone:</label> ";if($cas_icone != ''){ echo "<img src='$cas_icone' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
									<p><label>Alterar Ícone:</label> <input type='file' name='cas_icone[icone]' id='cas_icone'>
							<br><br>
							</div>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_aux_categoria_solicitacao' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_categoria_solicitacao.php?pagina=aux_categoria_solicitacao$autenticacao'; value='Cancelar'/></center>
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
include('../cas_rodape/rodape.php');
?>
</body>
</html>