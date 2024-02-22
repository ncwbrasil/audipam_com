<?php
$pagina_link = 'docadm_documentos';
include_once("../../core/mod_includes/php/funcoes.php");
include_once("../../core/mod_includes/php/funcoes_certificado.php");
sec_session_start();
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
    include_once('url.php');
    include_once("../../core/mod_includes/php/dadosGerais.php");
    ?>
    <!-- META TAGS -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Audipam">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Processo Legislativo | <?php echo $cli_nome; ?></title>

    <!-- ESTILO E JQUERY -->
    <link rel="shortcut icon" href="../../core/imagens/favicon.png">
    <link href="../../core/mod_menu/css/reset.css" rel="stylesheet"> <!-- CSS reset -->
    <link href="../mod_includes/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../../core/mod_includes/js/funcoes.js" type="text/javascript"></script>

    <!-- TOOLBAR -->
    <link href="../../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
    <link href="../../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
    <script src="../../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>

    <!-- ui -->
    <link href="../../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet">
    <script src="../../core/mod_includes/js/janela/jquery-ui.js"></script>

    <!-- ABAS -->
    <link href="../../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
    <script src="../../core/mod_includes/js/abas/bootstrap.js"></script>

    <!-- Material Design Bootstrap -->
    <link href="../../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">

    <!-- JS TREE -->
    <link rel="stylesheet" href="../../core/mod_includes/js/jstree/dist/themes/default/style.min.css" />

    <?php
    require_once('../mod_includes/php/funcoes-jquery.php');
    ?>
    <style>
        #formulario {
            width: 90%;
            max-width: 650px;
            display: table;
            margin: 0 auto;
            float: none;
            padding: 5%;
            border: 1px solid #ccc;
            display: table;
            background: #fff;

        }

        .content-wrapper {
            float: none;
            width: 80%;
            padding: 0;
            margin: 0 auto;
            margin-top: 8%;
        }

        #formulario form {
            width: 100%;
            max-width: 500px;
            display: table;
            margin: 0 auto;
        }

        #formulario form input {
            width: 96%;
            margin: 15px auto;
            padding: 2%;
            float: none;
            display: table;
        }

        #formulario .ttl {
            font-size: 25px;
            text-align: center;
            font-weight: bold;
        }
        .documento {
            display: none;
        }

        .bt_doc{
            margin: 1% auto;
            float: none;
            display: table;
        }
    </style>
        <script>
            function mostrarDocumento(){
                $('#formulario').fadeOut('fast');
                $('.documento').fadeIn('slow');

            }
    </script>
</head>

