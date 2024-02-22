<div  class="modal fade in" id="mandatosEdit<?php echo $id_mandato;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_mandatos'  enctype='multipart/form-data' method='post' action='cadastro_parlamentares/exib/".$id."/editar_mandatos#mandatos'>							
                    <input type='hidden' name='id_mandato' id='id_mandato' value='".$id_mandato."'  class='obg'>
                    <p><label>Legislatura*:</label> <select name='legislatura' id='legislatura' class='obg' >
                            <option value='$legislatura_id'>$legislatura</option>";
                                $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                        FROM aux_parlamentares_legislaturas
                                        WHERE aux_parlamentares_legislaturas.ativo = :ativo 
                                        ORDER BY numero";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                     
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['data_inicio']." - ".$result_int['data_fim'].")</option>";
                                }
                            echo "
                        </select>
                    <p><label>Coligação:</label> <select name='coligacao' id='coligacao'>
                        <option value='$coligacao_id'>$coligacao</option>";
                            $sql = "SELECT * 
                                    FROM aux_parlamentares_coligacoes
                                    WHERE ativo = :ativo
                                    ORDER BY nome";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                    
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Votos recebidos:</label> <input name='votos' value='$votos' placeholder='Votos recebidos' autocomplete='off'>
                    <p><label>Início mandato *:</label> <input name='data_inicio_mandato' value='$data_inicio_mandato'  placeholder='Início mandato'  class='obg' autocomplete='off'>
                    <p><label>Fim mandato*:</label> <input name='data_fim_mandato'  value='$data_fim_mandato'   placeholder='Fim mandato' class='obg'  autocomplete='off'>
                    <p><label>Tipo afastamento:</label> <select name='tipo_afastamento' id='tipo_afastamento'>
                        <option value='$tipo_afastamento'>$tipo_afastamento_n</option>";
                            $sql = "SELECT * 
                                    FROM aux_parlamentares_tipo_afastamento
                                    WHERE ativo = :ativo
                                    ORDER BY descricao";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Observação:</label> <textarea name='observacao' id='observacao' placeholder='Observação'>".$observacao."</textarea>                    
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarMandatos" >Salvar</button>                      
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
