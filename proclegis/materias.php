<?php
session_start();

?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">

<head>
    <?php
        include('header.php');
        $pagina = $_GET['pg'];
        $materia = $_GET['id'];
    ?>
    <script>
    function popupWindow(url, windowName, win, w, h) {
        const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
        const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
        return win.open(url, windowName, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`);
    }
    </script>
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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Matérias Legislativas
        </div>
    </header>
    <main style="background: <?php echo $cor_fundo ?>">
	<div id='janela' class='janela' style='display:none;'> </div>
        <div id='materias'>
            <div class="wrapper">
                
                    <?php
                        echo "
                        <div class='bloco'>
                            <p class='title'> Matérias Legislativas <i class='far fa-question-circle' title='Preencha os campos para encontrar as Matérias Legislativas e clique na escolhida para saber mais informações.' style='cursor:help'></i>  </p>";
                            $num_por_pagina = 10;
                            if (!$pag) {
                                $primeiro_registro = 0;
                                $pag = 1;
                            } else {
                                $primeiro_registro = ($pag - 1) * $num_por_pagina;
                            }
                            $tipo_materia = $_REQUEST['tipo_materia'];
                            if ($tipo_materia == '') {
                                $tipo_query = " 1 = 1 ";
                            } else {
                                $tipo_query = " (cadastro_materias.tipo = :tipo_materia ) ";
                            }
                            $numero_materia = $_REQUEST['numero_materia'];
                            if ($numero_materia == '') {
                                $numero_query = " 1 = 1 ";
                            } else {
                                $numero_query = " (cadastro_materias.numero = :numero_materia ) ";
                            }
                            $protocolo = $_REQUEST['protocolo'];
                            if ($protocolo == '') {
                                $protocolo_query = " 1 = 1 ";
                            } else {
                                $protocolo = "%".$protocolo."%";
                                $protocolo_query = " (cadastro_materias.protocolo LIKE :protocolo ) ";
                            }
                            $ano_materia = $_REQUEST['ano_materia'];
                            if ($ano_materia == '') {
                                $ano_query = " 1 = 1 ";
                            } else {
                                $ano_query = " (cadastro_materias.ano = :ano_materia ) ";
                            }
                            $fil_data_inicio = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio'])));
                            $fil_data_fim = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim'])));
                            if($fil_data_inicio == '' && $fil_data_fim == '')
                            {
                                $data_query = " 1 = 1 ";
                                
                            }
                            elseif($fil_data_inicio != '' && $fil_data_fim == '')
                            {
                                $data_query = " data_apresentacao >= '$fil_data_inicio' ";
                            }
                            elseif($fil_data_inicio == '' && $fil_data_fim != '')
                            {
                                $data_query = " data_apresentacao <= '$fil_data_fim' ";
                            }
                            elseif($fil_data_inicio != '' && $fil_data_fim != '')
                            {
                                $data_query = " data_apresentacao BETWEEN '$fil_data_inicio' AND '$fil_data_fim' ";
                            }
                            $assunto = $_REQUEST['assunto'];
                            if ($assunto == '') {
                                $assunto_query = " 1 = 1 ";
                            } else {
                                $assunto_query = " (cadastro_materias_assuntos.assunto = :assunto ) ";
                            }
                            $autor = $_REQUEST['autor'];
                            if ($autor == '') {
                                $autor_query = " 1 = 1 ";
                            } else {
                                $autor_query = " (aux_autoria_autores.id = :autor ) ";
                            }
                            $ementa = $_REQUEST['ementa'];
                            if ($ementa == '') {
                                $ementa_query = " 1 = 1 ";
                            } else {
                                $ementa = "%".$ementa."%";
                                $ementa_query = " (cadastro_materias.ementa LIKE :ementa ) ";
                            }

                            $sql = "SELECT *, aux_materias_tipos.nome as tipo_nome,
                                            aux_materias_tipos.sigla as tipo_sigla,
                                            cadastro_materias.id as id
                                    FROM cadastro_materias 
                                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo
                                    LEFT JOIN cadastro_materias_assuntos ON cadastro_materias_assuntos.materia = cadastro_materias.id
                                    LEFT JOIN ( cadastro_materias_autoria 
                                        LEFT JOIN (aux_autoria_autores 
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                                        ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                                    ON cadastro_materias_autoria.materia = cadastro_materias.id      
                                    WHERE " . $tipo_query . " AND " . $protocolo_query . " AND " . $numero_query . " AND " . $ano_query . "  AND " . $data_query . "   AND " . $autor_query . "   AND " . $assunto_query . "    AND " . $ementa_query." AND
                                        cadastro_materias.ativo = :ativo            
                                    GROUP BY cadastro_materias.id
                                    ORDER BY cadastro_materias.id DESC
                                    LIMIT :primeiro_registro, :num_por_pagina
                                    ";

                            // Paginação
                            $cnt = "SELECT COUNT(DISTINCT(cadastro_materias.id)) as count FROM cadastro_materias 
                                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo     
                                    LEFT JOIN cadastro_materias_assuntos ON cadastro_materias_assuntos.materia = cadastro_materias.id
                                    LEFT JOIN ( cadastro_materias_autoria 
                                        LEFT JOIN (aux_autoria_autores 
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                                        ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                                    ON cadastro_materias_autoria.materia = cadastro_materias.id      
                                    WHERE " . $tipo_query . "  AND " . $protocolo_query . " AND " . $numero_query . " AND " . $ano_query . "  AND " . $data_query . " AND " . $autor_query . "   AND " . $assunto_query . "  AND " . $ementa_query."                        
                                    ";
                            $stmt = $PDO_PROCLEGIS->prepare($cnt);
                            $stmt->bindParam(':tipo_materia',     $tipo_materia);
                            $stmt->bindParam(':protocolo',     $protocolo);
                            $stmt->bindParam(':numero_materia',     $numero_materia);
                            $stmt->bindParam(':ano_materia',     $ano_materia);
                            $stmt->bindParam(':autor',     $autor);
                            $stmt->bindParam(':assunto',     $assunto);
                            $stmt->bindParam(':ementa',     $ementa);
                            $stmt->bindValue(':ativo', 1); 
                            $stmt->execute();
                            $res = $stmt->fetch();
                            $total_linhas = $res['count'];                            
                            //list($total_linhas) = $stmt->fetch();
                            $variavel = "&tipo_materia=$tipo_materia&protocolo=$protocolo&numero_materia=$numero_materia&ano_materia=$ano_materia&fil_data_inicio=$fil_data_inicio&fil_data_fim=$fil_data_fim&autor=$autor&assunto=$assunto&ementa=$ementa";
                            //

                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                            $stmt_int->bindParam(':primeiro_registro',     $primeiro_registro);
                            $stmt_int->bindParam(':num_por_pagina',     $num_por_pagina);
                            $stmt_int->bindParam(':tipo_materia',     $tipo_materia);
                            $stmt_int->bindParam(':protocolo',     $protocolo);
                            $stmt_int->bindParam(':numero_materia',     $numero_materia);
                            $stmt_int->bindParam(':ano_materia',     $ano_materia);
                            $stmt_int->bindParam(':autor',     $autor);
                            $stmt_int->bindParam(':assunto',     $assunto);
                            $stmt_int->bindParam(':ementa',     $ementa);
                            $stmt_int->bindValue(':ativo', 1); 
                            $stmt_int->execute();
                            $rows_int = $stmt_int->rowCount();

                            ?>
                            <form name='form_materia' id='form' enctype='multipart/form-data' method='post' action='materias'>
                                <select type='text' id='tipo_materia' name='tipo_materia'>
                                    <option value=''>Tipo de Matéria</option>
                                    <?php
                                    $sql = " SELECT * FROM aux_materias_tipos WHERE ativo = :ativo
                                                ORDER BY nome";
                                    $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_filtro->bindValue(':ativo', 1); 
                                    $stmt_filtro->execute();
                                    while ($result_filtro = $stmt_filtro->fetch()) {
                                        echo "<option value='" . $result_filtro['id'] . "' ";
                                        if ($_POST['tipo_materia'] == $result_filtro['id']) echo " selected ";
                                        echo ">" . $result_filtro['nome'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type='text' id='protocolo' name='protocolo'  value='<?php echo $protocolo; ?>' placeholder='Protocolo'>
                                <input type='text' id='numero_materia' name='numero_materia' value='<?php echo $numero_materia; ?>' placeholder='Número da Matéria'>
                                <input type='text' id='ano_materia' name='ano_materia' value='<?php echo $ano_materia; ?>' placeholder='Ano da Matéria'>
                                <input type='text' id='fil_data_inicio' name='fil_data_inicio' value='<?php echo reverteData($fil_data_inicio); ?>' placeholder='Data Inicial'>
                                <input type='text' id='fil_data_fim' name='fil_data_fim' value='<?php echo reverteData($fil_data_fim); ?>' placeholder='Data Final'>
                                <select type='text' id='autor' name='autor' >
                                    <option value=''>Autor</option>
                                    <?php
                                        $sql = " SELECT * FROM aux_autoria_autores ORDER BY nome";
                                        $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                            
                                        $stmt_filtro->execute();
                                        while($result_filtro = $stmt_filtro->fetch())
                                        {
                                            echo "<option value='".$result_filtro['id']."' ";
                                            if ($_POST['autor'] == $result_filtro['id']) echo " selected ";
                                            echo ">".$result_filtro['nome']."</option>";
                                        }                        
                                    ?>
                                </select>            
                                <select type='text' id='assunto' name='assunto' >
                                    <option value=''>Assunto</option>
                                    <?php
                                        $sql = " SELECT * FROM aux_materias_assuntos ORDER BY descricao";
                                        $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                            
                                        $stmt_filtro->execute();
                                        while($result_filtro = $stmt_filtro->fetch())
                                        {
                                            echo "<option value='".$result_filtro['id']."' ";
                                            if ($_POST['assunto'] == $result_filtro['id']) echo " selected ";
                                            echo ">".$result_filtro['descricao']."</option>";
                                        }                        
                                    ?>
                                </select>  
                                <input type='text' id='ementa' name='ementa' placeholder='Ementa (digite a palavra desejada)'>
                                <input type='submit' value='Pesquisar'>
                            </form>
                            <hr>
                            <?php
                            echo "<p>Foram encontradas <span class='bold'>" . $total_linhas . "</span> matérias legislativas.</p>";

                            if ($rows_int > 0) {
                                while ($result_int = $stmt_int->fetch()) {
                                    $id = $result_int['id'];

                                    // AUTORES
                                    $autor = array();
                                    $sql = "SELECT *, 
                                            cadastro_parlamentares.nome as parlamentar
                                            FROM cadastro_materias_autoria
                                            LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor  
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar                                  
                                            WHERE cadastro_materias_autoria.materia = :materia	";
                                    $stmt_aut = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_aut->bindParam(':materia',     $id);
                                    $stmt_aut->execute();
                                    $rows_aut = $stmt_aut->rowCount();
                                    if ($rows_aut > 0) {
                                        while ($result_aut = $stmt_aut->fetch()) {
                                            $autor[] = $result_aut['parlamentar'];
                                        }
                                    }

                                    if ($c == 0) {
                                        $c1 = "linhaimpar";
                                        $c = 1;
                                    } else {
                                        $c1 = "linhapar";
                                        $c = 0;
                                    }
                                    echo "                            
                                    <div class='blocos ' >
                                        <a href='materia_legislativa/" . $result_int['id'] . "'>
                                            <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag&fil_tipo=$fil_tipo\");'>
                                                " . $result_int['tipo_sigla'] . " " . $result_int['numero'] . " de " . $result_int['ano'] . " - " . $result_int['tipo_nome'] . "
                                            </p>
                                            <span class='bold'>Ementa:</span> " . $result_int['ementa'] . "<br>
                                            <span class='bold'>Data apresentação:</span> " . reverteData($result_int['data_apresentacao']) . "<br>
                                            <span class='bold'>Autor(es):</span> " . implode(", ", $autor) . "<br>
                                            ";
                                    if ($result_int['texto_original']) {
                                        echo "<span class='bold'>Texto original:</span> <a href='admin/" . $result_int['texto_original'] . "' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";
                                    }
                                    echo "
                                            
                                        </a>
                                    </div>
                                    ";
                                }

                                include("../core/mod_includes/php/paginacao.php");
                            } else {
                                echo "Nenhum registro encontrado com os dados filtrados.";
                            }
                        echo "</div>"; 
                    ?>

            </div>
        </div>
        <?php
        include('mod_rodape_portal/rodape.php');
        ?>
    </main>
</body>

</html>

