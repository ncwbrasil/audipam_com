<div  class="modal fade in" id="anexadaAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_anexada' id='form_anexada' enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/adicionar_anexada#anexada'>							
                    <p><label>Tipo de Matéria*:</label> <select name='tipo_materia' id='tipo_materia' class='obg' >
                            <option value=''>Tipo de Matéria</option>";
                                $sql = "SELECT *
                                        FROM aux_materias_tipos
                                        WHERE ativo = :ativo
                                        ORDER BY  nome ASC";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt->bindValue(':ativo',1);                                
                                $stmt->execute();
                                while($result = $stmt->fetch())
                                {
                                    echo "<option value='".$result['id']."'>".$result['sigla']." - ".$result['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Matéria anexada*:</label> <select name='materia_anexada' id='materia_anexada' class='obg' > 
                        <option value=''>Matéria anexada</option>
                    </select>
                    <p><label>Data anexação*:</label> <input name='data_anexacao'  placeholder='Data anexação'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Data desanexação:</label> <input name='data_desanexacao'  placeholder='Data desanexação'  autocomplete='off'  onkeypress='return mascaraData(this,event);'>                                         
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
