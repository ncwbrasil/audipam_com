<div  class="modal fade in" id="od_materiasEdit<?php echo $id_od_materias;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_od_materias'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_od_materias/".$id."/view/editar_od_materias'>							
                    <input type='hidden' name='id_od_materias' id='id_od_materias' value='".$id_od_materias."'  class='obg'>
                    <p><label>Ordem*:</label> <input name='ordem' value='$ordem'  placeholder='Ordem'  class='obg' autocomplete='off'>
                    <p><label>Tipo de Matéria*:</label> <select name='tipo_materia' id='tipo_materia' class='obg' >
                            <option value='$tipo_materia'>$sigla - $nome</option>";
                                $sql = "SELECT *
                                FROM aux_materias_tipos
                                WHERE ativo = :ativo
                                ORDER BY  nome ASC ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                 
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Matéria*:</label> <select name='materia' id='materia'>
                        <option value='$materia'>Nº $numero de $ano</option>                           
                    </select>
                    <p><label>Tipo de votação*:</label> <select name='tipo_votacao' id='tipo_votacao' class='obg' > 
                        <option value='$tipo_votacao'>$tipo_votacao</option>
                        <option value='Simbólica'>Simbólica</option>
                        <option value='Nominal'>Nominal</option>
                        <option value='Secreta'>Secreta</option>
                        <option value='Apenas leitura'>Apenas leitura</option>
                    </select>
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação' >$observacao</textarea>    
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
