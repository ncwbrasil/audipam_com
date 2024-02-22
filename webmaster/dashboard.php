<?php 

//include("url.php");
include("../core/mod_includes/php/funcoes.php");
sec_session_start();
include_once("../core/mod_includes/php/connect_sistema.php");

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo include_once("url.php");?>
    <title>Audipam | Gerenciador de Sistemas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
<!-- ui -->
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

<!-- CHARTS -->
    <script src="../core/mod_includes/js/graficos/zingchart.min.js"></script>
    <script>zingchart.MODULESDIR="../core/mod_includes/js/graficos/modules/";</script>
    <script src= "https://cdn.zingchart.com/zingchart.min.js"></script>
	<script> zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
	ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];</script>


	
</head>
<body>
	<?php
			
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	include("../core/mod_menu/barra.php");
	?>
    
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../core/mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper" id="dashboard">			
    
			<!-- Modal -->
			
			
	
			<!-- TABELA -->
			<?php 			
			// $sql = "SELECT * FROM admin_setores_widget
			// 		LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
			// 		LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
			// 		WHERE set_id = :set_id AND wid_tipo = :wid_tipo
			// 		ORDER BY wid_id ASC
			// 		";
			// $stmt_wid = $PDO->prepare($sql);
			// $wid_tipo = "Tabela";
			// $stmt_wid->bindParam(':set_id', 	$_SESSION['setor_id']);				
			// $stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
			// $stmt_wid->execute();
			// $rows_wid = $stmt_wid->rowCount();  				
			// if($rows_wid > 0)
			// {
			// 	while($result_wid = $stmt_wid->fetch())
			// 	{						
			// 		include("../core/mod_includes/widgets/".$result_wid['wid_arquivo']);
			// 	}
			// }
			// ?>

			<!-- BLOCOS -->
			<div class='stats'>
			 	<?php 
				$sql = "SELECT * FROM admin_setores_widget
						LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
						LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
						WHERE set_id = :set_id AND wid_tipo = :wid_tipo
						";
				$stmt_wid = $PDO->prepare($sql);
				$wid_tipo = "Bloco";
				$stmt_wid->bindParam(':set_id', 	$_SESSION['setor_id']);				
				$stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
				$stmt_wid->execute();
				$rows_wid = $stmt_wid->rowCount();  				
				if($rows_wid > 0)
				{
					while($result_wid = $stmt_wid->fetch())
					{						
						include("../core/mod_includes/widgets/".$result_wid['wid_arquivo']);
					}
				}

			 	?>																					
			 </div>

			 <!-- GRAFICOS -->
			 <?php 
			$sql = "SELECT * FROM admin_setores_widget
					LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
					LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
					WHERE set_id = :set_id AND wid_tipo = :wid_tipo
					";
			$stmt_wid = $PDO->prepare($sql);
			$wid_tipo = "Grafico";
			$stmt_wid->bindParam(':set_id', 	$_SESSION['setor_id']);				
			$stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
			$stmt_wid->execute();
			$rows_wid = $stmt_wid->rowCount();  				
			if($rows_wid > 0)
			{
				while($result_wid = $stmt_wid->fetch())
				{						
					include("../core/mod_includes/widgets/".$result_wid['wid_arquivo']);
					
				}
			}

			?>	

				

			<br><br>			
		</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    <?php
	include("../core/mod_includes/js/graficos/charts.php");	
	?> 
	
	<!-- Bootstrap core CSS -->
	<!-- <link href="../core/mod_includes/js/mdbootstrap/css/bootstrap.css" rel="stylesheet"> -->
	<!-- Material Design Bootstrap -->
	<link href="../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">
	<?php include("../core/mod_includes/modal/cadastroVisitante.php");?>
</body>
</html>