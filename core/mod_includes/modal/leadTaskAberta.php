<div  class="modal fade in" id="leadTaskAberta<?php echo $lea_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "	
                <form name='formTaskAberta' id='formTaskAberta".$lea_id."' enctype='multipart/form-data' method='post' action='' style='width:100%; display:table;'>
                <input type='hidden' name='lea_id' id='lea_id' value='".$result["lea_id"]."'  class='obg'>	
                <input type='hidden' name='tas_id' id='tas_id' value='".$result_task["tas_id"]."'  class='obg'>	
                <div style='width:100%; margin:0 auto; padding:20px; display:table;'>
                    <p class='titulo'>Dados do Lead</p>						
                    <p><label>Cliente:</label> <div style='float:left; line-height:30px; width:80%; text-align:left;'>".$result["lea_nome"]." &nbsp; </div>
                    <p><label>Contato:</label> <div style='float:left; line-height:30px; width:80%; text-align:left;'>".$result["lea_nome_contato"]." | ".$result["lea_cargo"]."&nbsp; </div>
                    <p><label>Telefone:</label> <div style='float:left; line-height:30px; width:80%; text-align:left;'>".$result["lea_telefone"]." &nbsp; </div>
                    <p><label>Email:</label> <div style='float:left; line-height:30px; width:80%; text-align:left;'>".$result["lea_email"]." &nbsp; </div>
                    
                    <p class='titulo'>Tarefa</p>
                    <div style='width:100%; margin:0 auto; padding:20px; margin-top:-15px; display:table; background:#EEE;'>
                        <p><label>Tipo*:</label> <div style='float:left; line-height:30px; width:80%; text-align:left;'>".$result_task["tas_tipo"]." &nbsp; </div>
                        <p><label>Data*:</label> <div style='float:left; line-height:30px; width:80%;text-align:left;'>".reverteData($result_task['tas_data'])." &nbsp; </div>
                        <p><label>Hora:</label>  <div style='float:left; line-height:30px; width:80%;text-align:left;'>".$result_task['tas_hora']." &nbsp; </div> 
                        <p><label>Responsável:</label>  <div style='float:left; line-height:30px; width:80%;text-align:left;'>".$result_task['usu_nome']." &nbsp; </div>  
                        <p><label>Observação:</label>   <div style='float:left; line-height:30px; width:80%;text-align:left;'>".$result_task['tas_observacao']." &nbsp; </div>                                          
                    </div>
                </div>
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="concluirTask" >Concluir Tarefa</button>                      
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>