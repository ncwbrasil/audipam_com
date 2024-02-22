<div  class="modal fade in" id="doc_acessorioEdit<?php echo $id_doc_acessorio;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_doc_acessorio'  enctype='multipart/form-data' method='post' action='docadm_documentos/exib/".$id."/editar_doc_acessorio#doc_acessorio'>							
                    <input type='hidden' name='id_doc_acessorio' id='id_doc_acessorio' value='".$id_doc_acessorio."'  class='obg'>
                    <p><label>Tipo de Documento*:</label> <select name='tipo_documento' id='tipo_documento' class='obg' >
                            <option value='$tipo_documento'>$nome_tipo</option>";
                                $sql = "SELECT *
                                FROM aux_administrativo_tipo_documento
                                WHERE ativo = :ativo
                                ORDER BY  nome ASC ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                  
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Nome Documento*:</label> <input name='nome' placeholder='Nome Documento' id='nome' value='$nome' class='obg'>                        
                    <p><label>Data*:</label> <input name='data' value='$data'  placeholder='Data'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Autor:</label> <input name='autor' value='$autor'  placeholder='Autor'  autocomplete='off'>
                    <p><label>Anexo:</label> ";if($anexo != ''){ echo "<a href='".$anexo."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                    <p><label>Alterar Anexo:</label> <input type='file' name='anexo[anexo]'>
                    <p><label>Ementa:</label> <textarea name='ementa'  placeholder='Ementa'  autocomplete='off'>$ementa</textarea>
                        
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
