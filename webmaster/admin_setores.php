<?php
$pagina_link = 'admin_setores';
//include_once("url.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../core/mod_includes/php/connect_sistema.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo include_once("url.php");?>
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
        $page = "Administradores &raquo; <a href='admin_setores/view'>Setores</a>";
        if(isset($_GET['set_id'])){$set_id = $_GET['set_id'];}
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
                        $submodulo 	= $_POST['item_check_'.$sub_id];
                        $consultar 	= $_POST['consultar_'.$sub_id];
                        $adicionar 	= $_POST['adicionar_'.$sub_id];
                        $editar 	= $_POST['editar_'.$sub_id];
                        $excluir 	= $_POST['excluir_'.$sub_id];
                        if($submodulo != '')
                        {
                            $sql = "INSERT INTO admin_setores_permissoes SET 
							sep_setor 		= :ultimo_id,
							sep_modulo 		= :mod_id,
							sep_submodulo 	= :submodulo,
							sep_consultar 	= :sep_consultar,
							sep_adicionar 	= :sep_adicionar,
							sep_editar 		= :sep_editar,
							sep_excluir 	= :sep_excluir
							";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':mod_id', 		$mod_id);
                            $stmt_insert->bindParam(':submodulo', 		$submodulo);
                            $stmt_insert->bindParam(':sep_consultar', 	$consultar);
                            $stmt_insert->bindParam(':sep_adicionar', 	$adicionar);
                            $stmt_insert->bindParam(':sep_editar', 		$editar);
                            $stmt_insert->bindParam(':sep_excluir', 	$excluir);
                            if($stmt_insert->execute())
                            {
                            }
                            else
                            {
                                $erro=1;
                            }
                        }
                    }
                }	

                # WIDGET #
                $sql = "SELECT * FROM dashboard_widgets
                        ";
                $stmt = $PDO->prepare($sql);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0 )
                {
                    while($result = $stmt->fetch())
                    {
                        $wid_id = $result['wid_id'];                        
                        $submodulo 	= $_POST['item_check_dash_'.$wid_id];   
                        if($submodulo != '')
                        {
                            $sql = "INSERT INTO admin_setores_widget SET 
							sew_setor 		= :ultimo_id,
							sew_widget		= :wid_id
							";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':wid_id', 		$wid_id);
                            if($stmt_insert->execute())
                            {
                            }
                            else
                            {
                                $erro=1;
                            }
                        }
                    }
                }


                if($erro == 1)
                {
                    ?>
					<script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }
                else
                {
                    ?>
					<script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
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
                        $consultar 	= $_POST['consultar_'.$sub_id];if($consultar == ""){ $consultar = 0;}						
                        $adicionar 	= $_POST['adicionar_'.$sub_id];if($adicionar == ""){ $adicionar = 0;}
                        $editar 	= $_POST['editar_'.$sub_id];if($editar == ""){ $editar = 0;}
                        $excluir 	= $_POST['excluir_'.$sub_id];if($excluir == ""){ $excluir = 0;}
						
                        $sql = "SELECT * FROM admin_setores_permissoes WHERE sep_setor = :ultimo_id AND sep_modulo = :mod_id AND sep_submodulo = :sub_id ";
                        $stmt_compara = $PDO->prepare($sql);
                        $stmt_compara->bindParam(':ultimo_id', 	$ultimo_id);
                        $stmt_compara->bindParam(':mod_id', 	$mod_id);
                        $stmt_compara->bindParam(':sub_id', 	$sub_id);
                        $stmt_compara->execute();
                        $rows_compara = $stmt_compara->rowCount();
						if($rows_compara == 0 && $submodulo != '')
                        {
                            $sql = "INSERT INTO admin_setores_permissoes SET 
									sep_setor 		= :ultimo_id,
									sep_modulo 		= :mod_id,
									sep_submodulo 	= :submodulo,
									sep_consultar 	= :sep_consultar,
									sep_adicionar 	= :sep_adicionar,
									sep_editar 		= :sep_editar,
									sep_excluir 	= :sep_excluir
									";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':mod_id', 	$mod_id);
                            $stmt_insert->bindParam(':submodulo', 	$submodulo);
							$stmt_insert->bindParam(':sep_consultar', 	$consultar);
                            $stmt_insert->bindParam(':sep_adicionar', 	$adicionar);
                            $stmt_insert->bindParam(':sep_editar', 		$editar);
                            $stmt_insert->bindParam(':sep_excluir', 	$excluir);
                            if($stmt_insert->execute())
                            {
								//echo "Inserido";
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
								//echo "Deletado";
                            }
                            else
                            {
                                $erro=1;
                            }
                        }
						elseif($rows_compara > 0 && $submodulo != '')
						{
							$sep_id = $stmt_compara->fetch(PDO::FETCH_OBJ)->sep_id;
							$sql = "UPDATE admin_setores_permissoes SET 
									sep_setor 		= :ultimo_id,
									sep_modulo 		= :mod_id,
									sep_submodulo 	= :submodulo,
									sep_consultar 	= :sep_consultar,
									sep_adicionar 	= :sep_adicionar,
									sep_editar 		= :sep_editar,
									sep_excluir 	= :sep_excluir
									WHERE sep_id = :sep_id
									";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':mod_id', 	$mod_id);
                            $stmt_insert->bindParam(':submodulo', 	$submodulo);
							$stmt_insert->bindParam(':sep_consultar', 	$consultar);
                            $stmt_insert->bindParam(':sep_adicionar', 	$adicionar);
                            $stmt_insert->bindParam(':sep_editar', 		$editar);
                            $stmt_insert->bindParam(':sep_excluir', 	$excluir);
                            $stmt_insert->bindParam(':sep_id', 	$sep_id);
                            if($stmt_insert->execute())
                            {
								//echo "Atualizado";
                            }
                            else
                            {
                                $erro=1;
                            }
						}
                    }
                }

                # WIDGET #
                $sql = "SELECT * FROM dashboard_widgets ";
                $stmt_itens = $PDO->prepare($sql);
                $stmt_itens->execute();
                $rows_itens = $stmt_itens->rowCount();
                if($rows_itens > 0 )
                {
                    while($result = $stmt_itens->fetch())
                    {
						
                        $wid_id = $result['wid_id'];                        
                        $submodulo = $_POST['item_check_dash_'.$wid_id];                        
                        $sql = "SELECT * FROM admin_setores_widget 
                                WHERE sew_setor = :ultimo_id AND sew_widget = :wid_id ";
                        $stmt_compara = $PDO->prepare($sql);
                        $stmt_compara->bindParam(':ultimo_id', 	$ultimo_id);
                        $stmt_compara->bindParam(':wid_id', 	$wid_id);                        
                        $stmt_compara->execute();
                        $rows_compara = $stmt_compara->rowCount();
						if($rows_compara == 0 && $submodulo != '')
                        {
                            $sql = "INSERT INTO admin_setores_widget SET 
									sew_setor 		= :ultimo_id,
									sew_widget 		= :wid_id
									";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':wid_id', 	$wid_id);                            
                            if($stmt_insert->execute())
                            {
								//echo "Inserido";
                            }
                            else
                            {
                                $erro=1;
                            }
                        }
                        elseif($rows_compara > 0 && $submodulo == '')
                        {
                            $sew_id = $stmt_compara->fetch(PDO::FETCH_OBJ)->sew_id;
                            $sql = "DELETE FROM admin_setores_widget WHERE sew_id = :sew_id ";
                            $stmt_delete = $PDO->prepare($sql);
                            $stmt_delete->bindParam(':sew_id', 	$sew_id);
                            if($stmt_delete->execute())
                            {
								//echo "Deletado";
                            }
                            else
                            {
                                $erro=1;
                            }
                        }
						elseif($rows_compara > 0 && $submodulo != '')
						{
							$sew_id = $stmt_compara->fetch(PDO::FETCH_OBJ)->sew_id;
							$sql = "UPDATE admin_setores_widget SET 
									sew_setor 		= :ultimo_id,
									sew_widget 		= :wid_id
									WHERE sew_id = :sew_id
									";
                            $stmt_insert = $PDO->prepare($sql);
                            $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                            $stmt_insert->bindParam(':wid_id', 	$wid_id); 
                            $stmt_insert->bindParam(':sew_id', 	$sew_id);
                            if($stmt_insert->execute())
                            {
								//echo "Atualizado";
                            }
                            else
                            {
                                $erro=1;
                            }
						}
                    }
                }


				if($erro != 1)
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
			$sql = "DELETE FROM admin_setores WHERE set_id = :set_id ";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':set_id', 	$set_id);
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
        $sql = "SELECT * FROM admin_setores                
				ORDER BY set_nome ASC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);	
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
                            $set_id = $result['set_id'];
                            $set_nome = $result['set_nome'];
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                      <td>$set_nome</td>
                                      <td align=center>
									  	<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$set_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$set_id\");'><i class='fas fa-pencil-alt'></i></div>											
									  </td>
                                  </tr>";
                        }
                        echo "</table>";
                        $variavel = "&pagina=admin_setores".$autenticacao."";
                        $cnt = "SELECT COUNT(*) FROM admin_setores ";
                        $stmt = $PDO->prepare($cnt);
						include("../core/mod_includes/php/paginacao.php");
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum setor cadastrado.";
                }
        }
        if($pagina == 'add')
        {
			echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='admin_setores/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
				<ul class='nav nav-tabs'>
                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                    <li><a data-toggle='tab' href='#dashboard'>Dashboard</a></li>
				</ul>
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<label>Nome do Setor:</label> <input name='set_nome' id='set_nome' placeholder='Nome do Setor' class='obg'>
						<br>
						<br>
                        <label>Permissões:  <br>  <br>  <br> <input type='checkbox' class='todos' onclick='marcardesmarcar();' /> Marcar todos<br/></label>
                       
						";
						$sql = "SELECT * FROM admin_modulos ORDER BY mod_nome ASC";
						$stmt = $PDO->prepare($sql);
						$stmt->execute();
						$rows = $stmt->rowCount();
						if($rows > 0)
						{
							echo "<table width='80%' align='center' cellpadding='5' cellspacing='0'>";
							while($result = $stmt->fetch())
							{
								echo "
								<tr>
									<td class='titulo_tabela' align='center'>".$result['mod_nome']."</td>
									<td class='titulo_tabela' align='center'>Consultar</td>
									<td class='titulo_tabela' align='center'>Adicionar</td>
									<td class='titulo_tabela' align='center'>Editar</td>
									<td class='titulo_tabela' align='center'>Excluir</td>
								</tr>
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
										echo "
										<tr>
											<td><input type='checkbox' class='marcar' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."' > ".$result_submodulo['sub_nome']."</td>
											<td align='center'><input type='checkbox' class='marcar'  name='consultar_".$result_submodulo['sub_id']."' id='consultar_".$result_submodulo['sub_id']."' value='1' ></td>
											<td align='center'><input type='checkbox' class='marcar' name='adicionar_".$result_submodulo['sub_id']."' id='adicionar_".$result_submodulo['sub_id']."' value='1' ></td>
											<td align='center'><input type='checkbox' class='marcar' name='editar_".$result_submodulo['sub_id']."' id='editar_".$result_submodulo['sub_id']."' value='1' ></td>
											<td align='center'><input type='checkbox' class='marcar'  name='excluir_".$result_submodulo['sub_id']."' id='excluir_".$result_submodulo['sub_id']."' value='1' ></td>
										</tr>
										";
									}
								}
								else	
								{
									echo "<tr><td>Não há submódulos.</td><tr>";
								}
								
							}
							echo "</table>";
						}
						echo "
                    </div>
                    <div id='dashboard' class='tab-pane fade in'>
                        ";
                        $sql = "SELECT * FROM dashboard_widgets 
                                GROUP BY wid_tipo";
                        $stmt = $PDO->prepare($sql);
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if($rows > 0)
                        {
                            while($result = $stmt->fetch())
                            {
                                echo "<div style='width:100%; display:table;'>";
                                echo "<p class='titulo_tabela' style='padding:10px;'> ".$result['wid_tipo']."";                               
                                echo "
                                <div style='width:20%; float:left;'>
                                    <img src='../core/imagens/widget-".$result['wid_tipo'].".png' width='180'></label>
                                </div>";
                            
                               
                                $sql = "SELECT * FROM dashboard_widgets                                       
                                        WHERE wid_tipo = :wid_tipo ";
                                $stmt_submodulo = $PDO->prepare($sql);
                                $stmt_submodulo->bindParam(':wid_tipo', $result['wid_tipo']);
                                $stmt_submodulo->execute();
                                $rows_submodulo = $stmt_submodulo->rowCount();
                                if($rows_submodulo > 0)
                                {
                                    $i=0;
                                    echo "<div style='width:80%; float:left;'>";                            
                                    while($result_submodulo = $stmt_submodulo->fetch())
                                    {
                                        echo "
                                        <input type='checkbox' class='marcar' name='item_check_dash_".$result_submodulo['wid_id']."' id='item_check_dash_".$result_submodulo['wid_id']."' value='".$result_submodulo['wid_id']."' > ".$result_submodulo['wid_nome']."
                                        <br>                                                                               
                                        ";
                                    }
                                    echo "</div>";
                                }
                                else	
                                {
                                    echo "Não há widgets.";
                                }
                                echo "</div><br>";
                                
                            }
                           
                        }
                        echo "
                    </div>
					<center>
					<div id='erro' align='center'>&nbsp;</div>
					<input type='submit' id='bt_admin_setores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_setores/view'; value='Cancelar'/></center>
					</center>
				</div>
			</form>
            ";
        }
        
        if($pagina == 'edit')
        {
			$sql = "SELECT * FROM admin_setores WHERE set_id = :set_id ";
            $stmt = $PDO->prepare($sql);
			$stmt->bindParam(':set_id', $set_id);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $set_nome = $stmt->fetch(PDO::FETCH_OBJ)->set_nome;
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='admin_setores/view/editar/$set_id'>
                    <div class='titulo'> $page &raquo; Editar</div>
                   	<ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        <li><a data-toggle='tab' href='#dashboard'>Dashboard</a></li>
					</ul>
					<div class='tab-content'>
						<div id='dados_gerais' class='tab-pane fade in active'>
							<label>Nome do Setor:</label> <input name='set_nome' id='set_nome' value='$set_nome' placeholder='Nome do Setor' class='obg'>
							<br><br>
							<label>Permissões:  <br>  <br>  <br> <input type='checkbox' class='todos' onclick='marcardesmarcar();' /> Marcar todos<br/></label>
                            ";
							$sql = "SELECT * FROM admin_modulos ORDER BY mod_nome ASC";
							$stmt = $PDO->prepare($sql);
							$stmt->execute();
							$rows = $stmt->rowCount();
							if($rows > 0)
							{
								echo "<table width='80%' align='center' cellpadding='5' cellspacing='0'>";
								while($result = $stmt->fetch())
								{
									echo "
									<tr>
										<td class='titulo_tabela' align='center'>".$result['mod_nome']."</td>
										<td class='titulo_tabela' align='center'>Consultar</td>
										<td class='titulo_tabela' align='center'>Adicionar</td>
										<td class='titulo_tabela' align='center'>Editar</td>
										<td class='titulo_tabela' align='center'>Excluir</td>
									</tr>
									";	
									$sql = "SELECT * FROM admin_submodulos 
											LEFT JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo
											WHERE mod_id = :mod_id";
									$stmt_submodulo = $PDO->prepare($sql);
									$stmt_submodulo->bindParam(':mod_id', $result['mod_id']);
									$stmt_submodulo->execute();
									$rows_submodulo = $stmt_submodulo->rowCount();
									if($rows_submodulo > 0)
									{
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
											$consultar = $adicionar = $editar = $excluir = "";
											if($rows_compara > 0)
											{
												
												$result = $stmt_compara->fetch();
												$consultar = $result['sep_consultar'];
												$adicionar = $result['sep_adicionar'];
												$editar = $result['sep_editar'];
												$excluir = $result['sep_excluir'];
												echo "
												<tr>
													<td><input class='marcar'  checked type='checkbox' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."' > ".$result_submodulo['sub_nome']."</td>
													<td align='center'><input class='marcar' "; if($consultar == 1) echo "checked"; echo " type='checkbox' name='consultar_".$result_submodulo['sub_id']."' id='consultar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' "; if($adicionar == 1) echo "checked"; echo " type='checkbox' name='adicionar_".$result_submodulo['sub_id']."' id='adicionar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' "; if($editar == 1) echo "checked"; echo " type='checkbox' name='editar_".$result_submodulo['sub_id']."' id='editar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' "; if($excluir == 1) echo "checked"; echo " type='checkbox' name='excluir_".$result_submodulo['sub_id']."' id='excluir_".$result_submodulo['sub_id']."' value='1' ></td>
												</tr>
												";
											}
											else
											{
												echo "
												<tr>
													<td><input class='marcar' type='checkbox' name='item_check_".$result_submodulo['sub_id']."' id='item_check_".$result_submodulo['sub_id']."' value='".$result_submodulo['sub_id']."' > ".$result_submodulo['sub_nome']."</td>
													<td align='center'><input class='marcar' type='checkbox' name='consultar_".$result_submodulo['sub_id']."' id='consultar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' type='checkbox' name='adicionar_".$result_submodulo['sub_id']."' id='adicionar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' type='checkbox' name='editar_".$result_submodulo['sub_id']."' id='editar_".$result_submodulo['sub_id']."' value='1' ></td>
													<td align='center'><input class='marcar' type='checkbox' name='excluir_".$result_submodulo['sub_id']."' id='excluir_".$result_submodulo['sub_id']."' value='1' ></td>
												</tr>
												";
											}
										}
									}
									else
									{
										echo "<tr><td>Não há submódulos.</td><tr>";
									}
									
								}
								echo "</table>";
							}
							echo "
                        </div>
                        <div id='dashboard' class='tab-pane fade in'>
                            ";
                            $sql = "SELECT * FROM dashboard_widgets 
                                    GROUP BY wid_tipo";
                            $stmt = $PDO->prepare($sql);
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                            if($rows > 0)
                            {
                                while($result = $stmt->fetch())
                                {
                                    echo "<div style='width:100%; display:table;'>";
                                    echo "<p class='titulo_tabela' style='padding:10px;'> ".$result['wid_tipo']."";                               
                                    echo "
                                    <div style='width:20%; float:left;'>
                                        <img src='../core/imagens/widget-".$result['wid_tipo'].".png' width='180'></label>
                                    </div>";
                                
                                
                                    $sql = "SELECT * FROM dashboard_widgets                                       
                                            WHERE wid_tipo = :wid_tipo ";
                                    $stmt_submodulo = $PDO->prepare($sql);
                                    $stmt_submodulo->bindParam(':wid_tipo', $result['wid_tipo']);
                                    $stmt_submodulo->execute();
                                    $rows_submodulo = $stmt_submodulo->rowCount();
                                    if($rows_submodulo > 0)
                                    {
                                        $i=0;
                                        echo "<div style='width:80%; float:left;'>";                            
                                        while($result_submodulo = $stmt_submodulo->fetch())
                                        {



                                            $sql = "SELECT * FROM admin_setores_widget 
													WHERE sew_setor = :set_id AND sew_widget = :wid_id";
											$stmt_compara = $PDO->prepare($sql);
											$stmt_compara->bindParam(':set_id', $set_id);
											$stmt_compara->bindParam(':wid_id', $result_submodulo['wid_id']);
											$stmt_compara->execute();
											$rows_compara = $stmt_compara->rowCount();
											$consultar = $adicionar = $editar = $excluir = "";
											if($rows_compara > 0)
											{
												
												$result = $stmt_compara->fetch();												
                                                echo "
												<input type='checkbox' checked class='marcar' name='item_check_dash_".$result_submodulo['wid_id']."' id='item_check_dash_".$result_submodulo['wid_id']."' value='".$result_submodulo['wid_id']."' > ".$result_submodulo['wid_nome']."
                                                <br>
                                                ";
                                                
											}
											else
											{
												echo "
												<input type='checkbox' class='marcar' name='item_check_dash_".$result_submodulo['wid_id']."' id='item_check_dash_".$result_submodulo['wid_id']."' value='".$result_submodulo['wid_id']."' > ".$result_submodulo['wid_nome']."
                                                <br>
												";
											}                          
                                        }
                                        echo "</div>";
                                    }
                                    else	
                                    {
                                        echo "Não há widgets.";
                                    }
                                    echo "</div><br>";
                                    
                                }
                            
                            }
                            echo "
                        </div>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' id='bt_admin_setores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_setores/view'; value='Cancelar'/></center>
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