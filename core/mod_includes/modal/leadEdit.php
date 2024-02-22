<div  class="modal fade in" id="leadEdit<?php echo $lea_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "	
                <form name='form_lead_editar' id='form_lead_editar' enctype='multipart/form-data' method='post' action='cadastro_leads/view/editar'>							
                    <input type='hidden' name='lea_id' id='lea_id' value='".$lea_id."'  class='obg'>
                    <p><label>Origem*:</label> <select name='lea_origem' id='lea_origem' class='obg'>
                            <option value='".$result['lea_origem']."'>".$result['lea_origem']."</option>
                            <option value='Prospecção Ativa'>Prospecção Ativa</option>
                            <option value='Cliente Ativo'>Cliente Ativo</option>
                            <option value='Indicação'>Indicação</option>
                            <option value='Facebook'>Facebook</option>
                            <option value='Instagram'>Instagram</option>
                            <option value='LinkedIn'>LinkedIn</option>
                            <option value='Youtube'>Youtube</option>
                            <option value='Google'>Google</option>
                            <option value='Site'>Site</option>
                            <option value='Material Impresso'>Material Impresso</option>
                            <option value='E-mail Marketing'>E-mail Marketing</option>
                            <option value='Outros'>Outros</option>                          
                        </select>
                    <p><label>Nome Empresa*:</label> <input name='lea_nome' value='".$result['lea_nome']."'  placeholder='Nome Empresa' class='obg'  autocomplete='off'>
                    <p><label>Segmento:</label> <input name='lea_segmento' value='".$result['lea_segmento']."'  placeholder='Segmento' autocomplete='off'>
                    <p><label>Nome Contato*:</label> <input name='lea_nome_contato' value='".$result['lea_nome_contato']."'  placeholder='Nome Contato' class='obg'  autocomplete='off'>
                    <p><label>Cargo:</label> <input name='lea_cargo' value='".$result['lea_cargo']."'  placeholder='Cargo' autocomplete='off'>
                    <p><label>Telefone:</label> <input name='lea_telefone' value='".$result['lea_telefone']."'  placeholder='Telefone' autocomplete='off' onkeypress='return mascaraTELEFONE(this);'>
                    <p><label>Email:</label> <input name='lea_email' value='".$result['lea_email']."'  placeholder='Email' autocomplete='off'>
                    <p><label>Observação:</label> <textarea name='lea_observacao' id='lea_observacao' placeholder='Observação'>".$result['lea_observacao']."</textarea>                                             
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="editarLead" >Salvar</button>                      
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>