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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> <a href='normas/'>Leis e Normas</a>
        </div>
    </header>
    <main>
        <div class='vereadores'>
            <div class="wrapper">
                <div class='bloco'>
                    <?php
                        $sql = "SELECT *, t1.nome as tipo_nome,
                        t1.sigla as tipo_sigla,
                        t2.sigla as tipo_sigla_materia,
                        t2.nome as tipo_nome_materia,
                        cadastro_normas_juridicas.id as id,
                        t3.numero as numero_materia,
                        t3.ano as ano_materia,
                        t4.usu_nome as cadastrado_por,
                        t5.usu_nome as alterado_por,
                        cadastro_normas_juridicas.complementar as complementar,                                  
                        cadastro_normas_juridicas.tipo as tipo,
                        cadastro_normas_juridicas.numero as numero,
                        cadastro_normas_juridicas.ano as ano,
                        cadastro_normas_juridicas.alfa as alfa,
                        cadastro_normas_juridicas.iniciativa as iniciativa,
                        cadastro_normas_juridicas.ementa as ementa,
                        cadastro_normas_juridicas.data_apresentacao as data_apresentacao,
                        cadastro_normas_juridicas.esfera as esfera,
                        cadastro_normas_juridicas.data_publicacao as data_publicacao,
                        cadastro_normas_juridicas.vigencia as vigencia,
                        cadastro_normas_juridicas.data_fim_vigencia as data_fim_vigencia,
                        cadastro_normas_juridicas.conteudo as conteudo,
                        cadastro_normas_juridicas.word as word,                    
                        cadastro_normas_juridicas.texto_original as texto_original,
                        cadastro_normas_juridicas.prefeito as prefeito,
                        cadastro_normas_juridicas.presidente as presidente,
                        cadastro_normas_juridicas.status as status,
                        cadastro_normas_juridicas.data_cadastro as data_cadastro,
                        cadastro_normas_juridicas.data_alteracao as data_alteracao                                                                                                                        
                        FROM cadastro_normas_juridicas 
                        LEFT JOIN aux_normas_juridicas_tipos t1 ON t1.id = cadastro_normas_juridicas.tipo                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_normas_juridicas.tipo_materia                                                                                                        
                        LEFT JOIN cadastro_materias t3 ON t3.id = cadastro_normas_juridicas.materia                                                                                                        
                        LEFT JOIN cadastro_usuarios t4 ON t4.usu_id = cadastro_normas_juridicas.cadastrado_por                                                                                                        
                        LEFT JOIN cadastro_usuarios t5 ON t5.usu_id = cadastro_normas_juridicas.alterado_por                                                                                                        
                        WHERE cadastro_normas_juridicas.id = :id";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindParam(':id', $norma);
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0) {
                                            $result = $stmt->fetch();
                                            echo "
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                    <li><a data-toggle='tab' href='#assuntos' id='assuntos-tab'>Assuntos</a></li>        
                                    <li><a data-toggle='tab' href='#revogacoes' id='revogacoes-tab'>Revogações</a></li>        
                                    <li><a data-toggle='tab' href='#anexos' id='anexos-tab'>Anexos</a></li>
                                    <li><a data-toggle='tab' href='#autoria' id='autoria-tab'>Autoria</a></li>                                                               
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active' >
                                        <div style='display:table; width:100%'>
                                            <div class='exib_label'><span class='bold'>Tipo:</span> " . $result['tipo_sigla'] . " - " . $result['tipo_nome'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Número:</span> " . $result['numero'] . " " . $result['alfa'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Ano:</span> " . $result['ano'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Iniciativa:</span> " . $result['iniciativa'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Data Apresentação:</span> " . reverteData($result['data_apresentacao']) . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Esfera Federação:</span> " . $result['esfera'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Data Publicação:</span> " . reverteData($result['data_publicacao']) . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Vigência:</span> " . $result['vigencia'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Data Fim Vigência:</span> " . reverteData($result['data_fim_vigencia']) . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Prefeito:</span> " . $result['prefeito'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Presidente:</span> " . $result['presidente'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Status:</span> " . $result['status'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Complementar? </span>" . $result['complementar'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Tipo Matéria:</span> " . $result['tipo_sigla_materia'] . " - " . $result['tipo_nome_materia'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Matéria:</span> " . $result['numero_materia'] . " de " . $result['ano_materia'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Cadastrado por:</span> " . $result['cadastrado_por'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Alterado por:</span> " . $result['alterado_por'] . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Data cadastro:</span> " . reverteData(substr($result['data_cadastro'], 0, 10)) . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Data alteração:</span> " . reverteData($result['data_alteracao']) . " &nbsp;</div>
                                            <div class='exib_label'><span class='bold'>Doc:</span> ";;
                                            if ($result['word'] != '') {
                                                echo "<a href='admin/" . $result['word'] . "' target='_blank'><i class='fas fa-file-word' style='font-size:20px; color:blue;'></i></a>";
                                            }
                                            echo " &nbsp;</div>
                                            
                                            <div class='exib_label'><span class='bold'>Texto Original:</span> ";;
                                            if ($result['texto_original'] != '') {
                                                echo "<a href='admin/" . $result['texto_original'] . "' target='_blank'><i class='fas fa-file-pdf' style='font-size:20px; color:red;'></i></a>";
                                            }
                                            echo " &nbsp;</div>
                                            <div class='exib_bloco'>
                                                <span class='bold'>HTML:</span>
                                                ";;
                                            if ($result['conteudo'] != '') {
                                                echo "<a href='norma_juridica/" . $result['id'] . "' target='_blank'><i class='far fa-file-alt'></i></a>";
                                            }
                                            echo " &nbsp;
                                            </div><div class='exib_label'><span class='bold'>Ementa:</span> " . $result['ementa'] . " &nbsp;</div>
                                        </div>                                                                                                                                              
                                    </div>                        
                                    <div id='assuntos' class='tab-pane fade in'>
                                        ";
                                            $sql = "SELECT *, cadastro_normas_juridicas_assuntos.id as id_assuntos                                                  
                                                FROM cadastro_normas_juridicas_assuntos 
                                                LEFT JOIN aux_normas_juridicas_assuntos ON aux_normas_juridicas_assuntos.id = cadastro_normas_juridicas_assuntos.assunto
                                                WHERE norma = :norma
                                                ORDER BY cadastro_normas_juridicas_assuntos.id DESC
                                                ";
                                            $stmt = $PDO_PROCLEGIS->prepare($sql);
                                            $stmt->bindParam(':norma',     $norma);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Assunto</td>
                                                </tr>";
                                                $c = 0;
                                                while ($result = $stmt->fetch()) {
                                                    $id_assuntos = $result['id_assuntos'];
                                                    $descricao = $result['descricao'];
                                                    $assunto = $result['assunto'];

                                                    if ($c == 0) {
                                                        $c1 = "linhaimpar";
                                                        $c = 1;
                                                    } else {
                                                        $c1 = "linhapar";
                                                        $c = 0;
                                                    }
                                                    echo "<tr class='$c1'>
                                                            <td>$descricao</td>                                                    
                                                        </tr>";
                                                }


                                                echo "</table>";
                                            } else {
                                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                            }
                                            echo "
                                    </div>
                                    <div id='revogacoes' class='tab-pane fade in'>
                                        ";
                                            $sql = "SELECT *, cadastro_normas_juridicas_revogacoes.id as id_revogacoes  
                                                        , aux_normas_juridicas_tipos.sigla as sigla                                                                                 
                                                FROM cadastro_normas_juridicas_revogacoes 
                                                LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas_revogacoes.tipo_norma
                                                LEFT JOIN cadastro_normas_juridicas ON cadastro_normas_juridicas.id = cadastro_normas_juridicas_revogacoes.norma_revogada                                        
                                                LEFT JOIN aux_normas_juridicas_tipo_vinculo ON aux_normas_juridicas_tipo_vinculo.id = cadastro_normas_juridicas_revogacoes.tipo_vinculo                                        
                                                WHERE norma = :norma
                                                ORDER BY cadastro_normas_juridicas_revogacoes.id DESC
                                                ";
                                            $stmt = $PDO_PROCLEGIS->prepare($sql);
                                            $stmt->bindParam(':norma',     $norma);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();

                                            if ($rows > 0) {
                                                echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Tipo de norma</td>
                                                    <td class='titulo_tabela'>Norma Revogada</td>                                            
                                                    <td class='titulo_tabela'>Tipo de vínculo</td>                                            
                                                </tr>";
                                                $c = 0;
                                                while ($result = $stmt->fetch()) {
                                                    $id_revogacoes = $result['id_revogacoes'];
                                                    $tipo_norma = $result['tipo_norma'];
                                                    $sigla = $result['sigla'];
                                                    $nome = $result['nome'];
                                                    $norma_revogada = $result['norma_revogada'];
                                                    $numero = $result['numero'];
                                                    $ano = $result['ano'];
                                                    $tipo_vinculo = $result['tipo_vinculo'];
                                                    $descricao_ativa = $result['descricao_ativa'];
                                                    $ementa = $result['ementa'];


                                                    if ($c == 0) {
                                                        $c1 = "linhaimpar";
                                                        $c = 1;
                                                    } else {
                                                        $c1 = "linhapar";
                                                        $c = 0;
                                                    }
                                                    echo "<tr class='$c1'>
                                                            <td>$sigla - $nome</td>                                                    
                                                            <td>Nº $numero de $ano</td>
                                                            <td>$descricao_ativa</td>                                                    
                                                        </tr>";
                                                }

                                                echo "</table>";
                                            } else {
                                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                            }
                                            echo "
                                    </div>                              
                                    <div id='autoria' class='tab-pane fade in'>
                                        ";
                                            $sql = "SELECT *, cadastro_normas_juridicas_autoria.id as id_autoria                                                  
                                                FROM cadastro_normas_juridicas_autoria 
                                                LEFT JOIN aux_autoria_tipo_autor ON aux_autoria_tipo_autor.id = cadastro_normas_juridicas_autoria.tipo_autor
                                                LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_normas_juridicas_autoria.autor                                        
                                                WHERE norma = :norma
                                                ORDER BY cadastro_normas_juridicas_autoria.id DESC
                                                ";
                                            $stmt = $PDO_PROCLEGIS->prepare($sql);
                                            $stmt->bindParam(':norma',     $norma);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Autor</td>
                                                    <td class='titulo_tabela'>Primeiro autor?</td>                                            
                                                </tr>";
                                                $c = 0;
                                                while ($result = $stmt->fetch()) {
                                                    $id_autoria = $result['id_autoria'];
                                                    $tipo_autor = $result['tipo_autor'];
                                                    $descricao = $result['descricao'];
                                                    $autor = $result['autor'];
                                                    $nome = $result['nome'];
                                                    $primeiro_autor = $result['primeiro_autor'];

                                                    if ($c == 0) {
                                                        $c1 = "linhaimpar";
                                                        $c = 1;
                                                    } else {
                                                        $c1 = "linhapar";
                                                        $c = 0;
                                                    }
                                                    echo "<tr class='$c1'>
                                                            <td>$nome</td>                                                    
                                                            <td>$primeiro_autor</td>
                                                        </tr>";
                                                }
                                                echo "</table>";
                                            } else {
                                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                            }
                                            echo "
                                    </div>                               
                                    <div id='anexos' class='tab-pane fade in'>
                                        ";
                                            $sql = "SELECT *, cadastro_normas_juridicas_anexos.id as id_anexos                                                  
                                                FROM cadastro_normas_juridicas_anexos 
                                                LEFT JOIN aux_materias_documentos ON aux_materias_documentos.id = cadastro_normas_juridicas_anexos.anexo
                                                WHERE norma = :norma
                                                ORDER BY cadastro_normas_juridicas_anexos.id DESC
                                                ";
                                            $stmt = $PDO_PROCLEGIS->prepare($sql);
                                            $stmt->bindParam(':norma',     $norma);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                echo "
                                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                                <tr>
                                                    <td class='titulo_tabela'>Título</td>
                                                    <td class='titulo_tabela' align='center'>Anexo</td>
                                                </tr>";
                                                $c = 0;
                                                while ($result = $stmt->fetch()) {
                                                    $id_anexos = $result['id_anexos'];
                                                    $titulo = $result['titulo'];
                                                    $anexo = $result['anexo'];


                                                    if ($c == 0) {
                                                        $c1 = "linhaimpar";
                                                        $c = 1;
                                                    } else {
                                                        $c1 = "linhapar";
                                                        $c = 0;
                                                    }
                                                    echo "<tr class='$c1'>
                                                            <td>$titulo</td>                                                    
                                                            <td  align='center'>";
                                                    if ($anexo != "") {
                                                        echo "<a href='" . $anexo . "' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a>";
                                                    }
                                                    echo "</td>
                                                        </tr>";
                                                }
                                                echo "</table>";
                                            } else {
                                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                            }
                                            echo "
                                    </div>                              
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