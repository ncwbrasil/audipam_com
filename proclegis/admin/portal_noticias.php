<?php
$pagina_link = 'portal_noticias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");

$page = "<a href='portal_noticias/view'>Notícias</a>"; 

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
            $page = "Cadastros &raquo; <a href='portal_noticias/view'>Notícias</a>";
            if (isset($_GET['nt_id'])) {
                $nt_id = $_GET['nt_id'];
            }
            if ($nt_id == '') {
                $nt_id = $_POST['nt_id'];
            }
            $nt_titulo          = $_POST['nt_titulo'];
            $nt_descricao       =  $_POST['nt_descricao'];
            $nt_url             = geradorTags($nt_titulo);
            $nt_data            = implode("-", array_reverse(explode("/", $_POST['nt_data'])));
            $nt_status          = $_POST['nt_status'];
            $nt_usuario         =  $_SESSION['usuario_id']; 
            $dados = array(
                'nt_titulo'         => $nt_titulo,
                'nt_descricao'      => $nt_descricao,
                'nt_url'            => $nt_url,
                'nt_data'           => $nt_data,
                'nt_status'         => $nt_status,
                'nt_usuario'        => $nt_usuario,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO portal_noticias SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $nt_id = $PDO_PROCLEGIS->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/noticias/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $nt_imagem    = $caminho;
                                $nt_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($nt_imagem));
                                $imnfo = getimagesize($nt_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($nt_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($nt_imagem);
                                }
                                $sql = "UPDATE portal_noticias SET 
                                    nt_imagem 	 = :nt_imagem
                                    WHERE nt_id = :nt_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':nt_imagem', $nt_imagem);
                                $stmt->bindParam(':nt_id', $nt_id);

                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }
                            }
                        }
                    }
   
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

                $sql = "UPDATE portal_noticias SET " . bindFields($dados) . " WHERE nt_id = :nt_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['nt_id'] =  $nt_id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/noticias/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                # EXCLUI ANEXO ANTIGO #
                                $sql = "SELECT * FROM portal_noticias WHERE nt_id = :nt_id";
                                $stmt_antigo = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_antigo->bindParam(':nt_id', $nt_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $imagem_antigo = $result_antigo['nt_imagem'];
                                unlink($imagem_antigo);

                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $nt_imagem    = $caminho;
                                $nt_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($nt_imagem));
                                $imnfo = getimagesize($nt_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($nt_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($nt_imagem);
                                }
                                $sql = "UPDATE portal_noticias SET 
									nt_imagem 	 = :nt_imagem
									WHERE nt_id = :nt_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':nt_imagem', $nt_imagem);
                                $stmt->bindParam(':nt_id', $nt_id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }
                            }
                        }
                    }
                    //
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
                $sql = "DELETE FROM portal_noticias WHERE nt_id = :nt_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':nt_id', $nt_id);
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
                $sql = "UPDATE portal_noticias SET nt_status = :nt_status WHERE nt_id = :nt_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':nt_status', 1);
                $stmt->bindParam(':nt_id', $nt_id);
                $stmt->execute();
            }
            if ($action == 'desativar') {
                $sql = "UPDATE portal_noticias SET nt_status = :nt_status WHERE nt_id = :nt_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':nt_status', 0);
                $stmt->bindParam(':nt_id', $nt_id);
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
                $nome_query = " (nt_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM portal_noticias 
                WHERE " . $nome_query . "
                ORDER BY nt_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='portal_noticias/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>Notícia</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $nt_id         = $result['nt_id'];
                        $nt_titulo    = $result['nt_titulo'];
                        $nt_data    = implode("/", array_reverse(explode("-", $result['nt_data'])));
                        $nt_imagem    = $result['nt_imagem'];

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td width='1'><img src='$nt_imagem' style='object-fit:cover; width:120px; height:80px'></td>
                                <td>$nt_titulo</td>
                                <td align='center'>$nt_data</td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$nt_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$nt_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM portal_noticias WHERE " . $nome_query . "  ";
                    $stmt = $PDO_PROCLEGIS->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='portal_noticias/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='nt_titulo' id='nt_titulo' placeholder='Título' class='obg' >
						<p><label>Descrição*:</label> <div class='textarea'><textarea  name='nt_descricao' id='nt_descricao' placeholder='Descrição'></textarea></div>
                        <p><label>Imagem:</label> <input type='file' name='nt_imagem[imagem]' id='nt_imagem' class='obg'>
                        <p><label>Data:</label> <input name='nt_data' id='nt_data' placeholder='Data' class='obg'>
                        <p><label>Status:</label> <input type='radio' name='nt_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='nt_status' value='0'> Inativo<br>		
                        				
                    </div>                    
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='portal_noticias/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
            }
            if ($pagina == 'edit') {
                $sql = "SELECT * FROM portal_noticias 
					WHERE nt_id = :nt_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':nt_id', $nt_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='portal_noticias/view/editar/$nt_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='nt_titulo' id='nt_titulo' value='" . $result['nt_titulo'] . "' placeholder='Título' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea name='nt_descricao' id='nt_descricao' placeholder='Descrição'>" . $result['nt_descricao'] . "</textarea></div>
                                <p><label>Data:</label> <input name='nt_data' id='nt_data' value='" . implode("/", array_reverse(explode("-", $result['nt_data']))) . "' placeholder='Data' class='obg'>
                                <p><label>Imagem:</label> ";
                            if ($result['nt_imagem'] != '') {
                                echo "<img src='" . $result['nt_imagem'] . "' style='max-width:400px;'> ";
                            }
                            echo " &nbsp; 
                                <p><label>Alterar Imagem:</label> <input type='file' name='nt_imagem[imagem]' id='nt_imagem'>
                                <p><label>Status:</label>";
                            if ($result['nt_status'] == 1) {
                                echo "<input type='radio' name='nt_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='nt_status' value='0'> Inativo
                                        ";
                            } else {
                                echo "<input type='radio' name='nt_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='nt_status' value='0' checked> Inativo
                                        ";
                            }
                            echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='portal_noticias/view'; value='Cancelar'/></center>
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