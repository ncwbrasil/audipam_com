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
<!-- ABAS -->
<link rel="stylesheet" href="../mod_includes/js/abas/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../mod_includes/js/abas/bootstrap.js"></script>
<!-- ABAS -->
<script src="../mod_includes/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/chat.css" />
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/screen.css" />
<link rel="stylesheet" href="../css/eventCalendar.css">
<link rel="stylesheet" href="../css/eventCalendar_theme_responsive.css">

</head>
<body>

<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
?>
<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
    <?php include("../mod_menu/barra.php");?>
</div>
<div class='centro'>
    <div class='box'>
       <div class="corpo" id="agenda">
       		<div class='titulo'> Minha Agenda  </div>
        	<script>
				jQuery(document).ready(function() {
					jQuery("#eventCalendarShowDescription").eventCalendar({
						eventsjson: '../mod_includes/json/events.json.php?usuario_id=<?php echo $_SESSION['usuario_id'].$autenticacao;?>',
						showDescription: true,
					});
				});
			</script>
            <div id="eventCalendarShowDescription"></div>
            
   		 </div>
 	</div>
</div>
<!--<script type="text/javascript" src="../mod_includes/js/chat/chat.js"></script>-->

<?php
include('../mod_includes/js/chat/chat_js.php');
include('../mod_rodape/rodape.php');
?>
</body>
</html>
<?php
include("../mod_includes/js/jquery.eventCalendar.php");
?>