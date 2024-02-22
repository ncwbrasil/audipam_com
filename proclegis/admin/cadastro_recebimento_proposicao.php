<?php
$pagina_link = 'cadastro_recebimento_proposicao';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php                     
            $page = "Legislativo &raquo; <a href='cadastro_recebimento_proposicao/group'> Recebimento de Propositura </a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $confirmacao_recebimento  = $_POST['confirmacao_recebimento'];
            $usu_recebimento  = $_POST['usu_recebimento'];
        
            if($action == 'confirmar_recebimento'){

                $data_envio = date("Y-m-d H:i:s");
                $sql = "UPDATE cadastro_proposicoes SET data_recebido = :data_recebido  WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':data_recebido', $data_envio);
                $stmt->execute();

                $sql = "INSERT INTO cadastro_proposicoes_status SET proposicao = :proposicao, status = :status";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status','Recebido');
                $stmt->bindParam(':proposicao',$id);
                if($stmt->execute()){
                    $sqlP = "SELECT * FROM cadastro_proposicoes
                    LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
                    WHERE cadastro_proposicoes.id = :id"; 
                    $stmtP = $PDO_PROCLEGIS->prepare($sqlP);
                    $stmtP->bindParam(':id',$id);
                    $stmtP->execute();
                    $rows = $stmtP->rowCount(); 
                    if($rows>0){
                        $resultP = $stmtP->fetch(); 
                        $sql_tipo = "SELECT * FROM aux_materias_tipos WHERE nome like :nome"; 
                        $stmt_tipo = $PDO_PROCLEGIS->prepare($sql_tipo);
                        $stmt_tipo->bindValue(':nome',"%".$resultP['descricao']."%");
                        $stmt_tipo->execute();
                        $rows_tipo = $stmt_tipo->rowCount(); 
                        if($rows_tipo>0){ 

                            $result_tipo = $stmt_tipo->fetch(); 
                            $tipo  = $result_tipo['id']; 

                        }else {

                            $tp_sigla = explode(" ", $resultP['descricao']);

                            if(count($tp_sigla) > 1){
                                foreach($tp_sigla as $value){
                                    $sigla .= substr($value, 0);
                                }
                            }
                            else {
                                $sigla = $tp_sigla[0]; 
                                $sigla = substr($sigla, 0, 3);                                 
                            }

                            $sql_tipo_materia = "INSERT INTO aux_materias_tipos (sigla, nome, ativo) VALUES (:sigla, :nome, :ativo)"; 
                            $stmt_tipo_materia = $PDO_PROCLEGIS->prepare($sql_tipo_materia);
                            $stmt_tipo_materia->bindValue(':sigla', $sigla);
                            $stmt_tipo_materia->bindValue(':nome', $resultP['descricao']);
                            $stmt_tipo_materia->bindValue(':ativo', 1);
                            if($stmt_tipo_materia->execute()){
                                $tipo = $PDO_PROCLEGIS->lastInsertId();
                            }

                        }
                        $dadosM = array(
                            'tipo'              => $tipo,
                            'numero'            => $resultP['numero'],
                            'ano'               => $resultP['ano'], 
                            'objeto'            => $resultP['ementa'],
                            'ementa'            => $resultP['ementa'],
                            'endereco'          => $resultP['endereco'], 
                            'end_numero'        => $resultP['end_numero'], 
                            'cidade'            => $resultP['cidade'], 
                            'latitude'          => $resultP['latitude'], 
                            'longitude'         => $resultP['longitude'], 
                            'texto_original'         => $resultP['texto_original'], 
                            'cadastrado_por'    => $_SESSION['usuario_id'],
                        );

                        if($resultP['materia_anexada'] == ''){

                            $sql = "INSERT INTO cadastro_materias SET ".bindFields($dadosM);
                            $stmt = $PDO_PROCLEGIS->prepare($sql);	
                            if($stmt->execute($dadosM)){
    
                                $materia = $PDO_PROCLEGIS->lastInsertId();

                                $sql_materia_anexada = "UPDATE cadastro_proposicoes SET materia_gerada = :materia_gerada WHERE id = :id"; 
                                $stmt_materia_anexada = $PDO_PROCLEGIS->prepare($sql_materia_anexada);
                                $stmt_materia_anexada->bindValue(':materia_gerada', $materia);
                                $stmt_materia_anexada->bindValue(':id', $id);
                                $stmt_materia_anexada->execute();
    
                                $sql_protocolo='SELECT MAX(numero) FROM protocolo_gerais WHERE ano = :ano'; 
                                $stmt_protocolo = $PDO_PROCLEGIS->prepare($sql_protocolo);
                                $stmt_protocolo->bindValue(':ano', date('Y'));
                                $stmt_protocolo->execute();
    
                                $result_protocolo = $stmt_protocolo->fetch();
                                
                                $protocolo = $result_protocolo[0]+1; 
    
                                $dadosP = array(
                                    'natureza'          =>'Legislativo',
                                    'tipo_materia'      => $tipo, 
                                    'materia'           => $materia, 
                                    'numero'            => $protocolo, 
                                    'ano'               => date('Y'), 
                                    'interessado'       => $_SESSION['usuario_name'], 
                                    'cadastrado_por'    => $_SESSION['usuario_id']
                                );
    
                                $sql_p = "INSERT INTO protocolo_gerais SET ".bindFields($dadosP); 
                                $stmt_p = $PDO_PROCLEGIS->prepare($sql_p);	
                                if($stmt_p->execute($dadosP)){
    
                                    $protocolo_materia = str_pad($protocolo,6,'0',STR_PAD_LEFT).'/'.date('Y'); 
    
                                    $sqlM2 = "UPDATE cadastro_materias SET protocolo = :protocolo WHERE id = :id";
                                    $stmtM2 = $PDO_PROCLEGIS->prepare($sqlM2);
                                    $stmtM2->bindValue(':protocolo', $protocolo_materia);
                                    $stmtM2->bindValue(':id', $materia);
                                    if($stmtM2->execute()){
                                        log_operacao($materia, $PDO_PROCLEGIS);  

                                        ?>
                                        <script>
                                            mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                        </script>
                                        <?php
                    
                                    }else {
                                        $err = $stmt->errorInfo();
                                        print_r($err);
            
                                    }
                                }
                                else {
                                    $err = $stmt->errorInfo();
                                    print_r($err);
        
                                }   
    
                            }else {
                                $err = $stmt->errorInfo();
                                print_r($err);
                            }
                        }
                        else {
                            $sql = "INSERT INTO cadastro_materias SET ".bindFields($dadosM);
                            $stmt = $PDO_PROCLEGIS->prepare($sql);	
                            if($stmt->execute($dadosM)){
    
                                $materia = $PDO_PROCLEGIS->lastInsertId();
                                
                                $sql_materia_anexada = "UPDATE cadastro_proposicoes SET materia_gerada = :materia_gerada WHERE id = :id"; 
                                $stmt_materia_anexada = $PDO_PROCLEGIS->prepare($sql_materia_anexada);
                                $stmt_materia_anexada->bindValue(':materia_gerada', $materia);
                                $stmt_materia_anexada->bindValue(':id', $id);
                                $stmt_materia_anexada->execute();
                                

                                $sql_protocolo='SELECT MAX(numero) FROM protocolo_gerais WHERE ano = :ano'; 
                                $stmt_protocolo = $PDO_PROCLEGIS->prepare($sql_protocolo);
                                $stmt_protocolo->bindValue(':ano', date('Y'));
                                $stmt_protocolo->execute();
    
                                $result_protocolo = $stmt_protocolo->fetch();
                                
                                $protocolo = $result_protocolo[0]+1; 
    
                                $dadosP = array(
                                    'natureza'          =>'Legislativo',
                                    'tipo_materia'      => $tipo, 
                                    'materia'           => $materia, 
                                    'numero'            => $protocolo, 
                                    'ano'               => date('Y'), 
                                    'interessado'       => $_SESSION['usuario_name'], 
                                    'cadastrado_por'    => $_SESSION['usuario_id']
                                );
    
                                $sql_p = "INSERT INTO protocolo_gerais SET ".bindFields($dadosP); 
                                $stmt_p = $PDO_PROCLEGIS->prepare($sql_p);	
                                if($stmt_p->execute($dadosP)){
    
                                    $protocolo_materia = str_pad($protocolo,6,'0',STR_PAD_LEFT).'/'.date('Y'); 
    
                                    $sqlM2 = "UPDATE cadastro_materias SET protocolo = :protocolo WHERE id = :id";
                                    $stmtM2 = $PDO_PROCLEGIS->prepare($sqlM2);
                                    $stmtM2->bindValue(':protocolo', $protocolo_materia);
                                    $stmtM2->bindValue(':id', $materia);
                                    if($stmtM2->execute()){

                                        $dados_anexada = array(
                                            'materia'   => $materia_principal, 
                                            'tipo_materia' => $tipo,
                                            'materia_anexada'   => $materia,
                                        );

                                        $sql_anexada = "INSERT INTO cadastro_materias_anexadas SET".bindFields($dados_anexada); 
                                        $stmt_anexada = $PDO_PROCLEGIS->prepare($sql_anexada);	
                                        if($stmt_anexada->execute($dados_anexada)){
                                            log_operacao($materia, $PDO_PROCLEGIS);  

                                            ?>
                                                <script>
                                                    mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                                                </script>
                                            <?php
                                        }else {
                                            $err = $stmt_anexada->errorInfo();
                                            print_r($err);
                                            ?>
                                            <script>
                                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                            </script>
                                            <?php                
    
                                        }
                    
                                    }else {
                                        $err = $stmtM2->errorInfo();
                                        print_r($err);
                                        ?>
                                        <script>
                                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                        </script>
                                        <?php                    
            
                                    }
                                }
                                else {
                                    $err = $stmt_p->errorInfo();
                                    print_r($err);
                                    ?>
                                    <script>
                                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                    </script>
                                    <?php               
                                }   
    
                            }else {
                                $err = $stmt->errorInfo();
                                print_r($err);
                                ?>
                                <script>
                                    mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                                </script>
                                <?php
                            }   
                        }

                    }else {
                        $err = $stmtP->errorInfo();
                        print_r($err);
                        ?>
                        <script>
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                        <?php
                    }
                }
                else
                {
                    $err = $stmtP->errorInfo();
                    print_r($err);
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }        
            }        

            if($action == 'devolver'){

                $observacao = $_POST['observacao']; 

                $sql = "INSERT INTO cadastro_proposicoes_status SET proposicao = :proposicao, status = :status, observacao = :observacao";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status','Devolvido');
                $stmt->bindParam(':proposicao',$id);
                $stmt->bindParam(':observacao',$observacao);
                if($stmt->execute())
                {
                    log_operacao($id, $PDO_PROCLEGIS);  

                    ?>
                        <script>
                            mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                        </script>
                    <?php

                }
                else
                {
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

            $fil_tipo = $_REQUEST['fil_tipo'];
            if ($fil_tipo != '') {
                $tipo_query = " status = :status ";
            } else {
                $tipo_query = " status = 'Enviado' ";
            }

            $sql = "SELECT *, cadastro_proposicoes.id as id
                            , cadastro_proposicoes.ementa as ementa
                            , aux_proposicoes_tipos.descricao as tipo 
                    FROM cadastro_proposicoes 
                    LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
                    LEFT JOIN cadastro_proposicoes_status as h1 ON h1.proposicao = cadastro_proposicoes.id
                    WHERE $tipo_query AND h1.id = (SELECT MAX(h2.id) FROM cadastro_proposicoes_status h2 where h2.proposicao = h1.proposicao)  AND cadastro_proposicoes.ativo = :ativo
                    ORDER BY cadastro_proposicoes.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindParam(':fil_tipo1',     $fil_tipo1);
            $stmt->bindParam(':status',     $fil_tipo);
            $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
            $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
            $stmt->bindValue('ativo', 1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_recebimento_proposicao/view'>
                        <select name='fil_tipo' id='fil_tipo'>
                            <option value='Enviado'> Enviados </option>
                            <option value='Não Enviado'> Não Recebido</option>
                            <option value='Recebido'> Recebidos </option>
                            <option value='Devolvido'> Devolvidos </option>
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
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $id = $result['id'];
                        $tipo = $result['tipo'];
                        $numero = $result['numero'];
                        $ementa = $result['ementa'];
                        $status = $result['status'];
                        $data_envio = reverteData(substr($result['data_cadastro'], 0, 10)) . " às " . substr($result['data_cadastro'], 11, 5);

                        if($status ==  'Não Enviado'){
                            $status = "<b style='color:orange; font-weight: bold;'>".$status."</b>";
                        }
                        if($status ==  'Enviado'){
                            $status = "<b style='color:blue; font-weight: bold;'>".$status."</b>";
                        }
                        if($status ==  'Recebido'){
                            $status = "<b style='color:green; font-weight: bold;'>".$status."</b>";
                        }
                        if($status ==  'Devolvido'){
                            $status = "<b style='color:red; font-weight: bold;'>".$status."</b>";
                        }


                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                    <td>$tipo</td>
                                    <td>$numero</td>
                                    <td>$ementa</td>
                                    <td>$data_envio</td>
                                    <td>$status</td>
                                    <td align=center>
                                        <div class='g_exibir' title='Editar' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
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

            if ($pagina == 'exib') {
                $sql = "SELECT *, cadastro_proposicoes.ementa as ementa
                                , aux_proposicoes_tipos.descricao as tipo_descricao
                                , aux_materias_tipos.nome as nome_materia 
                                , cadastro_materias.numero as materia
                                , aux_materias_tipos.sigla as sigla_materia 
                                , cadastro_proposicoes.numero as propositura
                                , cadastro_proposicoes.ano as ano_propositura
                                , cadastro_proposicoes.observacao as observacao_propositura
                                , cadastro_proposicoes.texto_original as texto_propositura

                        FROM cadastro_proposicoes 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_proposicoes.tipo_materia
                        LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo  
                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_proposicoes.materia_anexada
                        LEFT JOIN cadastro_proposicoes_status as h1 ON h1.proposicao = cadastro_proposicoes.id
                        WHERE cadastro_proposicoes.id = :id AND h1.id = (SELECT MAX(h2.id) FROM cadastro_proposicoes_status h2 where h2.proposicao = h1.proposicao)";
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
                    $numero_materia         = $result['materia'];
                    $ano_materia         = $result['ano_materia'];
                    $data_envio         = $result['data_envio'];
                    $status             = $result['status']; 
                    $ano                = $result['ano_propositura']; 

                    echo "
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_recebimento_proposicao/view/confirmar_recebimento/$id'>";

                            if ($status == "Recebido") {
                                echo "<div class='g_exibir' title='Recibo de Envio' onclick='window.open(\"recibo_proposicao/$id\", \"_blank\", \"toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1200,height=650\");'><i class='far fa-file-alt'></i></div>";
                            }         
                            echo"<table width='100%' cellpadding='8'>
                                    <tr>
                                        <td class='bold' align='right' width='10%'>Tipo:</td>
                                        <td  width='40%'>$tipo_descricao</td>
                                    </tr>

                                    <tr>
                                        <td class='bold' align='right'  width='10%'>Número:</td>
                                        <td  width='40%'>$numero</td>
                                        <td class='bold' align='right' width='10%'>Ano:</td>
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
                        echo $nome_materia . " nº $numero_materia de $ano_materia";
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
                                
                                <div id='motivo' style='display:none;'>
                                    <p><label>Observação:</label> <textarea name='observacao' id='observacao' placeholder='Observação'></textarea>
                                </div>
                            </div>                        
                            <center>
                                <div id='erro' align='center'>&nbsp;</div>";
                                if($status != 'Recebido'){
                                    echo"
                                        <input type='submit' id='confirmarRecebimento' value='Confirmar Recebimento' onclick='confirmarRecebimento();' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                        <input type='button' id='devolver' value='Devolver Propositura' data-toggle='modal' data-target='#devolve_Propositura' />&nbsp;&nbsp;&nbsp;&nbsp;
                                    "; 
                                }
                                echo "<input type='button' id='voltar_proposicao' onclick=javascript:window.location.href='cadastro_recebimento_proposicao/view'; value='Voltar'/>
                            </center>
                            </form>
                        </div>

                        <div class='modal fade' id='devolve_Propositura' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                            <div class='modal-dialog' role='document'>
                                <div class='modal-content'>
                                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_recebimento_proposicao/view/devolver/$id'>
                                        <div class='modal-body'>
                                            <div class='form-group'>
                                                <label for='observacao' class='col-form-label'>Observação:</label>
                                                <textarea class='form-control' id='observacao' name='observacao'></textarea>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='submit' class='btn btn-primary'>Devolver Propositura</button>
                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                    ";
                }
            }

            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
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


