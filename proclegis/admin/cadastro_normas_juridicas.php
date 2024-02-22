<?php
$pagina_link = 'cadastro_normas_juridicas';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start();
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php"); ?> 

</head>
<body>
    <main class="cd-main-content">    
        <!--MENU-->
        <?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
        <div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Cadastro &raquo; <a href='cadastro_normas_juridicas/view'>Normas Jurídicas</a>";
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            $tipo = $_POST['tipo'];
            $numero = $_POST['numero'];
            $alfa = $_POST['alfa'];
            $ano = $_POST['ano'];
            $ementa = $_POST['ementa'];
            $iniciativa = $_POST['iniciativa'];
            $data_apresentacao = reverteData($_POST['data_apresentacao']);
            if ($data_apresentacao == "") {
                $data_apresentacao = null;
            }
            $esfera = $_POST['esfera'];
            $data_publicacao = reverteData($_POST['data_publicacao']);
            if ($data_publicacao == "") {
                $data_publicacao = null;
            }
            $vigencia = $_POST['vigencia'];
            $data_fim_vigencia = reverteData($_POST['data_fim_vigencia']);
            if ($data_fim_vigencia == "") {
                $data_fim_vigencia = null;
            }
            $prefeito = $_POST['prefeito'];
            $presidente = $_POST['presidente'];
            $status = $_POST['status'];
            $complementar = $_POST['complementar'];
            $tipo_materia = $_POST['tipo_materia'];
            if ($tipo_materia == "") {
                $tipo_materia = null;
            }
            $materia = $_POST['materia'];
            if ($materia == "") {
                $materia = null;
            }
            $conteudo = $_POST['conteudo'];

            $dados = array(

                'tipo' => $tipo,
                'numero' => $numero,
                'alfa' => $alfa,
                'ano' => $ano,
                'ementa' => $ementa,
                'iniciativa' => $iniciativa,
                'data_apresentacao' => $data_apresentacao,
                'esfera' => $esfera,
                'data_publicacao' => $data_publicacao,
                'vigencia' => $vigencia,
                'data_fim_vigencia' => $data_fim_vigencia,
                'prefeito' => $prefeito,
                'presidente' => $presidente,
                'status' => $status,
                'complementar' => $complementar,
                'tipo_materia' => $tipo_materia,
                'materia' => $materia,
                'conteudo' => $conteudo,
                'cadastrado_por' => $_SESSION['usuario_id']
            );

            if ($action == "adicionar") {
                $sql = "INSERT INTO cadastro_normas_juridicas SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/normas_juridicas/pdf/";
                    foreach ($_FILES as $key => $files) {

                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["texto_original"])) {

                                $nomeArquivo = $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original = $caminho;
                                $texto_original .= "texto_original_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                // $imnfo = getimagesize($texto_original);
                                // $img_w = $imnfo[0]; // largura
                                // $img_h = $imnfo[1]; // altura
                                // if ($img_w > 500 || $img_h > 500) {
                                //     $image = WideImage::load($texto_original);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($texto_original);
                                // }

                                $sql = "UPDATE cadastro_normas_juridicas SET 
                                        texto_original 	 = :texto_original
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':texto_original', $texto_original);
                                $stmt->bindParam(':id', $id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    //    
                    //UPLOAD ARQUIVOS DOC
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/normas_juridicas/doc/";

                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["word"])) {

                                $nomeArquivo = $files["name"]["word"];
                                $nomeTemporario = $files["tmp_name"]["word"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $word = $caminho;
                                $word .= "word_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($word));
                                // $imnfo = getimagesize($word);
                                // $img_w = $imnfo[0]; // largura
                                // $img_h = $imnfo[1]; // altura
                                // if ($img_w > 500 || $img_h > 500) {
                                //     $image = WideImage::load($word);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($word);
                                // }

                                $sql = "UPDATE cadastro_normas_juridicas SET 
                                        word 	 = :word
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':word', $word);
                                $stmt->bindParam(':id', $id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($word);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    //    
                    if ($erro != 1) {
                        log_operacao($id, $PDO_PROCLEGIS);
                        ?>
                                                            <script>
                                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                                            </script>
                                                            <?php
                    } else {
                        ?>
                                                            <script>
                                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg']; ?>");
                                                            </script>
                                                        <?php


                    }

                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }

            if ($action == 'editar') {
                $sql = "UPDATE cadastro_normas_juridicas SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] = $id;
                if ($stmt->execute($dados)) {

                    //UPLOAD ARQUIVOS PDF
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/normas_juridicas/pdf/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["texto_original"])) {
                                $nomeArquivo = $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original = $caminho;
                                $texto_original .= "texto_original_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                // $imnfo = getimagesize($texto_original);
                                // $img_w = $imnfo[0]; // largura
                                // $img_h = $imnfo[1]; // altura
                                // if ($img_w > 500 || $img_h > 500) {
                                //     $image = WideImage::load($texto_original);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($texto_original);
                                // }

                                $sql = "UPDATE cadastro_normas_juridicas SET 
                                        texto_original 	 = :texto_original
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':texto_original', $texto_original);
                                $stmt->bindParam(':id', $id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($word);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    //  
            
                    //UPLOAD ARQUIVOS DOC
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/normas_juridicas/doc/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["word"])) {

                                $nomeArquivo = $files["name"]["word"];
                                $nomeTemporario = $files["tmp_name"]["word"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $word = $caminho;
                                $word .= "word_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($word));
                                // $imnfo = getimagesize($word);
                                // $img_w = $imnfo[0]; // largura
                                // $img_h = $imnfo[1]; // altura
                                // if ($img_w > 500 || $img_h > 500) {
                                //     $image = WideImage::load($word);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($word);
                                // }

                                $sql = "UPDATE cadastro_normas_juridicas SET 
                                         word 	 = :word
                                         WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':word', $word);
                                $stmt->bindParam(':id', $id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($word);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    //  
                    log_operacao($id, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg']; ?>");
                                            </script>
                                            <?php
                }
            }

            if ($action == 'excluir') {
                $sql = "SELECT texto_original FROM cadastro_normas_juridicas WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $result = $stmt->fetch();
                    $texto_original = $result['texto_original'];
                }

                $sql = "UPDATE cadastro_normas_juridicas SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindValue(':ativo', 0);
                if ($stmt->execute()) {
                    //unlink($texto_original);
                    log_operacao($id, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php
                }
            }

            if ($action == "adicionar_revogacoes") {
                $tipo_norma = $_POST['tipo_norma'];
                $norma_revogada = $_POST['norma_revogada'];
                $tipo_vinculo = $_POST['tipo_vinculo'];

                $dados = array(
                    'norma' => $id,
                    'tipo_norma' => $tipo_norma,
                    'norma_revogada' => $norma_revogada,
                    'tipo_vinculo' => $tipo_vinculo
                );
                $sql = "INSERT INTO cadastro_normas_juridicas_revogacoes SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id = $PDO_PROCLEGIS->lastInsertId();
                    log_operacao($id, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }
            if ($action == "editar_revogacoes") {
                $id_revogacoes = $_POST['id_revogacoes'];
                $tipo_norma = $_POST['tipo_norma'];
                $norma_revogada = $_POST['norma_revogada'];
                $tipo_vinculo = $_POST['tipo_vinculo'];

                $dados = array(
                    'norma' => $id,
                    'tipo_norma' => $tipo_norma,
                    'norma_revogada' => $norma_revogada,
                    'tipo_vinculo' => $tipo_vinculo
                );


                $sql = "UPDATE cadastro_normas_juridicas_revogacoes SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] = $id_revogacoes;
                if ($stmt->execute($dados)) {
                    log_operacao($id_revogacoes, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }
            if ($action == 'excluir_revogacoes') {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_normas_juridicas_revogacoes SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id_sub);
                $stmt->bindValue(':ativo', 0);
                if ($stmt->execute()) {
                    log_operacao($id_revogacoes, $PDO_PROCLEGIS);
                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php
                }
            }

            if ($action == "adicionar_assuntos") {
                $assunto = $_POST['assunto'];

                $dados = array_filter(
                    array(
                        'norma' => $id,
                        'assunto' => $assunto
                    )
                );
                $sql = "INSERT INTO cadastro_normas_juridicas_assuntos SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id = $PDO_PROCLEGIS->lastInsertId();
                    log_operacao($id, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }

            if ($action == "editar_assuntos") {
                $id_assuntos = $_POST['id_assuntos'];
                $assunto = $_POST['assunto'];

                $dados = array_filter(
                    array(
                        'norma' => $id,
                        'assunto' => $assunto
                    )
                );


                $sql = "UPDATE cadastro_normas_juridicas_assuntos SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] = $id_assuntos;
                if ($stmt->execute($dados)) {
                    log_operacao($id_assuntos, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }

            if ($action == 'excluir_assuntos') {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_normas_juridicas_assuntos SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id_sub);
                $stmt->bindValue(':ativo', 0);
                if ($stmt->execute()) {
                    log_operacao($id_sub, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php
                }
            }

            if ($action == "adicionar_autoria") {
                $tipo_autor = $_POST['tipo_autor'];
                $autor = $_POST['autor'];
                $primeiro_autor = $_POST['primeiro_autor'];

                $dados = array_filter(
                    array(
                        'norma' => $id,
                        'tipo_autor' => $tipo_autor,
                        'autor' => $autor,
                        'primeiro_autor' => $primeiro_autor
                    )
                );
                $sql = "INSERT INTO cadastro_normas_juridicas_autoria SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id = $PDO_PROCLEGIS->lastInsertId();
                    log_operacao($id, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }

            if ($action == "editar_autoria") {
                $id_autoria = $_POST['id_autoria'];
                $tipo_autor = $_POST['tipo_autor'];
                $autor = $_POST['autor'];
                $primeiro_autor = $_POST['primeiro_autor'];

                $dados = array_filter(
                    array(
                        'norma' => $id,
                        'tipo_autor' => $tipo_autor,
                        'autor' => $autor,
                        'primeiro_autor' => $primeiro_autor
                    )
                );


                $sql = "UPDATE cadastro_normas_juridicas_autoria SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] = $id_autoria;
                if ($stmt->execute($dados)) {
                    log_operacao($id_autoria, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }

            if ($action == 'excluir_autoria') {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_normas_juridicas_autoria SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id_sub);
                $stmt->bindValue(':ativo', 0);
                if ($stmt->execute()) {
                    log_operacao($id_sub, $PDO_PROCLEGIS);

                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php
                }
            }

            if ($action == "adicionar_anexos") {

                $titulo = $_POST['titulo'];

                $dados = array_filter(
                    array(
                        'norma' => $id,
                        'titulo' => $titulo
                    )
                );
                $sql = "INSERT INTO cadastro_normas_juridicas_anexos SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id_anexos = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/anexos_normas_juridicas/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["anexo"])) {

                                $nomeArquivo = $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo = $caminho;
                                $anexo .= "anexo_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }
            
                                $sql = "UPDATE cadastro_normas_juridicas_anexos SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo', $anexo);
                                $stmt->bindParam(':id', $id_anexos);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    //  
                    log_operacao($id_anexos, $PDO_PROCLEGIS);
                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }
            if ($action == "editar_anexos") {
                $id_anexos = $_POST['id_anexos'];
                $titulo = $_POST['titulo'];

                $dados = array(
                    'norma' => $id,
                    'titulo' => $titulo
                );


                $sql = "UPDATE cadastro_normas_juridicas_anexos SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] = $id_anexos;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/anexos_normas_juridicas/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["anexo"])) {

                                $nomeArquivo = $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo = $caminho;
                                $anexo .= "anexo_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }
            
                                $sql = "UPDATE cadastro_normas_juridicas_anexos SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo', $anexo);
                                $stmt->bindParam(':id', $id_anexos);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }

                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }
                        }
                    }
                    log_operacao($id_anexos, $PDO_PROCLEGIS);
                    //  
                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                        <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                        <?php
                }
            }
            if ($action == 'excluir_anexos') {
                $id_sub = $_GET['id_sub'];

                $sql = "SELECT anexo FROM cadastro_normas_juridicas_anexos WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id_sub);
                if ($stmt->execute()) {
                    $result = $stmt->fetch();
                    $anexo = $result['anexo'];
                }


                $sql = "DELETE FROM cadastro_normas_juridicas_anexos WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id_sub);
                if ($stmt->execute()) {
                    //unlink($anexo);
                    log_operacao($id_sub, $PDO_PROCLEGIS);
                    ?>
                                            <script>
                                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                            </script>
                                            <?php
                } else {
                    ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php
                }
            }

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


            if (!$fil_numero && !$fil_autor && !$fil_tipo && !$fil_ano && !$fil_dt_inicio && !$fil_dt_fim && !$fil_palavra) {
                $numero_query = " 1 = 1 ";
            } else {
                if ($fil_numero) {
                    $n[0] = "cadastro_normas_juridicas.numero = :fil_numero";
                    $variavel .= "&fil_numero=$fil_numero";
                }
                if ($fil_ano) {
                    $n[1] = "cadastro_normas_juridicas.ano =:fil_ano";
                    $variavel .= "&fil_ano=$fil_ano";
                }
                if ($fil_tipo) {
                    $n[2] = "cadastro_normas_juridicas.tipo =:fil_tipo";
                    $variavel .= "&fil_tipo=$fil_tipo";
                }
                if ($fil_autor) {
                    $n[3] = "aux_autoria_autores.parlamentar =:fil_autor";
                    $variavel .= "&fil_autor=$fil_autor";
                }

                if ($fil_palavra) {
                    $n[4] = "cadastro_normas_juridicas.ementa like :ementa OR cadastro_normas_juridicas.conteudo like :conteudo";
                    $variavel .= "&fil_palavra=$fil_palavra";
                }

                if ($fil_dt_inicio && $fil_dt_fim) {
                    $n[5] = "cadastro_normas_juridicas.data_publicacao BETWEEN :fil_dt_inicio AND :fil_dt_fim";
                    $variavel .= "&fil_dt_inicio=$fil_dt_inicio&fil_dt_fim=$fil_dt_fim";
                } else {
                    if ($fil_dt_inicio) {
                        $n[5] = "cadastro_normas_juridicas.data_publicacao >= :fil_dt_inicio";
                        $variavel .= "&fil_dt_inicio=$fil_dt_inicio";
                    }

                    if ($fil_dt_fim) {
                        $n[5] = "cadastro_normas_juridicas.data_publicacao <= :fil_dt_fim";
                        $variavel .= "&fil_dt_fim=$fil_dt_fim";
                    }
                }

                $ultimo = count($n);
                $c = 0;
                foreach ($n as $valor) {
                    $c++;
                    if ($c == $ultimo) {
                        $numero_query .= $valor;
                    } else {
                        $numero_query .= $valor . ' AND ';
                    }
                }
            }

            $sql = "SELECT *, aux_normas_juridicas_tipos.nome as tipo_nome,
                              aux_normas_juridicas_tipos.sigla as tipo_sigla,
                              cadastro_normas_juridicas.id as id
                     FROM cadastro_normas_juridicas 
                    LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas.tipo                     
                    WHERE " . $numero_query . "	AND cadastro_normas_juridicas.ativo = :ativo		
                    ORDER BY cadastro_normas_juridicas.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindParam(':fil_numero', $fil_numero);
            $stmt->bindValue(':ativo', 1);
            $stmt->bindParam(':fil_autor', $fil_autor);
            $stmt->bindParam(':fil_tipo', $fil_tipo);
            $stmt->bindParam(':fil_ano', $fil_ano);
            $stmt->bindParam(':fil_dt_inicio', $fil_dt_inicio);
            $stmt->bindParam(':fil_dt_fim', $fil_dt_fim);
            $stmt->bindValue(':ementa', "%".$fil_palavra."%");
            $stmt->bindValue(':conteudo', "%".$fil_palavra."%");
            $stmt->bindParam(':primeiro_registro', $primeiro_registro);
            $stmt->bindParam(':num_por_pagina', $num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"" . $pagina_link . "/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_normas_juridicas/view'>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                        <select name='fil_ano' >";
                if ($fil_ano) {
                    echo "<option value ='$fil_ano'> $fil_ano </option>";
                }
                echo "<option value =''> Ano </option>";
                $anos = "SELECT ano, ativo FROM cadastro_normas_juridicas WHERE ativo = :ativo GROUP BY ano ORDER BY ano DESC";
                $sano = $PDO_PROCLEGIS->prepare($anos);
                $sano->bindValue(':ativo', 1);
                $sano->execute();
                $rows_ano = $sano->rowCount();
                if ($rows_ano > 0) {
                    while ($ano = $sano->fetch()) {
                        echo "<option value='" . $ano['ano'] . "'>" . $ano['ano'] . "</option>";
                    }
                }
                echo "</select>
        
                            <select name='fil_tipo'>";
                if ($fil_tipo) {
                    $tipos = "SELECT * FROM aux_normas_juridicas_tipos WHERE id = :id";
                    $stipo = $PDO_PROCLEGIS->prepare($tipos);
                    $stipo->bindValue(':id', $fil_tipo);
                    $stipo->execute();
                    $rows_tipo = $stipo->rowCount();
                    if ($rows_tipo > 0) {
                        $tipo = $stipo->fetch();
                        echo "<option value='" . $tipo['id'] . "'>" . $tipo['nome'] . "</option>";
                    }
                }

                echo "<option value =''>Tipo </option>";
                $tipos = "SELECT * FROM aux_normas_juridicas_tipos WHERE ativo = :ativo";
                $stipo = $PDO_PROCLEGIS->prepare($tipos);
                $stipo->bindValue(':ativo', 1);
                $stipo->execute();
                $rows_tipo = $stipo->rowCount();
                if ($rows_tipo > 0) {
                    while ($tipo = $stipo->fetch()) {
                        echo "<option value='" . $tipo['id'] . "'>" . $tipo['nome'] . "</option>";
                    }
                }

                echo "</select>
        
                            <select name ='fil_autor'>
                                <option value =''>Autoria </option>";
                $autores = "SELECT * FROM aux_autoria_autores WHERE ativo = :ativo AND nome IS NOT NULL GROUP BY nome ORDER BY nome ASC";
                $sautor = $PDO_PROCLEGIS->prepare($autores);
                $sautor->bindValue(':ativo', 1);
                $sautor->execute();
                $rows_autor = $sautor->rowCount();
                if ($rows_autor > 0) {
                    while ($autor = $sautor->fetch()) {
                        echo "<option value='" . $autor['id'] . "'>" . $autor['nome'] . "</option>";
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

                        // AUTORES
                        $autor = array();
                        $sql = "SELECT *
                                    FROM cadastro_normas_juridicas_autoria
                                    LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_normas_juridicas_autoria.autor                                    
                                    WHERE cadastro_normas_juridicas_autoria.norma = :norma	";
                        $stmt_aut = $PDO_PROCLEGIS->prepare($sql);
                        $stmt_aut->bindParam(':norma', $id);
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
                        echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag\");'>
                                            " . $result['tipo_sigla'] . " " . $result['numero'] . "/" . $result['ano'] . " - " . $result['tipo_nome'] . "
                                        </p>
                                        <span class='bold'>Ementa:</span> " . $result['ementa'] . "<p>
                                        <span class='bold'>Data publicação:</span> " . reverteData($result['data_publicacao']) . "<p>
                                        <span class='bold'>Autor(es):</span> " . implode(", ", $autor) . "<p>
                                        ";
                        if ($result['texto_original']) {
                            echo "<span class='bold'>PDF:</span> <a href='" . $result['texto_original'] . "' target='_blank'><i class='fas fa-file-pdf' style='font-size:20px; color:red;'></i></a><p>";
                        }
                        echo "
                                        ";
                        if ($result['word']) {
                            echo "<span class='bold'>DOC:</span> <a href='" . $result['word'] . "' target='_blank'><i class='fas fa-file-word' style='font-size:20px; color:blue;'></i></a><p>";
                        }
                        echo "
                                        ";
                        if ($result['conteudo']) {
                            echo "<span class='bold'>HTML:</span> <a href='../norma_juridica/" . $result['id'] . "' target='_blank'><i class='far fa-file-alt' style='font-size:20px;'></i></a><p>";
                        }
                        echo "
                                        
                                    </td>                                    
                                    <td align=center width='150'>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            
                                    </td>
                                </tr>";
                    }
                    echo "</table>";
                    $cnt = "SELECT COUNT(*) FROM cadastro_normas_juridicas  WHERE " . $numero_query . "";
                    $stmt = $PDO_PROCLEGIS->prepare($cnt);
                    $stmt->bindParam(':fil_numero', $fil_numero);
                    $stmt->bindParam(':fil_autor', $fil_autor);
                    $stmt->bindParam(':fil_tipo', $fil_tipo);
                    $stmt->bindParam(':fil_ano', $fil_ano);
                    $stmt->bindParam(':fil_dt_inicio', $fil_dt_inicio);
                    $stmt->bindParam(':fil_dt_fim', $fil_dt_fim);
                    $stmt->bindValue(':ementa', "%".$fil_palavra."%" );
                    $stmt->bindValue(':conteudo', "%".$fil_palavra."%" );
                    $variavel = "&fil_numero=$fil_numero&fil_autor=$fil_autor&fil_tipo=$fil_tipo&fil_ano=$fil_ano&fil_dt_inicio=$fil_dt_inicio&fil_dt_fim=$fil_dt_fim&ementa=$fil_palavra&conteudo=$fil_palavra";
                    include("../../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_normas_juridicas/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value=''>Tipo</option>";
                $sql = " SELECT * FROM aux_normas_juridicas_tipos 
                                             WHERE ativo = :ativo
                                             ORDER BY sigla";
                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                $stmt_int->bindValue(':ativo', 1);
                $stmt_int->execute();
                while ($result_int = $stmt_int->fetch()) {
                    echo "<option value='" . $result_int['id'] . "'>" . $result_int['sigla'] . " - " . $result_int['nome'] . "</option>";
                }
                echo "
                                </select>
                                <p><label>Número *:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                <p><label>Alfa:</label> <input name='alfa' id='alfa' placeholder='Alfa'>
                                <p><label>Ano *:</label> <input name='ano' id='ano' placeholder='Ano' class='obg'>
                                <p><label>Ementa *:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'></textarea>
                                <p><label>Iniciativa:</label> <select name='iniciativa' id='iniciativa'>
                                    <option value=''>Iniciativa</option>
                                    <option value='legislativo'>legislativo</option>
                                    <option value='executivo'>executivo</option> 
                                    <option value='judiciário'>judiciário</option>
                                    <option value='popular'>popular</option>
                                    <option value='outro'>outro</option>                                    
                                </select>
                                <p><label>Data apresentação:</label> <input name='data_apresentacao' placeholder='Data apresentação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Esfera Federação:</label> <select name='esfera' id='esfera'>
                                    <option value=''>Esfera Federação</option>
                                    <option value='Municipal'>Municipal</option>
                                    <option value='Estadual'>Estadual</option> 
                                    <option value='Federal'>Federal</option>                                    
                                </select>
                                <p><label>Data publicação *:</label> <input name='data_publicacao' placeholder='Data publicação' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Vigência:</label> <select name='vigencia' id='vigencia'>
                                    <option value=''>Vigência</option>
                                    <option value='Vigência Determinada'>Vigência Determinada</option>
                                    <option value='Vigência Indeterminada'>Vigência Indeterminada</option>                           
                                </select>
                                <p><label>Data fim vigência:</label> <input name='data_fim_vigencia' placeholder='Data fim vigência' onkeypress='return mascaraData(this,event);'>
                                <p><label>Texto Original (PDF):</label> <input type='file' name='texto_original[texto_original]' id='texto_original' placeholder='Texto Original'>                                 
                                <p><label>DOC:</label> <input type='file' name='word[word]' id='word' placeholder='DOC'>                                 
                                <p><label>Prefeito:</label> <input name='prefeito' id='prefeito' placeholder='Prefeito'>
                                <p><label>Presidente:</label> <input name='presidente' id='presidente' placeholder='Presidente'>
                                <p><label>Status:</label> <select name='status' id='status'>
                                    <option value=''>Status</option>
                                    <option value='Vigente sem Alteracao'>Vigente sem Alteração</option>
									<option value='Vigente Alterada'>Vigente Alterada</option>
									<option value='Revogada'>Revogada</option>
									<option value='Anulada'>Anulada</option>
									<option value='Declarada Inconstitucional'>Declarada Inconstitucional</option>                                     
                                </select>
                                <p><label>Complementar:</label> <select name='complementar' id='complementar'>
                                    <option value=''>Complementar</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                     
                                </select>
                                <p><label>Tipo Matéria:</label> <select name='tipo_materia' id='tipo_materia'>
                                    <option value=''>Tipo Matéria</option>";
                $sql = " SELECT * FROM aux_materias_tipos 
                                             WHERE ativo = :ativo 
                                             ORDER BY sigla";
                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                $stmt_int->bindValue(':ativo', 1);
                $stmt_int->execute();
                while ($result_int = $stmt_int->fetch()) {
                    echo "<option value='" . $result_int['id'] . "'>" . $result_int['sigla'] . " - " . $result_int['nome'] . "</option>";
                }
                echo "
                                </select>
                                <p><label>Matéria:</label> <select name='materia' id='materia'>
                                    <option value=''>Matéria Legislativa</option>                                    
                                </select>
                                <p><label>Conteúdo:</label><br><br><textarea name='conteudo' id='example' height='400' placeholder='Conteúdo da lei'></textarea>          
                                
                            </div>	                                                                                                   
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_normas_juridicas/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }

            if ($pagina == 'edit') {
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
                        WHERE cadastro_normas_juridicas.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_normas_juridicas/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                                                   
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value='" . $result['tipo'] . "'>" . $result['tipo_sigla'] . " - " . $result['tipo_nome'] . "</option>";
                    $sql = "SELECT * FROM aux_normas_juridicas_tipos 
                                            WHERE ativo = :ativo 
                                            ORDER BY sigla";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_int->bindValue(':ativo', 1);
                    $stmt_int->execute();
                    while ($result_int = $stmt_int->fetch()) {
                        echo "<option value='" . $result_int['id'] . "'>" . $result_int['sigla'] . " - " . $result_int['nome'] . "</option>";
                    }
                    echo "
                                </select>
                                <p><label>Número *:</label> <input name='numero' id='numero' value='" . $result['numero'] . "' placeholder='Número'  class='obg'>
                                <p><label>Alfa:</label> <input name='alfa' id='alfa' value='" . $result['alfa'] . "' placeholder='Alfa'>
                                <p><label>Ano *:</label> <input name='ano' id='ano' value='" . $result['ano'] . "' placeholder='Ano'>   
                                <p><label>Ementa *:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'>" . $result['ementa'] . "</textarea>                                                                                     
                                <p><label>Iniciativa:</label> <select name='iniciativa' id='iniciativa'>
                                    <option value='" . $result['iniciativa'] . "'>" . $result['iniciativa'] . "</option>
                                    <option value='legislativo'>legislativo</option>
                                    <option value='executivo'>executivo</option> 
                                    <option value='judiriário'>judiriário</option> 
                                    <option value='popular'>popular</option> 
                                    <option value='outro'>outro</option>                                   
                                </select>
                                <p><label>Data apresentação *:</label> <input name='data_apresentacao' value='" . reverteData($result['data_apresentacao']) . "'  class='obg' placeholder='Data apresentação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Esfera Federação *:</label> <select name='esfera' id='esfera' class='obg'>
                                    <option value='" . $result['esfera'] . "'>" . $result['esfera'] . "</option>
                                    <option value='Municipal'>Municipal</option>
                                    <option value='Estadual'>Estadual</option> 
                                    <option value='Federal'>Federal</option>                                   
                                </select> 
                                <p><label>Data publicação:</label> <input name='data_publicacao' id='data_publicacao' value='" . reverteData($result['data_publicacao']) . "' placeholder='Data publicação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Vigência:</label> <select name='vigencia' id='vigencia'>
                                    <option value='" . $result['vigencia'] . "'>" . $result['vigencia'] . "</option>
                                    <option value='Vigência Determinada'>Vigência Determinada</option>
                                    <option value='Vigência Indeterminada'>Vigência Indeterminada</option>                                                                      
                                </select> 
                                <p><label>Data fim vigência:</label> <input name='data_fim_vigencia' id='data_fim_vigencia' value='" . reverteData($result['data_fim_vigencia']) . "' placeholder='Data fim vigência'onkeypress='return mascaraData(this,event);'>
                                <p><label>Texto Original (PDF):</label> ";
                    if ($result['texto_original'] != '') {
                        echo "<a href='" . $result['texto_original'] . "' target='_blank'><i class='fas fa-file-pdf' style='float:left; color:red;'></i></a>";
                    }
                    echo " &nbsp; 
                                <p><label>Alterar Texto Original:</label> <input type='file' name='texto_original[texto_original]'>
                                <p><label>DOC:</label> ";
                    if ($result['word'] != '') {
                        echo "<a href='" . $result['word'] . "' target='_blank'><i class='fas fa-file-word' style='float:left; color:blue;'></i></a>";
                    }
                    echo " &nbsp; 
                                <p><label>Alterar DOC:</label> <input type='file' name='word[word]'>
                                <p><label>Prefeito:</label> <input name='prefeito' id='prefeito' value='" . $result['prefeito'] . "' placeholder='Prefeito'>
                                <p><label>Presidente:</label> <input name='presidente' id='presidente' value='" . $result['presidente'] . "' placeholder='Presidente'>
                                <p><label>Status:</label> <select name='status' id='status'>
                                <option value='" . $result['status'] . "'>" . $result['status'] . "</option>
                                <option value='Vigente sem Alteracao'>Vigente sem Alteração</option>
									<option value='Vigente Alterada'>Vigente Alterada</option>
									<option value='Revogada'>Revogada</option>
									<option value='Anulada'>Anulada</option>
									<option value='Declarada Inconstitucional'>Declarada Inconstitucional</option>                                    
                            </select>
                                <p><label>Complementar?</label> <select name='complementar' id='complementar'>
                                    <option value='" . $result['complementar'] . "'>" . $result['complementar'] . "</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>   
                                <p><label>Tipo Matéria:</label> <select name='tipo_materia' id='tipo_materia'>
                                    <option value='" . $result['tipo_materia'] . "'>" . $result['tipo_sigla_materia'] . " - " . $result['tipo_nome_materia'] . "</option>";
                    $sql = "SELECT * FROM aux_materias_tipos 
                                            WHERE ativo = :ativo
                                            ORDER BY sigla";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_int->bindValue(':ativo', 1);
                    $stmt_int->execute();
                    while ($result_int = $stmt_int->fetch()) {
                        echo "<option value='" . $result_int['id'] . "'>" . $result_int['sigla'] . " - " . $result_int['nome'] . "</option>";
                    }
                    echo "
                                </select>  
                                <p><label>Matéria:</label> <select name='materia' id='materia'>
                                    <option value='" . $result['materia'] . "'>Nº " . $result['numero_materia'] . " de " . $result['ano_materia'] . "</option>";
                    $sql = "SELECT * FROM cadastro_materias 
                                            WHERE  tipo = :tipo AND ativo = :ativo
                                            ORDER BY ano DESC, numero ASC";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_int->bindValue(':ativo', 1);
                    $stmt_int->bindParam(':tipo', $result['tipo_materia']);
                    $stmt_int->execute();
                    while ($result_int = $stmt_int->fetch()) {
                        echo "<option value='" . $result_int['id'] . "'>Nº " . $result_int['numero'] . " de " . $result_int['ano'] . "</option>";
                    }
                    echo "
                                </select>                  
                                <p><label>Conteúdo:</label><br><br><textarea name='conteudo' id='example' height='400' placeholder='Conteúdo da lei'>" . $result['conteudo'] . "</textarea>                          
                            </div>                                                                                                				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_normas_juridicas/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }
            if ($pagina == 'exib') {
                include("../mod_includes/modal/NormasRevogacoesAdd.php");
                include("../mod_includes/modal/NormasAssuntosAdd.php");
                include("../mod_includes/modal/NormasAutoriaAdd.php");
                include("../mod_includes/modal/NormasAnexosAdd.php");

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
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                        <div class='titulo'> $page &raquo; Exibir </div>
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
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo:</div>
                                        <div class='exib_value'>" . $result['tipo_sigla'] . " - " . $result['tipo_nome'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Número:</div>
                                        <div class='exib_value'>" . $result['numero'] . " " . $result['alfa'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Ano:</div>
                                        <div class='exib_value'>" . $result['ano'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Iniciativa:</div>
                                        <div class='exib_value'>" . $result['iniciativa'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data Apresentação:</div>
                                        <div class='exib_value'>" . reverteData($result['data_apresentacao']) . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Esfera Federação:</div>
                                        <div class='exib_value'>" . $result['esfera'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data Publicação:</div>
                                        <div class='exib_value'>" . reverteData($result['data_publicacao']) . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Vigência:</div>
                                        <div class='exib_value'>" . $result['vigencia'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data Fim Vigência:</div>
                                        <div class='exib_value'>" . reverteData($result['data_fim_vigencia']) . " &nbsp;</div>
                                    </div>
                                    
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Prefeito:</div>
                                        <div class='exib_value'>" . $result['prefeito'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Presidente:</div>
                                        <div class='exib_value'>" . $result['presidente'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Status:</div>
                                        <div class='exib_value'>" . $result['status'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Complementar?</div>
                                        <div class='exib_value'>" . $result['complementar'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo Matéria:</div>
                                        <div class='exib_value'>" . $result['tipo_sigla_materia'] . " - " . $result['tipo_nome_materia'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Matéria:</div>
                                        <div class='exib_value'>Nº " . $result['numero_materia'] . " de " . $result['ano_materia'] . " &nbsp;</div>
                                    </div>                                    
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Cadastrado por:</div>
                                        <div class='exib_value'>" . $result['cadastrado_por'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Alterado por:</div>
                                        <div class='exib_value'>" . $result['alterado_por'] . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data cadastro:</div>
                                        <div class='exib_value'>" . reverteData(substr($result['data_cadastro'], 0, 10)) . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data alteração:</div>
                                        <div class='exib_value'>" . reverteData($result['data_alteracao']) . " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Doc:</div>
                                        <div class='exib_value'>";
                    ;
                    if ($result['word'] != '') {
                        echo "<a href='" . $result['word'] . "' target='_blank'><i class='fas fa-file-word' style='font-size:20px; color:blue;'></i></a>";
                    }
                    echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Texto Original:</div>
                                        <div class='exib_value'>";
                    ;
                    if ($result['texto_original'] != '') {
                        echo "<a href='" . $result['texto_original'] . "' target='_blank'><i class='fas fa-file-pdf' style='font-size:20px; color:red;'></i></a>";
                    }
                    echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>HTML:</div>
                                        <div class='exib_value'>";
                    ;
                    if ($result['conteudo'] != '') {
                        echo "<a href='../norma_juridica/" . $id . "' target='_blank'><i class='far fa-file-alt' style='font-size:20px;float:left;'></i></a>";
                    }
                    echo " &nbsp;</div>
                                    </div>                                
                                    <div class='exib_bloco_long'>
                                        <div class='exib_label'>Ementa:</div>
                                        <div class='exib_value'>" . $result['ementa'] . " &nbsp;</div>
                                    </div>
                                </div>                                                                                                                                              
                            </div>                        
                            <div id='assuntos' class='tab-pane fade in'>
                                ";
                    $sql = "SELECT *, cadastro_normas_juridicas_assuntos.id as id_assuntos                                                  
                                        FROM cadastro_normas_juridicas_assuntos 
                                        LEFT JOIN aux_normas_juridicas_assuntos ON aux_normas_juridicas_assuntos.id = cadastro_normas_juridicas_assuntos.assunto
                                        WHERE norma = :norma AND cadastro_normas_juridicas_assuntos.ativo = :ativo
                                        ORDER BY cadastro_normas_juridicas_assuntos.id DESC
                                       ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':fil_nome1', $fil_nome1);
                    $stmt->bindParam(':norma', $id);
                    $stmt->bindValue(':ativo', 1);
                    $stmt->execute();
                    $rows = $stmt->rowCount();

                    echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#assuntosAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                    if ($rows > 0) {
                        echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Assunto</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
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
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_assuntos/$id_assuntos#assuntos\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#assuntosEdit" . $id_assuntos . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                            include("../mod_includes/modal/NormasAssuntosEdit.php");
                        }


                        echo "</table>";
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                    echo "
                            </div>
                            <div id='revogacoes' class='tab-pane fade in'>
                                ";
                    //AND cadastro_normas_juridicas_revogacoes.ativo = :ativo 
                    $sql = "SELECT *, cadastro_normas_juridicas_revogacoes.id as id_revogacoes  
                                                , aux_normas_juridicas_tipos.sigla as sigla                                                                                 
                                        FROM cadastro_normas_juridicas_revogacoes 
                                        LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_normas_juridicas_revogacoes.tipo_norma
                                        LEFT JOIN cadastro_normas_juridicas ON cadastro_normas_juridicas.id = cadastro_normas_juridicas_revogacoes.norma_revogada                                        
                                        LEFT JOIN aux_normas_juridicas_tipo_vinculo ON aux_normas_juridicas_tipo_vinculo.id = cadastro_normas_juridicas_revogacoes.tipo_vinculo                                        
                                        WHERE norma = :norma AND norma_revogada <> '' AND cadastro_normas_juridicas_revogacoes.ativo = :ativo
                                        ORDER BY cadastro_normas_juridicas_revogacoes.id DESC
                                       ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':fil_nome1', $fil_nome1);
                    $stmt->bindParam(':norma', $id);
                    $stmt->bindValue(':ativo', 1);
                    $stmt->execute();
                    $rows = $stmt->rowCount();

                    echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#revogacoesAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                    if ($rows > 0) {
                        echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de norma</td>
                                            <td class='titulo_tabela'>Norma Revogada</td>                                            
                                            <td class='titulo_tabela'>Tipo de vínculo</td>                                            
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
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
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_revogacoes/$id_revogacoes#revogacoes\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#revogacoesEdit" . $id_revogacoes . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                            include("../mod_includes/modal/NormasRevogacoesEdit.php");
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
                                        WHERE cadastro_normas_juridicas_autoria.norma = :norma AND cadastro_normas_juridicas_autoria.ativo = :ativo
                                        ORDER BY cadastro_normas_juridicas_autoria.id DESC
                                       ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':fil_nome1', $fil_nome1);
                    $stmt->bindParam(':norma', $id);
                    $stmt->bindValue(':ativo', 1);
                    $stmt->execute();
                    $rows = $stmt->rowCount();

                    echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#autoriaAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                    if ($rows > 0) {
                        echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Autor</td>
                                            <td class='titulo_tabela'>Primeiro autor?</td>                                            
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
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
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_autoria/$id_autoria#autoria\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#autoriaEdit" . $id_autoria . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                            include("../mod_includes/modal/NormasAutoriaEdit.php");
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
                                        WHERE norma = :norma AND cadastro_normas_juridicas_anexos.ativo = :ativo
                                        ORDER BY cadastro_normas_juridicas_anexos.id DESC
                                       ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':fil_nome1', $fil_nome1);
                    $stmt->bindParam(':norma', $id);
                    $stmt->bindValue(':ativo', 1);

                    $stmt->execute();
                    $rows = $stmt->rowCount();

                    echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#anexosAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                    if ($rows > 0) {
                        echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Título</td>
                                            <td class='titulo_tabela' align='center'>Anexo</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
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
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/exib/$id/excluir_anexos/$id_anexos#anexos\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#anexosEdit" . $id_anexos . "'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                            include("../mod_includes/modal/NormasAnexosEdit.php");
                        }


                        echo "</table>";
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                    echo "
                            </div>                              
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_normas_juridicas/view'; value='Voltar'/></center>
                            </center>
                        </div>
                    ";
                }
            }
            ?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
    <script>                
        //CALENDÁRIOinput
        jQuery("input[name*='data'], #fil_ext_de, #fil_ext_ate").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
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

    <!-- FROALA --> 
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>  	
    
    <script> 
            new FroalaEditor('#example',{
                height: 450, 

                imageManagerLoadURL: "load_file.php",

                // Set the load images request type.
                imageManagerLoadMethod: "POST",
                
                // Set page size.
                imageManagerPageSize: 20,

                // Set a scroll offset (value in pixels).
                imageManagerScrollOffset: 10,

                
                // Set the image upload parameter.
                imageUploadParam: 'file',

                // Set the image upload URL.
                imageUploadURL: 'upload_file.php',

                // Additional upload params.
                imageUploadParams: {id: 'my_editor'},

                // Set request type.
                imageUploadMethod: 'POST',

                // Set max image size to 5MB.
                imageMaxSize: 5 * 1024 * 1024,

                // Allow to upload PNG and JPG.
                imageAllowedTypes: ['jpeg', 'jpg', 'png', 'PNG'],

                events: {
                'image.beforeUpload': function (images) {
                    // Return false if you want to stop the image upload.
                },
                'image.uploaded': function (response) {
                    // Image was uploaded to the server.
                },
                'image.inserted': function ($img, response) {
                    // Image was inserted in the editor.
                },
                'image.replaced': function ($img, response) {
                    // Image was replaced in the editor.
                },
                'image.error': function (error, response) {
                    // Bad link.
                    if (error.code == 1) {  }

                    // No link in upload response.
                    else if (error.code == 2) {  }

                    // Error during image upload.
                    else if (error.code == 3) {  }

                    // Parsing response failed.
                    else if (error.code == 4) {  }

                    // Image too text-large.
                    else if (error.code == 5) {  }

                    // Invalid image type.
                    else if (error.code == 6) {  }

                    // Image can be uploaded only to same domain in IE 8 and IE 9.
                    else if (error.code == 7) {  }

                    // Response contains the original server response to the request if available.
                },
                'imageManager.error': function (error, response) {
                        // Bad link. One of the returned image links cannot be loaded.
                        if (error.code == 10) {}

                        // Error during request.
                        else if (error.code == 11) {}

                        // Missing imagesLoadURL option.
                        else if (error.code == 12) {}

                        // Parsing response failed.
                        else if (error.code == 13) {}

                        
                    },
                    'imageManager.imagesLoaded': function (images) {
                        // Do something when the request finishes with success.
                        //alert ('Images have been loaded.'+images);
                    },
                    'imageManager.imageLoaded': function ($img) {
                        // Do something when an image is loaded in the image manager
                        //alert ('Imaaaage has been loaded.');
                    },
                    'imageManager.beforeDeleteImage': function ($img) {
                        // Do something before deleting an image from the image manager.
                        //alert ('Image will be deleted.');
                    },
                    'imageManager.imageDeleted': function (response) {
                        // Do something after the image was deleted from the image manager.
                        //alert ('Image has been deleted.');
                    }
                }
            });
        </script>
</body>
</html>

<script>
    
    $('document').ready(function(){ 
        var elemento = document.querySelector(".fr-wrapper:nth-child(1)");
        elemento.parentNode.removeChild(elemento);
    });
</script>