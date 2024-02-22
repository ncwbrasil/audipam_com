<div  class="modal fade in" id="assuntosEdit<?php echo $id_assuntos;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_assuntos'  enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/editar_assuntos#assuntos'>							
                    <input type='hidden' name='id_assuntos' id='id_assuntos' value='".$id_assuntos."'  class='obg'>
                    <p><label>Assunto*:</label> <select name='assunto' id='assunto' class='obg' >
                            <option value='$assunto'>$descricao</option>";
                                $sql = "SELECT *
                                FROM aux_materias_assuntos
                                WHERE ativo = :ativo
                                ORDER BY  descricao ASC ";
                                
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql); 
                                $stmt_int->bindValue(':ativo',1);       
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
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
