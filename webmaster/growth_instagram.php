<?php
$pagina_link = 'growth_instagram';
include_once("url.php");
include_once("../core/mod_includes/php/connect_sistema.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Audipam | Gerenciador de Sistemas</title>
    <meta	name="viewport" content="width=device-width, initial-scale=1">
    <meta 	name="author" content="MogiComp">
    <meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link 	rel="shortcut icon" href="../core/imagens/favicon.png">
    <link 	href="../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
    <link 	href="../core/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
    <link 	href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
    <script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
    <script src="../core/mod_includes/js/janela/jquery-ui.js"></script>
    <!-- MENU -->
    <link 	href="../core/mod_menu/css/style.css" rel="stylesheet" > <!-- Resource style -->
    <script src="../core/mod_menu/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="../core/mod_menu/js/jquery.menu-aim.js"></script>
    <script src="../core/mod_menu/js/main.js"></script> <!-- Resource jQuery -->    
    <!-- FIM MENU -->
    <!-- ABAS -->
    <link 	href="../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
    <script src="../core/mod_includes/js/abas/bootstrap.js"></script>
    <!-- ABAS -->
     <!-- Bootstrap core CSS -->
	<!-- Material Design Bootstrap -->
	<link href="../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">
</head>
<body>
	<?php
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	require_once('../core/mod_includes/php/verificapermissao.php');
	include("../core/mod_menu/barra.php");
	?>
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../core/mod_menu/menu.php"); ?>
            
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
        <div class='mensagem'></div>
		<?php
        $page = "Growth &raquo; <a href='growth_instagram/view'>Instagram</a>";
        if(isset($_GET['ins_id'])){$ins_id = $_GET['ins_id'];} 
		if($ins_id == ''){ $ins_id = $_POST['ins_id'];}
        $ins_profile = $_POST['ins_profile'];
        $dados = array_filter(array(
            'ins_cliente' 	=> $_SESSION['cliente_id'],
            'ins_profile' 	=> $ins_profile
        ));
        
        if($action == "adicionar")
        {
            $sql = "INSERT INTO growth_instagram SET ".bindFields($dados);
            $stmt = $PDO->prepare($sql);	
            if($stmt->execute($dados))
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
			
            $sql = "UPDATE growth_instagram SET ".bindFields($dados)." WHERE ins_id = :ins_id ";
            $stmt = $PDO->prepare($sql); 
            $dados['ins_id'] =  $ins_id;
            if($stmt->execute($dados))
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
            unset($_SESSION['action']);
            $sql = "DELETE FROM growth_instagram WHERE ins_id = :ins_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':ins_id',$ins_id);
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
       
        
        
        $num_por_pagina = 100;
        if(!$pag){$primeiro_registro = 0; $pag = 1;}
        else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
        $fil_nome = $_REQUEST['fil_nome'];
        if($fil_nome == '')
        {
            $nome_query = " 1 = 1 ";
        }
        else
        {
            $fil_nome1 = "%".$fil_nome."%";
            $nome_query = " (ins_profile LIKE :fil_nome1  ) ";
        }
        $fil_executado = $_REQUEST['fil_executado'];
        if($fil_executado == '')
        {
            $executado_query = " ins_executado = 0 ";
            $fil_executado_n = "Executado?";
        }
        elseif($fil_executado == 'Todos')
        {
            $executado_query = " 1 = 1 ";
            $fil_executado_n = "Executado?";
        }
        else
        {
            $executado_query = " ins_executado = :fil_executado ";	
            if($fil_executado == "1")
            {
                $fil_executado_n = "Sim";
            }
            elseif($fil_executado == "0")
            {
                $fil_executado_n = "Não";
            }
            		
        }
        
        $sql = "SELECT * FROM growth_instagram 
                WHERE ".$nome_query." AND ".$executado_query."
                ORDER BY ins_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fil_nome1', 	$fil_nome1);
        $stmt->bindParam(':fil_executado', 	$fil_executado);
        $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
        $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
        $stmt->execute();
        $rows = $stmt->rowCount();

        $sql = "SELECT * FROM growth_instagram 
                WHERE ".$nome_query." AND ".$executado_query."
                ORDER BY ins_id DESC
                ";
        $stmt_all = $PDO->prepare($sql);
        $stmt_all->bindParam(':fil_nome1', 	$fil_nome1);
        $stmt_all->bindParam(':fil_executado', 	$fil_executado);
        $stmt_all->execute();
        $rows_all = $stmt_all->rowCount();

        if($pagina == "view")
        {
            echo "
            <div class='titulo'> $page  </div>
            <div id='botoes'>
				<div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                <div class='filtrar'><i class='fas fa-filter'></i></div>
               
                <div class='filtro'>
                    <form name='form_growth_instagram' id='form_growth_instagram' enctype='multipart/form-data' method='post' action='growth_instagram/view'>
                        <input type='text' name='fil_nome' id='fil_nome' placeholder='Profile' value='$fil_nome'>                    
                        <select name='fil_executado' id='fil_executado'>
                            <option value='$fil_executado'>$fil_executado_n</option>
                            <option value='1'>Sim</option> 
                            <option value='0'>Não</option>                                                            
                            <option value='Todos'>Todos</option>
                        </select>
                        <input type='submit' value=' Filtrar '>
                    </form>            
                
                </div>
            </div>  
            $rows_all profiles
            <p>       
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_first' align='left'>Profile</td>
                        <td class='titulo_first' align='center'>Executado?</td>
                        <td class='titulo_last' align='right'>Gerenciar</td>
                    </tr>";
                    $c=0;
                     while($result = $stmt->fetch())
                    {
                        $ins_id 	= $result['ins_id'];
                        $ins_profile 	= $result['ins_profile'];
                        $ins_executado 	= $result['ins_executado'];
                        if($ins_executado == 1)
                        {
                            $executado = "<i class='fas fa-check green'></i>";
                        }
                        else
                        {
                            $executado = "<i class='fas fa-times red'></i>";
                        }
                    
                        $id = $ins_id;
                        //include("../core/mod_includes/modal/popUp.php");
                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
                        echo "<tr class='$c1'>
                                  <td>$ins_profile</td>
                                  <td align='center'>$executado</td>
                                  <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$ins_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_exibir' title='Link' onclick='MyPopUpWin(\"https://instagram.com/".$ins_profile."\", 500, 500); marcarExecutado(\"$ins_id\",this);'><i class='fas fa-external-link-alt'></i></div>
                                  
                                  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM growth_instagram WHERE ".$nome_query." AND  ".$executado_query." ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                    $stmt->bindParam(':fil_executado', 	$fil_executado);
                    include("../core/mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br>Não há nenhum tipo de solicitação cadastrado.";
            }
        }
        if($pagina == 'add')
        {
            echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='growth_instagram/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Tipo de Despesa:</label> <input name='ins_profile' id='ins_profile' placeholder='Tipo de Despesa' class='obg'>
						<br>							
                    </div>
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='growth_instagram/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
        }
        if($pagina == 'edit')
        {
            $sql = "SELECT * FROM growth_instagram WHERE ins_id = :ins_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':ins_id', $ins_id);
            $stmt->execute();
            $rows = $stmt->rowCount();    	
            if($rows > 0)
            {
                $result = $stmt->fetch();
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='growth_instagram/view/editar/$ins_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                            <label>Tipo de Despesa:</label> <input name='ins_profile' id='ins_profile' value='".$result['ins_profile']."' placeholder='Tipo de Despesa' class='obg'>
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='growth_instagram/view'; value='Cancelar'/></center>
						</center>
                    </div>
                </form>
                ";
            }
        }	
        
		?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
    
    <!-- MODAL -->
	<script type="text/javascript" src="../core/mod_includes/js/mdbootstrap/js/jquery-3.4.1.min.js"></script>
	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="../core/mod_includes/js/mdbootstrap/js/popper.min.js"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="../core/mod_includes/js/mdbootstrap/js/bootstrap.min.js"></script>
	<!-- MDB core JavaScript -->
    <script type="text/javascript" src="../core/mod_includes/js/mdbootstrap/js/mdb.min.js"></script>
    
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
    <script src="../core/mod_includes/js/janela/jquery-ui.js"></script>
    
	<script>    
    $(function() 
    {
        $('[data-toggle="modal"]').click(function() 
        {
            var modalId = $(this).data('target');
            $(modalId).modal('show');
        });                        
    });           
    </script>

    <script>
        function MyPopUpWin(url, width, height) {
        var leftPosition, topPosition;
        //Allow for borders.
        leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
        //Allow for title and status bars.
        topPosition = (window.screen.height / 2) - ((height / 2) + 50);
        //Open the window.
        window.open(url, "Window2",
        "status=no,height=" + height + ",width=" + width + ",resizable=yes,left="
        + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY="
        + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
    }
    </script>
    <!-- MODAL -->
</body>
</html>