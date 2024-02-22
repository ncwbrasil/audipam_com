<div  class="modal fade in" id="dependentesAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_dependentes' id='form_dependentes' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/exib/".$id."/adicionar_dependentes#dependentes'>							
                    <p><label>Tipo de dependente*:</label> <select name='tipo_dependente' id='tipo_dependente' class='obg' >
                            <option value=''>Tipo de dependente</option>";
                                $sql = "SELECT * 
                                        FROM aux_parlamentares_tipo_dependentes
                                        WHERE ativo = :ativo
                                        ORDER BY descricao";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                              
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                }
                            echo "
                        </select>                    
                    <p><label>Nome*:</label> <input name='nome'  placeholder='Nome' autocomplete='off'>
                    <p><label>Sexo:</label> <select name='sexo'>
                            <option value=''>Sexo</option>
                            <option value='Masculino'>Masculino</option>
                            <option value='Feminino'>Feminino</option>
                        </select>
                    <p><label>Data nascimento:</label> <input name='data_nasc'  placeholder='Data nascimento' autocomplete='off'>                    
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarDependentes" >Salvar</button>                      
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
