<div  class="modal fade in" id="mandatosAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_mandatos' id='form_mandatos' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/exib/".$id."/adicionar_mandatos#mandatos'>							
                    <p><label>Legislatura*:</label> <select name='legislatura' id='legislatura' class='obg' >
                            <option value=''>Legislatura</option>";
                                $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                        FROM aux_parlamentares_legislaturas 
                                        WHERE aux_parlamentares_legislaturas.ativo = :ativo
                                        ORDER BY numero";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt->bindValue(':ativo', 	1);                                 
                                $stmt->execute();
                                while($result = $stmt->fetch())
                                {
                                    echo "<option value='".$result['id']."'>".$result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")</option>";
                                }
                            echo "
                        </select>
                    <p><label>Coligação:</label> <select name='coligacao' id='coligacao'>
                        <option value=''>Coligação</option>";
                            $sql = "SELECT * 
                                    FROM aux_parlamentares_coligacoes
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
                    <p><label>Votos recebidos:</label> <input name='votos'  placeholder='Votos recebidos' autocomplete='off'>
                    <p><label>Início mandato *:</label> <input name='data_inicio_mandato'  placeholder='Início mandato'  class='obg' autocomplete='off'>
                    <p><label>Fim mandato*:</label> <input name='data_fim_mandato'  placeholder='Fim mandato' class='obg'  autocomplete='off'>
                    <p><label>Tipo afastamento:</label> <select name='tipo_afastamento' id='tipo_afastamento'>
                        <option value=''>Tipo afastamento</option>";
                            $sql = "SELECT * 
                                    FROM aux_parlamentares_tipo_afastamento
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
                    <p><label>Observação:</label> <textarea name='observacao' id='observacao' placeholder='Observação'></textarea>                                             
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>  
                <button type="button" class="btn btn-primary" id="cadastrarMandatos" >Salvar</button>                      
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
