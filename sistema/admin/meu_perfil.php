<?php
session_start (); 
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
<script type='text/javascript' src='../mod_includes/js/jcrop/jquery.Jcrop.min.js'></script>
<script type='text/javascript' src='../mod_includes/js/jcrop/script_perfil.js'></script>
<link type='text/css' href='../mod_includes/js/jcrop/jquery.Jcrop.min.css' rel='stylesheet'  />

</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
include		("../mod_topo/altera_foto_perfil.php");
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
    $page = "Meu Perfil";
    if($action == 'editar')
    {
        $usu_id = $_GET['usu_id'];
        $usu_nome = $_POST['usu_nome'];
        $usu_login = $_POST['usu_login'];
        $usu_telefone = $_POST['usu_telefone'];
        $usu_email = $_POST['usu_email'];
        $usu_senha = md5($_POST['usu_senha']);
        $sql = "SELECT * FROM admin_usuarios WHERE usu_id = :id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':id', $usu_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
        if($rows > 0)
        {
            $senhacompara = $stmt->fetch(PDO::FETCH_OBJ)->usu_senha;		
        }
        if($_POST['usu_senha'] == $senhacompara)
        {
            $usu_senha = $senhacompara;
        }
        $sql = "UPDATE admin_usuarios SET 
				 usu_nome = '$usu_nome',
				 usu_login = '$usu_login',
				 usu_telefone = '$usu_telefone',
				 usu_email = '$usu_email',
				 usu_senha = '$usu_senha'
				 WHERE usu_id = :id ";
    	$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':id', $usu_id);
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
    
    if($pagina == 'meu_perfil')
    {
        $usu_id = $_SESSION['usuario_id'];
		
        $sql = "SELECT * FROM admin_usuarios 
				LEFT JOIN admin_setores ON admin_setores.set_id = admin_usuarios.usu_setor
				WHERE usu_id = :id ";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':id', $usu_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
       	if($rows > 0)
        {
			$result = $stmt->fetch();
        	$usu_nome 		= $result['usu_nome'];
			$usu_login 		= $result['usu_login'];
			$usu_senha 		= $result['usu_senha'];
			$usu_telefone 	= $result['usu_telefone'];
			$usu_email 		= $result['usu_email'];
			$usu_foto 		= $result['usu_foto'];
		
  			include		("../mod_topo/foto_perfil.php");
            
            echo "
            <form name='form_meu_perfil' id='form_meu_perfil' enctype='multipart/form-data' method='post' action='meu_perfil.php?pagina=meu_perfil&action=editar&usu_id=$usu_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro_foto'>
								<div class='formtitulo'>Foto do Perfil</div>
								<a id='box_foto_perfil'>";
								if($usu_foto != ''){echo "<img src='$usu_foto' border='0' width='250' />";}else{echo "<img src='../imagens/perfil.png' border='0' width='250' />";}
								echo "
								</a>
							</div>
							<p>
							<div class='quadro'>
								<div class='formtitulo'>Meus Dados</div>
								<p><label>Nome do Usuário:</label> <input name='usu_nome' id='usu_nome' value='$usu_nome' placeholder='Nome do Usuário'>
								<p><label>Login:</label> <input type='text' name='usu_login' id='usu_login' value='$usu_login' placeholder='Login'>
								<p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' value='$usu_senha' placeholder='Senha'>
								<p><label>Telefone:</label> <input name='usu_telefone' id='usu_telefone' value='$usu_telefone' placeholder='Telefone' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
								<p><label>Email:</label> <input name='usu_email' id='usu_email' value='$usu_email' placeholder='Email'>
							</div>	
							<br><br>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_meu_perfil' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='meu_perfil.php?pagina=meu_perfil$autenticacao'; value='Cancelar'/></center>
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