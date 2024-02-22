<div  class="modal fade in" id="composicaoEdit<?php echo $id_composicao;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_composicao'  enctype='multipart/form-data' method='post' action='cadastro_comissoes/exib/".$id."/editar_composicao#composicao'>							
                    <input type='hidden' name='id_composicao' id='id_composicao' value='".$id_composicao."'  class='obg'>
                    <p><label>Período*:</label> <select name='periodo' id='periodo' class='obg' >
                            <option value='$periodo_id'>$data_inicio - $data_fim</option>";
                                $sql = "SELECT *
                                        FROM aux_comissoes_periodos
                                        WHERE ativo = :ativo
                                        ORDER BY data_inicio";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo',1);                                 
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".reverteData($result_int['data_inicio'])." - ".reverteData($result_int['data_fim'])."</option>";
                                }
                            echo "
                        </select>
                        <p><label>Parlamentar*:</label> <select name='parlamentar' id='parlamentar'>
                        <option value='$parlamentar_id'>$parlamentar</option>";
                            $sql = "SELECT * 
                                    FROM cadastro_parlamentares
                                    WHERE ativo = :ativo
                                    ORDER BY nome";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo',1);                                  
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Cargo*:</label> <select name='cargo' id='cargo'>
                        <option value='$cargo_id'>$cargo</option>";
                            $sql = "SELECT * 
                                    FROM aux_comissoes_cargos
                                    WHERE ativo = :ativo
                                    ORDER BY descricao";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo',1);                                  
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Titular:</label> <select name='titular' id='titular'>
                        <option value='$titular'>$titular</option>
                        <option value='Sim'>Sim</option>
                        <option value='Não'>Não</option> 
                    </select>
                    <p><label>Data designacao*:</label> <input name='data_designacao' value='$data_designacao'  placeholder='Data designacao'  class='obg' autocomplete='off'>
                    <p><label>Data desligamento:</label> <input name='data_desligamento' value='$data_desligamento'  placeholder='Data desligamento'  autocomplete='off'>
                    <p><label>Motivo desligamento:</label> <input name='motivo_desligamento' value='$motivo_desligamento'  placeholder='Motivo desligamento'  autocomplete='off'>
                        
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarComposicao" >Salvar</button>                      
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
