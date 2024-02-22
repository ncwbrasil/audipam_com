<div  class="modal fade in" id="ausenciasEdit<?php echo $id_ausencias;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_ausencias'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_ab_ausencias/$id/view/editar_ausencias/$id_ausencias'>							
                    <input type='hidden' name='id_ausencias' id='id_ausencias' value='".$id_ausencias."'  class='obg'>
                    <p><label>Parlamentar*:</label> <select name='parlamentar' id='parlamentar' class='obg' >
                            <option value='$parlamentar'>$nome</option>";
                                $sql = "SELECT *, cadastro_parlamentares.id as id
                                        FROM cadastro_parlamentares
                                        LEFT JOIN cadastro_parlamentares_mandatos ON cadastro_parlamentares_mandatos.parlamentar = cadastro_parlamentares.id
                                        WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura AND cadastro_parlamentares.ativo = :ativo
                                        AND   status = :status	
                                        ORDER BY nome ASC ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                  
                                $stmt_int->bindParam(':legislatura', 	$id_legislatura);                                                                                                        
                                $stmt_int->bindValue(':status', 	1); 
                                $stmt_int->bindValue(':ativo', 	1);                             
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Data*:</label> <input name='data' value='$data'  placeholder='Data'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Hora*:</label> <input name='horario' value='$horario'  placeholder='Hora'  class='obg' autocomplete='off' maxlength='5' onkeypress='return mascaraHorario(this,event);'>
                     <p><label>Tipo de justificativa*:</label> <select name='tipo_justificativa' id='tipo_justificativa' class='obg' >
                            <option value='$tipo_justificativa'>$descricao</option>";
                                $sql = "SELECT *
                                        FROM aux_sessoes_plenarias_tipo_justificativa
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
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação'  autocomplete='off'>$observacao</textarea>
                        
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
