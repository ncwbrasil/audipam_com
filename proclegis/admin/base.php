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


            $sql = "SELECT * FROM _base 
                    
                    ";		
            $stmt = $PDO_PROCLEGIS->prepare($sql);	
            $stmt->bindParam(':primeiro_registro', $primeiro_registro);
            $stmt->bindParam(':num_por_pagina', $num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
            
                    if ($rows > 0)
                    {
                        echo "
                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                            <tr>
                                <td class='titulo_tabela'>Sessão</td>
                                <td class='titulo_tabela'>Tipo Matéria</td>
                                <td class='titulo_tabela'>Matéria</td>
                                <td class='titulo_tabela'>Ordem</td>
                            </tr>";
                            $c=0;
                            while($result = $stmt->fetch())
                            {
                                $id 	= $result['id'];
                                $sessao 	= $result['sessao'];
                                $numeroini 	= $result['numeroini'];
                                $numerofim 	= $result['numerofim'];
                                $ano 	= $result['ano'];
                                
                                $ordem = 1;

                                

                                for($x = $numeroini; $x <= $numerofim; $x++)
                                {
                                    $materia= "";
                                    $sql = "SELECT * FROM cadastro_materias WHERE tipo = :tipo AND numero = :numero AND ano = :ano  ";		
                                    $stmt_mat = $PDO_PROCLEGIS->prepare($sql);	
                                    $stmt_mat->bindValue(':tipo', 7);
                                    $stmt_mat->bindParam(':numero', $x);
                                    $stmt_mat->bindParam(':ano', $ano);
                                    $stmt_mat->execute();
                                    $rows_mat = $stmt_mat->rowCount();
                                    if($rows_mat > 0)
                                    {
                                        $result_mat = $stmt_mat->fetch();
                                        $materia = $result_mat['id'];


                                        $id_sessao= "";
                                        $sql = "SELECT * FROM cadastro_sessoes_plenarias WHERE numero = :numero ";		
                                        $stmt_ses = $PDO_PROCLEGIS->prepare($sql);	
                                        $stmt_ses->bindValue(':tipo', 7);
                                        $stmt_ses->bindParam(':numero', $sessao);
                                        $stmt_ses->bindParam(':ano', $ano);
                                        $stmt_ses->execute();
                                        $rows_ses = $stmt_ses->rowCount();
                                        if($rows_ses > 0)
                                        {
                                            $result_ses = $stmt_ses->fetch();
                                            $id_sessao = $result_ses['id'];
                                        }
                                        echo "<tr>
                                        <td>$id_sessao</td>
                                        <td>7</td>
                                        <td>$materia</td>
                                        <td>$ordem</td>
                                        </tr>";
                                    }
                                    
                                    
                                
                                $ordem++;
                                }
                              
                            }
                            echo "</table>";
                            
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