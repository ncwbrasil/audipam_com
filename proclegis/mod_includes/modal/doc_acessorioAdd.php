<div  class="modal fade in" id="doc_acessorioAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_doc_acessorio' id='form_doc_acessorio' enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/adicionar_doc_acessorio#doc_acessorio'>							
                    <p><label>Tipo de documento*:</label> <select name='tipo_documento' id='tipo_documento' class='obg' >
                            <option value=''>Tipo de documento</option>";
                                $sql = "SELECT *
                                        FROM aux_materias_documentos
                                        WHERE ativo = :ativo
                                        ORDER BY descricao ASC";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt->bindValue(':ativo', 	1);                                  
                                $stmt->execute();
                                while($result = $stmt->fetch())
                                {
                                    echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Nome*:</label> <input name='nome' id='nome' placeholder='Nome' class='obg' > 
                    <p><label>Data*:</label> <input name='data'  placeholder='Data'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Autor:</label> <input name='autor'  placeholder='Autor'  autocomplete='off'> 
                    <p><label>Anexo:</label> <input type='file' name='anexo[anexo]'  placeholder='Anexo' >   
                    <p><label>Ementa:</label> <textarea name='ementa'  placeholder='Ementa' ></textarea>                                                                              
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
