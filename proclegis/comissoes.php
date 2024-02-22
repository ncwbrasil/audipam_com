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
        <div class='naveg'>
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Comissões
        </div>
	</header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div id='comissoes'>
    	    <div class="wrapper">
                <div class='bloco'>
                    <p class='title'> Comissões <i class="far fa-question-circle" title="Selecione o ano para saber sobre as comissões desse período." style="cursor:help"></i></p>
                        Selecione abaixo o período desejado para exibir as comissões.<br>
                        <select id='fil_comissoes_periodo' name='fil_comissoes_periodo' >
                            <option value=''>Selecione o período</option>
                            <?php
                            $sql = "SELECT *, YEAR(data_inicio) as inicio, YEAR(data_fim) as fim FROM aux_comissoes_periodos
                                    ORDER BY data_inicio DESC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['inicio']."'>".$result_int['inicio']."</option>";
                            }                        
                            ?>
                        </select>                       
                        <div class='result'>

                        </div>           
                        
                    </form>
                </div>               
            </div>  
        </div>
        <?php
		    include('mod_rodape_portal/rodape.php');
		?>
    </main>
</body>
</html>
