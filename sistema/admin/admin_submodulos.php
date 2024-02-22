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
    $mod_id = $_GET['mod_id'];
	$sub_id = $_GET['sub_id'];
       
    $page = "Administradores &raquo; <a href='admin_modulos.php?pagina=admin_modulos".$autenticacao."'>Módulos</a> &raquo; <a href='admin_submodulos.php?pagina=admin_submodulos&mod_id=$mod_id".$autenticacao."'>Submódulos</a>";
    if($action == "adicionar")
    {
        $sub_nome = $_POST['sub_nome'];
        $sub_link = $_POST['sub_link'];
        $sql = "INSERT INTO admin_submodulos (
        sub_modulo,
        sub_nome,
        sub_link
        ) 
        VALUES 
        (
        :mod_id,
        :sub_nome,
        :sub_link
        )";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':mod_id', 	$mod_id);
        $stmt->bindParam(':sub_nome', 	$sub_nome);
        $stmt->bindParam(':sub_link', 	$sub_link);
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
        $sub_nome = $_POST['sub_nome'];
        $sub_link = $_POST['sub_link'];
        $sql = "UPDATE admin_submodulos SET 
				 sub_nome = :sub_nome,
				 sub_link = :sub_link
				 WHERE sub_id = :sub_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':sub_nome', 	$sub_nome);
        $stmt->bindParam(':sub_link', 	$sub_link);
        $stmt->bindParam(':sub_id', 	$sub_id);
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
        $sql = "DELETE FROM admin_submodulos WHERE sub_id = :sub_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':sub_id', $sub_id);
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
                '<img src=../imagens/x.png> Este submódulo não pode ser excluído pois está relacionado com algum setor.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM admin_submodulos 
            LEFT JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo
            WHERE mod_id = :mod_id
            ORDER BY mod_nome ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':mod_id', 			$mod_id);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
	if($pagina == "admin_submodulos")
    {
        echo "
            <div class='titulo'> $page  </div>
            <div id='botoes'><input value='Novo Submódulo' type='button' onclick=javascript:window.location.href='admin_submodulos.php?pagina=admin_submodulos_adicionar&mod_id=$mod_id".$autenticacao."'; /></div>
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_first'>Nome</td>
                        <td class='titulo_tabela'>Link</td>
                        <td class='titulo_last' align='center'>Gerenciar</td>
                    </tr>";
                    $c=0;
                    while($result = $stmt->fetch())
                    {
                        $sub_id 	= $result['sub_id'];
                        $sub_nome 	= $result['sub_nome'];
                        $sub_link 	= $result['sub_link'];
                        
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
                                $('#normal-button-$sub_id').toolbar({content: '#user-options-$sub_id', position: 'top', hideOnClick: true});
                                $('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
                                $('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
                                $('#button-left').toolbar({content: '#user-options', position: 'left'});
                                $('#button-right').toolbar({content: '#user-options', position: 'right'});
                                $('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
                            });
                        </script>
                        <div id='user-options-$sub_id' class='toolbar-icons' style='display: none;'>
                            <a title='Editar' href='admin_submodulos.php?pagina=admin_submodulos_editar&mod_id=$mod_id&sub_id=$sub_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
                            <a title='Excluir' onclick=\"
                                abreMask(
                                    'Deseja realmente excluir o submódulo <b>$sub_nome</b>?<br><br>'+
                                    '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'admin_submodulos.php?pagina=admin_submodulos&action=excluir&mod_id=$mod_id&sub_id=$sub_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                \">
                                <img border='0' src='../imagens/icon-excluir.png'></i>
                            </a>
                        </div>
                        ";
                        echo "<tr class='$c1'>
                                  <td>$sub_nome</td>
                                  <td>$sub_link</td>
                                  <td align=center><div id='normal-button-$sub_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&pagina=admin_submodulos&mod_id=$mod_id".$autenticacao."";
					$cnt = "SELECT COUNT(*) FROM admin_modulos
							LEFT JOIN admin_submodulos ON admin_submodulos.sub_modulo = admin_modulos.mod_id 
							WHERE mod_id = :mod_id ";   
					$stmt = $PDO->prepare($cnt);
					$stmt->bindParam(':mod_id', $mod_id);
                    include("../mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br>Não há nenhum submódulo cadastrado.";
            }
    }
    if($pagina == 'admin_submodulos_adicionar')
    {
        echo "	
        <form name='form_admin_submodulos' id='form_admin_submodulos' enctype='multipart/form-data' method='post' action='admin_submodulos.php?pagina=admin_submodulos&action=adicionar&mod_id=$mod_id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0'>
                <tr>
                    <td align='center'>
                        <input name='sub_nome' id='sub_nome' placeholder='Nome do Submódulo'>
                        <br>
                        <br>
                        <input name='sub_link' id='sub_link' placeholder='Link'>
                        <br>
                        <br>
                        <center>
                        <input type='submit' id='bt_admin_submodulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_submodulos.php?pagina=admin_submodulos&mod_id=$mod_id".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'admin_submodulos_editar')
    {
        $sql = "SELECT * FROM admin_submodulos WHERE sub_id = :sub_id";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':sub_id', $sub_id);
		$stmt->execute();
		$rows = $stmt->rowCount();    	
        if($rows > 0)
        {
			$result = $stmt->fetch();
            $sub_nome = $result['sub_nome'];
            $sub_link = $result['sub_link'];
            echo "
            <form name='form_admin_submodulos' id='form_admin_submodulos' enctype='multipart/form-data' method='post' action='admin_submodulos.php?pagina=admin_submodulos&action=editar&mod_id=$mod_id&sub_id=$sub_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
                <table align='center' cellspacing='0'>
                <tr>
                    <td align='center'>
                            <input name='sub_nome' id='sub_nome' value='$sub_nome' placeholder='Nome do Submódulo'>
                            <br><br>
                            <input name='sub_link' id='sub_link' value='$sub_link' placeholder='Link'>
                            <br><br>
                            <center>
                            <input type='submit' id='bt_admin_submodulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_submodulos.php?pagina=admin_submodulos&mod_id=$mod_id$autenticacao'; value='Cancelar'/></center>
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