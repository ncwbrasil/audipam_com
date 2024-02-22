<div  class="modal fade in" id="leg_citadaEdit<?php echo $id_leg_citada;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >

        <!--Content-->
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body"  style="padding-left:0; padding-right:0; text-align:center;">                
                <?php
                echo "	
                <form name='form_leg_citada'  enctype='multipart/form-data' method='post' action='cadastro_materias/exib/".$id."/editar_leg_citada#leg_citada'>							
                    <input type='hidden' name='id_leg_citada' id='id_leg_citada' value='".$id_leg_citada."'  class='obg'>
                    <p><label>Tipo de Norma*:</label> <select name='tipo_norma' id='tipo_norma' class='obg' >
                            <option value='$tipo_norma'>$sigla - $nome</option>";
                                $sql = "SELECT *
                                FROM aux_normas_juridicas_tipos
                                WHERE ativo = :ativo
                                ORDER BY  nome ASC ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                $stmt_int->bindValue(':ativo', 	1);                                
                                $stmt_int->execute();
                                while($result_int = $stmt_int->fetch())
                                {
                                    echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                }
                            echo "
                        </select>
                    <p><label>Norma Jurídica*:</label> <select name='norma_juridica' id='norma_juridica'  class='obg' >
                        <option value='$norma_juridica'>$numero de $ano</option>                           
                    </select>                   
                    <p><label>Ementa:</label> <textarea name='ementa_norma' id='ementa_norma' readonly='readonly' >$ementa</textarea>
                    <p><label>Disposição:</label> <input name='disposicao' id='disposicao' value='$disposicao' placeholder='Disposição'>
                    <p><label>Parte:</label> <input name='parte' id='parte' value='$parte' placeholder='Parte'>
                    <p><label>Livro:</label> <input name='livro' id='livro' value='$livro' placeholder='Livro'>
                    <p><label>Título:</label> <input name='titulo' id='titulo' value='$titulo' placeholder='Título'>
                    <p><label>Capítulo:</label> <input name='capitulo' id='capitulo' value='$capitulo' placeholder='Capítulo'>
                    <p><label>Seção:</label> <input name='secao' id='secao' value='$secao' placeholder='Seção'>          
                    <p><label>Subseção:</label> <input name='subsecao' id='subsecao' value='$subsecao' placeholder='Subseção'>
                    <p><label>Artigo:</label> <input name='artigo' id='artigo' value='$artigo' placeholder='Artigo'>
                    <p><label>Parágrafo:</label> <input name='paragrafo' id='paragrafo' value='$paragrafo' placeholder='Parágrafo'>
                    <p><label>Inciso:</label> <input name='inciso' id='inciso' value='$inciso' placeholder='Inciso'>
                    <p><label>Alínea:</label> <input name='alinea' id='alinea' value='$alinea' placeholder='Alínea'>
                    <p><label>Item:</label> <input name='item' id='item' value='$item' placeholder='Item'>
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
