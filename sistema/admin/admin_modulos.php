<?php
session_start (); 
$pagina_link = 'admin_modulos';
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
    $page = "Administradores &raquo; <a href='admin_modulos.php?pagina=admin_modulos".$autenticacao."'>Módulos</a>";
	$mod_id = $_GET['mod_id'];        
    if($action == "adicionar")
    {
        $mod_nome = $_POST['mod_nome'];
        $sql = "INSERT INTO admin_modulos (
        mod_nome
        ) 
        VALUES 
        (
        :mod_nome
        )";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':mod_nome', 	$mod_nome);
        if($stmt->execute())
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
        $mod_nome = $_POST['mod_nome'];
        $sql = "UPDATE admin_modulos SET 
				 mod_nome = :mod_nome
				 WHERE mod_id = :mod_id";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':mod_nome', 	$mod_nome);
        $stmt->bindParam(':mod_id', 	$mod_id);
        if($stmt->execute())
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
        $sql = "DELETE FROM admin_modulos WHERE mod_id = :mod_id ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':mod_id', $mod_id);
        if($stmt->execute())
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Exclusão realizada com sucesso<br><br>'+
                '<input value=\' OK \' type=\'button\'  class=\'close_janela\'>' );
            </SCRIPT>
                ";
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Este módulo não pode ser excluído pois está relacionado com algum setor.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM admin_modulos 
            ORDER BY mod_nome ASC
            LIMIT :primeiro_registro, :num_por_pagina ";		
	$stmt = $PDO->prepare($sql);	
	$stmt->bindParam(':primeiro_registro', $primeiro_registro);
	$stmt->bindParam(':num_por_pagina', $num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "admin_modulos")
    {
        echo "
            <div class='titulo'> $page  </div>
            <div id='botoes'><input value='Novo Módulo' type='button' onclick=javascript:window.location.href='admin_modulos.php?pagina=admin_modulos_adicionar".$autenticacao."'; /></div>
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_first'>Nome</td>
                        <td class='titulo_last' align='center'>Gerenciar</td>
                    </tr>";
                    $c=0;
					while($result = $stmt->fetch())
                    {
                        $mod_id 	= $result['mod_id'];
                        $mod_nome 	= $result['mod_nome'];
                        
                        if ($c == 0)
                        {
                         $c1 = "linhaimpar";
                         $c=1;
                        }
                        else
                        {
                        $c1 = "linhapar";
                         $c=0;
                        } 
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
                            <a title='Submódulos' href='admin_submodulos.php?pagina=admin_submodulos&mod_id=$mod_id&$autenticacao'><img border='0' src='../imagens/icon-submodulo.png'></a>
                            <a title='Editar' href='admin_modulos.php?pagina=admin_modulos_editar&mod_id=$mod_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
                            <a title='Excluir' onclick=\"
                                abreMask(
                                    'Deseja realmente excluir o módulo <b>$mod_nome</b>?<br><br>'+
                                    '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'admin_modulos.php?pagina=admin_modulos&action=excluir&mod_id=$mod_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                \">
                                <img border='0' src='../imagens/icon-excluir.png'></i>
                            </a>
                        </div>
                        ";
                        echo "<tr class='$c1'>
                                  <td>$mod_nome</td>
                                  <td align=center><div id='normal-button-$mod_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&pagina=admin_modulos".$autenticacao."";
					$cnt = "SELECT COUNT(*) FROM admin_modulos ";
					$stmt = $PDO->prepare($cnt);
					include("../mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br>Não há nenhum módulo cadastrado.";
            }
    }
    if($pagina == 'admin_modulos_adicionar')
    {
        echo "	
        <form name='form_admin_modulos' id='form_admin_modulos' enctype='multipart/form-data' method='post' action='admin_modulos.php?pagina=admin_modulos&action=adicionar$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0'>
                <tr>
                    <td align='center'>
                        <input name='mod_nome' id='mod_nome' placeholder='Nome do Módulo'>
                        <br>
                        <br>
                        <center>
                        <input type='submit' id='bt_admin_modulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_modulos.php?pagina=admin_modulos".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
            
        </form>
        ";
    }
    
    if($pagina == 'admin_modulos_editar')
    {
        $sql = "SELECT * FROM admin_modulos WHERE mod_id = :mod_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':mod_id', $mod_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
        if($rows > 0)
        {
			$mod_nome = $stmt->fetch(PDO::FETCH_OBJ)->mod_nome;
            echo "
            <form name='form_admin_modulos' id='form_admin_modulos' enctype='multipart/form-data' method='post' action='admin_modulos.php?pagina=admin_modulos&action=editar&mod_id=$mod_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
                <table align='center' cellspacing='0'>
                    <tr>
                        <td align='center'>
                            <input name='mod_nome' id='mod_nome' value='$mod_nome' placeholder='Nome do Módulo'>
                            <br><br>
                            <center>
                            <input type='submit' id='bt_admin_modulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_modulos.php?pagina=admin_modulos$autenticacao'; value='Cancelar'/></center>
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