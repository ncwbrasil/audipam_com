<?php
session_start();
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<!-- W8 -->
<link rel='stylesheet' href='../mod_includes/js/w8/demo-styles.css' />
<script src='../mod_includes/js/w8/modernizr-1.5.min.js'></script>
<script src='../mod_includes/js/w8/scripts.js'></script>
<!-- W8 -->
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/chat.css" />
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/screen.css" />
</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
include		('../mod_includes/php/funcoes.php');
function emoticons($text) {
    $icons = array(
            ':)'    =>  ' <img src="../imagens/icon-exibir.png" alt="smile" class="icon_smile" /> ',
            ' :-) '   =>  ' <img src="../images/blank.gif" alt="smile" class="icon_smile" /> ',
            ' :D '    =>  ' <img src="/images/blank.gif" alt="smile" class="icon_laugh" /> ',
            ' :d '    =>  ' <img src="/images/blank.gif" alt="laugh" class="icon_laugh" /> ',
            ' ;) '    =>  ' <img src="/images/blank.gif" alt="wink" class="icon_wink" /> ',
            ' :P '    =>  ' <img src="/images/blank.gif" alt="tounge" class="icon_tounge" /> ',
            ' :-P '   =>  ' <img src="/images/blank.gif" alt="tounge" class="icon_tounge" /> ',
            ' :-p '   =>  ' <img src="/images/blank.gif" alt="tounge" class="icon_tounge" /> ',
            ' :p '    =>  ' <img src="/images/blank.gif" alt="tounge" class="icon_tounge" /> ',
            ' :( '    =>  ' <img src="/images/blank.gif" alt="sad face" class="icon_sad" /> ',
            ' :o '    =>  ' <img src="/images/blank.gif" alt="shock" class="icon_shock" /> ',
            ' :O '    =>  ' <img src="/images/blank.gif" alt="shock" class="icon_shock" /> ',
            ' :0 '    =>  ' <img src="/images/blank.gif" alt="shock" class="icon_shack" /> ',
            ' :| '    =>  ' <img src="/images/blank.gif" alt="straight face" class="icon_straight" /> ',
            ' :-| '   =>  ' <img src="/images/blank.gif" alt="straight face" class="icon_straight" /> ',
            ' :/ '    =>  ' <img src="/images/blank.gif" alt="straight face" class="icon_straight" /> ',
            ' :-/ '   =>  ' <img src="/images/blank.gif" alt="straight face" class="icon_straight" /> '
    );
    return strtr($text, $icons);
}
?>
<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
    <?php include("../mod_menu/barra.php");?>
</div>
<div class='centro'>
    <div class='box'>
    	<div class='conversas'>
        <!--<div id="main_container">-->
       	<?php
		if($action == "adicionar")
		{
			$usu_id = $_POST['usu_id'];
			$mensagem = $_POST['mensagem'];
			$sql = "INSERT INTO social_mensagens (
			msg_remetente,
			msg_destinatario,
			msg_mensagem,
			msg_data
			) 
			VALUES 
			(
			".$_SESSION['usuario_id'].",
			".$usu_id.",
			'".$mensagem."',
			'".date('Y-m-d H:i:s')."'
			)";
			if(mysql_query($sql,$conexao))
			{		
				/*echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
					'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
				</SCRIPT>
					";*/
			}
			else
			{
				echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../imagens/x.png> Erro ao criar conversa.<br><br>'+
					'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
				</SCRIPT>
					"; 
			}	
		}
		$sql_mensagens = "	SELECT * FROM
							(
								SELECT * FROM
								(
									   SELECT * FROM social_mensagens INNER JOIN admin_usuarios ON admin_usuarios.usu_id = social_mensagens.msg_destinatario ORDER BY msg_id DESC
								) as t1 
								
								WHERE msg_remetente = ".$_SESSION['usuario_id']." GROUP BY msg_destinatario
							  
							  UNION
								SELECT * FROM
								(
									   SELECT * FROM social_mensagens INNER JOIN admin_usuarios ON admin_usuarios.usu_id = social_mensagens.msg_remetente ORDER BY msg_id DESC
								) as t2
								
								WHERE msg_destinatario = ".$_SESSION['usuario_id']."  GROUP BY msg_remetente
								
								ORDER BY msg_data DESC
							) as t3
							GROUP BY usu_id
							ORDER BY msg_data DESC
						 ";
		$query_mensagens = mysql_query($sql_mensagens,$conexao);
		$rows_mensagens = mysql_num_rows($query_mensagens);
		if($rows_mensagens > 0)
		{
			while($row_mensagens = mysql_fetch_array($query_mensagens))
			{
					$sql_lida = "SELECT * FROM social_mensagens 
								 INNER JOIN admin_usuarios ON admin_usuarios.usu_id = social_mensagens.msg_remetente
								 WHERE msg_destinatario = ".$_SESSION['usuario_id']." AND msg_remetente =  ".$row_mensagens['usu_id']." AND msg_lida = 0";
					$query_lida = mysql_query($sql_lida,$conexao);
					$rows_lida = mysql_num_rows($query_lida);
					if($row_mensagens['msg_destinatario'] == $_SESSION['usuario_id'])
					{
						$tag = "&#8630;";
					}
					else
					{
						$tag = "&#10150;";
					}
					
					echo "
					<div class='mensagemBox "; if($rows_lida > 0){ echo "n_lida";} echo "'>
						<div class='mensagens' onclick='javascript:chatWith(".$row_mensagens['usu_id'].",\"".$row_mensagens['usu_nome']."\")'>
							<div class='foto'>";
								if($row_mensagens['usu_foto'] != '')
								{
									echo "<img src='".$row_mensagens['usu_foto']."' border='0' valign='middle'>";
								}
								else
								{
									echo "<img src='../imagens/perfil.png' border='0' valign='middle'>";
								}
								echo "
							</div>
							<div class='infos'>
									<span class='nome'>".$row_mensagens['usu_nome']."</span>
									<br>
									<span class='msg'>$tag ".truncate(strip_tags($row_mensagens['msg_mensagem']),43)."</span>
							</div>
							
						</div>
						<div class='abrir'>
						<a href='#' onclick='abrirConversa(".$row_mensagens['usu_id'].");'>+</a>
						</div>
					</div>
					
					";
			}
		}
		else
		{
			echo "Não há mensagens.";
		}
		
		?>
        <div class='mensagemBox' onclick='javascript:novoChat();'>
            <div class='mensagens'>
                <div class='foto'>
                    <img src='../imagens/perfil.png' border='0' valign='middle'>
                </div>
                <div class='infos'>
                        <span class='msg'>Iniciar nova conversa</span>
                </div>
                
            </div>
        </div>
        <!--</div>-->
        </div>
        <div class='conversa' id='conversa'>
        
        </div>
 	</div>
</div>
<!--<script type="text/javascript" src="../mod_includes/js/chat/chat.js"></script>-->

<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>