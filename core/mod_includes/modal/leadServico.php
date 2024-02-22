<div  class="modal fade in" id="leadServico<?php echo $lea_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "	
                <form name='form_lead_servico' id='form_lead_servico' enctype='multipart/form-data' method='post' action=''>							                    
                    <div id='p_scents_servicos'> 
                        ";
                        $sql = "SELECT * FROM cadastro_leads_servicos 
                                LEFT JOIN aux_tipo_servico ON aux_tipo_servico.tps_id = cadastro_leads_servicos.lse_servico
                                LEFT JOIN cadastro_leads ON cadastro_leads.lea_id = cadastro_leads_servicos.lse_lead
                                WHERE lse_lead = :lse_lead";
                        $stmt_servico = $PDO->prepare($sql);
                        $stmt_servico->bindParam(':lse_lead', $lea_id);
                        $stmt_servico->execute();
                        $rows_servico = $stmt_servico->rowCount();
                        if($rows_servico > 0)
                        {
                            $x=0;
                            while($result_servico = $stmt_servico->fetch())
                            {
                                $x++;
                                echo "
                                
                                <div class='bloco_servicos'>
                                    <input type='hidden' name='servicos[$x][lea_id]' value='".$lea_id."'  class='obg'>
                                    <input type='hidden' name='servicos[$x][lse_id]' id='lse_id' value='".$result_servico['lse_id']."'>
                                    "; if($x > 1){ echo "<br><br><p>";}else{ echo "<br>";} echo "
                                    <p><label>Serviço/Produto*:</label> <select name='servicos[$x][lse_servico]' class='obg'>
                                            <option value='".$result_servico['lse_servico']."'>".$result_servico['tps_nome']."</option>
                                            "; 
                                            $sql_ser = " SELECT * FROM aux_tipo_servico ORDER BY tps_nome ";
                                            $stmt_ser = $PDO->prepare($sql_ser);
                                            $stmt_ser->execute();
                                            while($result_ser = $stmt_ser->fetch())
                                            {
                                                echo "<option value='".$result_ser['tps_id']."'>".$result_ser['tps_nome']."</option>";
                                            }
                                            echo "                               
                                        </select>
                                    <p><label>Tipo*:</label> <select name='servicos[$x][lse_tipo]' id='lse_tipo' class='obg'>
                                        <option value='".$result_servico['lse_tipo']."'>".$result_servico['lse_tipo']."</option>
                                        <option value='Avulso'>Avulso</option>
                                        <option value='Recorrente'>Recorrente</option>                     
                                    </select>
                                    <p><label>Valor*:</label> <input name='servicos[$x][lse_valor]' value='".number_format($result_servico['lse_valor'],2,",",".")."'  placeholder='Valor (em R$)' class='obg'  autocomplete='off' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
                                    <p><label>Observação:</label> <textarea name='servicos[$x][lse_observacao]' placeholder='Observação'>".$result_servico['lea_observacao']."</textarea>
                                    <br>
                                    <p><i class='fas fa-plus botao_dinamico_add' id='add_servicos' title='Adicionar'></i> <i class='far fa-trash-alt botao_dinamico_rmv' id='rem_servicos' title='Remover' ></i>
                                    <hr style='width:100%; border:none; height:1px; background:#DDD;'>
                                </div>                                                                
                                ";
                            }
                        }
                        else
                        {
                            echo "
                            <div class='bloco_servicos'>
                                <input type='hidden' name='servicos[1][lea_id]' value='".$lea_id."'  class='obg'>
                                <p><label>Serviço/Produto*:</label> <select name='servicos[1][lse_servico]' class='obg'>
                                        <option value=''>Serviço</option>
                                        "; 
                                        $sql_ser = " SELECT * FROM aux_tipo_servico ORDER BY tps_nome ";
                                        $stmt_ser = $PDO->prepare($sql_ser);
                                        $stmt_ser->execute();
                                        while($result_ser = $stmt_ser->fetch())
                                        {
                                            echo "<option value='".$result_ser['tps_id']."'>".$result_ser['tps_nome']."</option>";
                                        }
                                        echo "                               
                                    </select>
                                <p><label>Tipo*:</label> <select name='servicos[1][lse_tipo]' id='lse_tipo' class='obg'>
                                    <option value=''>Tipo</option>
                                    <option value='Avulso'>Avulso</option>
                                    <option value='Recorrente'>Recorrente</option>                     
                                </select>
                                <p><label>Valor*:</label> <input name='servicos[1][lse_valor]' value='".$result['lse_valor']."'  placeholder='Valor (em R$)' class='obg'  autocomplete='off' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
                                <p><label>Observação:</label> <textarea name='servicos[1][lse_observacao]' placeholder='Observação'>".$result['lea_observacao']."</textarea>                                             
                                <p><i class='fas fa-plus botao_dinamico_add' id='add_servicos' title='Adicionar'></i></p>
                                <hr style='width:100%; border:none; height:1px; background:#DDD;'>
                            </div>
                            ";
                        }
                        echo "
                    </div>
                    <br><br>
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
<script>