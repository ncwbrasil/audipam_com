<div  class="modal fade in" id="exp_materiasVotacao<?php echo $id_exp_materias;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:left; line-height:30px;">                
                <?php
                echo "	
                <form name='form_exp_materias'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_exp_materias/".$id."/view/registrar_votacao'>							
                    <input type='hidden' name='id_exp_materias' id='id_exp_materias' value='".$id_exp_materias."'  class='obg'>
                    <p><label>Matéria:</label> $nome Nº $numero de $ano                                               
                    <p><label>Ementa:</label> $ementa                                              
                    <p><label>Tipo de votação*:</label> $tipo_votacao
                    <p><label>Sim*:</label> <input name='sim' value='$sim' placeholder='Sim' class='obg'>
                    <p><label>Não*:</label> <input name='nao' value='$nao' placeholder='Não' class='obg'>
                    <p><label>Abstenção*:</label> <input name='abstencao' value='$abstencao' placeholder='Abstenção' class='obg'>
                    <p><label>Inclui voto do presidente?</label> <select name='inclui_presidente'  class='obg'>
                            <option value='$inclui_presidente'>$inclui_presidente</option>
                            <option value='Sim'>Sim</option>
                            <option value='Não'>Não</option>
                        </select>
                    <p><label>Resultado da votação*:</label> <select name='resultado' class='obg' >
                        <option value='$resultado'>$descricao</option>";
                            $sql = "SELECT *
                                    FROM aux_sessoes_plenarias_tipo_resultado 
                                    WHERE ativo = :ativo
                                    ORDER BY descricao ASC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                  
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                               echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";                                
                            }
                        echo "
                    </select>
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação' >$observacao_votacao</textarea>  
                    <p><label>Total votos:</label> <input name='total_votos' value='$total_votos' placeholder='0' readonly style='width:18px; border:none; font-size:15px; padding:0; margin:0;'> de $total_presenca 
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="float:left;" onclick="cancelarVotacaoExp(this, <?php echo $id;?>, <?php echo $id_exp_materias;?>);">Cancelar Votação</button>  
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
