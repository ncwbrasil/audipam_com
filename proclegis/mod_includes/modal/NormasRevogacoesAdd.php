<div  class="modal fade in" id="revogacoesAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">
                <?php
                echo "
                <form name='form_revogacoes' id='form_revogacoes' enctype='multipart/form-data' method='post' action='cadastro_normas_juridicas/exib/".$id."/adicionar_revogacoes#revogacoes'>							
                    <p><label>Tipo de Norma*:</label> <select name='tipo_norma' id='tipo_norma' class='obg' >
                            <option value=''>Tipo de Norma</option>";
                                $sql = "SELECT *
                                        FROM aux_normas_juridicas_tipos
                                        WHERE ativo = :ativo
                                        ORDER BY  nome ASC";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                               
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Norma Jurídica*:</label> <select name='norma_revogada' id='norma_revogada' class='obg' > 
                        <option value=''>Norma Jurídica</option>
                    </select> 
                    <p><label>Tipo de Vínculo*:</label> <select name='tipo_vinculo' id='tipo_vinculo' class='obg' >
                        <option value=''>Tipo de Vínculo</option>";
                            $sql = "SELECT *
                                    FROM aux_normas_juridicas_tipo_vinculo
                                    WHERE ativo = :ativo
                                    ORDER BY descricao_ativa ASC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 	1);                                  
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                                echo "<option value='".$result_int['id']."'>".$result_int['descricao_ativa']."</option>";
                            }
                        echo "
                    </select>   
                    <p><label>Ementa:</label> <textarea name='ementa_norma' id='ementa_norma' readonly='readonly' ></textarea>          
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
