<?php
$pagina_link = 'cadastro_recebimento_tramitacao';
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
            $page = "Cadastro &raquo; <a href='cadastro_recebimento_tramitacao/group'> Recebimento em Lote </a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $confirmacao_recebimento  = $_POST['confirmacao_recebimento'];
            $usu_recebimento  = $_POST['usu_recebimento'];
        
            if($action == 'confirmar_recebimento')
            {
                $id_tramitacao = $_POST['materia'];
                $i=0; 
                foreach($id_tramitacao as $tramitacao){
                    $usu_recebimento = $_SESSION['usuario_id']; 
                    $sql = "UPDATE cadastro_materias_tramitacao SET confirmacao_recebimento = :confirmacao_recebimento, usu_recebimento = :usu_recebimento WHERE id = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindValue(':confirmacao_recebimento',1);
                    $stmt->bindValue(':usu_recebimento',$usu_recebimento);
                    $stmt->bindParam(':id',$tramitacao);
                    $stmt->execute();
                    if($stmt->execute())
                    {   
                        $id = $PDO_PROCLEGIS->lastInsertId();
                        log_operacao($id, $PDO_PROCLEGIS);  
                    }
                    else
                    {
                        $i=1;
                    }        
                }

                if($i == 0)
                {
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

            $num_por_pagina = 20;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_tipo = $_REQUEST['fil_tipo'];
            if($fil_tipo == '')
            {
                $tipo_query = " 1 = 1 ";
            }
            else
            {
                $tipo_query = " (cadastro_materias.tipo = :fil_tipo) ";
            }
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $numero_query = " (cadastro_materias.numero = :fil_numero) ";
            }
            $fil_ano = $_REQUEST['fil_ano'];
            if($fil_ano == '')
            {
                $ano_query = " 1 = 1 ";
            }
            else
            {
                $ano_query = " (cadastro_materias.ano = :fil_ano) ";
            }
            $fil_ementa = $_REQUEST['fil_ementa'];
            if($fil_ementa == '')
            {
                $ementa_query = " 1 = 1 ";
            }
            else
            {
                $fil_ementa1 = "%".$fil_ementa."%";
                $ementa_query = " (ementa LIKE :fil_ementa1 ) ";
            }

            if($pagina == "view")
            {
                $num_por_pagina = 20;
                if(!$pag){$primeiro_registro = 0; $pag = 1;}
                else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}

                $sql = "SELECT *, cadastro_materias_tramitacao.id as id_tramitacao
                        , aux_materias_status_tramitacao.nome as nome_status                                                   
                        , cadastro_usuarios.usu_nome as nome_responsavel 
                        FROM cadastro_materias_tramitacao 
                        LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = cadastro_materias_tramitacao.unidade_origem
                        LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = cadastro_materias_tramitacao.unidade_destino                                        
                        LEFT JOIN aux_materias_status_tramitacao ON aux_materias_status_tramitacao.id = cadastro_materias_tramitacao.status_tramitacao  
                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_materias_tramitacao.responsavel
                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_materias_tramitacao.materia
                        WHERE  $tipo_query AND $numero_query AND $ano_query AND $ementa_query AND t2.usuario_responsavel = :usuario_responsavel AND cadastro_materias_tramitacao.confirmacao_recebimento is NUll
                        ORDER BY cadastro_materias_tramitacao.data_tramitacao ASC
                        LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->bindParam(':fil_tipo', 	$fil_tipo);                
                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);                
                $stmt->bindParam(':fil_numero', 	$fil_numero);                
                $stmt->bindParam(':fil_ano', 	$fil_ano); 

                $stmt->bindParam(':usuario_responsavel', $_SESSION['usuario_id']); 
                $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();
                                     
                echo "
                <div class='titulo'> $page  &raquo; $tipo_nome </div>
                <div id='botoes'>
                    <input type='checkbox' class='todos' name='todos' id='todos' style='display:none'>
                    <label for='todos' style='margin:0 auto; display:table; float:right' onclick='marcarRecebidos()'><div class='g_recebimento' title='Confirmar Recebimento de todas as Tramitações'><i class='fas fa-check'></i></div></label>                                        

                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_recebimento_tramitacao/view'>
                        <select name='fil_tipo' id='fil_tipo'>
                            <option  value=''>Tipo de Matéria</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_materias_tipos 

                                    ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_tipo'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
                            }                        
                            echo "
                        </select>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                        <input name='fil_ano' id='fil_ano' value='$fil_ano' placeholder='Ano'>
                        <input name='fil_ementa' id='fil_ementa' value='$fil_ementa' placeholder='Ementa'>
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>    
                </div>
                ";
                if ($rows > 0)
                {
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_recebimento_tramitacao/view/confirmar_recebimento'>
                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                            <tr>
                                <td class='titulo_tabela'  align='center'>Data</td>
                                <td class='titulo_tabela'>Tramitação</td>
                                <td class='titulo_tabela'>Matéria Leislativa</td>
                                <td class='titulo_tabela' align='right' width='40px'>Receber</td>
                            </tr>";
                            $c=0;
                            $z = 0; 
                            while($result = $stmt->fetch())
                            {
                                $z++; 
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
                                $nome_documento = $result['nome_documento'];
                                $materia = $result['materia'];
                                $id_tramitacao = $result['id_tramitacao']; 
                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                echo "
                                    <tr class='$c1'>
                                        <td align='center' valign='top'>$data_tramitacao<br>$hora_tramitacao</td>
                                        <td>
                                            <span class='bold'>$origem <i class='fas fa-long-arrow-alt-right'></i> $destino </span> <br>
                                            ";if($anexo != '')
                                            { 
                                                echo "<a href='".$anexo."' target='_blank'><i class='far fa-file' style='vertical-align:bottom; font-size:20px; margin-right: 7px;'></i>Documento juntado</a>";
                                                if($paginas)
                                                {
                                                    echo " - página(s) ".$paginas;
                                                }
                                                echo "<br>";
                                            } echo "
                                            $nome_status <br>
                                            $texto_acao <br>
                                        </td>   
                                        <td>";
                                            $sql_materias = "SELECT *, aux_materias_tipos.nome as tipo_nome,
                                                            aux_materias_tipos.sigla as tipo_sigla,
                                                            cadastro_materias.id as id
                                                    FROM cadastro_materias 
                                                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo  
                                                    LEFT JOIN ( cadastro_materias_autoria 
                                                        LEFT JOIN (aux_autoria_autores 
                                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                                                        ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                                                    ON cadastro_materias_autoria.materia = cadastro_materias.id                   
                                                    WHERE cadastro_materias.id = :materia";
                                            $stmt_materias = $PDO_PROCLEGIS->prepare($sql_materias);    
                                            $stmt_materias->bindParam(':materia', 	$materia);
                                            $stmt_materias->execute();
                                            $result_materias=$stmt_materias->fetch(); 
                                            echo "<p><span class='bold'>".$result_materias['tipo_sigla']." ".$result_materias['numero']."/".$result_materias['ano']." - ".$result_materias['tipo_nome']."</span> <br>
                                            Ementa:".$result_materias['ementa']."<br/>
                                            Data apresentação:".reverteData($result_materias['data_apresentacao'])."<br/>
                                            "; if($result_materias['texto_original']){ echo "Texto original:<a href='".$result_materias['texto_original']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        </td>                                    
                                        <td align='right'> 
                                            <input type='checkbox' class='recebimento' id='materia$z' name='materia[]' value ='$id_tramitacao' style='display:none'>
                                            <label for='materia$z' style='margin:0 auto; display:table; float:right'><div class='g_recebimento r$z' id='$z' title='Confirmar Recebimento'><i class='fas fa-check'></i></div></label>                                        
                                        </td>
                                    </tr>
                                ";
                            }
                        echo "</table>      
                        <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Confirmar Recebimento' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        </center>              
                    </form>                            
                    ";                                        
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
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

<script>
$('.g_recebimento').click(function() {
    var r = $(this).attr('id'); 

    $('#materia'+r).change(function() {
        if($(this).is(":checked")) {
            $('.r'+r).css('background','#1DBA9B');
            $('.r'+r).css('border','1px solid #008000');
        }
        else {
            $('.r'+r).css('background','#ccc'); 
            $('.r'+r).css('border','1px solid #585858'); 
        }
    });
});

function marcarRecebidos(){
    if(!$('.todos').prop("checked"))
    {
        $('.recebimento').each(
            function(){
                if($(this).prop("disabled")){}
                else
                {
                    $(this).prop("checked", true);
                    $('.g_recebimento').css('background','#1DBA9B');
                    $('.g_recebimento').css('border','1px solid #008000');
                }
            }
        );
    }
    else
    {
        $('.recebimento').each(
            function(){
                $(this).prop("checked", false);   
                $('.g_recebimento').css('background','#ccc');
                $('.g_recebimento').css('border','1px solid #585858');   
            }
        );
    }
}

</script>