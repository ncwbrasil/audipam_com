<?php 

include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
include_once("../../core/mod_includes/php/dadosGerais.php"); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>Processo Legislativo</title>
    <meta   name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0,  user-scalable=no">
    <meta 	name="author" content="MogiComp">
    <meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link 	rel="shortcut icon" href="../../core/imagens/favicon.png">
    <link 	href="../../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
    <link 	href="../mod_includes/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
    <!-- TOOLBAR -->
    <link 	href="../../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
    <link 	href="../../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
    <script src="../../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
    <!-- TOOLBAR -->
    <link 	href="../../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
    <script src="../../core/mod_includes/js/janela/jquery-ui.js"></script>
    <!-- MENU -->
    <link 	href="../mod_menu/css/style.css" rel="stylesheet" > <!-- Resource style -->
    <script src="../mod_menu/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="../mod_menu/js/jquery.menu-aim.js"></script>
    <script src="../mod_menu/js/main.js"></script> <!-- Resource jQuery -->    
    <!-- FIM MENU -->
    <!-- ABAS -->
    <link 	href="../../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
    <script src="../../core/mod_includes/js/abas/bootstrap.js"></script>
    <!-- ABAS -->
</head>
<body>
	<?php
	require_once('../mod_includes/php/funcoes-jquery.php');
	require_once('../mod_includes/php/verificalogin.php');
	include("../mod_menu/barra.php");
	?>
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
        <div class='mensagem'></div>
			<?php
            $page = "Meu Perfil";
            if($action == 'editar')
            {
                $usu_id = $_GET['usu_id'];
				$usu_setor = $_POST['usu_setor'];
				$usu_email = $_POST['usu_email'];
				$usu_cpf = $_POST['usu_cpf'];
				$usu_senha = hash('sha512',$_POST['usu_senha']);
				$usu_status = $_POST['usu_status'];
				$sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id ";
				$stmt = $PDO_PROCLEGIS->prepare($sql);
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
					'usu_setor' 		=> $usu_setor,
					'usu_cpf' 			=> $usu_cpf,
					'usu_email' 		=> $usu_email,
					'usu_senha' 		=> $usu_senha,
					'usu_status' 		=> $usu_status
				));
               	$sql = "UPDATE cadastro_usuarios SET ".bindFields($dados)." WHERE usu_id = :usu_id ";
				$stmt = $PDO_PROCLEGIS->prepare($sql); 
				$dados['usu_id'] =  $usu_id;
				if($stmt->execute($dados))
				{
					//UPLOAD ARQUIVOS
					require_once '../../core/mod_includes/php/lib/WideImage.php';
					$caminho = "../../core/uploads/usuarios/";
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
								$foto	= $caminho;
								$foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
								move_uploaded_file($nomeTemporario, ($foto));
								$imnfo = getimagesize($foto);
								$img_w = $imnfo[0];	  // largura
								$img_h = $imnfo[1];	  // altura
								if($img_w > 500 || $img_h > 500)
								{
									$image = WideImage::load($foto);
									$image = $image->resize(500, 500);
									$image->saveToFile($foto);
								}		
								
								$sql = "UPDATE cadastro_usuarios SET 
										usu_foto 	 = :usu_foto
										WHERE usu_id = :usu_id ";
								$stmt = $PDO_PROCLEGIS->prepare($sql);
								$stmt->bindParam(':usu_foto',$foto);
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
            
            if($pagina == '')
            {
                $sql = "SELECT * FROM cadastro_usuarios
                        LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
                        WHERE usu_id = :usu_id";
                $stmt = $PDO_PROCLEGIS->prepare( $sql );
                $stmt->bindParam( ':usu_id', $_SESSION['usuario_id']);
                $stmt->execute();
                $rows = $stmt->rowCount();
                 if($rows > 0)
                {
                    while ($result = $stmt->fetch()) 
                    { 
                        $set_nome 	= $result['set_nome'];if($set_nome == ''){ $set_nome = "Setor";}
                        $usu_senha 	= $result['usu_senha'];
                        $usu_email 	= $result['usu_email'];
						$nome 		= $result['usu_nome'];
                        $usu_cpf 	= $result['usu_cpf'];
						$foto 		= $result['usu_foto'];
                    }
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='meu_perfil/editar/".$_SESSION['usuario_id']."'>
                        <div class='titulo'> $page &raquo; Editar </div>
						<ul class='nav nav-tabs'>
							<li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
							<li><a data-toggle='tab' href='#foto'>Foto</a></li>
						</ul>
						<div class='tab-content'>
							<div id='dados_gerais' class='tab-pane fade in active'>
								<p><label>Nome:</label> <input name='usu_nome' id='usu_nome' value='$nome' placeholder='Nome do Usuário' class='obg'>
								<p><label>CPF:</label> <input name='usu_cpf' id='usu_cpf' value='$telefone' placeholder='CPF' onkeypress='mascaraCPF(this);' maxlength='14'>
								<p><label>Email:</label> <input name='usu_email' id='usu_email' value='$usu_email' placeholder='Email'>
								<p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' value='$usu_senha' placeholder='Senha' class='obg'>
								
								
							</div>
							<div id='foto' class='tab-pane fade in'>
								<p><label>Foto:</label> ";if($foto != ''){ echo "<img src='$foto' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
								<p><label>Alterar Foto:</label> <input type='file' name='usu_foto[foto]' id='usu_foto'>
							</div>
							<br><br>
							<center>
							<div id='erro' align='center'>&nbsp;</div>
							<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
							<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='home'; value='Cancelar'/></center>
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