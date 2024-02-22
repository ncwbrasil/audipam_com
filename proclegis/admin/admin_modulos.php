<?php 
$pagina_link = 'admin_modulos';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include('header.php'); ?>
</head>
<body>	
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Administradores &raquo; <a href='admin_modulos/view'>Módulos</a>";
            if(isset($_GET['mod_id'])){$mod_id = $_GET['mod_id'];}      
            if($action == "adicionar")
            {
                $mod_nome = $_POST['mod_nome'];
                $mod_img = $_POST['mod_img'];
                $sql = "INSERT INTO admin_modulos set
                        mod_nome = :mod_nome,
                        mod_img = :mod_img
                        ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':mod_nome', 	$mod_nome);
                $stmt->bindParam(':mod_img', 	$mod_img);
                if($stmt->execute())
                {		
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            
            if($action == 'editar')
            {
                $mod_nome = $_POST['mod_nome'];
                $mod_img = $_POST['mod_img'];
                $sql = "UPDATE admin_modulos SET 
                        mod_nome = :mod_nome,
                        mod_img = :mod_img
                        WHERE mod_id = :mod_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':mod_nome', 	$mod_nome);
                $stmt->bindParam(':mod_img', 	$mod_img);
                $stmt->bindParam(':mod_id', 	$mod_id);
                if($stmt->execute())
                {
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }
            }
            
            if($action == 'excluir')
            {
                $sql = "DELETE FROM admin_modulos WHERE mod_id = :mod_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':mod_id', $mod_id);
                if($stmt->execute())
                {
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }
            }
            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $sql = "SELECT * FROM admin_modulos 
                    ORDER BY mod_nome ASC
                    LIMIT :primeiro_registro, :num_por_pagina ";		
            $stmt = $PDO_PROCLEGIS->prepare($sql);	
            $stmt->bindParam(':primeiro_registro', $primeiro_registro);
            $stmt->bindParam(':num_por_pagina', $num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                echo "
                    <div class='titulo'> $page  </div>
                    <div id='botoes'>
                        <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                    </div>
                    ";
                    if ($rows > 0)
                    {
                        echo "
                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                            <tr>
                                <td class='titulo_first'>Nome</td>
                                <td class='titulo_last' align='right'>Gerenciar</td>
                            </tr>";
                            $c=0;
                            while($result = $stmt->fetch())
                            {
                                $mod_id 	= $result['mod_id'];
                                $mod_nome 	= $result['mod_nome'];
                                $mod_img 	= $result['mod_img'];
                                
                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                echo "<tr class='$c1'>
                                        <td>$mod_img  &nbsp; $mod_nome</td>
                                        <td align=center>
                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$mod_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                    \">	<i class='far fa-trash-alt'></i>
                                                </div>
                                                <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$mod_id\");'><i class='fas fa-pencil-alt'></i></div>
                                                <div class='g_status' title='Submódulos' onclick='verificaPermissao(".$permissoes["edit"].",\"admin_submodulos/$mod_id/view\");'><i class='fas fa-list-ul'></i></div>											
                                        </td>
                                    </tr>";
                            }
                            echo "</table>";
                            //$variavel = "&pagina=admin_modulos";
                            $cnt = "SELECT COUNT(*) FROM admin_modulos ";
                            $stmt = $PDO_PROCLEGIS->prepare($cnt);
                            include("../../core/mod_includes/php/paginacao.php");
                    }
                    else
                    {
                        echo "<br><br><br>Não há nenhum módulo cadastrado.";
                    }
            }
            if($pagina == 'add')
            {
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='admin_modulos/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                            <label>Nome do módulo:</label> <input name='mod_nome' id='mod_nome' placeholder='Nome do Módulo' class='obg'>
                            <p><label>Ícone:</label> <input name='mod_img' id='mod_img' placeholder='Ícone'>						
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' id='bt_admin_modulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_modulos/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {
                $sql = "SELECT * FROM admin_modulos WHERE mod_id = :mod_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':mod_id', $mod_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    $mod_nome = $result['mod_nome'];
                    $mod_img = $result['mod_img'];
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='admin_modulos/view/editar/$mod_id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <label>Nome do módulo:</label> <input name='mod_nome' id='mod_nome' value='$mod_nome' placeholder='Nome do Módulo' class='obg'>	
                                <p><label>Ícone:</label> <input name='mod_img' id='mod_img' value='$mod_img' placeholder='Ícone'>							
                            </div>
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' id='bt_admin_modulos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_modulos/view'; value='Cancelar'/></center>
                            </center>
                        </div>                           
                    </form>
                    ";
                }
            }
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>