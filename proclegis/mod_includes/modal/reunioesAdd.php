<div  class="modal fade in" id="reunioesAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_reunioes' id='form_reunioes' enctype='multipart/form-data' method='post' action='cadastro_comissoes/exib/".$id."/adicionar_reunioes#reunioes'>							
                    <p><label>Período*:</label> <select name='periodo' id='periodo' class='obg' >
                            <option value=''>Período</option>";
                                $sql = "SELECT *
                                        FROM aux_comissoes_periodos
                                        WHERE ativo = :ativo
                                        ORDER BY data_inicio";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt->bindValue(':ativo', 	1);                                
                                $stmt->execute();
                                while($result = $stmt->fetch())
                                {
                                    echo "<option value='".$result['id']."'>".reverteData($result['data_inicio'])." - ".reverteData($result['data_fim'])."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Número*:</label> <input name='numero'  placeholder='Número'  class='obg' autocomplete='off'>
                    <p><label>Nome da Reunião*:</label> <input name='nome'  placeholder='Nome da Reunião'  class='obg' autocomplete='off'>
                    <p><label>Local da Reunião:</label> <input name='local'  placeholder='Local da Reunião'  autocomplete='off'>
                    <p><label>Data reunião*:</label> <input name='data_reuniao'  placeholder='Data reunião'  class='obg' autocomplete='off'>
                    <p><label>Hora início:</label> <input name='hora_inicio'  placeholder='Hora início'  autocomplete='off' onkeypress='return mascaraHorario(this,event);' maxlength='5'>
                    <p><label>Hora término:</label> <input name='hora_termino'  placeholder='Hora término'  autocomplete='off' onkeypress='return mascaraHorario(this,event);' maxlength='5'>
                    <p><label>Obsevação:</label> <input name='observacao'  placeholder='Obsevação'  autocomplete='off'>
                    <p><label>Pauta:</label> <input type='file' name='pauta[pauta]' >
                    <p><label>Ata:</label> <input type='file' name='ata[ata]' >
                                         
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarReunioes" >Salvar</button>                      
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
