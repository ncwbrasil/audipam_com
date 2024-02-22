<?php
session_start();

?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<head>
<?php	

?>
	<?php 
		include('header.php'); 
		$pagina = 'home'; 
	?>

</head>
<body >
    <header>
		<?php 
        	#region MOD INCLUDES
			include('mod_topo_portal/topo.php');
			// include('banner.php');
            include('mod_includes_portal/php/funcoes-jquery.php'); 
			#endregion
		?>
	</header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div id='home'>
    	    <div class="wrapper">
                <div class='pesquisa-materias'>
                    <p class='title'> Pesquisar Matérias legislativas  <i class="far fa-question-circle" title="Ultize os filtros abaixo para pesquisar a matéria legislativa desejada." style="cursor:help"></i></p>
                    <form name='form_materia' id='form' enctype='multipart/form-data' method='post' action='materias/'>
                        <select type='text' id='tipo_materia' name='tipo_materia' >
                            <option value=''>Tipo de Matéria</option>
                            <?php
                                    $sql = " SELECT * FROM aux_materias_tipos 
                                              
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                            
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }                        
                            ?>
                        </select>
                        <input type='text' id='numero_processo' name='numero_processo' placeholder='Número Processo'>
                        <input type='text' id='numero_materia' name='numero_materia' placeholder='Número da Matéria'>
                        <input type='text' id='ano_materia' name='ano_materia' placeholder='Ano da Matéria'>
                        <input type='text' id='fim_data_inicio' name='fim_data_inicio' placeholder='Data Inicial'>
                        <input type='text' id='fil_data_fim' name='fil_data_fim' placeholder='Data Final'>        
                        <select type='text' id='autor' name='autor' >
                            <option value=''>Autor</option>
                            <?php
                                $sql = " SELECT * FROM aux_autoria_autores ORDER BY nome";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                    
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                }                        
                            ?>
                        </select>            
                        <select type='text' id='assunto' name='assunto' >
                            <option value=''>Assunto</option>
                            <?php
                                $sql = " SELECT * FROM aux_materias_assuntos ORDER BY descricao";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                    
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                }                        
                            ?>
                        </select>  
                        <input type='text' id='ementa' name='ementa' placeholder='Ementa (digite a palavra desejada)'>        
                        <input type='submit' value='Pesquisar'>     
                    </form>
                </div>

                <div class='vereadores'>
                    <p class='title'> Vereadores</p>
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
                            <div class='blocos'>
                                <a href='vereador/".$result_int['parlamentar_id']."'>
                                    <div class='vereadores-foto' style='background:url(".str_replace("../","",$result_int['foto']).") top center no-repeat; background-size: 80%; border-radius:60px; width:80px; height:80px; border:1px solid #CCC;' border='0'></div>
                                    <div class='vereadores-nome'><span class='bold'>".$result_int['parlamentar_nome']."</span><br>".$result_int['sigla']."</div>
                                </a>
                            </div>
                            ";
                        }                           
                        ?>
                </div>

                <div class='sessoes'>
                    <p class='title'> Últimas Sessões Realizadas</p>
                    <?php
                        $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                                        , cadastro_sessoes_plenarias.numero as numero
                                        , aux_parlamentares_legislaturas.numero as numero_legislatura
                                        , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                                        , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                                        , aux_mesa_diretora_sessoes.numero as numero_sessao
                                        , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                                        , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                                FROM cadastro_sessoes_plenarias 
                                LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                                LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                                LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao
                                ORDER BY cadastro_sessoes_plenarias.id DESC
                                LIMIT :limit
                                ";
                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                        $hoje = date("Y");                                                     
                        $stmt_int->bindValue(":limit", 10);                                                                                   
                        $stmt_int->execute();
                        while($result_int = $stmt_int->fetch())
                        {
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "                            
                            <div class='blocos ' >
                                <a href='sessao/".$result_int['id']."'>
                                    <p class='bold hand'>
                                        ".$result_int['numero']."ª Sessão ".$result_int['descricao']." da ".$result_int['numero_sessao']." Sessão Legislativa da ".$result_int['numero_legislatura']." Legislatura
                                    </p>
                                    <span class='bold'>Tipo:</span>  ".$result_int['descricao']."<br>
                                    <span class='bold'>Data:</span> ".reverteData($result_int['data_abertura'])." <br>
                                    "; if($result_int['pauta']){ echo "<span class='bold'>Pauta:</span> <a href='".str_replace("../","",$result_int['pauta'])."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><br>";} echo "
                                    "; if($result_int['ata']){ echo "<span class='bold'>Ata:</span> <a href='".str_replace("../","",$result_int['ata'])."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><br>";} echo "
                                    "; if($result_int['anexo']){ echo "<span class='bold'>Anexo:</span> <a href='".str_replace("../","",$result_int['anexo'])."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><br>";} echo "
                                    
                                </a>
                            </div>
                            ";
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
