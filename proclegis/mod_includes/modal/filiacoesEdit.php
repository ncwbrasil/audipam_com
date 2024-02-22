<div  class="modal fade in" id="filiacoesEdit<?php echo $id_filiacoes;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_filiacoes'  enctype='multipart/form-data' method='post' action='cadastro_parlamentares/exib/".$id."/editar_filiacoes#filiacoes'>							
                    <input type='hidden' name='id_filiacoes' id='id_filiacoes' value='".$id_filiacoes."'  class='obg'>
                    <p><label>Partido*:</label> <select name='partido' id='partido' class='obg' >
                            <option value='$partido'>$partido_sigla - $partido_nome</option>";
                                $sql = "SELECT * FROM aux_parlamentares_partidos
                                        WHERE ativo = :ativo
                                        ORDER BY sigla";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                  
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Data filiação *:</label> <input name='data_filiacao' value='$data_filiacao'  placeholder='Data filiação'  class='obg' autocomplete='off'>
                    <p><label>Data desfiliação:</label> <input name='data_desfiliacao'  value='$data_desfiliacao'   placeholder='Data desfiliação'  autocomplete='off'>
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarFiliacoes" >Salvar</button>                      
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
