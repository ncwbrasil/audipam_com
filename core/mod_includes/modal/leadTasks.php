<div  class="modal fade in" id="tasks<?php echo $lea_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "	
                <form name='form' id='form".$lea_id."' enctype='multipart/form-data' method='post' action=''>							
                    <input type='hidden' name='lea_id' id='lea_id' value='".$lea_id."'  class='obg'>
                    <p><label>Tipo*:</label> <select name='tas_tipo' id='tas_tipo' class='obg'>
                            <option value=''>Tipo</option>
                            <option value='Ligação'>Ligação</option>
                            <option value='Email'>Email</option>
                            <option value='Reunião'>Reunião</option>                            
                        </select>
                    <p><label>Data*:</label> <input name='tas_data'  placeholder='Data' class='obg'  autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Hora:</label> <input name='tas_hora'  id='tas_hora'  placeholder='hora' autocomplete='off' onkeypress='return mascaraHorario(this,event);' maxlength='5'>
                    <p><label>Observação:</label> <textarea name='tas_observacao' id='tas_observacao' placeholder='Observação'></textarea>
                    <p><label>Responsável*:</label> <select name='tas_responsavel' id='tas_responsavel' class='obg'>
                        <option value=''>Responsável</option>
                        "; 
                        $sql_resp = " SELECT * FROM cadastro_usuarios ORDER BY usu_nome ";
                        $stmt_resp = $PDO->prepare($sql_resp);
                        $stmt_resp->execute();
                        while($result_resp = $stmt_resp->fetch())
                        {
                            echo "<option value='".$result_resp['usu_id']."'>".$result_resp['usu_nome']."</option>";
                        }
                        echo "                           
                    </select>                          
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarTask" >Salvar</button>                      
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>