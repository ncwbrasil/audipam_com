<?php
session_start();

?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">

<head>
    <?php
    include('header.php');
    $pagina = 'home';
    $norma = $_GET['id'];
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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Leis e Normas
        </div>
    </header>
    <main style="background: <?php echo $cor_fundo ?>">
        <div class='vereadores'>
            <div class="wrapper">
                <div class='bloco'>
                    <p class='title'>Leis e Normas <i class="far fa-question-circle" title="Preencha com o numero da Lei para encontra-la e clique na escolhida para saber mais informações." style="cursor:help"></i> </p>
                    <?php
                    $num_por_pagina = 10;
                    if (!$pag) {
                        $primeiro_registro = 0;
                        $pag = 1;
                    } else {
                        $primeiro_registro = ($pag - 1) * $num_por_pagina;
                    }


                    $fil_numero = $_REQUEST['fil_numero'];
                    $fil_ano = $_REQUEST['fil_ano'];
                    $fil_tipo = $_REQUEST['fil_tipo'];
                    $fil_autor = $_REQUEST['fil_autor'];

                    $fil_dt_inicio = $_REQUEST['fil_dt_inicio'];
                    $fil_dt_fim = $_REQUEST['fil_dt_fim'];

                    $fil_palavra = $_REQUEST['fil_palavra'];

                    
                    if (!$fil_numero&& !$fil_autor && !$fil_tipo && !$fil_ano && !$fil_dt_inicio && !$fil_dt_fim && !$fil_palavra) {
                        $numero_query = " 1 = 1 ";
                    } else {
                        if($fil_numero){
                            $n[0] = "cadastro_normas_juridicas.numero = :fil_numero"; 
                            $variavel .= "&fil_numero=$fil_numero";
                        }
                        if($fil_ano){
                            $n[1] = "cadastro_normas_juridicas.ano =:fil_ano"; 
                            $variavel .= "&fil_ano=$fil_ano";
                        }
                        if($fil_tipo){
                            $n[2] = "cadastro_normas_juridicas.tipo =:fil_tipo"; 
                            $variavel .= "&fil_tipo=$fil_tipo";
                        }
                        if($fil_autor){
                            $n[3] = "aux_autoria_autores.parlamentar =:fil_autor"; 
                            $variavel .= "&fil_autor=$fil_autor";
                        }

                        if($fil_palavra){
                            $n[4] = "cadastro_normas_juridicas.ementa like :ementa OR cadastro_normas_juridicas.conteudo like :conteudo"; 
                            $variavel .= "&fil_palavra=$fil_palavra";
                        }

                        if($fil_dt_inicio && $fil_dt_fim){
                            $n[5] = "cadastro_normas_juridicas.data_publicacao BETWEEN :fil_dt_inicio AND :fil_dt_fim"; 
                            $variavel .= "&fil_dt_inicio=$fil_dt_inicio&fil_dt_fim=$fil_dt_fim";
                        }
                        else {
                            if($fil_dt_inicio){
                                $n[5] = "cadastro_normas_juridicas.data_publicacao >= :fil_dt_inicio"; 
                                $variavel .= "&fil_dt_inicio=$fil_dt_inicio";
                            }  
                            
                            if ($fil_dt_fim){
                                $n[5] = "cadastro_normas_juridicas.data_publicacao <= :fil_dt_fim"; 
                                $variavel .= "&fil_dt_fim=$fil_dt_fim";
                            }
                        }

                        $ultimo = count($n);
                        $c = 0; 
                        foreach ($n as $valor) {
                            $c++; 
                            if ($c == $ultimo){
                                $numero_query .= $valor; 
                            }else {
                                $numero_query .= $valor.' AND '; 
                            }
                        }
                    }

                    $sql = "SELECT *, aux_normas_juridicas_tipos.nome as tipo_nome,
                                                    aux_normas_juridicas_tipos.sigla as tipo_sigla,
                                                    cadastro_normas_juridicas.id as id
                                            FROM cadastro_normas_juridicas 
                                        LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas.tipo
                                        LEFT JOIN cadastro_normas_juridicas_autoria ON cadastro_normas_juridicas_autoria.norma = cadastro_normas_juridicas.id
                                        LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_normas_juridicas_autoria.autor                     
                                        WHERE $numero_query 			
                                        ORDER BY cadastro_normas_juridicas.id DESC
                                        LIMIT :primeiro_registro, :num_por_pagina ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':fil_numero',     $fil_numero);
                    $stmt->bindParam(':fil_autor',     $fil_autor);
                    $stmt->bindParam(':fil_tipo',     $fil_tipo);
                    $stmt->bindParam(':fil_ano',     $fil_ano);
                    $stmt->bindParam(':fil_dt_inicio',     $fil_dt_inicio);
                    $stmt->bindParam(':fil_dt_fim',     $fil_dt_fim);
                    $stmt->bindValue(':ementa',    "%".$fil_palavra."%");
                    $stmt->bindValue(':conteudo',    "%".$fil_palavra."%");
                    $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
                    $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    echo "
                                    <div id='botoes'>
                                        <div class='filtro'>
                                        <h4> <b>Utilize os filtros para facilitar sua busca<b></h4>
                                            <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='normas/'>
                                                <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                                                <select name='fil_ano' >";
                                                    if($fil_ano){
                                                        echo "<option value ='$fil_ano'> $fil_ano </option>"; 
                                                    }
                                                    echo "<option value =''> Ano </option>";
                                                    $anos = "SELECT ano, ativo FROM cadastro_normas_juridicas WHERE ativo = :ativo GROUP BY ano ORDER BY ano DESC"; 
                                                    $sano = $PDO_PROCLEGIS->prepare($anos);
                                                    $sano->bindValue(':ativo',     1);
                                                    $sano->execute();
                                                    $rows_ano = $sano->rowCount();
                                                    if($rows_ano > 0){
                                                        while($ano = $sano->fetch()){
                                                            echo "<option value='".$ano['ano']."'>".$ano['ano']."</option>";
                                                        }
                                                    }
                                                echo "</select>

                                                <select name='fil_tipo'>";
                                                    if($fil_tipo){
                                                        $tipos = "SELECT * FROM aux_normas_juridicas_tipos WHERE id = :id"; 
                                                        $stipo = $PDO_PROCLEGIS->prepare($tipos);
                                                        $stipo->bindValue(':id',     $fil_tipo);
                                                        $stipo->execute();
                                                        $rows_tipo = $stipo->rowCount();
                                                        if($rows_tipo > 0){
                                                            $tipo = $stipo-> fetch(); 
                                                            echo "<option value='".$tipo['id']."'>".$tipo['nome']."</option>";
                                                        }
                                                    }

                                                    echo "<option value =''>Tipo </option>";
                                                    $tipos = "SELECT * FROM aux_normas_juridicas_tipos WHERE ativo = :ativo"; 
                                                    $stipo = $PDO_PROCLEGIS->prepare($tipos);
                                                    $stipo->bindValue(':ativo',     1);
                                                    $stipo->execute();
                                                    $rows_tipo = $stipo->rowCount();
                                                    if($rows_tipo > 0){
                                                        while($tipo = $stipo->fetch()){
                                                            echo "<option value='".$tipo['id']."'>".$tipo['nome']."</option>";
                                                        }
                                                    }

                                                echo "</select>

                                                <select name ='fil_autor'>";
                                                    if($fil_autor){
                                                        $autores = "SELECT * FROM aux_autoria_autores WHERE ativo = :ativo AND parlamentar = :parlamentar"; 
                                                        $sautor = $PDO_PROCLEGIS->prepare($autores);
                                                        $sautor->bindValue(':ativo',     1);
                                                        $sautor->bindValue(':parlamentar',     $fil_autor);
                                                        $sautor->execute();
                                                        $rows_autor = $sautor->rowCount();
                                                        if($rows_autor > 0){
                                                            while($autor = $sautor->fetch()){
                                                                echo "<option value='".$autor['parlamentar']."'>".$autor['nome']."</option>";
                                                            }
                                                        }                                                    
                                                    }else {
                                                        echo "<option value =''>Autoria </option>";
                                                    }
                                                    $autores = "SELECT * FROM aux_autoria_autores WHERE ativo = :ativo AND nome IS NOT NULL GROUP BY nome ORDER BY nome ASC"; 
                                                    $sautor = $PDO_PROCLEGIS->prepare($autores);
                                                    $sautor->bindValue(':ativo',     1);
                                                    $sautor->execute();
                                                    $rows_autor = $sautor->rowCount();
                                                    if($rows_autor > 0){
                                                        while($autor = $sautor->fetch()){
                                                            echo "<option value='".$autor['parlamentar']."'>".$autor['nome']."</option>";
                                                        }
                                                    }

                                                echo "</select>
                                                <br><br>
                                                <input name='fil_palavra' id='fil_palavra' value='$fil_palavra' placeholder='Palavra chave'>
                                                De <input type='date' name='fil_dt_inicio' id='fil_dt_inicio' value='$fil_dt_inicio' placeholder='Data Início'>
                                                Até <input type='date' name='fil_dt_fim' id='fil_dt_fim' value='$fil_dt_fim' placeholder='Data Fim'>


                                            <input type='submit' value='Filtrar'> 
                                            </form>            
                                        </div>    
                                    </div>
                                ";

                    if ($rows > 0) {
                        echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        ";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $id = $result['id'];
                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                    <td>
                                                        <a href='norma/$id'><p class='bold' style='font-size:18px; text-decoration:underline;'>
                                                            " . $result['tipo_sigla'] . " " . $result['numero'] . "/" . $result['ano'] . " - " . $result['tipo_nome'] . "
                                                        </p></a>
                                                        <span class='bold'>Ementa:</span>  " . $result['ementa'] . "<p>
                                                        <span class='bold'>Data publicação:</span>  " . reverteData($result['data_publicacao']) . "<p>
                                                        <span class='bold'>Conteúdo:</span> ";
                            if ($result['conteudo'] != '') {
                                echo "<a href='norma_juridica/" . $result['id'] . "' target='_blank'><i class='far fa-file-alt' style='font-size:20px'></i></a>";
                            }
                            echo "</td>                                    
                                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_normas_juridicas                                         
                            LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas.tipo
                            LEFT JOIN cadastro_normas_juridicas_autoria ON cadastro_normas_juridicas_autoria.norma = cadastro_normas_juridicas.id
                            LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_normas_juridicas_autoria.autor                     
                            WHERE " . $numero_query . "";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);
                        $stmt->bindvalue(':fil_numero',     "%" . $fil_numero . "%");
                        $stmt->bindParam(':fil_autor',     $fil_autor);
                        $stmt->bindParam(':fil_tipo',     $fil_tipo);
                        $stmt->bindParam(':fil_ano',     $fil_ano);
                        $stmt->bindParam(':fil_dt_inicio',     $fil_dt_inicio);
                        $stmt->bindParam(':fil_dt_fim',     $fil_dt_fim);   
                        $stmt->bindValue(':ementa',    "%".$fil_palavra."%");
                        $stmt->bindValue(':conteudo',    "%".$fil_palavra."%");
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }

                    ?>

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