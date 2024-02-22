<?php
session_start (); 
$pagina_link = 'cadastro_prospeccoes';
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
include		("../mod_topo/altera_foto_perfil.php");
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
    $page = "Cadastros &raquo; <a href='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes".$autenticacao."'>Ícones Prospecções</a>";
	$pro_id = $_GET['pro_id'];
	$pro_nome = $_POST['pro_nome'];
	$pro_icone = $_POST['pro_icone'];
	$dados = array(
		'pro_nome' 		=> $pro_nome,
		'pro_icone'		=> $pro_icone
	);
	if($action == "adicionar")
    {
        $sql = "INSERT INTO cadastro_prospeccoes SET ".bindFields($dados);
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
        $sql = "UPDATE cadastro_prospeccoes SET ".bindFields($dados)." WHERE pro_id = :pro_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['pro_id'] =  $pro_id;
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
       	$sql = "DELETE FROM cadastro_prospeccoes WHERE pro_id = :pro_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':pro_id',$pro_id);
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
    $sql = "SELECT * FROM cadastro_prospeccoes 
			ORDER BY pro_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "cadastro_prospeccoes")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Novo Ícone' type='button' onclick=javascript:window.location.href='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Ícone</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$pro_id 			= $result['pro_id'];
					$pro_nome 		= $result['pro_nome'];
					$pro_icone 		= $result['pro_icone'];
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$pro_id').toolbar({content: '#user-options-$pro_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$pro_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes_editar&pro_id=$pro_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir este parecer?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_prospeccoes.php?pagina=cadastro_prospeccoes&action=excluir&pro_id=$pro_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td><div class='icone_pro'>$pro_icone<br>$pro_nome</div></td>
							  <td align=center><div id='normal-button-$pro_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=cadastro_prospeccoes".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM cadastro_prospeccoes ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br><br>Não há nenhum ícone cadastrado.";
		}
    }
    if($pagina == 'cadastro_prospeccoes_adicionar')
    {
        echo "	
        <form name='form_cadastro_prospeccoes' id='form_cadastro_prospeccoes' enctype='multipart/form-data' method='post' action='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0' width='100%'>
                <tr>
                    <td align='left'>
						<div class='quadro'>
							<div class='formtitulo'>Dados Gerais</div>
							<label>Nome ícone:</label> <input name='pro_nome' id='pro_nome' placeholder='Nome ícone'>								
							<p><label>Ícone:</label> <input name='pro_icone' id='pro_icone' placeholder='Ícone'>							
                        <br><br>
						</div>
                        <center>
						<div id='erro' align='center'>&nbsp;</div>
                        <input type='button' id='bt_cadastro_prospeccoes' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'cadastro_prospeccoes_editar')
    {
        $sql = "SELECT * FROM cadastro_prospeccoes 
				WHERE pro_id = :pro_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':pro_id', $pro_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$pro_nome 	= $result['pro_nome'];
			$pro_icone	= $result['pro_icone'];
			echo "
            <form name='form_cadastro_prospeccoes' id='form_cadastro_prospeccoes' enctype='multipart/form-data' method='post' action='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes&action=editar&pro_id=$pro_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro'>
								<div class='formtitulo'>Dados Gerais</div>
								<label>Nome ícone:</label> <input name='pro_nome' id='pro_nome' value='$pro_nome' placeholder='Nome ícone'>								
								<p><label>Ícone:</label> <input name='pro_icone' id='pro_icone' value='$pro_icone' placeholder='Ícone'>
							<br><br>
							</div>
							<br><br>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_cadastro_prospeccoes' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_prospeccoes.php?pagina=cadastro_prospeccoes$autenticacao'; value='Cancelar'/></center>
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