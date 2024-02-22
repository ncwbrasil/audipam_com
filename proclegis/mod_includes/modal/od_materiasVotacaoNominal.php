<div  class="modal fade in" id="od_materiasVotacaoNominal<?php echo $id_od_materias;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:left; line-height:30px;">                
                <?php
                echo "	
                <form name='form_od_materias'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_od_materias/".$id."/view/registrar_votacao_nominal'>							
                    <input type='hidden' name='id_od_materias' id='id_od_materias' value='".$id_od_materias."'  class='obg'>
                    <p><label>Matéria:</label> $nome Nº $numero de $ano                                               
                    <p><label>Ementa:</label> $ementa                                              
                    <p><label>Tipo de votação*:</label> $tipo_votacao    
                    <hr style='width:95%'> 
                    ";
                    $sql = "SELECT *
                            FROM cadastro_sessoes_plenarias_presenca 
                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_presenca.parlamentar
                            WHERE cadastro_sessoes_plenarias_presenca.sessao_plenaria = :sessao_plenaria AND cadastro_sessoes_plenarias_presenca.ativo = :ativo
                            ORDER BY nome ASC";
                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                    $stmt_int->bindParam(':sessao_plenaria', 	$id);      
                    $stmt_int->bindValue(':ativo', 1);                         
                    $stmt_int->execute();
                    while($result_int = $stmt_int->fetch())
                    {

                        // PEGAR VOTOS DE CADA PARLAMENTAR                        
                        $sql = "SELECT *
                                FROM cadastro_sessoes_plenarias_od_materias_votacao_nominal 
                                LEFT JOIN cadastro_sessoes_plenarias_od_materias_votacao ON cadastro_sessoes_plenarias_od_materias_votacao.id = cadastro_sessoes_plenarias_od_materias_votacao_nominal.votacao
                                WHERE cadastro_sessoes_plenarias_od_materias_votacao.materia_od = :materia_od AND 
                                      cadastro_sessoes_plenarias_od_materias_votacao_nominal.parlamentar = :parlamentar
                                      AND cadastro_sessoes_plenarias_od_materias_votacao_nominal.ativo = :ativo
                                ";
                        $stmt_par = $PDO_PROCLEGIS->prepare($sql);                           
                        $stmt_par->bindParam(':materia_od', 	$id_od_materias);                                
                        $stmt_par->bindParam(':parlamentar', 	$result_int['parlamentar']);    
                        $stmt_par->bindValue(':ativo', 1);                              
                        $stmt_par->execute();
                        $rows_par =  $stmt_par->rowCount();
                        if($rows_par > 0)
                        {
                            while($result_par = $stmt_par->fetch())
                            {
                                $voto_parlamentar = $voto_parlamentar_n = $result_par['voto'];                                
                            }
                        }
                        else
                        {
                            $voto_parlamentar = "";
                            $voto_parlamentar_n = "Voto";
                        }
                        
                        echo "
                        <p><label>".$result_int['nome']."</label><input name='id_parlamentar[]' type='hidden' value='".$result_int['parlamentar']."'> 
                        <select name='voto[]'  class='obg' >
                            <option value='$voto_parlamentar'>$voto_parlamentar_n</option>
                            <option value='Sim'>Sim</option>
                            <option value='Não'>Não</option>
                            <option value='Abstenção'>Abstenção</option>
                        </select>
                        ";                                
                    }
                    echo "
                    <hr style='width:95%'> 
                    
                    <div style='background:#EEE;>
                    <p><label>Observação:</label> <textarea name='observacao'  placeholder='Observação' >$observacao_votacao</textarea>  
                    <p><label>Sim:</label> <input name='total_votos_sim' value='$total_votos_sim' placeholder='0' readonly style='width:18px; border:none; background:none; font-size:15px; padding:7px 0; margin:0;'> 
                    <br><label>Não:</label> <input name='total_votos_nao' value='$total_votos_nao' placeholder='0' readonly style='width:18px; border:none; background:none; font-size:15px; padding:7px 0; margin:0;'> 
                    <br><label>Abstenção:</label> <input name='total_votos_abstencao' value='$total_votos_abstencao' placeholder='0' readonly style='width:18px; border:none; background:none; font-size:15px; padding:7px 0; margin:0;'> 
                    </div>
                    <p><label>Resultado da votação*:</label> <select name='resultado' class='obg' >
                        <option value='$resultado'>$descricao</option>";
                            $sql = "SELECT *
                                    FROM aux_sessoes_plenarias_tipo_resultado
                                    WHERE ativo = :ativo
                                    ORDER BY descricao ASC";
                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                            $stmt_int->bindValue(':ativo', 1);                                  
                            $stmt_int->execute();
                            while($result_int = $stmt_int->fetch())
                            {
                               echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";                                
                            }
                        echo "
                    </select>
                </form>
                ";
                ?>        
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-center">            
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="float:left;" onclick="cancelarVotacaoOd(this, <?php echo $id;?>, <?php echo $id_od_materias;?>);">Cancelar Votação</button>  
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
