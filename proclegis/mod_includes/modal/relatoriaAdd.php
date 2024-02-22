<div  class="modal fade in" id="relatoriaAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_relatoria' id='form_relatoria' enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/adicionar_relatoria#relatoria'>							
                    <p><label>Comissão*:</label> <select name='comissao' id='comissao' class='obg' readonly='readonly' >
                            <option value='$ultima_tramitacao'>$destino</option>
                        </select>
                    <p><label>Período da Composição*:</label> <select name='periodo' id='periodo' class='obg relatoria_periodo' >
                        <option value=''>Período da Composição</option>";
                            $sql = "SELECT *, aux_comissoes_periodos.id as id
                                    FROM cadastro_comissoes_composicao
                                    LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo
                                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = cadastro_comissoes_composicao.comissao
                                    WHERE cadastro_comissoes_composicao.comissao = :comissao AND cadastro_comissoes_composicao.ativo = :ativo
                                    GROUP BY aux_comissoes_periodos.id
                                    ORDER BY  aux_comissoes_periodos.data_inicio DESC ";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt->bindValue(':ativo', 1);                               
                            $stmt->bindParam(':comissao', 	$ultima_tramitacao);                                
                            $stmt->execute();
                            while($result = $stmt->fetch())
                            {
                                echo "<option value='".$result['id']."'>".$result['sigla'].": ".reverteData($result['data_inicio'])." - ".reverteData($result['data_fim'])."</option>";
                            }
                        echo "
                    </select>
                    <p><label>Parlamentar*:</label> <select name='parlamentar' id='parlamentar' class='obg' > 
                        <option value=''>Parlamentar</option>
                    </select>
                    <p><label>Data designação*:</label> <input name='data_designacao'  placeholder='Data designação'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Data destituição:</label> <input name='data_destituicao'  placeholder='Data destituição'  autocomplete='off'  onkeypress='return mascaraData(this,event);'>                                         
                    <p><label>Motivo fim relatoria:</label> <select name='motivo_fim_relatoria' id='motivo_fim_relatoria' >
                        <option value=''>Motivo fim relatoria</option>";
                            $sql = "SELECT * FROM aux_materias_tipo_fim_relatoria
                                    WHERE ativo = :ativo
                                    ORDER BY  descricao ASC ";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt->bindValue(':ativo', 1);                                                                                        
                            $stmt->execute();
                            while($result = $stmt->fetch())
                            {
                                echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
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
