<?php
$pagina_link = 'portal_tv_camara';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");

$page = "<a href='portal_tv_camara/view'>TV Câmara</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("header.php")?>

    <!-- DRAGDROP -->
    <link href="../../core/mod_includes/js/dragdrop/dropzone.css" type="text/css" rel="stylesheet" />
    <script src="../../core/mod_includes/js/dragdrop/dropzone.js"></script>

</head>
<body>
	<main class="cd-main-content">
    <?php include("../mod_menu/menu.php"); ?>

    <div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Cadastros &raquo; <a href='portal_tv_camara/view'>TV Câmara</a>";
            if (isset($_GET['tv_id'])) {
                $tv_id = $_GET['tv_id'];
            }
            if ($tv_id == '') {
                $tv_id = $_POST['tv_id'];
            }
            $tv_titulo          = $_POST['tv_titulo'];
            $tv_url             = $_POST['tv_url'];
            $tv_data            = implode("-", array_reverse(explode("/", $_POST['tv_data'])));
            $tv_status          = $_POST['tv_status'];
            $dados = array(
                'tv_titulo'         => $tv_titulo,
                'tv_url'            => $tv_url,
                'tv_data'           => $tv_data,
                'tv_status'         => $tv_status,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO portal_tv_camara SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    ?>
                        <script>
                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
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

            if ($action == 'editar') {

                $sql = "UPDATE portal_tv_camara SET " . bindFields($dados) . " WHERE tv_id = :tv_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['tv_id'] =  $tv_id;
                if ($stmt->execute($dados)) {
                    ?>
                        <script>
                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
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

            if ($action == 'excluir') {
                $sql = "DELETE FROM portal_tv_camara WHERE tv_id = :tv_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':tv_id', $tv_id);
                if ($stmt->execute()) {
                    ?>
                        <script>
                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
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
            if ($action == 'ativar') {
                $sql = "UPDATE portal_tv_camara SET tv_status = :tv_status WHERE tv_id = :tv_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':tv_status', 1);
                $stmt->bindParam(':tv_id', $tv_id);
                $stmt->execute();
            }
            if ($action == 'desativar') {
                $sql = "UPDATE portal_tv_camara SET tv_status = :tv_status WHERE tv_id = :tv_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':tv_status', 0);
                $stmt->bindParam(':tv_id', $tv_id);
                $stmt->execute();
            }

            $num_por_pagina = 20;
            if (!$pag) {
                $primeiro_registro = 0;
                $pag = 1;
            } else {
                $primeiro_registro = ($pag - 1) * $num_por_pagina;
            }
            $fil_nome = $_REQUEST['fil_nome'];
            if ($fil_nome == '') {
                $nome_query = " 1 = 1 ";
            } else {
                $fil_nome1 = "%" . $fil_nome . "%";
                $nome_query = " (tv_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM portal_tv_camara 
                WHERE " . $nome_query . "
                ORDER BY tv_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindParam(':fil_nome1',     $fil_nome1);
            $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
            $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                echo "
                    <div class='titulo'> $page  </div>
                    <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"" . $pagina_link . "/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='portal_tv_camara/view'>
                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>                    
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>
                </div>
                ";
                if ($rows > 0) {
                    echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>TV Câmara</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_tabela' align='center'>URL</td>
                        <td class='titulo_tabela align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $tv_id      = $result['tv_id'];
                        $tv_titulo  = $result['tv_titulo'];
                        $tv_url     = str_replace("https://www.youtube.com/watch?v=", "", str_replace("&ab_channel=C%C3%A2maraMunicipaldeMogidasCruzes", "", $result['tv_url'])); 
                        $tv_data    = implode("/", array_reverse(explode("-", $result['tv_data'])));

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td><iframe width='100px' height='80px' src='https://www.youtube.com/embed/$tv_url' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe></td>
                                <td>$tv_titulo</td>
                                <td align='center'>$tv_data</td>
                                <td align='center'>https://www.youtube.com/watch?v=$tv_url&ab_channel=C%C3%A2maraMunicipaldeMogidasCruzes</td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$tv_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$tv_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM portal_tv_camara WHERE " . $nome_query . "  ";
                    $stmt = $PDO_PROCLEGIS->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='portal_tv_camara/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='tv_titulo' id='tv_titulo' placeholder='Título' class='obg' >
                        <p><label>URL *:</label> <input name='tv_url' id='tv_url' placeholder='URL' class='obg' >
                        <p><label>Data:</label> <input name='tv_data' id='tv_data' placeholder='Data' class='obg'>
                        <p><label>Status:</label> <input type='radio' name='tv_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='tv_status' value='0'> Inativo<br>		
                        				
                    </div>                    
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='portal_tv_camara/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
            }
            if ($pagina == 'edit') {
                $sql = "SELECT * FROM portal_tv_camara 
					WHERE tv_id = :tv_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':tv_id', $tv_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='portal_tv_camara/view/editar/$tv_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='tv_titulo' id='tv_titulo' value='" . $result['tv_titulo'] . "' placeholder='Título' class='obg' >
                                <p><label>URL*:</label> <input name='tv_url' id='tv_url' value='" . $result['tv_url'] . "' placeholder='URL' class='obg' >
                                <p><label>Data:</label> <input name='tv_data' id='tv_data' value='" . implode("/", array_reverse(explode("-", $result['tv_data']))) . "' placeholder='Data' class='obg'>
                                <p><label>Status:</label>";
                            if ($result['tv_status'] == 1) {
                                echo "<input type='radio' name='tv_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='tv_status' value='0'> Inativo
                                        ";
                            } else {
                                echo "<input type='radio' name='tv_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='tv_status' value='0' checked> Inativo
                                        ";
                            }
                            echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='portal_tv_camara/view'; value='Cancelar'/></center>
						</center>
                    </div>
                </form>
                ";
                }
            }

            ?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
</body>

</html>