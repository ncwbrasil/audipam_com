<?php
$pagina_link = 'cadastro_usuarios';
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
                
        $page = "Cadastro &raquo; <a href='cadastro_usuarios/view'>Usuários</a>";
        $usu_id = $_GET['usu_id'];
        $usu_setor = $_POST['usu_setor'];
        $usu_nome = $_POST['usu_nome'];
        $usu_email = $_POST['usu_email'];
        $usu_senha = hash('sha512',$_POST['usu_senha']);
        $usu_status = $_POST['usu_status'];      
        $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':usu_id',$usu_id);
        $stmt->execute();
        $row = $stmt->rowCount();
        if($row > 0)
        {
            $senhacompara = $stmt->fetch(PDO::FETCH_OBJ)->usu_senha;		
        }
        if($_POST['usu_senha'] == $senhacompara)
        {
            $usu_senha = $senhacompara;
        }
        $dados = array_filter(array(
            'usu_cliente' 		=> $_SESSION['cliente_id'],
            'usu_setor' 		=> $usu_setor,        
            'usu_nome' 		    => $usu_nome,            
            'usu_email' 		=> $usu_email,
            'usu_senha' 		=> $usu_senha,
            'usu_status' 		=> $usu_status
        ));
        
        if($action == "adicionar")
        {                       
           
            $sql = "INSERT INTO cadastro_usuarios SET ".bindFields($dados);
            $stmt = $PDO->prepare($sql);	
            if($stmt->execute($dados))
            {		
                $usu_id = $PDO->lastInsertId();

                //UPLOAD ARQUIVOS
				require_once '../core/mod_includes/php/lib/WideImage.php';
				
				$caminho = "../core/uploads/usuarios/";
				foreach($_FILES as $key => $files)
				{
					$files_test = array_filter($files['name']);
					if(!empty($files_test))
					{
						if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
						if(!empty($files["name"]["foto"]))
						{
							$nomeArquivo 	= $files["name"]["foto"];
                            $nomeTemporario = $files["tmp_name"]["foto"];                            
							$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
							$usu_foto	= $caminho;
							$usu_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($usu_foto));
							$imnfo = getimagesize($usu_foto);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($usu_foto);
								$image = $image->resize(500, 500);
								$image->saveToFile($usu_foto);
							}
							$sql = "UPDATE cadastro_usuarios SET 
									usu_foto 	 = :usu_foto
									WHERE usu_id = :usu_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':usu_foto',$usu_foto);
							$stmt->bindParam(':usu_id',$usu_id);
                            if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                            
                            //CONVERTE FOTO PARA BASE64
                            $imagedata = file_get_contents($usu_foto);                             
                            $base64 = base64_encode($imagedata);
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
            $sql = "UPDATE cadastro_usuarios SET ".bindFields($dados)." WHERE usu_id = :usu_id ";
            $stmt = $PDO->prepare($sql); 
            $dados['usu_id'] =  $usu_id;
            if($stmt->execute($dados))
            {
                //UPLOAD ARQUIVOS
				require_once '../core/mod_includes/php/lib/WideImage.php';
				$caminho = "../core/uploads/usuarios/";
				foreach($_FILES as $key => $files)
				{
					$files_test = array_filter($files['name']);
					if(!empty($files_test))
					{
						if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
						if(!empty($files["name"]["foto"]))
						{
							$nomeArquivo 	= $files["name"]["foto"];
							$nomeTemporario = $files["tmp_name"]["foto"];
							$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
							$usu_foto	= $caminho;
							$usu_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($usu_foto));
							$imnfo = getimagesize($usu_foto);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($usu_foto);
								$image = $image->resize(500, 500);
								$image->saveToFile($usu_foto);
							}
							$sql = "UPDATE cadastro_usuarios SET 
									usu_foto 	 = :usu_foto
									WHERE usu_id = :usu_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':usu_foto',$usu_foto);
							$stmt->bindParam(':usu_id',$usu_id);
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
			$sql = "DELETE FROM cadastro_usuarios WHERE usu_id = :usu_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':usu_id',$usu_id);
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
        if($action == 'ativar')
        {
			$sql = "UPDATE cadastro_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(':usu_status',1);
            $stmt->bindParam(':usu_id',$usu_id);
            $stmt->execute();
        }
        if($action == 'desativar')
        {
			$sql = "UPDATE cadastro_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(':usu_status',0);
            $stmt->bindParam(':usu_id',$usu_id);
            $stmt->execute();
        }
        $num_por_pagina = 10;
        if(!$pag){$primeiro_registro = 0; $pag = 1;}
        else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
        $sql = "SELECT * FROM cadastro_usuarios 
                LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor				
				ORDER BY usu_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);        
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
			</div>
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_first'>Foto</td>
                        <td class='titulo_tabela'>Email</td>
                        <td class='titulo_tabela'>Nome</td>
                        <td class='titulo_tabela'>Setor</td>
                        <td class='titulo_tabela' align='center'>Status</td>
                        <td class='titulo_last' align='right'>Gerenciar</td>
                    </tr>";
                    $c=0;
                    while($result = $stmt->fetch())
                    {
                        $usu_id = $result['usu_id'];
                        $usu_email = $result['usu_email'];
                        $set_nome = $result['set_nome'];
                        if($result['usu_visitante'] != "")
                        {
                            $set_nome = "Visitante<br><span class='detalhe'>Sem acesso ao sistema</span>";
                        }
                        $usu_nome = $result['usu_nome'];
                        $foto = $result['usu_foto'].$result['mof_foto'].$result['fun_foto'].$result['vis_foto'];
                        $usu_status = $result['usu_status'];
                        if($foto == '')
                        {
                            $foto = '../core/imagens/perfil.png';
                        }
                        
                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                        echo "<tr class='$c1'>
                                  <td><div class='foto_perfil' style='width:50px; height:50px; background:url($foto) center center; background-size: cover; border-radius:50px;' border='0'></div></td>
                                  <td>$usu_email</td>
                                  <td>$usu_nome</td>
                                  <td>$set_nome</td>
                                  <td align=center>";
                                  if($usu_status == 1)
                                  {
                                    echo "<i class='fas fa-check' style='color: green;'></i>";
                                  }
                                  else
                                  {
                                    echo "<i class='fas fa-times'  style='color: red;'></i>";
                                  }
                                  echo "
                                  </td>
                                  <td align=center>
								  		<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$usu_id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$usu_id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                        ";
                                        if($usu_status == 1)
										{
											echo "<div class='g_status' title='Desativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/desativar/$usu_id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
										}
										else
										{
											echo "<div class='g_status' title='Ativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/ativar/$usu_id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
										}
										echo "
								  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $cnt = "SELECT COUNT(*) FROM cadastro_usuarios   ";
                   	$stmt = $PDO->prepare($cnt);                    
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
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_usuarios/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>
					</ul>
					<div class='tab-content'>
						<div id='dados_gerais' class='tab-pane fade in active'>
                            <p><label>Setor:</label> <select name='usu_setor' id='usu_setor' class='obg'>
                                <option value=''>Setor</option>";
                                $sql = " SELECT * FROM admin_setores ORDER BY set_nome";
                                    $stmt = $PDO->prepare($sql);                                    
                                    $stmt->execute();
                                    while($result = $stmt->fetch())
                                    {
                                        echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
                                    }
                                echo "
                            </select>                            
                            <p><label>Nome:</label> <input name='usu_nome' id='usu_nome' placeholder='Nome' class='obg'>
                            <p><label>Email:</label> <input name='usu_email' id='usu_email' placeholder='Email' class='obg'>
                            <p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' placeholder='Senha' class='obg'>                            
                            <p><label>Status:</label> 
                            <input type='radio' name='usu_status' value='1' checked> Ativo <br>
                            <input type='radio' name='usu_status' value='0'> Inativo<br>
                        </div>	                            
                        <div id='foto' class='tab-pane fade in' style='text-align:center'>
                            <p><label>Foto:</label> <input type='file' name='usu_foto[foto]' id='usu_foto' placeholder='Foto'>                            
                        </div>
					</div>
					<center>
					<div id='erro' align='center'>&nbsp;</div>
					<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_usuarios/view'; value='Cancelar'/></center>
					</center>
				</div>
			</form>
            ";
        }
        
        if($pagina == 'edit')
        {            		
			$sql = "SELECT * FROM cadastro_usuarios 
                    LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
                    WHERE usu_id = :usu_id ";
            $stmt = $PDO->prepare($sql);            
			$stmt->bindParam(':usu_id', $usu_id);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $result = $stmt->fetch();
                $usu_setor = $result['usu_setor'];
                $set_nome = $result['set_nome'];if($set_nome == ''){ $set_nome = "Setor";}                                
                $usu_nome = $result['usu_nome'];
                $usu_email = $result['usu_email'];
                $usu_senha = $result['usu_senha'];
                $usu_status = $result['usu_status'];                                               
                $usu_foto = $result['usu_foto'];
                if($usu_acesso == 1)
                {
                    $usu_acesso_n = "Limitado";
                }
                else
                {
                    $usu_acesso_n = "Ilimitado";
                }
                
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_usuarios/view/editar/$usu_id'>
                   	<div class='titulo'> $page &raquo; Editar </div>
                   	<ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>
					</ul>
					<div class='tab-content'>
						<div id='dados_gerais' class='tab-pane fade in active'>
							<label>Setor:</label> <select name='usu_setor' id='usu_setor' class='obg'>
								<option value='$usu_setor'>$set_nome</option>";
								$sql = " SELECT * FROM admin_setores ORDER BY set_nome";
								$stmt = $PDO->prepare($sql);								
								$stmt->execute();
								while($result = $stmt->fetch())
								{
									echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
								}
								echo "
                            </select>                            
							<p><label>Nome:</label> <input name='usu_nome' id='usu_nome' value='$usu_nome' placeholder='Nome' class='obg'>
							<p><label>Email:</label> <input name='usu_email' id='usu_email' value='$usu_email' placeholder='Email' class='obg'>
							<p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' value='$usu_senha' placeholder='Senha' class='obg'>							
                            <p><label>Status:</label> ";
							if($usu_status == 1)
							{
								echo "<input type='radio' name='usu_status' value='1' checked> Ativo <br>
									  <input type='radio' name='usu_status' value='0'> Inativo
									 ";
							}
							else
							{
								echo "<input type='radio' name='usu_status' value='1'> Ativo <br>
									  <input type='radio' name='usu_status' value='0' checked> Inativo
									 ";
							}
                            echo "                                                        
                        </div>                        
                        <div id='foto' class='tab-pane fade in'>
							<p><label>Foto:</label> ";if($usu_foto != ''){ echo "<img src='".$usu_foto."' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
							<p><label>Alterar Foto:</label> <input type='file' name='usu_foto[foto]' id='usu_foto'>							
						</div>							
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_usuarios/view'; value='Cancelar'/></center>
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