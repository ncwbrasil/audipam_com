<div class="modal fade in" id="darBaixaDespesa<?php echo $des_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "	
                <form name='form' id='form".$des_id."' enctype='multipart/form-data' method='post' action=''>							
                    <input type='hidden' name='des_id' id='des_id' value='".$des_id."'  class='obg'>
                    <p><label>Valor pago*:</label> <input name='des_valor_pago' id='des_valor_pago' placeholder='Valor pago' class='obg' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
                    <p><label>Data pagamento:</label> <input name='des_data_pagamento'  placeholder='Data pagamento' class='obg'  autocomplete='off' onkeypress='return mascaraData(this,event);'>
    
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="darBaixaDespesa" >Salvar</button>                      
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>