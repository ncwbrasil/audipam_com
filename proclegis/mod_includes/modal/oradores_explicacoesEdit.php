<div  class="modal fade in" id="oradores_explicacoesEdit<?php echo $id_oradores_explicacoes;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_oradores_explicacoes'  enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_oradores_explicacoes/$id/view/editar_oradores_explicacoes/$id_oradores_explicacoes'>							
                    <input type='hidden' name='id_oradores_explicacoes' id='id_oradores_explicacoes' value='".$id_oradores_explicacoes."'  class='obg'>
                    <p><label>Parlamentar*:</label> <select name='parlamentar' id='parlamentar' class='obg' >
                            <option value='$parlamentar'>$nome</option>";
                                $sql = "SELECT *, cadastro_parlamentares.id as id
                                        FROM cadastro_parlamentares
                                        LEFT JOIN cadastro_parlamentares_mandatos ON cadastro_parlamentares_mandatos.parlamentar = cadastro_parlamentares.id
                                        WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura AND cadastro_parlamentares.ativo = :ativo
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
                    <p><label>Ordem pronunciamento*:</label> <input name='ordem' value='$ordem'  placeholder='Ordem pronunciamento'  class='obg' autocomplete='off'>
                    <p><label>Url vídeo:</label> <input name='url_video'  value='$url_video'   placeholder='Url vídeo (https://)' autocomplete='off'>
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
