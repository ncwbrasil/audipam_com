<div  class="modal fade in" id="exp_materiasLeitura<?php echo $id_exp_materias;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:left; line-height:30px;">                
                <?php
                echo "	
                <form name='form_exp_materias'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_exp_materias/".$id."/view/registrar_leitura'>							
                    <input type='hidden' name='id_exp_materias' id='id_exp_materias' value='".$id_exp_materias."'  class='obg'>
                    <p><label>Matéria:</label> $nome Nº $numero de $ano                                               
                    <p><label>Ementa:</label> $ementa                                              
                    <p><label>Tipo de votação*:</label> $tipo_votacao
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação' >$observacao_leitura</textarea>    
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="float:left;" onclick="cancelarLeituraExp(this, <?php echo $id;?>, <?php echo $id_exp_materias;?>);">Cancelar Leitura</button>  
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
