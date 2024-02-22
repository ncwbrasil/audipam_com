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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Sessões Plenárias
        </div>
	</header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div id='sessoes'>
    	    <div class="wrapper">
                <div class='bloco'>
                    <p class='title'> Sessões Plenárias <i class="far fa-question-circle" title="Preencha os campos para encontrar as sessões legislativas e clique na escolhida para saber mais informações." style="cursor:help"></i> </p>
                   
                    <?php

                    $num_por_pagina = 10;
                    if(!$pag){$primeiro_registro = 0; $pag = 1;}
                    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                    $tipo_sessao = $_REQUEST['tipo_sessao'];
                    if($tipo_sessao == '')
                    {
                        $tipo_query = " 1 = 1 ";
                    }
                    else
                    {                        
                        $tipo_query = " (cadastro_sessoes_plenarias.tipo_sessao = :tipo_sessao ) ";
                    }
                    $numero_sessao = $_REQUEST['numero_sessao'];
                    if($numero_sessao == '')
                    {
                        $numero_query = " 1 = 1 ";
                    }
                    else
                    {                        
                        $numero_query = " (cadastro_sessoes_plenarias.numero = :numero_sessao ) ";
                    }

                    $fil_data_inicio = reverteData($_REQUEST['fil_data_inicio']);
                    $fil_data_fim = reverteData($_REQUEST['fil_data_fim']);
                    if($fil_data_inicio == '' && $fil_data_fim == '')
                    {
                        $data_query = " 1 = 1 ";
                    }
                    elseif($fil_data_inicio != '' && $fil_data_fim == '')
                    {
                        
                        $data_query = " cadastro_sessoes_plenarias.data_abertura >= :fil_data_inicio ";
                    }
                    elseif($fil_data_inicio == '' && $fil_data_fim != '')
                    {
                        $fil_data_fim_h = $fil_data_fim." 23:59:59";
                        $data_query = " cadastro_sessoes_plenarias.data_abertura <= :fil_data_fim ";
                    }
                    elseif($fil_data_inicio != '' && $fil_data_fim != '')
                    {
                        $fil_data_fim_h = $fil_data_fim." 23:59:59";
                        $data_query = " cadastro_sessoes_plenarias.data_abertura BETWEEN :fil_data_inicio AND :fil_data_fim ";
                    }


                    $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                                    , cadastro_sessoes_plenarias.nome as nome
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
                            WHERE ".$tipo_query." AND ".$numero_query." AND ".$data_query." AND cadastro_sessoes_plenarias.ativo = :ativo 
                            ORDER BY cadastro_sessoes_plenarias.data_abertura DESC, cadastro_sessoes_plenarias.id DESC
                            LIMIT :primeiro_registro, :num_por_pagina 
                            ";
                    
                    // Paginação
                    $cnt = "SELECT COUNT(*) as count FROM cadastro_sessoes_plenarias 
                            LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                            LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                            LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao
                            WHERE ".$tipo_query." AND ".$numero_query." AND ".$data_query." AND cadastro_sessoes_plenarias.ativo = :ativo 
                            ";
                    $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                    $stmt->bindParam(':tipo_sessao', 	$tipo_sessao);
                    $stmt->bindParam(':numero_sessao', 	$numero_sessao);
                    $stmt->bindParam(':fil_data_inicio', 	$fil_data_inicio);
	                $stmt->bindParam(':fil_data_fim', 		$fil_data_fim_h);
                    $stmt->bindValue(':ativo', 		1);
                    $stmt->execute();			
                    list($total_linhas) = $stmt->fetch();
                    $variavel = "&fil_sigla=$fil_sigla"; 
                    //
                    
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                                             
                    $stmt_int->bindParam(':primeiro_registro', 	$primeiro_registro);
                    $stmt_int->bindParam(':num_por_pagina', 	$num_por_pagina);       
                    $stmt_int->bindParam(':tipo_sessao', 	$tipo_sessao);                                                                        
                    $stmt_int->bindParam(':numero_sessao', 	$numero_sessao);   
                    $stmt_int->bindParam(':fil_data_inicio', 	$fil_data_inicio);
	                $stmt_int->bindParam(':fil_data_fim', 		$fil_data_fim_h);  
                    $stmt_int->bindValue(':ativo', 		1);                                                                   
                    $stmt_int->execute();
                    $rows_int = $stmt_int->rowCount();

                    ?>
                    <form name='form_materia' id='form' enctype='multipart/form-data' method='post' action='sessoes'>
                        <select type='text' id='tipo_sessao' name='tipo_sessao' >
                            <option value=''>Tipo de Sessão</option>
                            <?php
                                $sql = " SELECT * FROM aux_sessoes_plenarias_tipos 
                                        
                                        ORDER BY descricao";
                                $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                    
                                $stmt_filtro->execute();
                                while($result_filtro = $stmt_filtro->fetch())
                                {
                                    echo "<option value='".$result_filtro['id']."' ";if($_POST['tipo_sessao'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['descricao']."</option>";
                                }                        
                            ?>
                        </select>
                        <input type='text' id='numero_sessao' name='numero_sessao' value='<?php echo $numero_sessao;?>' placeholder='Número da Sessão'>
                        <input type='text' id='fil_data_inicio' name='fil_data_inicio' value='<?php echo reverteData($fil_data_inicio);?>'placeholder='Data Inicial'>
                        <input type='text' id='fil_data_fim' name='fil_data_fim'  value='<?php echo reverteData($fil_data_fim);?>' placeholder='Data Final'>                                
                        <input type='submit' value='Pesquisar'>     
                    </form>
                    <hr>
                    <?php
                    echo "<p>Foram encontradas <span class='bold'>".$total_linhas."</span> sessões legislativas.</p>";
                    
                    if($rows_int > 0)
                    {
                        while($result_int = $stmt_int->fetch())
                        {
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "                            
                            <div class='blocos ' >
                                <a href='sessao/".$result_int['id']."'>
                                    <p class='bold hand'>
                                    ";
                                            if($result_int['nome'] != "")
                                            {
                                                echo $result_int['nome'];
                                            }
                                            else
                                            {
                                                echo $result_int['numero']."ª Sessão ".$result_int['descricao']." da ".$result_int['numero_sessao']." Sessão Legislativa da ".$result_int['numero_legislatura']." Legislatura ";
                                            }
                                            echo "
                                        
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
                        
                        include("../core/mod_includes/php/paginacao.php");
                    }
                    else
                    {
                        echo "Nenhum registro encontrado com os dados filtrados.";
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
