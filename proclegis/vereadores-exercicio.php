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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Vereadores em Exercício
        </div>
	</header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div id='vereadores'>
    	    <div class="wrapper">
                <div class='bloco'>
                    <p class='title'> Vereadores em exercício <i class="far fa-question-circle" title="Clique em algum vereador(a) para saber mais informações sobre o mesmo." style="cursor:help"></i></p>
                        <?php
                        $sql = "SELECT *,cadastro_parlamentares.id as parlamentar_id, cadastro_parlamentares.nome as parlamentar_nome FROM cadastro_parlamentares
                                LEFT JOIN ( cadastro_parlamentares_mandatos 
                                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_parlamentares_mandatos.legislatura )
                                ON cadastro_parlamentares_mandatos.parlamentar =  cadastro_parlamentares.id 
                                LEFT JOIN ( cadastro_parlamentares_filiacoes 
                                    LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido )
                                ON cadastro_parlamentares_filiacoes.parlamentar =  cadastro_parlamentares.id 
                                WHERE YEAR(aux_parlamentares_legislaturas.data_inicio) <= :hoje AND YEAR(aux_parlamentares_legislaturas.data_fim) >= :hoje2
                                GROUP BY cadastro_parlamentares.id
                                ORDER BY cadastro_parlamentares.nome";
                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                        $hoje = date("Y");                                                     
                        $stmt_int->bindParam(":hoje", $hoje);                                                           
                        $stmt_int->bindParam(":hoje2", $hoje); 
                        $stmt_int->execute();
                        while($result_int = $stmt_int->fetch())
                        {
                            echo "
                            <a href='vereador/".$result_int['parlamentar_id']."'>
                            <div class='blocos'>
                                <div class='vereadores-foto' style='background:url(".str_replace("../","",$result_int['foto']).") top center no-repeat; background-size: 80%; border-radius:60px; width:80px; height:80px; border:1px solid #CCC;' border='0'></div>
                                <div class='vereadores-nome'><span class='bold'>".$result_int['parlamentar_nome']."</span><br>".$result_int['sigla']."</div>
                            </div>
                            </a>";
                        }                           
                        ?>                                            
                </div>               
            </div>  
        </div>
        <?php
		    include('mod_rodape_portal/rodape.php');
		?>
    </main>
</body>
</html>