<body>
    <main class="cd-main-content">
        <!--MENU-->
        <?php //include("../mod_menu/menu.php"); 
        ?>

        <!--CONTEUDO CENTRO-->
        <div class="content-wrapper">
            <div class="mensagem"></div>
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }

            $id_documento   = $id;
            $nome   = $_POST['nome'];
            $email   = $_POST['email'];

            $dados = array(
                'id_documento'             => $id_documento,
                'nome'             => $nome,
                'email'             => $email,
            );

            if ($pagina == 'exib') {
                echo "
                        <div id='formulario'>
                            <div style='display:table; width:100%'>
                                <p class='ttl'> Insira seu nome e-email abaixo para acessar o documento</p>
                                <form enctype='multipart/form-data' method='post' action='docadm_documentos_qrcode/exib/$id/adicionar'>
                                    <input type='text' id='nome' name='nome' placeholder='Nome' required>
                                    <input type='email' id='email' name='email' placeholder='E-mail' required>

                                    <input type='submit' class='bt_doc' value='Acessar'>
                                </form>
                            </div>
                        </div>
                ";


                echo "<div class='documento'>";
                $sql = "SELECT *, t1.nome as tipo_nome,
                                    t1.sigla as tipo_sigla,  
                                    t2.nome as classificacao_nome,                                
                                    docadm_documentos.id as id
                            FROM docadm_documentos 
                            LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = docadm_documentos.tipo                                                             
                            LEFT JOIN aux_administrativo_classificacao t2 ON t2.id = docadm_documentos.classificacao                        
                            LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos.cadastrado_por  
                            WHERE docadm_documentos.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                            <div class='titulo'> Documento Nº  " . $result['numero'] . "</div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                <li><a data-toggle='tab' href='#anexada' id='anexada-tab'>Anexadas</a></li>        
                                <li><a data-toggle='tab' href='#doc_acessorio' id='doc_acessorio-tab'>Doc. Acessório</a></li>    
                                <li><a data-toggle='tab' href='#tramitacao' id='tramitacao-tab'>Tramitação</a></li>    
                                
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active' >
                                    <div style='display:table; width:100%'>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Tipo:</div>
                                            <div class='exib_value'>" . $result['tipo_nome'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Classificação:</div>
                                            <div class='exib_value'>" . $result['classificacao_nome'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Número:</div>
                                            <div class='exib_value'>" . $result['numero'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Ano:</div>
                                            <div class='exib_value'>" . $result['ano'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Data Apresetação:</div>
                                            <div class='exib_value'>" . reverteData($result['data']) . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Interessado:</div>
                                            <div class='exib_value'>" . $result['interessado'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Em tramitação?</div>
                                            <div class='exib_value'>" . $result['em_tramitacao'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Regime de tramitação:</div>
                                            <div class='exib_value'>" . $result['regime_tramitacao'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Doc. restrito?</div>
                                            <div class='exib_value'>" . $result['restrito'] . " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Texto Original:</div>
                                            <div class='exib_value'>";;
                    if ($result['texto_original'] != '') {
                        echo "<a href='" . $result['texto_original'] . "' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";
                    }
                    echo " &nbsp;</div>
                                        </div>
                                        <div class='exib_bloco'>
                                            <div class='exib_label'>Cadastrado por:</div>
                                            <div class='exib_value'>" . $result['usu_nome'] . " &nbsp;</div>
                                        </div>
                                    </div>                                                                                                                                              
                                </div>  

                                <div id='anexada' class='tab-pane fade in'>
                                    ";
                    $sql = "SELECT *, docadm_documentos_anexados.id as id_anexado                                                  
                                            FROM docadm_documentos_anexados 
                                            LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos_anexados.tipo_documento
                                            LEFT JOIN docadm_documentos ON docadm_documentos.id = docadm_documentos_anexados.documento_anexado                                        
                                            WHERE documento = :documento
                                            ORDER BY docadm_documentos_anexados.id DESC
                                        ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':documento',     $id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Tipo de documento</td>
                                                <td class='titulo_tabela'>Documento Anexado</td>                                            
                                                <td class='titulo_tabela'>Data Anexação</td>
                                                <td class='titulo_tabela'>Data desanexação</td>
                                                <td class='titulo_tabela' align='right'>Gerenciar</td>
                                            </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $id_anexado = $result['id_anexado'];
                            $tipo_documento = $result['tipo_documento'];
                            $sigla = $result['sigla'];
                            $documento_anexado = $result['documento_anexado'];
                            $nome = $result['nome'];
                            $numero = $result['numero'];
                            $ano = $result['ano'];
                            $data_anexacao = reverteData($result['data_anexacao']);
                            $data_desanexacao = reverteData($result['data_desanexacao']);


                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                        <td>$sigla - $nome</td>                                                    
                                                        <td><a href='docadm_documentos/exib/" . $documento_anexado . "' target='_blank'>Nº $numero de $ano</a></td>
                                                        <td>$data_anexacao</td>
                                                        <td>$data_desanexacao</td>
                                                        <td align=center>
                                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_anexada/$id_anexado#anexada\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                    \">	<i class='far fa-trash-alt'></i>
                                                                </div>
                                                                <div class='g_editar' title='Editar' data-toggle='modal' data-target='#anexadoEdit" . $id_anexado . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                        </td>
                                                    </tr>";
                            include("../mod_includes/modal/Docadm_AnexadoEdit.php");
                        }


                        echo "</table>";
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                    echo "
                                </div>  

                                <div id='doc_acessorio' class='tab-pane fade in'>
                                    ";
                    $sql = "SELECT *, docadm_documentos_doc_acessorio.id as id_doc_acessorio
                                                    , docadm_documentos_doc_acessorio.nome as nome
                                                    , aux_administrativo_tipo_documento.nome as nome_tipo                                                  
                                            FROM docadm_documentos_doc_acessorio 
                                            LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos_doc_acessorio.tipo_documento
                                            WHERE documento = :documento
                                            ORDER BY docadm_documentos_doc_acessorio.id DESC
                                        ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':documento',     $id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Tipo de documento</td>
                                                <td class='titulo_tabela'>Nome</td>                                            
                                                <td class='titulo_tabela'>Autor</td>
                                                <td class='titulo_tabela'>Data</td>
                                                <td class='titulo_tabela' align='center'>Anexo</td>
                                                <td class='titulo_tabela' align='right'>Gerenciar</td>
                                            </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $id_doc_acessorio = $result['id_doc_acessorio'];
                            $tipo_documento = $result['tipo_documento'];
                            $nome_tipo = $result['nome_tipo'];
                            $nome = $result['nome'];
                            $ementa = $result['ementa'];
                            $autor = $result['autor'];
                            $data = reverteData($result['data']);
                            $anexo = $result['anexo'];


                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                        <td>$nome_tipo</td>                                                    
                                                        <td>$nome</td>
                                                        <td>$autor</td>
                                                        <td>$data</td>
                                                        <td  align='center'>";
                            if ($anexo != "") {
                                echo "<a href='" . $anexo . "' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a>";
                            }
                            echo "</td>
                                                        <td align=center>
                                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_doc_acessorio/$id_doc_acessorio#doc_acessorio\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                    \">	<i class='far fa-trash-alt'></i>
                                                                </div>
                                                                <div class='g_editar' title='Editar' data-toggle='modal' data-target='#doc_acessorioEdit" . $id_doc_acessorio . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                        </td>
                                                    </tr>";
                            include("../mod_includes/modal/Docadm_Doc_acessorioEdit.php");
                        }


                        echo "</table>";
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                    echo "
                                </div>  

                                <div id='tramitacao' class='tab-pane fade in'>
                                    ";
                    $sql = "SELECT *, docadm_documentos_tramitacao.id as id_tramitacao
                                                    , aux_administrativo_status_tramitacao.nome as nome_status                                                   
                                                    , cadastro_usuarios.usu_nome as nome_responsavel                                               
                                            FROM docadm_documentos_tramitacao 
                                            LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = docadm_documentos_tramitacao.unidade_origem
                                            LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = docadm_documentos_tramitacao.unidade_destino                                        
                                            LEFT JOIN aux_administrativo_status_tramitacao ON aux_administrativo_status_tramitacao.id = docadm_documentos_tramitacao.status_tramitacao  
                                            LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos_tramitacao.responsavel                                                                                          
                                            WHERE documento = :documento
                                            ORDER BY docadm_documentos_tramitacao.data_tramitacao ASC
                                        ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':documento',     $id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Origem / Destino</td>                                            
                                                <td class='titulo_tabela'>Responsável</td>                                            
                                                <td class='titulo_tabela'>Data tramitação</td>
                                                <td class='titulo_tabela'>Status</td>
                                                <td class='titulo_tabela' align='right'>Gerenciar</td>
                                            </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
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
                            $stmt_origem->bindParam(':id',     $unidade_origem);
                            if ($stmt_origem->execute()) {
                                $result_origem = $stmt_origem->fetch();
                                $origem = $result_origem['nome_parlamentar'] . $result_origem['sigla_orgao'] . " " . $result_origem['nome_orgao'] . $result_origem['sigla_comissao'] . " " . $result_origem['nome_comissao'];
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
                            $stmt_destino->bindParam(':id',     $unidade_destino);
                            if ($stmt_destino->execute()) {
                                $result_destino = $stmt_destino->fetch();
                                $destino = $result_destino['nome_parlamentar'] . $result_destino['sigla_orgao'] . " " . $result_destino['nome_orgao'] . $result_destino['sigla_comissao'] . " " . $result_destino['nome_comissao'];

                                $ultima_tramitacao = $result_destino['comissao'];
                            }

                            $data_tramitacao = reverteData($result['data_tramitacao']);
                            $hora_tramitacao = substr($result['hora_tramitacao'], 0, 5);
                            $data_encaminhamento = reverteData($result['data_encaminhamento']);
                            $data_fim_prazo = reverteData($result['data_fim_prazo']);
                            $status_tramitacao = $result['status_tramitacao'];
                            $nome_status = $result['nome_status'];

                            $urgente = $result['urgente'];
                            $texto_acao = $result['texto_acao'];
                            $anexo = $result['anexo'];
                            $responsavel = $result['responsavel'];
                            $nome_responsavel = $result['nome_responsavel'];


                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                        <td>$origem <i class='fas fa-long-arrow-alt-right' style='font-size:18px; margin:0 5px'></i> $destino</td>
                                                        <td>$nome_responsavel</td>
                                                        <td>$data_tramitacao<br>$hora_tramitacao</td>
                                                        <td>$nome_status</td>
                                                        <td align=center>
                                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_tramitacao/$id_tramitacao#tramitacao\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                    \">	<i class='far fa-trash-alt'></i>
                                                                </div>
                                                                <div class='g_editar' title='Editar' data-toggle='modal' data-target='#tramitacaoEdit" . $id_tramitacao . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                        </td>
                                                    </tr>";
                            include("../mod_includes/modal/Docadm_TramitacaoEdit.php");
                        }



                        echo "</table>";
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                    include("../mod_includes/modal/Docadm_TramitacaoAdd.php");
                    echo "
                                </div>
                            </div>
                        ";
                }
                echo "</div>";
            }

            if ($action == "adicionar") {
                $sql = "INSERT INTO docadm_qrcode SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    ?>
                        <script>
                            mostrarDocumento(); 
                        </script>
                    <?php
                } else {
                    ?>
                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                    <?php
                }
            }

            ?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
    <script>
        //CALENDÁRIOinput
        jQuery("input[name*='data'], #fil_ext_de, #fil_ext_ate").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
            dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });
    </script>
    <!-- MODAL -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/mdb.min.js"></script>




</body>

</html>