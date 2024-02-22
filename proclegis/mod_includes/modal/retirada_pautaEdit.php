<div  class="modal fade in" id="retirada_pautaEdit<?php echo $id_retirada_pauta;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_retirada_pauta'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_ab_retirada_pauta/$id/view/editar_retirada_pauta/$id_retirada_pauta'>							
                    <input type='hidden' name='id_retirada_pauta' id='id_retirada_pauta' value='".$id_retirada_pauta."'  class='obg'>
                    <p><label>Requerente*:</label> <select name='parlamentar' id='parlamentar' class='obg' >
                            <option value='$parlamentar'>$nome</option>";
                                $sql = "SELECT *, cadastro_parlamentares.id as id
                                        FROM cadastro_parlamentares
                                        LEFT JOIN cadastro_parlamentares_mandatos ON cadastro_parlamentares_mandatos.parlamentar = cadastro_parlamentares.id
                                        WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura
                                        AND cadastro_parlamentares.ativo = :ativo
                                        AND   status = :status	
                                        ORDER BY nome ASC ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                  
                                $stmt_int->bindParam(':legislatura', 	$id_legislatura);                                                                                                        
                                $stmt_int->bindValue(':status', 	1);
                                $stmt_int->bindValue(':ativo', 	1);                           
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Data*:</label> <input name='data' value='$data'  placeholder='Data'  class='obg' autocomplete='off' onkeypress='return mascaraData(this,event);'>
                    <p><label>Tipo de retirada*:</label> <select name='tipo_retirada' id='tipo_retirada' class='obg' >
                            <option value='$tipo_retirada'>$descricao</option>";
                                $sql = "SELECT *
                                        FROM aux_sessoes_plenarias_tipo_retirada_pauta
                                        WHERE ativo = :ativo
                                        ORDER BY descricao ASC";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                 
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Matéria Ordem do Dia:</label> <select name='materia_ordem_dia' id='materia_ordem_dia' >
                            <option value='$materia_ordem_dia'>$materia_od</option>";
                                $sql = "SELECT *
                                        FROM cadastro_sessoes_plenarias_od_materias
                                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_od_materias.tipo_materia
                                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_od_materias.materia
                                        WHERE sessao_plenaria = :sessao_plenaria AND cadastro_sessoes_plenarias_od_materias.ativo = :ativo
                                        ORDER BY ordem ASC";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindParam(':sessao_plenaria', 	$id);
                                $stmt_int->bindValue(':ativo', 	1);                                 
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']." Nº  ".$result_int['numero']." de ".$result_int['ano']."</option>";
                                }
                            echo "
                        </select>
                    <p> OU
                    <p><label>Matéria do Expediente:</label> <select name='materia_expediente' id='materia_expediente' >
                            <option value='$materia_expediente'>$materia_exp</option>";
                                $sql = "SELECT *
                                        FROM cadastro_sessoes_plenarias_exp_materias
                                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_exp_materias.tipo_materia
                                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_exp_materias.materia
                                        WHERE sessao_plenaria = :sessao_plenaria AND cadastro_sessoes_plenarias_exp_materias.ativo = :ativo 
                                        ORDER BY ordem ASC";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindParam(':sessao_plenaria', 	$id);
                                $stmt_int->bindValue(':ativo', 	1);                              
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['nome']." Nº  ".$result_int['numero']." de ".$result_int['ano']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação'  autocomplete='off'>$observacao</textarea>
                        
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
