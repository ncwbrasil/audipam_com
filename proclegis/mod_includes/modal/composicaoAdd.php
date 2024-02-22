<div  class="modal fade in" id="composicaoAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_composicao' id='form_composicao' enctype='multipart/form-data' method='post' action='cadastro_comissoes/exib/".$id."/adicionar_composicao#composicao'>							
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
                        <p><label>Parlamentar*:</label> <select name='parlamentar' id='parlamentar' class='obg' > 
                        <option value=''>Parlamentar</option>";
                            $sql = "SELECT * 
                                    FROM cadastro_parlamentares 
                                    WHERE ativo = :ativo
                                    ORDER BY nome";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt->bindValue(':ativo', 	1);                                 
                            $stmt->execute();
                            while($result = $stmt->fetch())
                            {
                                echo "<option value='".$result['id']."'>".$result['nome']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Cargo*:</label> <select name='cargo' id='cargo' class='obg' >
                        <option value=''>Cargo</option>";
                            $sql = "SELECT * 
                                    FROM aux_comissoes_cargos 
                                    WHERE ativo = :ativo
                                    ORDER BY descricao";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt->bindValue(':ativo', 	1);                                 
                            $stmt->execute();
                            while($result = $stmt->fetch())
                            {
                                echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Titular:</label> <select name='titular' id='titular'>
                        <option value=''>Titular</option>
                        <option value='Sim'>Sim</option>
                        <option value='Não'>Não</option> 
                    </select>
                    <p><label>Data designacao*:</label> <input name='data_designacao'  placeholder='Data designacao'  class='obg' autocomplete='off'>
                    <p><label>Data desligamento:</label> <input name='data_desligamento'  placeholder='Data desligamento'  autocomplete='off'>
                    <p><label>Motivo desligamento:</label> <input name='motivo_desligamento'  placeholder='Motivo desligamento'  autocomplete='off'>
                                         
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarComposicao" >Salvar</button>                      
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
