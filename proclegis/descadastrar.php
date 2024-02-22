<?php
session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<head>
	<?php 
		include('header.php'); 
		$pagina = 'home'; 
	?>

</head>
<body>
    <header>
		<?php 
        
			#region MOD INCLUDES
			include('mod_topo_portal/topo.php');
			include('banner.php');
            include('mod_includes_portal/php/funcoes-jquery.php');			
			#endregion
		?>
	</header>
    <main>
        <div id='home'>
    	    <div class="wrapper">
               <?php

                if(isset($_GET['m'])){ $m = $_GET['m'];}
                if(isset($_GET['mat'])){ $mat = $_GET['mat'];}
                

                $sql = "DELETE FROM aux_acompanhar_materia WHERE am_materia = :am_materia  AND am_email = :am_email ";
                $stmt_delete = $PDO_PROCLEGIS->prepare($sql);
                $stmt_delete->bindParam(':am_email', 	$m);
                $stmt_delete->bindParam(':am_materia', 	$mat);
                if($stmt_delete->execute())
                {
                    echo "<center> <br><br><br><br>Seu e-mail foi descadastrado com sucesso.<br><br><br><br></center>";
                }
                else
                {
                    $erro=1;
                }
                ?>
            </div>  
        </div>
        <?php
		    include('mod_rodape_portal/rodape.php');
		?>
    </main>
</body>
</html>
