<div  class="modal fade in" id="autoriaAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_autoria' id='form_autoria' enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/adicionar_autoria#autoria'>							
                    <p><label>Tipo de Autor*:</label> <select name='tipo_autor' id='tipo_autor' class='obg tp_autor' >
                            <option value=''>Tipo de Autor</option>";
                                $sql = "SELECT *
                                        FROM aux_autoria_tipo_autor
                                        WHERE ativo = :ativo
                                        ORDER BY  descricao ASC";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt->bindValue(':ativo', 	1);                                 
                                $stmt->execute();
                                while($result = $stmt->fetch())
                                {
                                    echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Autor*:</label> <select name='autor' id='autor' class='obg' > 
                        <option value=''>Autor</option>
                    </select>
                    <p><label>Primeiro autor?</label> <select name='primeiro_autor' id='primeiro_autor' class='obg' > 
                        <option value=''>Primeiro autor?</option>
                        <option value='Sim'>Sim</option>
                        <option value='Não'>Não</option>
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
