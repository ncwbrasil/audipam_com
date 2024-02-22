<?php 
$page_link = "dashboard";
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");


?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>    
	<?php include("header.php");?>
</head>
<body>
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php");?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper" id="dashboard">			
    
			
			<?php
			if(isset($_SESSION['webmaster']['audipam']))
			{
				$query_setor = " 1 = 1 ";
			}
			else
			{
				$query_setor = " sew_setor = :sew_setor ";        
			}
			?>
			
	
			<!-- TABELA -->
			<?php 			
			$id = 0; 
			log_operacao($id, $PDO_PROCLEGIS); 
			$sql = "SELECT * FROM admin_setores_widget
					LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
					LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
					WHERE ".$query_setor." AND wid_tipo = :wid_tipo
					ORDER BY wid_id ASC
					";
			$stmt_wid = $PDO_PROCLEGIS->prepare($sql);
			$wid_tipo = "Tabela";
			$stmt_wid->bindParam(':sew_setor', 	$_SESSION['setor_id']);				
			$stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
			$stmt_wid->execute();
			$rows_wid = $stmt_wid->rowCount();  				
			if($rows_wid > 0)
			{
				while($result_wid = $stmt_wid->fetch())
				{						
					include("../mod_includes/widgets/".$result_wid['wid_arquivo']);
				}
			}
			?>

			<!-- BLOCOS -->
			<div class='stats'>
				 <?php 
				 
				$sql = "SELECT * FROM admin_setores_widget
						LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
						LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
						WHERE ".$query_setor." AND wid_tipo = :wid_tipo
						";
				$stmt_wid = $PDO_PROCLEGIS->prepare($sql);
				$wid_tipo = "Bloco";
				$stmt_wid->bindParam(':sew_setor', 	$_SESSION['setor_id']);				
				$stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
				$stmt_wid->execute();
				$rows_wid = $stmt_wid->rowCount();  				
			
				if($rows_wid > 0)
				{
					
					while($result_wid = $stmt_wid->fetch())
					{			
							
						include("../mod_includes/widgets/".$result_wid['wid_arquivo']);
					}
				}

			 	?>																					
			 </div>

			 <!-- GRAFICOS -->
			 <?php 
			$sql = "SELECT * FROM admin_setores_widget
					LEFT JOIN dashboard_widgets  ON dashboard_widgets .wid_id = admin_setores_widget.sew_widget
					LEFT JOIN admin_setores  ON admin_setores .set_id = admin_setores_widget.sew_setor
					WHERE ".$query_setor." AND wid_tipo = :wid_tipo
					";
			$stmt_wid = $PDO_PROCLEGIS->prepare($sql);
			$wid_tipo = "Grafico";
			$stmt_wid->bindParam(':sew_setor', 	$_SESSION['setor_id']);				
			$stmt_wid->bindParam(':wid_tipo', 	$wid_tipo);				
			$stmt_wid->execute();
			$rows_wid = $stmt_wid->rowCount();  				
			if($rows_wid > 0)
			{
				while($result_wid = $stmt_wid->fetch())
				{						
					include("../mod_includes/widgets/".$result_wid['wid_arquivo']);
					
				}
			}

			?>	

				

			<br><br>			
		</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    <?php
	include("../mod_includes/php/charts.php");	
	?> 
	
	<!-- Bootstrap core CSS -->
	<!-- <link href="../../core/mod_includes/js/mdbootstrap/css/bootstrap.css" rel="stylesheet"> -->
	<!-- Material Design Bootstrap -->
	<link href="../../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">	
</body>
</html>