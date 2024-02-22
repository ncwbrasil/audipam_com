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
    <main>
	<div id='janela' class='janela' style='display:none;'> </div>
        <div id='materias'>
            <div class="wrapper">
                
                    <?php
                    if ($materia == '') {
                        echo "
                        <div class='bloco'>
                            <p class='title'> Matérias Legislativas</p>";
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
                            $protocolo = $_REQUEST['numero_processo'];
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
                                    WHERE " . $tipo_query . " AND " . $protocolo_query . " AND " . $numero_query . " AND " . $ano_query . "  AND " . $data_query . "   AND " . $autor_query . "   AND " . $assunto_query . "    AND " . $ementa_query."                      
                                    GROUP BY cadastro_materias.id
                                    ORDER BY cadastro_materias.id DESC
                                    LIMIT :primeiro_registro, :num_por_pagina
                                    ";

                            // Paginação
                            $cnt = "SELECT COUNT(*) as count FROM cadastro_materias 
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

                            $stmt->execute();
                            $res = $stmt->fetch();
                            $total_linhas = $res['count'];
                            //list($total_linhas) = $stmt->fetch();
                            $variavel = "&fil_sigla=$fil_sigla";
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
                            $stmt_int->execute();
                            $rows_int = $stmt_int->rowCount();

                            ?>
                            <form name='form_materia' id='form' enctype='multipart/form-data' method='post' action='materias'>
                                <select type='text' id='tipo_materia' name='tipo_materia'>
                                    <option value=''>Tipo de Matéria</option>
                                    <?php
                                    $sql = " SELECT * FROM aux_materias_tipos 
                                                
                                                ORDER BY nome";
                                    $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);

                                    $stmt_filtro->execute();
                                    while ($result_filtro = $stmt_filtro->fetch()) {
                                        echo "<option value='" . $result_filtro['id'] . "' ";
                                        if ($_POST['tipo_materia'] == $result_filtro['id']) echo " selected ";
                                        echo ">" . $result_filtro['nome'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type='text' id='numero_processo' name='numero_processo'  value='<?php echo $numero_processo; ?>' placeholder='Número Processo'>
                                <input type='text' id='numero_materia' name='numero_materia' value='<?php echo $numero_materia; ?>' placeholder='Número da Sessão'>
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
                                    $sql = "SELECT *
                                            FROM cadastro_materias_autoria
                                            LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
                                            WHERE cadastro_materias_autoria.materia = :materia	";
                                    $stmt_aut = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_aut->bindParam(':materia',     $id);
                                    $stmt_aut->execute();
                                    $rows_aut = $stmt_aut->rowCount();
                                    if ($rows_aut > 0) {
                                        while ($result_aut = $stmt_aut->fetch()) {
                                            $autor[] = $result_aut['nome'];
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
                                        <a href='materias/" . $result_int['id'] . "'>
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
                    } else {
                        $sql = "SELECT *, t1.nome as tipo_nome,
                        t1.sigla as tipo_sigla,
                        t2.sigla as sigla_externa,
                        t2.nome as nome_externa,
                        t3.sigla as sigla_origem,
                        t3.nome as nome_origem,
                        cadastro_materias.id as id
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos t1 ON t1.id = cadastro_materias.tipo                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_materias.tipo_origem_externa                                         
                        LEFT JOIN aux_materias_origem t3 ON t3.id = cadastro_materias.local_origem                                         
                        WHERE cadastro_materias.id = :id ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);            
                        $stmt->bindParam(':id', $materia);
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();                                                 
                            echo "
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                    <li><a data-toggle='tab' href='#anexada' id='anexada-tab'>Anexadas</a></li>        
                                    <li><a data-toggle='tab' href='#assuntos' id='assuntos-tab'>Assuntos</a></li>
                                    <li><a data-toggle='tab' href='#autoria' id='autoria-tab'>Autoria</a></li>    
                                    <li><a data-toggle='tab' href='#despacho' id='despacho-tab'>Despacho</a></li>    
                                    <li><a data-toggle='tab' href='#doc_acessorio' id='doc_acessorio-tab'>Doc. Acessório</a></li>    
                                    <li><a data-toggle='tab' href='#leg_citada' id='leg_citada-tab'>Leg. Citada</a></li>                 
                                    <li><a data-toggle='tab' href='#tramitacao' id='tramitacao-tab'>Tramitação</a></li>    
                                    <li><a data-toggle='tab' href='#relatoria' id='relatoria-tab'>Relatoria</a></li>                                 
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active' >
                                        <div style='display:table; width:100%'>
                                                <div class='exib_label'><span class='bold'>Tipo:</span> ".$result['tipo_nome']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Número:</span> ".$result['numero']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Ano:</span> ".$result['ano']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Data Apresetação:</span> ".reverteData($result['data_apresentacao'])." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Tipo de apresentação:</span> ".$result['apresentacao']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Protocolo:</span> ".$result['protocolo']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Texto Original:</span> "; ;if($result['texto_original'] != ''){ echo "<a href='admin/".$result['texto_original']."' target='_blank'><i class='fas fa-paperclip' ></i></a>";} echo " &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Apelido:</span> ".$result['apelido']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Dias prazo:</span> ".$result['dias_prazo']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Matéria polêmica?</span> ".$result['materia_polemica']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Objeto:</span> ".$result['objeto']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Regime de tramitação:</span> ".$result['regime_tramitacao']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Em tramitação?</span> ".$result['em_tramitacao']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Fim prazo:</span> ".reverteData($result['data_fim_prazo'])." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Data publicação:</span> ".reverteData($result['data_publicacao'])." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>É complementar?</span> ".$result['complementar']." &nbsp;</div>
                                                <div class='exib_label'><span class='bold'>Ementa:</span> ".$result['ementa']." &nbsp;</div>";
                                            if($result['endereco']!=''){
                                                echo "
                                                    <div class='exib_label'><span class='bold'>Endereço :</span> ".$result['endereco'].",".$result['end_numero']." - ".$result['cidade']."</div>
                                                ";
                                            }
                                        echo "</div>                                                                                                                                              
                                    </div>                        
                                    <div id='anexada' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_anexadas.id as id_anexada                                                  
                                                FROM cadastro_materias_anexadas 
                                                LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias_anexadas.tipo_materia
                                                LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_materias_anexadas.materia_anexada                                        
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_anexadas.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Tipo de matéria</td>
                                                    <td class='titulo_tabela'>Matéria Anexada</td>                                            
                                                    <td class='titulo_tabela'>Data Anexação</td>
                                                    <td class='titulo_tabela'>Data desanexação</td>
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_anexada = $result['id_anexada'];
                                                    $tipo_materia = $result['tipo_materia'];
                                                    $sigla = $result['sigla'];
                                                    $materia_anexada = $result['materia_anexada'];
                                                    $nome = $result['nome'];
                                                    $numero = $result['numero'];
                                                    $ano = $result['ano'];                                 
                                                    $data_anexacao = reverteData($result['data_anexacao']);
                                                    $data_desanexacao = reverteData($result['data_desanexacao']);                                            
                                                    
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$sigla - $nome</td>                                                    
                                                            <td>Nº $numero de $ano</td>
                                                            <td>$data_anexacao</td>
                                                            <td>$data_desanexacao</td>
                                                        </tr>";
                                                }
                                                

                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div>
                                    <div id='assuntos' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_assuntos.id as id_assuntos                                                  
                                                FROM cadastro_materias_assuntos 
                                                LEFT JOIN aux_materias_assuntos ON aux_materias_assuntos.id = cadastro_materias_assuntos.assunto
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_assuntos.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Assunto</td>
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_assuntos = $result['id_assuntos'];
                                                    $descricao = $result['descricao'];
                                                    $assunto = $result['assunto'];                                            
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$descricao</td>                                                    
                                                        </tr>";
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div>  
                                    <div id='autoria' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_autoria.id as id_autoria                                                  
                                                FROM cadastro_materias_autoria 
                                                LEFT JOIN aux_autoria_tipo_autor ON aux_autoria_tipo_autor.id = cadastro_materias_autoria.tipo_autor
                                                LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor  
                                                LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id  =   aux_autoria_autores.parlamentar                               
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_autoria.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();

                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Autor</td>
                                                    <td class='titulo_tabela'>Primeiro autor?</td>                                            
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_autoria = $result['id_autoria'];
                                                    $tipo_autor = $result['tipo_autor'];
                                                    $descricao = $result['descricao'];
                                                    $autor = $result['autor'];
                                                    $nome = $result['nome'];
                                                    $primeiro_autor = $result['primeiro_autor'];                                            
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td><img src='".str_replace("../","",$result['foto'])."' style='width:80px; height:80px; object-fit:cover; border-radius:50%; float:left; margin-right:10px'> <br>$nome</td>                                                    
                                                            <td>$primeiro_autor</td>
                                                        </tr>";
                                                }
                                                

                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div>
                                    <div id='despacho' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_despacho.id as id_despacho                                                  
                                                FROM cadastro_materias_despacho 
                                                LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = cadastro_materias_despacho.comissao
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_despacho.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Comissão</td>
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_despacho = $result['id_despacho'];
                                                    $comissao = $result['comissao'];
                                                    $nome = $result['nome'];
                                                    $sigla = $result['sigla'];
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$sigla - $nome</td>                                                    
                                                        </tr>";
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div>    
                                    <div id='doc_acessorio' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_doc_acessorio.id as id_doc_acessorio                                                  
                                                FROM cadastro_materias_doc_acessorio 
                                                LEFT JOIN aux_materias_documentos ON aux_materias_documentos.id = cadastro_materias_doc_acessorio.tipo_documento
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_doc_acessorio.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Tipo de documento</td>
                                                    <td class='titulo_tabela'>Nome</td>                                            
                                                    <td class='titulo_tabela'>Autor</td>
                                                    <td class='titulo_tabela'>Data</td>
                                                    <td class='titulo_tabela' align='center'>Anexo</td>
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_doc_acessorio = $result['id_doc_acessorio'];
                                                    $tipo_documento = $result['tipo_documento'];
                                                    $descricao = $result['descricao'];
                                                    $nome = $result['nome'];
                                                    $ementa = $result['ementa'];
                                                    $autor = $result['autor'];
                                                    $data = reverteData($result['data']);
                                                    $anexo = $result['anexo'];                                 
                                                    
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$descricao</td>                                                    
                                                            <td>$nome</td>
                                                            <td>$autor</td>
                                                            <td>$data</td>
                                                            <td  align='center'>";if($anexo != ""){ echo "<a href='admin/".$anexo."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a>";} echo "</td>
                                                        </tr>";
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div>  
                                    <div id='leg_citada' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_leg_citada.id as id_leg_citada                                                  
                                                FROM cadastro_materias_leg_citada 
                                                LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_materias_leg_citada.tipo_norma
                                                LEFT JOIN cadastro_normas_juridicas ON cadastro_normas_juridicas.id = cadastro_materias_leg_citada.norma_juridica                                        
                                                WHERE cadastro_materias_leg_citada.materia = :materia
                                                ORDER BY cadastro_materias_leg_citada.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                                        
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Tipo de norma</td>
                                                    <td class='titulo_tabela'>Norma jurídica</td>                                            
                                                    <td class='titulo_tabela'>Citações</td>                                            
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_leg_citada = $result['id_leg_citada'];
                                                    $tipo_norma = $result['tipo_norma'];
                                                    $sigla = $result['sigla'];
                                                    $nome = $result['nome'];
                                                    $norma_juridica = $result['norma_juridica'];
                                                    $numero = $result['numero'];
                                                    $ano = $result['ano'];                                 
                                                    $ementa = $result['ementa'];                                 
                                                    $disposicao   = $result['disposicao'];
                                                    $parte   = $result['parte'];
                                                    $livro   = $result['livro'];
                                                    $titulo   = $result['titulo'];
                                                    $capitulo   = $result['capitulo'];
                                                    $secao   = $result['secao'];
                                                    $subsecao   = $result['subsecao'];
                                                    $artigo   = $result['artigo'];
                                                    $paragrafo   = $result['paragrafo'];
                                                    $inciso   = $result['inciso'];
                                                    $alinea   = $result['alinea'];
                                                    $item   = $result['item'];

                                                    $citacoes = array_filter(array(
                                                        'Disposição: ' 		    => $disposicao,
                                                        'Parte: ' 		        => $parte,
                                                        'Livro: ' 		        => $livro,
                                                        'Título: '        => $titulo,
                                                        'Capítulo: '        => $capitulo,
                                                        'Seção: '        => $secao,
                                                        'Subseção: '        => $subsecao,
                                                        'Artigo: '        => $artigo,
                                                        'Parágrafo: '        => $paragrafo,
                                                        'Inciso: '        => $inciso,
                                                        'Alínea: '        => $alinea,
                                                        'Item: '        => $item
                                                        ));
                                                    

                                                        
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$sigla - $nome</td>                                                    
                                                            <td>Nº $numero de $ano</td>
                                                            <td>";
                                                            foreach($citacoes as $key => $value)
                                                            {
                                                                echo $key."<span class='bold'>".$value."</span><br>";
                                                            }
                                                            echo "</td>
                                                        </tr>";
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                
                                        echo "
                                    </div> 
                                    <div id='tramitacao' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_tramitacao.id as id_tramitacao
                                                        , aux_materias_status_tramitacao.nome as nome_status                                                   
                                                        , cadastro_usuarios.usu_nome as nome_responsavel 
                                                FROM cadastro_materias_tramitacao 
                                                LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = cadastro_materias_tramitacao.unidade_origem
                                                LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = cadastro_materias_tramitacao.unidade_destino                                        
                                                LEFT JOIN aux_materias_status_tramitacao ON aux_materias_status_tramitacao.id = cadastro_materias_tramitacao.status_tramitacao  
                                                LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_materias_tramitacao.responsavel
                                                WHERE materia = :materia
                                                ORDER BY cadastro_materias_tramitacao.data_tramitacao DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo "
                                            <a onclick='popupWindow(\"pasta_virtual_materias/$materia/\", \"PastaVirtual\", window, 1200, 800);'><div class='g_botao'>Pasta Virtual</div></a>
                                            <br><p><br>
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'  align='center'>Data</td>
                                                    <td class='titulo_tabela'>Tramitação</td>
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_tramitacao = $result['id_tramitacao'];
                                                    $unidade_origem = $result['unidade_origem'];
                                                    
                                                    // PEGA DADOS DA UNIDADE ORIGEM
                                                    $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                                                    , aux_materias_orgaos.nome as nome_orgao
                                                                    , cadastro_comissoes.sigla as sigla_comissao
                                                                    , cadastro_comissoes.nome as nome_comissao
                                                                    , cadastro_parlamentares.nome as nome_parlamentar
                                                            FROM aux_materias_unidade_tramitacao
                                                            LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                                            LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                                            WHERE aux_materias_unidade_tramitacao.id = :id
                                                        ";
                                                    $stmt_origem = $PDO_PROCLEGIS->prepare($sql);                                                
                                                    $stmt_origem->bindParam(':id', 	$unidade_origem);                                    
                                                    if($stmt_origem->execute())
                                                    {
                                                        $result_origem = $stmt_origem->fetch();
                                                        $origem = $result_origem['nome_parlamentar'].$result_origem['sigla_orgao']." ".$result_origem['nome_orgao'].$result_origem['sigla_comissao']." ".$result_origem['nome_comissao'];
                                                    }
                                                    
                                                    
                                                    $unidade_destino = $result['unidade_destino'];
                                                    // PEGA DADOS DA UNIDADE DESTINO
                                                    $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                                                    , aux_materias_orgaos.nome as nome_orgao
                                                                    , cadastro_comissoes.sigla as sigla_comissao
                                                                    , cadastro_comissoes.nome as nome_comissao
                                                                    , cadastro_parlamentares.nome as nome_parlamentar
                                                                    
                                                            FROM aux_materias_unidade_tramitacao
                                                            LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                                            LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                                            WHERE aux_materias_unidade_tramitacao.id = :id
                                                        ";
                                                    $stmt_destino = $PDO_PROCLEGIS->prepare($sql);                                                
                                                    $stmt_destino->bindParam(':id', 	$unidade_destino);                                    
                                                    if($stmt_destino->execute())
                                                    {
                                                        $result_destino = $stmt_destino->fetch();
                                                        $destino = $result_destino['nome_parlamentar'].$result_destino['sigla_orgao']." ".$result_destino['nome_orgao'].$result_destino['sigla_comissao']." ".$result_destino['nome_comissao'];

                                                        $ultima_tramitacao = $result_destino['comissao'];
                                                    }

                                                    $data_tramitacao = reverteData($result['data_tramitacao']);
                                                    $hora_tramitacao = substr($result['hora_tramitacao'],0,5);
                                                    $data_encaminhamento = reverteData($result['data_encaminhamento']);                                            
                                                    $data_fim_prazo = reverteData($result['data_fim_prazo']);                                            
                                                    $status_tramitacao = $result['status_tramitacao'];                                 
                                                    $nome_status = $result['nome_status'];                                 
                                                    $turno = $result['turno'];
                                                    $urgente = $result['urgente'];
                                                    $texto_acao = $result['texto_acao'];
                                                    $responsavel = $result['responsavel'];
                                                    $nome_responsavel = $result['nome_responsavel'];
                                                    $confirmacao_recebimento = $result['confirmacao_recebimento'];
                                                    $usu_recebimento = $result['usu_recebimento'];
                                                    $anexo = $result['anexo'];
                                                    $paginas = $result['paginas'];
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td align='center' valign='top'>$data_tramitacao<br>$hora_tramitacao</td>
                                                            <td>
                                                                <span class='bold'>$origem <i class='fas fa-long-arrow-alt-right'></i> $destino </span> <br>
                                                                ";if($anexo != '')
                                                                { 
                                                                    echo "<a href='admin/".$anexo."' target='_blank'><i class='far fa-file' style='vertical-align:bottom; font-size:20px; margin-right: 7px;'></i>Documento juntado</a>";
                                                                    if($paginas)
                                                                    {
                                                                        echo " - página(s) ".$paginas;
                                                                    }
                                                                    echo "<br>";
                                                                } echo "
                                                                $nome_status <br>
                                                                $texto_acao <br>
                                                            </td>";                                                 
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }                                   
                                        echo "
                                    </div>
                                    <div id='relatoria' class='tab-pane fade in'>
                                        ";
                                        $sql = "SELECT *, cadastro_materias_relatoria.id as id_relatoria
                                                        , cadastro_materias_relatoria.data_designacao as data_designacao 
                                                        , cadastro_materias_relatoria.parlamentar as parlamentar
                                                        , cadastro_comissoes.sigla as sigla
                                                        , cadastro_comissoes.nome as nome_comissao
                                                        , aux_comissoes_periodos.data_inicio as data_inicio
                                                        , aux_comissoes_periodos.data_fim as data_fim
                                                        , cadastro_materias_relatoria.comissao as comissao 
                                                        , aux_materias_tipo_fim_relatoria.descricao as descricao_motivo                  
                                                FROM cadastro_materias_relatoria
                                                LEFT JOIN ( aux_comissoes_periodos 
                                                    LEFT JOIN cadastro_comissoes_composicao ON cadastro_comissoes_composicao.periodo = aux_comissoes_periodos.id )
                                                ON aux_comissoes_periodos.id = cadastro_materias_relatoria.periodo
                                                LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = cadastro_materias_relatoria.comissao                                        
                                                LEFT JOIN aux_materias_tipo_fim_relatoria ON aux_materias_tipo_fim_relatoria.id = cadastro_materias_relatoria.motivo_fim_relatoria
                                                LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_materias_relatoria.parlamentar                                        
                                                WHERE materia = :materia
                                                GROUP BY id_relatoria
                                                ORDER BY cadastro_materias_relatoria.id DESC
                                                ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                        $stmt->bindParam(':materia', 	$materia);                                    
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0)
                                        {
                                            echo " 
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Comissão</td>
                                                    <td class='titulo_tabela'>Período da Composição</td>
                                                    <td class='titulo_tabela'>Parlamentar</td>                                            
                                                    <td class='titulo_tabela'>Data Designação</td>                                            
                                                </tr>";
                                                $c=0;
                                                while($result = $stmt->fetch())
                                                {
                                                    $id_relatoria = $result['id_relatoria'];
                                                    $sigla = $result['sigla'];
                                                    $data_inicio = reverteData($result['data_inicio']);
                                                    $data_fim = reverteData($result['data_fim']);                                            
                                                    $data_designacao = reverteData($result['data_designacao']);                                            
                                                    $data_destituicao = reverteData($result['data_destituicao']);                                            
                                                    $parlamentar = $result['parlamentar'];                                                                                                                    
                                                    $nome = $result['nome'];                                                                                                                    
                                                    $comissao = $result['comissao'];                                                                                                                    
                                                    $nome_comissao = $result['nome_comissao'];
                                                    $periodo = $result['periodo'];
                                                    $motivo_fim_relatoria = $result['motivo_fim_relatoria'];
                                                    $descricao_motivo = $result['descricao_motivo'];
                                                    
                                                    if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                    echo "<tr class='$c1'>
                                                            <td>$sigla - $nome_comissao</td>                                                    
                                                            <td>$data_inicio - $data_fim</td>                                                    
                                                            <td>$nome</td>
                                                            <td>$data_designacao</td>                                                    
                                                        </tr>";
                                                }
                                                echo "</table>";                                        
                                        }
                                        else
                                        {
                                            echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                        }    

                                        echo "
                                    </div>
                                </div>
                            ";
                        }

                        else {
                            echo "Nenhuma matéria encontrada"; 
                        }
                        echo "                    
                            <div class='acompanhar'>
                                <p class='titulo'> Deseja acompanhar esta Matéria Legislativa?
                                <form id='form' enctype='multipart/form-data' method='post' action='materias/$materia/enviar'>
                                    <input type='hidden' id='materia' name='materia' value='$materia'>
                                    <input type='email' id='email' name='email' placeholder='Email' class='obg'>
                                    <input type='submit' id='bt_enviar' value='Acompanhar'>
                                </form>
                            </div>
                        </div>"; 

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

<?php
    if($pagina =='enviar'){
        $materia = $_POST['materia']; 
        $email = $_POST['email']; 

        $sql_conf = "SELECT * FROM aux_acompanhar_materia WHERE am_materia = :am_materia AND am_email = :am_email ";
        $stmt_conf = $PDO_PROCLEGIS->prepare($sql_conf);
        $stmt_conf->bindValue(':am_materia', $materia);
        $stmt_conf->bindValue(':am_email', $email);
        $stmt_conf->execute(); 
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            ?>
                <script>
                    abreMask('Este e-mail já esta cadastrado para receber notificações desta Matéria Legislativa! <br><br>' +
                    '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                </script>
            <?php 

        }
        else {
            $sql = "INSERT INTO aux_acompanhar_materia SET am_materia = :am_materia, am_email = :am_email ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindValue(':am_materia', $materia);
            $stmt->bindValue(':am_email', $email);
            if ($stmt->execute()) {
            ?>
                <script>
                    abreMask('Cadastro realizado com sucesso! <br><br>' +
                    '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                </script>
            <?php 
            }else {
                ?>
                <script>
                    abreMask('Falha ao tentar realizar operação, tente novamente mais tarde! <br><br>' +
                    '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                </script>
            <?php 
            }
        }
    }
?>