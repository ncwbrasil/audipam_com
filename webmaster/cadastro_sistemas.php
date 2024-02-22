<?php
$pagina_link = 'cadastro_sistemas';
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
                
        $page = "Cadastro &raquo; <a href='cadastro_sistemas/view'>Clientes</a>";
        if(isset($_GET['sis_id'])){$sis_id = $_GET['sis_id'];}
        $sis_nome = $_POST['sis_nome'];
        $sis_url = $_POST['sis_url'];
        $sis_dominio = $_POST['sis_dominio'];
        $dados = array_filter(array(
            'sis_nome' 			=> $sis_nome,
            'sis_dominio' 			=> $sis_dominio,
            'sis_url' 			=> $sis_url
        ));
        
        if($action == "adicionar")
        {                                  
            $sql = "INSERT INTO cadastro_sistemas SET ".bindFields($dados);
            $stmt = $PDO->prepare($sql);	
            if($stmt->execute($dados))
            {		
                $sis_id = $PDO->lastInsertId();
               
                //UPLOAD ARQUIVOS
				require_once 'mod_includes/php/lib/WideImage.php';
				$caminho = "../core/uploads/sistemas/";
				foreach($_FILES as $key => $files)
				{
					$files_test = array_filter($files['name']);
					if(!empty($files_test))
					{
						if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
						if(!empty($files["name"]["logo"]))
						{
							$nomeArquivo 	= $files["name"]["logo"];
							$nomeTemporario = $files["tmp_name"]["logo"];
							$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
							$sis_logo	= $caminho;
							$sis_logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($sis_logo));
							$imnfo = getimagesize($sis_logo);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($sis_logo);
								$image = $image->resize(500, 500);
								$image->saveToFile($sis_logo);
							}
							$sql = "UPDATE cadastro_sistemas SET 
									sis_logo 	 = :sis_logo
									WHERE sis_id = :sis_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':sis_logo',$sis_logo);
							$stmt->bindParam(':sis_id',$sis_id);
							if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
						}					
					}
				}
				//
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
            $sql = "UPDATE cadastro_sistemas SET ".bindFields($dados)." WHERE sis_id = :sis_id ";
            $stmt = $PDO->prepare($sql); 
            $dados['sis_id'] =  $sis_id;
            if($stmt->execute($dados))
            {
                
                 //UPLOAD ARQUIVOS
				require_once '../core/mod_includes/php/lib/WideImage.php';
				$caminho = "../core/uploads/sistemas/";
				foreach($_FILES as $key => $files)
				{
					$files_test = array_filter($files['name']);
					if(!empty($files_test))
					{
						if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
						if(!empty($files["name"]["logo"]))
						{
							$nomeArquivo 	= $files["name"]["logo"];
							$nomeTemporario = $files["tmp_name"]["logo"];
							$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
							$sis_logo	= $caminho;
							$sis_logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($sis_logo));
							$imnfo = getimagesize($sis_logo);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($sis_logo);
								$image = $image->resize(500, 500);
								$image->saveToFile($sis_logo);
							}
							$sql = "UPDATE cadastro_sistemas SET 
									sis_logo 	 = :sis_logo
									WHERE sis_id = :sis_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':sis_logo',$sis_logo);
							$stmt->bindParam(':sis_id',$sis_id);
							if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
						}					
					}
				}
				//
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
			$sql = "DELETE FROM cadastro_sistemas WHERE sis_id = :sis_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':sis_id',$sis_id);
            if($stmt->execute())
            {
                unlink($foto_antiga);                            
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
        $fil_nome = $_REQUEST['fil_nome'];
        if($fil_nome == '')
        {
            $nome_query = " 1 = 1 ";
        }
        else
        {
            $fil_nome1 = $fil_nome2 = "%".$fil_nome."%";
            $nome_query = " (sis_nome LIKE :fil_nome ) ";
        }
        
        $sql = "SELECT * FROM cadastro_sistemas 	
				 WHERE ".$nome_query."
                ORDER BY sis_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fil_nome', 	$fil_nome1);
        $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
        $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
        $stmt->execute();
        $rows = $stmt->rowCount();
		if($pagina == "view")
        {
			echo "
            <div class='titulo'> $page  </div>
           	<div id='botoes'>
				<div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>           
                <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                <div class='filtro'>
                    <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_sistemas/view'>
                    <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
                    <input type='submit' value='Filtrar'> 
                    </form>            
                </div>
            </div>
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_tabela' align='left' >Logo</td>
                        <td class='titulo_tabela' align='left' >Sistema</td>
                        <td class='titulo_tabela' align='left' >Domínio</td>
                        <td class='titulo_tabela' align='right'>Gerenciar</td>
                    </tr>";
                    $c=0;
                    while($result = $stmt->fetch())
                    {
                        $sis_id 			= $result['sis_id'];
                        $sis_nome 			= $result['sis_nome'];
                        $sis_url 			= $result['sis_url'];
                        $sis_dominio 			= $result['sis_dominio'];
                        $sis_logo = $result['sis_logo'];                    
                        if($sis_logo == '')
                        {
                            $sis_logo = '../core/imagens/perfil.png';
                        }
                        
                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                        echo "<tr class='$c1'>
                                <td><div class='foto_perfil' style='width:50px; height:50px; background:url($sis_logo) center center; background-size: cover; border-radius:50px;' border='0'></div></td>
                                <td>$sis_nome</td>
                                <td>$sis_dominio</td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$sis_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$sis_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                </td>
                              </tr>";
                    }
                    echo "</table>";
                    $cnt = "SELECT COUNT(*) FROM cadastro_sistemas WHERE ".$nome_query." ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                    $stmt->bindParam(':fil_nome2', 	$fil_nome2);                    				              					
                    include("../core/mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
            }
        }
        if($pagina == 'add')
        {
            echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sistemas/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>                  
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <br><p><label>Nome Sistema:</label> <input name='sis_nome' id='sis_nome' placeholder='Nome Sistema' class='obg'>
                        <p><label>Domínio:</label> <input name='sis_dominio' id='sis_dominio' placeholder='Domínio (https://www.dominio.com.br)' class='obg'>                        							
                        <p><label>URL Base:</label> <input name='sis_url' id='sis_url' placeholder='URL Base' class='obg'>                        							
                        <p><label>Logo:</label> <input type='file' name='sis_logo[logo]' id='sis_logo' placeholder='Logo'>
                    </div>                    
                    
                </div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sistemas/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
        }
        
        if($pagina == 'edit')
        {
            
            $sql = "SELECT * FROM cadastro_sistemas 
                    LEFT JOIN parametros_gerais ON parametros_gerais.par_sistema = cadastro_sistemas.sis_id
                    WHERE sis_id = :sis_id";
            $stmt = $PDO->prepare($sql);	
            $stmt->bindParam(':sis_id', $sis_id);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $result = $stmt->fetch();
                $sis_nome 	= $result['sis_nome'];
                $sis_url	 		= $result['sis_url'];
                $sis_dominio	 		= $result['sis_dominio'];
                
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sistemas/view/editar/$sis_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>          
                    </ul>
                    <div class='tab-content'>           
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <br><input type='hidden' name='sis_id' id='sis_id' value='$sis_id'>
                                <label>Nome Sistema:</label> <input name='sis_nome' id='sis_nome' value='$sis_nome' placeholder='Nome Sistema' class='obg'>
                                <p><label>Domínio:</label> <input name='sis_dominio' id='sis_dominio' value='$sis_dominio' placeholder='Domínio (https://www.dominio.com.br)' class='obg'>                        							
                                <p><label>URL Base:</label> <input name='sis_url' id='sis_url' value='$sis_url' placeholder='URL Base' class='obg'>                           					
                                <p><label>Logo:</label> ";if($result['sis_logo'] != ''){ echo "<img src='".$result['sis_logo']."' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
                                <p><label>Alterar Logo:</label> <input type='file' name='sis_logo[logo]' id='sis_logo'>
                            </div>                                            
                        </div>
                    </div>
                    <center>
                    <div id='erro' align='center'>&nbsp;</div>
                    <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sistemas/view'; value='Cancelar'/></center>
                    </center>
                </form>
                ";
            }
        }	
        ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>