<?php
session_start (); 
$pagina_link = 'admin_setores';
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
    $page = "Administradores &raquo; <a href='admin_setores.php?pagina=admin_setores".$autenticacao."'>Setores</a>";
	$set_id = $_GET['set_id'];
    if($action == "adicionar")
    {
        $set_nome = $_POST['set_nome'];
        $sql = "INSERT INTO admin_setores (
        set_nome
        ) 
        VALUES 
        (
        :set_nome
        )";
       	$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':set_nome', 	$set_nome);
        if($stmt->execute())
        {
            $ultimo_id = $PDO->lastInsertId();
            $erro=0;
            $sql = "SELECT * FROM admin_submodulos
                    INNER JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo ";
			$stmt = $PDO->prepare($sql);
            $stmt->execute();
			$rows = $stmt->rowCount();
            if($rows > 0 )
            {
               	while($result = $stmt->fetch())
                {
                    $mod_id = $result['mod_id'];
                    $sub_id = $result['sub_id'];
                    $submodulo = $_POST['item_check_'.$sub_id];
                    if($submodulo != '')
                    {
                        $sql = "INSERT INTO admin_setores_permissoes (sep_setor, sep_modulo, sep_submodulo) VALUES (:ultimo_id, :mod_id, :submodulo) ";
                        $stmt_insert = $PDO->prepare($sql);
            			$stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
						$stmt_insert->bindParam(':mod_id', 		$mod_id);
						$stmt_insert->bindParam(':submodulo', 	$submodulo);
						if($stmt_insert->execute())
                        {
                        }
                        else
                        {
							echo "aa";
                            $erro=1;
                        }
                    }
                }
            }	
            if($erro == 1)
            {
                echo "
                <SCRIPT language='JavaScript'>
                    abreMask(
                    '<img src=../imagens/x.png> Erro ao adicionar módulos.<br><br>'+
                    '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
                </SCRIPT>
                    "; 
            }
            else
            {
                echo "
                <SCRIPT language='JavaScript'>
                    abreMask(
                    '<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
                    '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
                </SCRIPT>
                    ";
            }
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
        $set_nome = $_POST['set_nome'];
        $sql = "UPDATE admin_setores SET 
				 set_nome = :set_nome
				 WHERE set_id = :set_id ";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':set_nome', 	$set_nome);
		$stmt->bindParam(':set_id', 	$set_id);
		if($stmt->execute())
        {
            $ultimo_id = $set_id;
            $erro=0;
            $sql = "SELECT * FROM admin_submodulos 
                          INNER JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo ";
            $stmt_itens = $PDO->prepare($sql);
            $stmt_itens->execute();
			$rows_itens = $stmt_itens->rowCount();
            if($rows_itens > 0 )
            {
                while($result = $stmt_itens->fetch())
                {
                    $mod_id = $result['mod_id'];
                    $sub_id = $result['sub_id'];
                    $submodulo = $_POST['item_check_'.$sub_id];
                    
                    $sql = "SELECT * FROM admin_setores_permissoes WHERE sep_setor = :ultimo_id AND sep_modulo = :mod_id AND sep_submodulo = :sub_id ";
                    $stmt_compara = $PDO->prepare($sql);
					$stmt_compara->bindParam(':ultimo_id', 	$ultimo_id);
					$stmt_compara->bindParam(':mod_id', 	$mod_id);
					$stmt_compara->bindParam(':sub_id', 	$sub_id);
					$stmt_compara->execute();
					$rows_compara = $stmt_compara->rowCount();
                    if($rows_compara == 0 && $submodulo != '')
                    {
                        
                        $sql = "INSERT INTO admin_setores_permissoes (sep_setor, sep_modulo, sep_submodulo) VALUES (:ultimo_id, :mod_id, :submodulo) ";
                        $stmt_insert = $PDO->prepare($sql);
						$stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
						$stmt_insert->bindParam(':mod_id', 	$mod_id);
						$stmt_insert->bindParam(':submodulo', 	$submodulo);
						if($stmt_insert->execute())
                        {
                        }
                        else
                        {
                            $erro=1;
                        }
                    }
                    elseif($rows_compara > 0 && $submodulo == '')
                    {
                        $sep_id = $stmt_compara->fetch(PDO::FETCH_OBJ)->sep_id;
                        $sql = "DELETE FROM admin_setores_permissoes WHERE sep_id = :sep_id ";
						$stmt_delete = $PDO->prepare($sql);
						$stmt_delete->bindParam(':sep_id', 	$sep_id);
						if($stmt_delete->execute())
                        {
                        }
                        else
                        {
                            $erro=1;
                        }
                    }
                }
            }
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
        $sql = "DELETE FROM admin_setores WHERE set_id = :set_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':set_id', 	$set_id);
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
                '<img src=../imagens/x.png> Este setor não pode ser excluído pois está relacionado com algum administrador.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM admin_setores 
            ORDER BY set_nome ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
	$stmt = $PDO->prepare($sql);	
	$stmt->bindParam(':primeiro_registro', $primeiro_registro);
	$stmt->bindParam(':num_por_pagina', $num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
   
    if($pagina == "admin_setores")
    {
        echo "
            <div class='titulo'> $page  </div>
            <div id='botoes'><input value='Novo Setor' type='button' onclick=javascript:window.location.href='admin_setores.php?pagina=admin_setores_adicionar".$autenticacao."'; /></div>
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
                        $set_id = $result['set_id'];
                        $set_nome = $result['set_nome'];
                        
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
                                $('#normal-button-$set_id').toolbar({content: '#user-options-$set_id', position: 'top', hideOnClick: true});
                                $('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
                                $('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
                                $('#button-left').toolbar({content: '#user-options', position: 'left'});
                                $('#button-right').toolbar({content: '#user-options', position: 'right'});
                                $('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
                            });
                        </script>
                        <div id='user-options-$set_id' class='toolbar-icons' style='display: none;'>
                            <a title='Editar' href='admin_setores.php?pagina=admin_setores_editar&set_id=$set_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
                            <a title='Excluir' onclick=\"
                                abreMask(
                                    'Deseja realmente excluir o setor <b>$set_nome</b>?<br><br>'+
                                    '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'admin_setores.php?pagina=admin_setores&action=excluir&set_id=$set_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                \">
                                <img border='0' src='../imagens/icon-excluir.png'></i>
                            </a>
                        </div>
                        ";
                        echo "<tr class='$c1'>
                                  <td>$set_nome</td>
                                  <td align=center><div id='normal-button-$set_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&pagina=admin_setores".$autenticacao."";
					$cnt = "SELECT COUNT(*) FROM admin_setores ";
    				$stmt = $PDO->prepare($cnt);
                    include("../mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br>Não há nenhum setor cadastrado.";
            }
    }
    if($pagina == 'admin_setores_adicionar')
    {
        echo "	
        <form name='form_admin_setores' id='form_admin_setores' enctype='multipart/form-data' method='post' action='admin_setores.php?pagina=admin_setores&action=adicionar$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0'>
                <tr>
                    <td align='left' width='700'>
                        <input name='set_nome' id='set_nome' placeholder='Nome do Setor'>
                        <br>
                        <br>
                        ";
                        $sql = "SELECT * FROM admin_modulos ORDER BY mod_nome ASC";
                        $stmt = $PDO->prepare($sql);
						$stmt->execute();
						$rows = $stmt->rowCount();
						if($rows > 0)
						{
							while($result = $stmt->fetch())
							{
                                echo "
                                <div class='formtitulo'>".$result['mod_nome']."</div>
                                <table width='90%' align='center'>
                                <tr>
                                ";	
                                $sql = "SELECT * FROM admin_submodulos
									    LEFT JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo
									    WHERE mod_id = :mod_id ";
                                $stmt_submodulo = $PDO->prepare($sql);
								$stmt_submodulo->bindParam(':mod_id', $result['mod_id']);
								$stmt_submodulo->execute();
								$rows_submodulo = $stmt_submodulo->rowCount();
								if($rows_submodulo > 0)
                                {
                                    $i=0;
                                    while($result_submodulo = $stmt_submodulo->fetch())
                                    {
                                        $i++;
                                        if($i % 2 == 0 ? $coluna="</td></tr><tr>" : $coluna="</td>")
                                        echo "<td align='left' width='25%'>";
                                        echo "
                                            <input type='checkbox' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."' > ".$result_submodulo['sub_nome']." ";
                                        echo $coluna;
                                    }
                                }
                                else
                                {
                                    echo "<tr><td>Não há submódulos.</td><tr>";
                                }
                                echo "</table>";
                            }
                        }
                        echo "
                        
                        <br><br>
                        <center>
                        <input type='submit' id='bt_admin_setores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_setores.php?pagina=admin_setores".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'admin_setores_editar')
    {
        $sql = "SELECT * FROM admin_setores WHERE set_id = :set_id";
        $stmt = $PDO->prepare($sql);
		$stmt->bindParam(':set_id', $set_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
        if($rows > 0)
        {
            $set_nome = $stmt->fetch(PDO::FETCH_OBJ)->set_nome;
            echo "
            <form name='form_admin_setores' id='form_admin_setores' enctype='multipart/form-data' method='post' action='admin_setores.php?pagina=admin_setores&action=editar&set_id=$set_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
                <table align='center' cellspacing='0'>
                    <tr>
                        <td align='left' width='700'>
                            <input name='set_nome' id='set_nome' value='$set_nome' placeholder='Nome do Setor'>
                            <br><br>";
                            $sql = "SELECT * FROM admin_modulos ORDER BY mod_nome ASC";
                            $stmt = $PDO->prepare($sql);
							$stmt->execute();
							$rows = $stmt->rowCount();
                            if($rows > 0)
                            {
                                while($result = $stmt->fetch())
                                {
                                    echo "
                                    <div class='formtitulo'>".$result['mod_nome']."</div>
                                    <table width='100%'>
                                    <tr>";
                                    $sql = "SELECT * FROM admin_submodulos 
										    LEFT JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo
										    WHERE mod_id = :mod_id";
                                    $stmt_submodulo = $PDO->prepare($sql);
									$stmt_submodulo->bindParam(':mod_id', $result['mod_id']);
									$stmt_submodulo->execute();
									$rows_submodulo = $stmt_submodulo->rowCount();
                                    if($rows_submodulo > 0)
                                    {
                                        $i=0;
                                        while($result_submodulo = $stmt_submodulo->fetch())
                                        {
											$i++;
                                            if($i % 2 == 0 ? $coluna="</td></tr><tr>" : $coluna="</td>")
                                            echo "<td align='left' width='25%'>";
                                            
                                            $sql = "SELECT * FROM admin_setores_permissoes 
                                                    WHERE sep_setor = :set_id AND sep_submodulo = :sub_id";
                                            $stmt_compara = $PDO->prepare($sql);
											$stmt_compara->bindParam(':set_id', $set_id);
											$stmt_compara->bindParam(':sub_id', $result_submodulo['sub_id']);
											$stmt_compara->execute();
											$rows_compara = $stmt_compara->rowCount();
											if($rows_compara > 0)
                                            {
                                                echo "
                                                <input checked type='checkbox' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."' > ".$result_submodulo['sub_nome']." ";
                                            }
                                            else
                                            {
                                                echo "
                                                <input type='checkbox' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."'> ".$result_submodulo['sub_nome']." ";
                                            }
                                            echo $coluna;
                                        }
                                    }
                                    else
                                    {
                                        echo "<tr><td>Não há submódulos.</td><tr>";
                                    }
                                    echo "</table>";
                                }
                            }
                            echo "
                            
                            <br><br>
                            <center>
                            <input type='submit' id='bt_admin_setores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_setores.php?pagina=admin_setores$autenticacao'; value='Cancelar'/></center>
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