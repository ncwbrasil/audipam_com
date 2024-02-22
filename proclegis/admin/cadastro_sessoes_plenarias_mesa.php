<?php
$pagina_link = 'cadastro_sessoes_plenarias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php    
            if(isset($_GET['id'])){$id = $_GET['id'];}
                     
            $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                            , cadastro_sessoes_plenarias.numero as numero
                            , aux_parlamentares_legislaturas.numero as numero_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                            , aux_mesa_diretora_sessoes.numero as numero_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                    FROM cadastro_sessoes_plenarias 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                    LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                    LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao                     
                    WHERE cadastro_sessoes_plenarias.id = :id 			
                    ORDER BY cadastro_sessoes_plenarias.id DESC  ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
                
            $stmt->bindParam(':id', 	$id);
           
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $result = $stmt->fetch();
                $n = $result['numero'];
                $d = $result['descricao'];
                $n_s = $result['numero_sessao'];
                $n_l = $result['numero_legislatura'];
            }
            
            $page = "Cadastro &raquo; <a href='cadastro_sessoes_plenarias/view'>Sessões Plenárias</a> &raquo; <a href='cadastro_sessoes_plenarias/exib/$id'>Exibir</a>";
            
                                    
            $sql = "SELECT *, cadastro_sessoes_plenarias_mesa.id as id     
                            , aux_parlamentares_legislaturas.id as id_legislatura                       
                    FROM cadastro_sessoes_plenarias_mesa
                    LEFT JOIN ( cadastro_sessoes_plenarias 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                    )
                    ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_mesa.sessao_plenaria                     
                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_mesa.parlamentar                     
                    LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_sessoes_plenarias_mesa.cargo
                    WHERE sessao_plenaria = :sessao_plenaria		
                    ORDER BY cadastro_sessoes_plenarias_mesa.id ASC
                    ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                
            $stmt->bindParam(':sessao_plenaria', 	$id);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias/exib/$id/editar_mesa/#abertura'>                            
                    <div class='titulo'> $page   &raquo; Mesa</div>                
                    <div class='conteudo'>
                             ".$n."ª Sessão Plenária ".$d." da ".$n_s." Sessão Legislativa da ".$n_l." Legislatura
                            <div id='p_scents_mesa'>
								";
								if($rows > 0)
								{
									$x=0;
									while($result = $stmt->fetch())
									{
                                        $id_legislatura = $result['id_legislatura'];
                                     
										echo "
										<div class='bloco_mesa' style='text-align:left;'>
											<input type='hidden' name='mesa[$x][id]' value='".$result['id']."'>
											"; if($x > 1){ echo "<br><br><p>";}else{ echo "<br>";} echo "
											<p><label>Parlamentar *:</label>	<select name='mesa[$x][parlamentar]' class='obg'>
                                                                                    <option value='".$result['parlamentar']."'>".$result['nome']."</option>
                                                                                    "; 
                                                                                    $sql = "SELECT * FROM cadastro_parlamentares 
                                                                                            LEFT JOIN cadastro_parlamentares_mandatos ON cadastro_parlamentares_mandatos.parlamentar = cadastro_parlamentares.id
                                                                                            WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura
                                                                                            ORDER BY nome";
                                                                                    $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);
                                                                                    echo $id_legislatura;
                                                                                    $stmt_tipo->bindParam(':legislatura', 	$id_legislatura);
                                                                                    $stmt_tipo->execute();
                                                                                    while($result_tipo = $stmt_tipo->fetch())
                                                                                    {
                                                                                        echo "<option value='".$result_tipo['id']."'>".$result_tipo['nome']."</option>";
                                                                                    }
                                                                                    echo "
                                                                                </select> 
                                            <p><label>Cargo *:</label>	<select name='mesa[$x][cargo]' class='obg'>
                                                                                    <option value='".$result['cargo']."'>".$result['descricao']."</option>
                                                                                    "; 
                                                                                    $sql = " SELECT * FROM aux_mesa_diretora_cargos
                                                                                      
                                                                                    ORDER BY descricao";
                                                                                    $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);
                                                                                    $stmt_tipo->execute();
                                                                                    while($result_tipo = $stmt_tipo->fetch())
                                                                                    {
                                                                                        echo "<option value='".$result_tipo['id']."'>".$result_tipo['descricao']."</option>";
                                                                                    }
                                                                                    echo "
                                                                                </select>                                            
                                            <br>
											<p><i class='fas fa-plus-circle adicionar'  title='Adicionar +' id='add_mesa'></i> &nbsp; <i class='fas fa-minus-circle remover'  title='Remover' id='rem_mesa'></i>
										</div>
										";
									}
								}
								else
								{
									echo "
									<div class='bloco_mesa' style='text-align:left;'>
                                        <input type='hidden' name='mesa[1][id]'>
                                        <p><label>Parlamentar *:</label>	<select name='mesa[1][parlamentar]' class='obg'>
                                                                                <option value=''>Parlamentar</option>
                                                                                "; 
                                                                                $sql = " SELECT * FROM cadastro_parlamentares  ORDER BY nome";
                                                                                $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);
                                                                                $stmt_tipo->execute();
                                                                                while($result_tipo = $stmt_tipo->fetch())
                                                                                {
                                                                                    echo "<option value='".$result_tipo['id']."'>".$result_tipo['nome']."</option>";
                                                                                }
                                                                                echo "
                                                                            </select>    
                                        <p><label>Cargo *:</label>	<select name='mesa[1][cargo]' class='obg'>
                                                                                <option value=''>Cargo</option>
                                                                                "; 
                                                                                $sql = " SELECT * FROM aux_mesa_diretora_cargos
                                                                                  
                                                                                ORDER BY descricao";
                                                                                $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);
                                                                                $stmt_tipo->execute();
                                                                                while($result_tipo = $stmt_tipo->fetch())
                                                                                {
                                                                                    echo "<option value='".$result_tipo['id']."'>".$result_tipo['descricao']."</option>";
                                                                                }
                                                                                echo "
                                                                            </select>                                    
                                        <p><i class='fas fa-plus-circle adicionar'  title='Adicionar +' id='add_mesa'></i></p>
                                        <br>    
									</div>
									";
								}
								echo "
							</div>                                                                                                             
                        </div>                                                                                                 				
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sessoes_plenarias/view'; value='Cancelar'/></center>
                        </center>                    
                </form>";     
            }
           	
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    <script>           
        /// SESSOES PLENARIAS - MESA ///
        $(document).on('click','#add_mesa',function() {
            
            var x = $('div.bloco_mesa').length + 1;
            
            var total=0;
            $('<div class="bloco_mesa">'+
                '<input type="hidden" name="mesa['+x+'][id]">'+
                '<br><br>'+
                '<p><label>Parlamentar *:</label>	<select name="mesa['+x+'][parlamentar]" class="obg">'+
                                                        '<option value="">Parlamentar</option>'+
                                                        '<?php $sql = " SELECT * FROM cadastro_parlamentares ORDER BY nome";?>'+
                                                        '<?php $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);?>'+
                                                        '<?php $stmt_tipo->bindParam(":cliente", 	$_SESSION["cliente_id"]); ?>'+
                                                        '<?php $stmt_tipo->execute();?>'+
                                                        '<?php while($result_tipo = $stmt_tipo->fetch()) {?>'+
                                                        '<?php $id = $result_tipo["id"];?>'+
                                                        '<?php $nome = $result_tipo["nome"];?>'+
                                                        '<option value="<?php echo $id; ?>"><?php echo $nome; ?></option>'+
                                                        '<?php }?>'+														
                                                    '</select>'+
                '<p><label>Cargo *:</label>	<select name="mesa['+x+'][cargo]" class="obg">'+
                                                        '<option value="">Cargo</option>'+
                                                        '<?php $sql = " SELECT * FROM aux_mesa_diretora_cargos ORDER BY descricao";?>'+
                                                        '<?php $stmt_tipo = $PDO_PROCLEGIS->prepare($sql);?>'+
                                                        '<?php $stmt_tipo->bindParam(":cliente", 	$_SESSION["cliente_id"]); ?>'+
                                                        '<?php $stmt_tipo->execute();?>'+
                                                        '<?php while($result_tipo = $stmt_tipo->fetch()) {?>'+
                                                        '<?php $id = $result_tipo["id"];?>'+
                                                        '<?php $descricao = $result_tipo["descricao"];?>'+
                                                        '<option value="<?php echo $id; ?>"><?php echo $descricao; ?></option>'+
                                                        '<?php }?>'+														
                                                    '</select>'+
                '<p><i class="fas fa-plus-circle adicionar"  title="Adicionar +" id="add_mesa"></i> &nbsp; <i class="fas fa-minus-circle remover"  title="Remover" id="rem_mesa"></i></div>').appendTo("#p_scents_mesa");
            //i++;
            x++;			
            return false;
        });

        $(document).on('click','#rem_mesa', function() { 
            var x = $('div.bloco_mesa').length + 1;
            if( x >= 1 )
            {
                $(this).parents('div.bloco_mesa').remove();
                //i--;
                //x--;			
            }
            return false;
        });     
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
    <!-- MODAL -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/jquery-3.4.1.min.js"></script>
	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/popper.min.js"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/bootstrap.min.js"></script>
	<!-- MDB core JavaScript -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/mdb.min.js"></script>
  
</body>
</html>