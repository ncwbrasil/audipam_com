<?php
$pagina_link = 'cadastro_proposicoes';
include_once("../../core/mod_includes/php/funcoes.php");
include_once("../../core/mod_includes/php/funcoes_certificado.php");
sec_session_start();
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html
    PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php 
    include("header.php"); 
    include '../mod_includes/php/phpqrcode/qrlib.php';
    ?>

</head>

<body>
    <main class="cd-main-content">
        <!--MENU-->
        <?php include("../mod_menu/menu.php"); ?>

        <!--CONTEUDO CENTRO-->
        <div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Cadastro &raquo; <a href='cadastro_proposicoes/view'>Proposições</a>";
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            $tipo   = $_POST['tipo'];
            $numero   = $_POST['numero'];
            $ementa   = $_POST['ementa'];
            $observacao   = $_POST['observacao'];
            $tipo_materia   = $_POST['tipo_materia'];
            if ($tipo_materia == "") {
                $tipo_materia = null;
            }
            $materia_anexada   = $_POST['materia_anexada'];if($materia_anexada == ""){$materia_anexada = null;}
            $ano_materia   = $_POST['ano_materia'];if($ano_materia == ""){$ano_materia = null;}
            $endereco = $_POST['endereco'];
            $end_numero = $_POST['end_numero'];
            $cidade = $_POST['cidade'];
            $ano    = $_POST['ano']; 
            $prazo_final  = reverteData($_POST['prazo_final']); 

            if ($endereco != "") {
                $address = $endereco . ", " . $end_numero . " - " . $cidade_cidade;
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode($address) . '&key=AIzaSyDxr7kMaP7K8wVBB9fDqzjYfWVdklRvajM');

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $json = json_decode(curl_exec($curl), true);
                // echo "<pre>";
                // print_r($json);
                $latitude = $json['results'][0]['geometry']['location']['lat'];
                $longitude = $json['results'][0]['geometry']['location']['lng'];
                curl_close($curl);
            } else {
                $endereco = null;
                $end_numero = null;
                $cidade = null;
                $latitude = null; 
                $longitude = null; 
            }

            $dados = array(

                'tipo'              => $tipo,
                'numero'            => $numero,
                'ementa'            => $ementa,
                'ano'               => $ano, 
                'observacao'        => $observacao,
                'prazo_final'       => $prazo_final, 
                'tipo_materia'      => $tipo_materia,
                'materia_anexada'   => $materia_anexada,
                'ano_materia'       => $ano_materia,
                'autor'             => $_SESSION['autor_id'], 
                'endereco'          => $endereco, 
                'end_numero'        => $end_numero,
                'cidade'            => $cidade, 
                'latitude'          => $latitude, 
                'longitude'         => $longitude,
            );

            // print_r($dados);
            // exit; 

            // print_r($dados); 
            // exit;

            if ($action == "adicionar") {
                $sql = "INSERT INTO cadastro_proposicoes SET " . bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                if ($stmt->execute($dados)) {
                    $id = $PDO_PROCLEGIS->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/proposicoes/$id/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["texto_original"])) {

                                $nomeArquivo     = $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original    = $caminho;
                                $texto_original .= "texto_original_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                $imnfo = getimagesize($texto_original);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 500 || $img_h > 500) {
                                    $image = WideImage::load($texto_original);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($texto_original);
                                }

                                $sql = "UPDATE cadastro_proposicoes SET 
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

                    $sql_status = "INSERT INTO cadastro_proposicoes_status SET proposicao = :proposicao, status = :status";
                    $stmt_status = $PDO_PROCLEGIS->prepare($sql_status);
                    $stmt_status->bindParam(':proposicao', $id); 
                    $stmt_status->bindValue(':status', 'Não Enviado'); 
                    if($stmt_status->execute()){
                    }
                    else {
                        $erro = 1; 
                    }
                    //  

                    if ($erro != 1) {
                        log_operacao($id, $PDO_PROCLEGIS); 
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
                } else {
                    ?>
            <script>
            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
            </script>
            <?php
                }
            }

            if ($action == 'editar') {
                //$dados['autor'] =  "";                             
                $sql = "UPDATE cadastro_proposicoes SET " . bindFields($dados) . " WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $dados['id'] =  $id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';

                    $caminho = "../uploads/proposicoes/$id/";

                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["texto_original"])) {

                                $nomeArquivo     = $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original    = $caminho;
                                $texto_original .= "texto_original_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                $imnfo = getimagesize($texto_original);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 500 || $img_h > 500) {
                                    $image = WideImage::load($texto_original);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($texto_original);
                                }
                                $sql = "UPDATE cadastro_proposicoes SET 
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
                    log_operacao($id, $PDO_PROCLEGIS); 
                ?>
            <script>
            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
            </script>
            <?php
                } else {  

                    $err = $stmt->errorInfo();                    
                    print_r($err); 
                    
                ?>
            <script>
            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
            </script>
            <?php
                }
            }

            if ($action == 'enviar') {
                $sql = "SELECT *
                        FROM cadastro_proposicoes 
                        WHERE  id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',     $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    $texto_original = $result['texto_original'];

                    if ($texto_original == "") {
                        ?>
            <script>
            mensagem("X",
                "<i class='fa fa-exclamation-circle'></i> Não é possível enviar uma proposição sem o texto original. Por favor adicione um texto original nessa proposição."
                );
            </script>
            <?php
                    } else {

                        $data_envio = date("Y-m-d H:i:s");
                        $sql = "UPDATE cadastro_proposicoes SET data_envio = :data_envio  WHERE id = :id ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':data_envio', $data_envio);
                        $stmt->execute();
                        
                        $sql = "INSERT INTO cadastro_proposicoes_status SET proposicao = :id, status = :status";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                        $stmt->bindParam(':id', $id);
                        $stmt->bindValue(':status', 'Enviado');
                        if ($stmt->execute()) {

                            log_operacao($id, $PDO_PROCLEGIS); 

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
                }
            }

            if ($action == 'excluir') {

                $sql = "UPDATE cadastro_proposicoes SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindValue(':ativo', 0);
                if ($stmt->execute()) {
                    //unlink($foto_antiga);
                    log_operacao($id, $PDO_PROCLEGIS); 

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

            if($action == 'arquivar'){
                $sql = "INSERT INTO cadastro_proposicoes_status SET proposicao = :id, status = :status";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindValue(':status', 'Arquivado'); 
                if ($stmt->execute()) {
                    log_operacao($id, $PDO_PROCLEGIS); 
                    ?>
            <script>
            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
            </script>
            <?php
                }
            }

            if($action == 'desarquivar'){
                $sql = "INSERT INTO cadastro_proposicoes_status SET proposicao = :id, status = :status";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindValue(':status', 'Não Enviado');  
                if ($stmt->execute()) {
                    log_operacao($id, $PDO_PROCLEGIS); 
                    ?>
            <script>
            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
            </script>
            <?php
                }
            }
            if($action == 'confirmar_assinatura')
            {              
               
                $sql = "SELECT *
                        FROM cadastro_proposicoes
                        WHERE id = :id                      
                    ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);                    
                $stmt->bindParam(':id', 	$id);                                    
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {                   
                    while($result = $stmt->fetch())
                    {
                        $anexo = $result['texto_original'];           
                        
                        //PEGA DADOS DA ASSINATURA DO USUÁRIO, SE ELE TIVER
                        $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id";
                        $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                        $stmt_usu->bindParam(':usu_id', 	$_SESSION['usuario_id']);                                    
                        if($stmt_usu->execute())
                        {                                                                                                      
                            $result_usu = $stmt_usu->fetch();
                            $cert = $result_usu['usu_certificado'];
                            $pass = $result_usu['usu_cert_senha'];                            
                            $certificado = recuperaDadosCertificado($cert, $pass);                            
                            $validTo = substr($certificado['validTo'],0,-1);
                            $validade = date("Y-m-d H:i:s", strtotime("20".substr($validTo,0,2)."-".substr($validTo,2,2)."-".substr($validTo,4,2)." ".substr($validTo,6,2).":".substr($validTo,8,2).":".substr($validTo,10,2)));
                            if($validade < $data_ass)
                            {
                                ?>
            <script>
            mensagem("X",
                "<i class='fas fa-exclamation-circle'></i> Seu certificado digital está expirado, contate o administrador do sistema"
                );
            </script>
            <?php                 
                            }
                            else
                            {                                    
                                $dados_ass = $certificado['subject']['CN'];
                                $data_ass  = date("d/m/Y H:i:s");
                                
                                // PEGA QTD DE ASSINATURAS
                                $sql = "SELECT * FROM cadastro_proposicoes_assinaturas 
                                        WHERE proposicao = :proposicao ";
                                $stmt_qtd = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_qtd->bindParam(':proposicao',$id);  
                                $stmt_qtd->execute();  
                                $rows_qtd = $stmt_qtd->rowCount();                                                                                                                                                                                                                   
                              
                                $retorno = array();                        
                                $retorno = assinaDocumento($cert, $pass, $anexo, $dados_ass, $data_ass, $rows_qtd); 
                                                            
                                if($retorno['result'] == "Documento assinado com sucesso!")
                                {        
                                    
                                    $nome_anexo = end(explode("/",$retorno['file']));

                                    $sql = "UPDATE cadastro_proposicoes SET 
                                            texto_original           = :texto_original
                                            WHERE id = :id ";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                    $stmt->bindParam(':texto_original',$retorno['file']);
                                    $stmt->bindParam(':nome_anexo',$nome_anexo);
                                    //$stmt->bindParam(':nome_documento',$retorno['file']);
                                    $stmt->bindValue(':confirmacao_recebimento',1);
                                    $stmt->bindValue(':usu_recebimento',$_SESSION['usuario_id']);
                                    $stmt->bindParam(':id',$id);
                                    $stmt->execute();
                                    if($stmt->execute())
                                    {                   
                                                             
                                        $sql = "INSERT INTO cadastro_proposicoes_assinaturas SET 
                                                        proposicao           = :proposicao, 
                                                        credenciais      = :credenciais,                                             
                                                        assinado = :assinado ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindParam(':proposicao',$id);
                                        $cred = $dados_ass. " - ". $data_ass;
                                        $stmt->bindParam(':credenciais',$cred);
                                        $stmt->bindValue(':assinado',1);                                                                                                                                                                
                                        if($stmt->execute())
                                        {
                                            
                                        }
                                        log_operacao($id, $PDO_PROCLEGIS);

                                        ?>
            <script>
            mensagem("Ok", "<i class='fas fa-check-circle'></i> Documento recebido e assinado com sucesso!");
            </script>
            <?php
                                    }
                                    else
                                    {
                                        ?>
            <script>
            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
            </script>
            <?php
                                    }
                                }
                                else
                                {
                                    ?>
            <script>
            mensagem("X", "<i class='fa fa-exclamation-circle'></i> <?php echo $retorno['result'];?> ");
            </script>
            <?php
                                }

                            }                        
                        }
                    }
                }               

                            
            }

            $num_por_pagina = 10;
            if (!$pag) {
                $primeiro_registro = 0;
                $pag = 1;
            } else {
                $primeiro_registro = ($pag - 1) * $num_por_pagina;
            }

            $fil_tipo = $_REQUEST['fil_tipo'];
            if ($fil_tipo == '') {
                $numero_query = " 1 = 1 ";
            } else {
                $fil_tipo1 = $fil_tipo2 = $fil_tipo3 = "%" . $fil_tipo . "%";
                $numero_query = " (numero LIKE :fil_tipo1 ) ";
            }

            $fil_protocolado = $_REQUEST['fil_protocolado'];
            if($fil_protocolado == '0'){
                $protocolado_query = 'materia_gerada IS NOT NULL ';
            }
            else if($fil_protocolado == '1') {
                $protocolado_query = 'materia_gerada IS NULL ';
            }
            else {
                $protocolado_query = '1=1';
            }


            $sql = "SELECT *, cadastro_proposicoes.id as id
                        , cadastro_proposicoes.ementa as ementa
                        , aux_proposicoes_tipos.descricao as tipo 
                FROM cadastro_proposicoes 
                LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
                LEFT JOIN cadastro_proposicoes_status as h1 ON h1.proposicao = cadastro_proposicoes.id 
                WHERE " . $numero_query . " AND autor = :autor AND h1.id = (SELECT MAX(h2.id) FROM cadastro_proposicoes_status h2 where h2.proposicao = h1.proposicao) AND ".$protocolado_query." AND cadastro_proposicoes.ativo = :ativo
                ORDER BY cadastro_proposicoes.id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindParam(':fil_tipo1',     $fil_tipo1);
            $stmt->bindParam(':autor', $_SESSION['autor_id']);
            $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
            $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
            $stmt->bindValue(':ativo', 1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"" . $pagina_link . "/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_proposicoes/view'>
                        <input name='fil_tipo' id='fil_tipo' value='$fil_tipo' placeholder='Tipo de Porposição'>
                            <select id='fil_protocolado' name='fil_protocolado'>
                            <option value=''>Selecione </option>
                            <option value='1'>Elaborado </option>
                            <option value='0'>Protocolado </option> 
                        </select>

                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>    
                </div>
                ";
                if ($rows > 0) {
                    echo "
                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                        <tr>
                            <td class='titulo_tabela'>Tipo Proposição</td>
                            <td class='titulo_tabela'>Número</td>
                            <td class='titulo_tabela'>Ementa</td>                            
                            <td class='titulo_tabela'>Última Atualização</td>                            
                            <td class='titulo_tabela'>Status</td>  
                            <td class='titulo_tabela'>Prazo Final</td>  
                            <td class='titulo_tabela' align='center'>Texto Original</td>  
                            <td class='titulo_tabela' align='center'>Assinado digitalmente?</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $id = $result['id'];
                        $tipo = $result['tipo'];
                        $numero = $result['numero'];
                        $ementa = $result['ementa'];
                        $status = $result['status'];
                        $arquivar = $result['arquivar']; 
                        $texto_original = $result['texto_original']; 

                        if($result['prazo_final'] < date('Y-m-d')){
                            $prazo_final = "<b style='color:red; font-weight: bold;'>".date('d/m/Y', strtotime($result['prazo_final']))."</b>"; 
                        }
                        else if($result['prazo_final'] == date('Y-m-d')){
                            $prazo_final = "<b style='color:orange; font-weight: bold;'>".date('d/m/Y', strtotime($result['prazo_final']))."</b>"; 
                        }else {
                            $prazo_final = "<b style='color:green; font-weight: bold;'>".date('d/m/Y', strtotime($result['prazo_final']))."</b>"; 
                        }

                        if($status ==  'Não Enviado'){
                            $status1 = "<b style='color:orange; font-weight: bold;'>".$status."</b>";
                        }
                        if($status ==  'Enviado'){
                            $status1 = "<b style='color:blue; font-weight: bold;'>".$status."</b>";
                        }   
                        if($status ==  'Recebido'){
                            $status1 = "<b style='color:green; font-weight: bold;'>".$status."</b>";
                        }
                        if($status ==  'Devolvido'){
                            $status1 = "<b style='color:red; font-weight: bold;'>".$status."</b>";
                        }
                        if ($status ==  'Arquivado'){
                            $status1 = "<b style='color:red; font-weight: bold;'>".$status."</b>";
                        }


                        $data_envio = reverteData(substr($result['data_cadastro'], 0, 10)) . " às " . substr($result['data_cadastro'], 11, 5);
                        
                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        $propoicoes = 'proposicoes';

                        //PEGA DADOS DA ASSINATURA DO USUÁRIO, SE ELE TIVER
                        $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id";
                        $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                        $stmt_usu->bindParam(':usu_id', 	$_SESSION['usuario_id']);                                    
                        if($stmt_usu->execute())
                        {                                                
                            $result_usu = $stmt_usu->fetch();
                            $cert = $result_usu['usu_certificado'];
                            $pass = $result_usu['usu_cert_senha'];
                           
                            $certificado = recuperaDadosCertificado($cert, $pass);
                            $validTo = substr($certificado['validTo'],0,-1);
                            $validade = date("Y-m-d H:i:s", strtotime("20".substr($validTo,0,2)."-".substr($validTo,2,2)."-".substr($validTo,4,2)." ".substr($validTo,6,2).":".substr($validTo,8,2).":".substr($validTo,10,2)));
                            
                            if($validade < date("Y-m-d"))
                            {
                                $dados_ass = "Seu certificado digital está expirado, contate o administrador do sistema";
                                $perm_ass = 0;
                            }
                            else
                            {    
                                $dados_ass = $certificado['subject']['CN'];
                                $data_ass  = date("d/m/Y H:i:s");
                                $perm_ass = "1";
                            }                        
                        }

                        //PEGA ASSINATURAS
                        $sql = "SELECT * FROM cadastro_proposicoes_assinaturas 
                                WHERE proposicao = :proposicao AND assinado = :assinado";
                        $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                        $stmt_usu->bindParam(':proposicao', $id); 
                        $stmt_usu->bindValue(':assinado', 1); 
                        $stmt_usu->execute();
                        $rows = $stmt_usu->rowCount();
                        $credenciais="";
                        if($rows > 0)
                        {               
                            $credenciais="";                                 
                            while($result_usu = $stmt_usu->fetch())
                            {
                                $credenciais .= $result_usu['credenciais']."\n";
                            }                                                                      
                        }

                        echo "<tr class='$c1'>
                                    <td>$tipo</td>
                                    <td>$numero</td>
                                    <td>$ementa</td>
                                    <td>$data_envio</td>
                                    <td>$status1</td>
                                    <td>$prazo_final</td>
                                    <td align='center'>";
                                        if($result['texto_original']){ echo "<a href='".$result['texto_original']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a>";} 
                                    echo "</td>
                                    <td align='center'>";
                                            if($texto_original != '' && $credenciais != '')
                                            {                                                          
                                                echo "<i class='fas fa-file-signature hand' style='color:green; font-size:22px;' data-toggle='tooltip' data-placement='bottom'  title='".$credenciais."'></i><br>";                                                           
                                            }  
                                            elseif($texto_original != '' && $credenciais == '')
                                            {     
                                                echo "<span style='color:red; font-weight:bold;'>Não assinado</span>";                                                     
                                                //echo "<i class='fas fa-file-signature hand' style='color:red; font-size:22px;' data-placement='bottom'></i>";                                                           
                                            }           
                                        echo "</td>
                                    <td align=center>
                                    ";
                                        if (($status == 'Não Enviado' || $status ==  'Devolvido' || $status == 'Arquivado') && $credenciais == "") {   
                                            echo "
                                                <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                                    <div class='g_excluir' title='Excluir' onclick=\"
                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                    \">	<i class='far fa-trash-alt'></i>
                                                </div>";                                                                                           
                                        };
                                            echo "
                                            <div class='g_exibir' title='Editar' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            ";

                                            if($texto_original != ''  && $perm_ass == 1){
                                                echo "<div class='g_exibir' title='Assinar e receber documento' onclick=\"
                                                         abreMask('<p class=\'titulo\'>Alerta</p><p>'+
                                                             '<form name=\'form_filtro\' id=\'form_filtro\' enctype=\'multipart/form-data\' method=\'post\'  action=\'$pagina_link/view/confirmar_assinatura/$id\'>'+
                                                             'Essa operação não poderá ser desfeita.<br>Deseja realmente confirmar o recebimento e assinar o arquivo abaixo? <br><br>'+
                                                             '<span class=\'bold\'>Dados da assinatura:</span><br>".$dados_ass." <br><br>'+
                                                             '<span class=\'bold\'>Anexo a ser assinado:</span><br><a href=\'".$texto_original."\' target=\'_blank\'><i class=\'fas fa-file-signature\' style=\'vertical-align:bottom; font-size:20px; margin-right: 7px;\'></i></a> <br><br><br>'+
                                                             '<input value=\' Sim \' type=\'submit\' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                             '<input value=\' Não \' type=\'button\' class=\'close_janela\'></for');
                                                         \">	<i class='fas fa-signature'></i>
                                                     </div>"; 
                                             }
                                             echo "
                                            <div class='g_black' title='QRCODE' onclick=\"
                                                abreMask('<p class=\'titulo\'>QRCODE</p><p>'+
                                                     '<img src=\'qrcode_materias.php?id=$id&pagina=$propoicoes\' width=\'200\' ><br><br>'+
                                                    '<input value=\' Fechar \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='fa fa-qrcode' aria-hidden='true'></i>
                                            </div>
                                            
                                    </td>
                                </tr>";
                    }
                    echo "</table>";
                    $cnt = "SELECT COUNT(*) FROM cadastro_proposicoes  
                                LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
                                WHERE " . $numero_query . " ";
                    $stmt = $PDO_PROCLEGIS->prepare($cnt);
                    $stmt->bindParam(':fil_tipo1',     $fil_tipo1);

                    $variavel = "&fil_tipo=$fil_tipo";
                    include("../../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                if($_SESSION['autor_id'] == "")
                {            
                    ?>
            <script>
            mensagem("X",
                "<i class='fa fa-exclamation-circle'></i> Seu usuário não está vinculado a um autor, portanto, não pode criar uma proposição."
                );
            </script>
            <?php
                    exit; 
                }
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_proposicoes/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                           
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                
                                <p><label>Tipo*:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value=''>Tipo</option>";
                $sql = "SELECT * FROM aux_proposicoes_tipos 
                        WHERE ativo = :ativo                                        
                        ORDER BY descricao";
                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                $stmt_int->bindValue(':ativo',1);   
                $stmt_int->execute();
                while ($result_int = $stmt_int->fetch()) {
                    echo "<option value='" . $result_int['id'] . "'>" . $result_int['descricao'] . "</option>";
                }
                echo "
                                </select>     
                                <p><label>Ano*:</label> <input name='ano' id='ano' placeholder='Ano' class='obg' value='".date('Y')."'>
                                <div class='tipo'>
                                    <p><label>Número*:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                    <p><label>Prazo Final*:</label> <input name='prazo_final' id='prazo_final' placeholder='Prazo Final' class='obg' readonly>
                                </div>
                                <p><label>Ementa*:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'></textarea>
                                <p><label>Observação:</label> <textarea name='observacao' id='observacao' placeholder='Observação'></textarea>
                                <p><label>Texto Original:</label> <input type='file' name='texto_original[texto_original]' id='texto_original' placeholder='Texto Original'> 
                                <p><label>Endereço:</label> <input type='text' name='endereco' id='endereco' placeholder='Local onde a Moção foi realizada'> 
                                <p><label>Número:</label> <input type='text' name='end_numero' id='end_numero' placeholder='Número'> 
                                <p><label>Cidade:</label> <input type='text' name='cidade' id='cidade' placeholder='Cidade'> 
                                <p class='titulo'>Vincular Máteria Legislativa</p>
                                <p><label>Tipo de Matéria:</label> <select name='tipo_materia' id='tipo_materia'>
                                    <option value=''>Tipo de Matéria</option> ";
                $sql = "SELECT *
                                                FROM aux_materias_tipos WHERE ativo = :ativo
                                                
                                                ORDER BY sigla";
                $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                $stmt_int->bindValue(':ativo',1);   
                $stmt_int->execute();
                while ($result_materia= $stmt_int->fetch()) {
                    echo "<option value='" . $result_materia['id'] . "'>" . $result_materia['nome'] . " (" . $result_materia['sigla'] . ")</option>";
                }
                echo "                                                                
                                </select>   
                                
                                <p><label>Ano:</label> <select name='ano_materia' id='ano_materia'>
                                    <option value=''>Selecione </option> 
                                </select>

                                <p><label>Número da Matéria:</label> <select name='materia_anexada' id='materia_anexada'>
                                    <option value=''>Selecione </option> 
                                </select>

                            </div>	                                                                            
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_proposicoes/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            if ($pagina == 'edit') {
                if ($_SESSION['autor_id'] == "") {
                ?>
            <script>
            mensagem("X",
                "<i class='fa fa-exclamation-circle'></i> Seu usuário não está vinculado a um autor, portanto, não pode editar uma proposição."
                );
            </script>
            <?php
                    exit;
                }
                $sql = "SELECT *, cadastro_proposicoes.ementa as ementa
                                , cadastro_proposicoes.numero as numero_proposicao
                                , cadastro_proposicoes.ano as ano_proposicao
                                , cadastro_proposicoes.texto_original as texto_original
                                , aux_proposicoes_tipos.descricao as tipo_descricao
                                , aux_proposicoes_tipos.id as tipo
                                , aux_materias_tipos.nome as nome_materia 
                                , aux_materias_tipos.sigla as sigla_materia 
                                , cadastro_materias.numero as materia_anexada
                                , cadastro_materias.id as id_materia_anexada
                        FROM cadastro_proposicoes 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_proposicoes.tipo_materia
                        LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo   
                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_proposicoes.materia_anexada                  
                        WHERE cadastro_proposicoes.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {

                    $result = $stmt->fetch();
                    $tipo    = $result['tipo'];
                    $tipo_descricao    = $result['tipo_descricao'];
                    $numero         = $result['numero_proposicao'];
                    $ano_proposicao         = $result['ano_proposicao'];
                    $ementa         = $result['ementa'];
                    $observacao         = $result['observacao'];
                    $texto_original         = $result['texto_original'];
                    $tipo_materia       = $result['tipo_materia'];
                    $nome_materia       = $result['nome_materia'];
                    $sigla_materia       = $result['sigla_materia'];
                    $materia_anexada         = $result['materia_anexada'];
                    $ano_materia         = $result['ano_materia'];
                    $id_materia_anexada  = $result['id_materia_anexada'];
                    $prazo_final = date('d/m/Y', strtotime($result['prazo_final'])); 


                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_proposicoes/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                                  
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo*:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value='$tipo'>$tipo_descricao</option>";
                    $sql = "SELECT *
                                                FROM aux_proposicoes_tipos 
                                                WHERE ativo = :ativo
                                                
                                                ORDER BY descricao";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_int->bindValue(':ativo',1);   
                    $stmt_int->execute();
                    while ($result_int = $stmt_int->fetch()) {
                        echo "<option value='" . $result_int['id'] . "'>" . $result_int['descricao'] . "</option>";
                    }
                    echo "
                                </select>  
                                <div class='tipo'>
                                    <p><label>Número*:</label> <input name='numero' id='numero' placeholder='Número' value='$numero' class='obg'>
                                    <p><label>Prazo Final*:</label> <input name='prazo_final' id='prazo_final' placeholder='Prazo Final' value='$prazo_final' class='obg' readonly>
                                </div>
                                
                                <p><label>Ano*:</label> <input name='ano' id='ano' value='$ano_proposicao' class='obg'>                                
                          
                                <p><label>Ementa*:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'>$ementa</textarea>
                                <p><label>Observação:</label> <textarea name='observacao' id='observacao' placeholder='Observação'>$observacao</textarea>
                                <p><label>Texto Original:</label> ";
                    if ($texto_original != '') {
                        echo "<a href='$texto_original' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";
                    }
                    echo " &nbsp; 
                                <p><label>Alterar Texto Original:</label> <input type='file' name='texto_original[texto_original]'>
                                <p><label>Endereço:</label> <input name='endereco' id='endereco' placeholder='Endereço' value='".$result['endereco']."'>
                                <p><label>Número:</label> <input name='end_numero' id='end_numero' placeholder='Número' value='".$result['end_numero']."'>
                                <p><label>Cidade:</label> <input name='cidade' id='cidade' placeholder='cidade' value='".$result['cidade']."'>
                                <p class='titulo'>Vincular Máteria Legislativa</p>
                                <p><label>Tipo de Matéria:</label> <select name='tipo_materia' id='tipo_materia'>
                                    <option value='$tipo_materia'>$nome_materia ($sigla_materia)</option> ";
                    $sql = "SELECT *
                                                FROM aux_materias_tipos
                                                WHERE ativo = :ativo
                                                ORDER BY sigla";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_int->bindValue(':ativo',1);  
                    $stmt_int->execute();
                    while ($result_int = $stmt_int->fetch()) {
                        echo "<option value='" . $result_int['id'] . "'>" . $result_int['nome'] . " (" . $result_int['sigla'] . ")</option>";
                    }
                    echo "                                                                
                                </select>   
                                <p><label>Ano:</label><select name='ano_materia' id='ano_materia'>
                                    <option value='$ano_materia'> $ano_materia </option>
                                </select> 
                             
                                <p><label>Número Matéria:</label> <select id='materia_anexada' name='materia_anexada'>
                                    <option value='$id_materia_anexada'>  Nº $materia_anexada</option>
                                </select>
                            </div>                        
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_proposicoes/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }
            if ($pagina == 'exib') {
                $sql = "SELECT *, cadastro_proposicoes.ementa as ementa
                                , aux_proposicoes_tipos.descricao as tipo_descricao
                                , aux_materias_tipos.nome as nome_materia 
                                , aux_materias_tipos.sigla as sigla_materia 
                                , cadastro_materias.numero as materia
                                , cadastro_proposicoes.numero as propositura
                                , cadastro_proposicoes.ano as ano_propositura
                                , cadastro_proposicoes.observacao as observacao_propositura
                                , cadastro_proposicoes.texto_original as texto_propositura


                        FROM cadastro_proposicoes 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_proposicoes.tipo_materia
                        LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo  
                        LEFT JOIN cadastro_proposicoes_status as h1 ON h1.proposicao = cadastro_proposicoes.id
                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_proposicoes.materia_anexada
                        WHERE cadastro_proposicoes.id = :id  AND h1.id = (SELECT MAX(h2.id) FROM cadastro_proposicoes_status h2 where h2.proposicao = h1.proposicao)";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {

                    $result = $stmt->fetch();
                    $tipo    = $result['tipo'];
                    $tipo_descricao    = $result['tipo_descricao'];
                    $numero         = $result['propositura'];
                    $ementa         = $result['ementa'];
                    $observacao         = $result['observacao_propositura'];
                    $texto_original         = $result['texto_propositura'];
                    $tipo_materia       = $result['tipo_materia'];
                    $nome_materia       = $result['nome_materia'];
                    $sigla_materia       = $result['sigla_materia'];
                    $materia_anexada         = $result['materia'];
                    $ano_materia         = $result['ano_materia'];
                    $data_envio         = $result['data_envio'];
                    $status             = $result['status']; 
                    $ano                = $result['ano_propositura']; 

                    //PEGA ASSINATURAS
                    $sql = "SELECT * FROM cadastro_proposicoes_assinaturas 
                            WHERE proposicao = :proposicao AND assinado = :assinado";
                    $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                    $stmt_usu->bindParam(':proposicao', $id); 
                    $stmt_usu->bindValue(':assinado', 1); 
                    $stmt_usu->execute();
                    $rows = $stmt_usu->rowCount();
                    $credenciais="";
                    if($rows > 0)
                    {                                                
                        while($result_usu = $stmt_usu->fetch())
                        {
                            $credenciais .= $result_usu['credenciais']."\n";
                        }                                                                      
                    }

                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_proposicoes/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>";
                    if ($status == "Não Enviado" || $status == 'Devolvido') {
                        echo "
                            <div class='g_editar' title='Arquivar Proposição' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/arquivar/$id\");'><i class='fas fa-folder'></i></div>
                            ";
                            if($credenciais != "")
                            {
                                echo "<div class='g_adicionar' title='Enviar' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/enviar/$id?pag=$pag\");'><i class='fas fa-paper-plane'></i></div>";
                            }
                            
                    }   
                    else if ($status == 'Arquivado'){
                        echo"<div class='g_adicionar' title='Desarquivar Proposição' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/desarquivar/$id\");'><i class='fas fa-folder-open'></i></div>";
                    }
                    else {
                        echo "<div class='g_exibir' title='Recibo de Envio' onclick='window.open(\"recibo/$id\", \"_blank\", \"toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1200,height=650\");'><i class='far fa-file-alt'></i></div>";
                    }
                    echo "
                                <table width='100%' cellpadding='8'>
                                    <tr>
                                        <td class='bold' align='right' width='10%'>Tipo:</td>
                                        <td  width='40%'>$tipo_descricao</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right'  width='10%'>Número:</td>
                                        <td  width='40%'>$numero</td>
                                        <td class='bold' align='right'  width='10%'>Ano:</td>
                                        <td  width='40%'>$ano</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right'>Ementa:</td>
                                        <td  >$ementa</td>
                                        <td class='bold' align='right'>Observação:</td>
                                        <td >$observacao</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' >Texto Original:</td>
                                        <td colpsan='3'>";
                    if ($texto_original != '') {
                        echo "<a href='$texto_original' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";
                    }
                    echo " &nbsp;</td>
                                    </tr>                                    
                                    </tr>
                                    <td class='bold' align='right'>Matéria vinculada:</td>
                                    <td colspan='3' >";
                    if ($nome_materia) {
                        echo $nome_materia . " nº $materia_anexada de $ano_materia";
                    }
                    echo " </td>
                                </tr>";    
                                if($result['endereco']!=''){
                                    echo "
                                        <tr>
                                            <td class='bold' align='right' width='10%'>Endereço:</td>
                                            <td  width='40%'>".$result['endereco'].", ".$result['numero']." - ".$result['cidade']."</td>
                                        </tr>                                   
                                    ";
                                }

                                $sql_h="SELECT * FROM cadastro_proposicoes 
                                    LEFT JOIN cadastro_proposicoes_status ON cadastro_proposicoes_status.proposicao = cadastro_proposicoes.id
                                    WHERE proposicao =:proposicao"; 
                                $stmt_h = $PDO_PROCLEGIS->prepare($sql_h); 
                                $stmt_h->bindParam(':proposicao', $id);
                                $stmt_h->execute(); 
                                $rows_h = $stmt_h->rowCount(); 
                                if($rows_h>0){
                                    echo "<tr>
                                            <td class='bold' align='right' width='10%'>Histórico</td> 
                                        </tr>";
                                    while($result_h=$stmt_h->fetch()){
                                        $data_status = reverteData(substr($result_h['data_cadastro'], 0, 10)) . " às " . substr($result_h['data_cadastro'], 11, 5);
                                        echo "
                                            <tr>
                                                <td align='right' width='10%'>$data_status</td>
                                                <td width='40%'>".$result_h['status']."</td>
                                                <td width='40%'>".$result_h['observacao']."</td>
                                            </tr>
                                        ";
                                    }
                                }   
                                echo "</table>
                               
                            </div>                        
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_proposicoes/view'; value='Voltar'/></center>
                            </center>
                        </div>
                    </form>
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
        dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
        dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
            'Outubro', 'Novembro', 'Dezembro'
        ],
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

<script>
$("#tipo").change(function() {
    var tipo = $(this).val();
    var ano = $('#ano').val();
    var cli = '<?php echo $_SESSION['cliente_url']?>';
    var sis = '<?php echo $_SESSION['sistema_url']?>';
    $.post("carrega_numeracao.php", {
            acao: 'carrega_numero_proposicao',
            tp: tipo,
            an: ano,
            cliente_url: cli,
            sistema_url: sis,
        },
        function(dados) {
            if (dados != '') {
                $('.tipo').html(dados);

            }
        }
    )
});

$("#ano").change(function() {
    var tipo = $('#tipo').val();
    var ano = $(this).val();
    var cli = '<?php echo $_SESSION['cliente_url']?>';
    var sis = '<?php echo $_SESSION['sistema_url']?>';
    $.post("carrega_numeracao.php", {
            acao: 'carrega_numero_proposicao',
            tp: tipo,
            an: ano,
            cliente_url: cli,
            sistema_url: sis,
        },
        function(dados) {
            if (dados != '') {
                $('#numero').val(dados);
            }
        }
    )
});

$("#tipo_materia").change(function() {
    var tipo = $(this).val();
    var cli = '<?php echo $_SESSION['cliente_url']?>';
    var sis = '<?php echo $_SESSION['sistema_url']?>';
    $.post("carrega_numeracao.php", {
            acao: 'carrega_ano_materia',
            tp: tipo,
            cliente_url: cli,
            sistema_url: sis,
        },
        function(dados) {
            if (dados != '') {
                $('#ano_materia').append(dados);
            }
        }
    )
});


$("#ano_materia").change(function() {
    var ano = $(this).val();
    var tipo = $("#tipo_materia").val();
    var cli = '<?php echo $_SESSION['cliente_url']?>';
    var sis = '<?php echo $_SESSION['sistema_url']?>';
    $.post("carrega_numeracao.php", {
            acao: 'carrega_numero_materia_proposicao',
            tp: tipo,
            an: ano,
            cliente_url: cli,
            sistema_url: sis,
        },
        function(dados) {
            if (dados != '') {
                $('#materia_anexada').append(dados);
            }
        }
    )
});
</script>