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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Mesa Diretora
        </div>
	</header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div id='mesa-diretora'>
    	    <div class="wrapper">
                <div class='bloco'>
                    <p class='title'> Mesa Diretora <i class="far fa-question-circle" title="Pesquise a composição da Mesa Diretora através da legistatura e da sessão legislativa" style="cursor:help"></i> </p>
                    Selecione abaixo a legislatura e a sessão legislativa desejada para exibir a composição da Mesa Diretora.<br>
                        <select id='fil_mesa_legislatura' name='fil_mesa_legislatura' >
                            <option value=''>Selecione a legislatura</option>
                            <?php
                            $sql = "SELECT *, YEAR(data_inicio) as inicio, YEAR(data_fim) as fim FROM aux_parlamentares_legislaturas 
                                    WHERE aux_parlamentares_legislaturas.ativo = :ativo 
                                    ORDER BY data_inicio DESC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                            $stmt_int->bindValue(':ativo', 	1);
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['inicio']." - ".$result_int['fim'].")</option>";
                            }                        
                            ?>
                        </select>
                        <select hidden id='fil_sessao' name='fil_sessao' >
                            <option value=''>Selecione sessão legislativa</option>                           
                        </select>
                        <div class='result'>

                        </div>                        
                </div>               
            </div>  
        </div>
        <?php
		    include('mod_rodape_portal/rodape.php');
		?>
    </main>
</body>
</html>
