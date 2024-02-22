<div  class="modal fade in" id="tramitacaoEdit<?php echo $id_tramitacao;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_tramitacao'  enctype='multipart/form-data' method='post' action='docadm_documentos/exib/".$id."/editar_tramitacao#tramitacao'>							
                    <input type='hidden' name='id_tramitacao' id='id_tramitacao' value='".$id_tramitacao."'  class='obg'>
                    <p><label>Unidade Origem*:</label> <select name='unidade_origem' id='unidade_origem' class='obg' >
                        <option value='$unidade_origem'>$origem</option>";
                            $sql = "SELECT *, aux_materias_unidade_tramitacao.id as id
                                            , aux_materias_orgaos.sigla as sigla_orgao
                                            , aux_materias_orgaos.nome as nome_orgao
                                            , cadastro_comissoes.sigla as sigla_comissao
                                            , cadastro_comissoes.nome as nome_comissao
                                            , cadastro_parlamentares.nome as nome_parlamentar
                                    FROM aux_materias_unidade_tramitacao 
                                    LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                    WHERE aux_materias_unidade_tramitacao.ativo = :ativo
                                    ORDER BY aux_materias_unidade_tramitacao.id DESC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                 
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                if($result_int['orgao'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla_orgao']." - ".$result_int['nome_orgao']."</option>";
                                }
                                elseif($result_int['comissao'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla_comissao']." - ".$result_int['nome_comissao']."</option>";
                                }
                                elseif($result_int['parlamentar'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome_parlamentar']."</option>";
                                }
                                
                            }
                        echo "
                    </select>
                    <p><label>Unidade Destino*:</label> <select name='unidade_destino' id='unidade_destino' class='obg' >
                        <option value='$unidade_destino'>$destino</option>";
                            $sql = "SELECT *, aux_materias_unidade_tramitacao.id as id
                                            , aux_materias_orgaos.sigla as sigla_orgao
                                            , aux_materias_orgaos.nome as nome_orgao
                                            , cadastro_comissoes.sigla as sigla_comissao
                                            , cadastro_comissoes.nome as nome_comissao
                                            , cadastro_parlamentares.nome as nome_parlamentar
                                    FROM aux_materias_unidade_tramitacao 
                                    LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                    WHERE aux_materias_unidade_tramitacao.ativo = :ativo
                                    ORDER BY aux_materias_unidade_tramitacao.id DESC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                   
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                if($result_int['orgao'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla_orgao']." - ".$result_int['nome_orgao']."</option>";
                                }
                                elseif($result_int['comissao'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla_comissao']." - ".$result_int['nome_comissao']."</option>";
                                }
                                elseif($result_int['parlamentar'])
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome_parlamentar']."</option>";
                                }
                                
                            }
                        echo "
                    </select>
                    <input name='responsavel' type='hidden'  placeholder='Responsável'  value='".$_SESSION['usuario_id']."' class='obg' readonly  autocomplete='off' '>
                    <p><label>Responsável*:</label> <input name='responsavel_nome'  value='".$_SESSION['usuario_name']."'  readonly placeholder='Responsável'  class='obg' autocomplete='off' '>
                    
                    <p><label>Data tramitação*:</label> <input name='data_tramitacao' value='$data_tramitacao'  placeholder='Data tramitação'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Hora tramitação*:</label> <input name='hora_tramitacao' value='$hora_tramitacao'  placeholder='Hora tramitação'  class='obg' autocomplete='off' onkeypress='return mascaraHorario(this,event);' maxlength='5'>
                    <p><label>Data encaminhamento:</label> <input name='data_encaminhamento' value='$data_encaminhamento'  placeholder='Data encaminhamento'  autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Data fim prazo:</label> <input name='data_fim_prazo' value='$data_fim_prazo'  placeholder='Data fim prazo'  autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Status tramitação*:</label> <select name='status_tramitacao' id='status_tramitacao' class='obg' >
                        <option value='$status_tramitacao'>$nome_status</option>";
                            $sql = "SELECT *
                                    FROM aux_administrativo_status_tramitacao
                                    WHERE ativo = :ativo
                                    ORDER BY nome ASC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                               
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                            echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";                                
                            }
                        echo "
                    </select>
                    <p><label>Urgente?*</label> <select name='urgente' id='urgente' class='obg' >
                        <option value='$urgente'>$urgente</option>
                        <option value='Sim'>Sim</option>
                        <option value='Não'>Não</option>
                    </select>
                    <p><label>Texto da ação:</label> <textarea name='texto_acao' id='texto_acao' placeholder='Texto da ação'>$texto_acao</textarea>
                    <p><label>Anexo:</label> ";if($anexo != ''){ echo "<a href='".$anexo."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                    <p><label>Alterar Anexo:</label> <input type='file' name='anexo[anexo]'>
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarModal" >Salvar</button>                      
            </div>
        </div>
        <!--/.Content-->
    </div>
    
</div>


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
