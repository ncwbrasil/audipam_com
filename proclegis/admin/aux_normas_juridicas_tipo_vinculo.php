<?php
$pagina_link = 'aux_normas_juridicas';
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
            $page = "Auxiliares &raquo; <a href='aux_normas_juridicas/view'>Normas Jurídicas</a> &raquo; <a href='aux_normas_juridicas_tipo_vinculo/view'>Tipo de Vínculo</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}            
            $sigla = $_POST['sigla'];
            $descricao_ativa = $_POST['descricao_ativa'];            
            $descricao_passiva = $_POST['descricao_passiva'];            
            $revoga_integralmente = $_POST['revoga_integralmente'];
            $legislatura = $_POST['legislatura'];             
           
            $dados = array(
                
                'sigla' 		=> $sigla,
                'descricao_ativa' 		    => $descricao_ativa,                
                'descricao_passiva' 		    => $descricao_passiva,                
                'revoga_integralmente' 		=> $revoga_integralmente
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_normas_juridicas_tipo_vinculo SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();                               				
                
                    if($erro != 1)
                    {
                        ?>
                        <script>
                            mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                        </script>
                        <?php
                    }
                    else
                    {
                        ?>
                        <script>
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                        </script>
                        <?php 
                    

                    }
                
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            
            if($action == 'editar')
            {
                $sql = "UPDATE aux_normas_juridicas_tipo_vinculo SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {                                                                                                                     
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                    </script>
                    <?php
                }            
            }
            
            if($action == 'excluir')
            {
                
                $sql = "DELETE FROM aux_normas_juridicas_tipo_vinculo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
                {                   
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }            
            }
            
            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_sigla = $_REQUEST['fil_sigla'];
            if($fil_sigla == '')
            {
                $sigla_query = " 1 = 1 ";
            }
            else
            {
                $fil_sigla1 = "%".$fil_sigla."%";
                $sigla_query = " (aux_normas_juridicas_tipo_vinculo.sigla LIKE :fil_sigla1 ) ";
            }
              
            $sql = "SELECT * FROM aux_normas_juridicas_tipo_vinculo 
                    WHERE ".$sigla_query." 			
                    ORDER BY aux_normas_juridicas_tipo_vinculo.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_sigla1', 	$fil_sigla1);
                
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
              
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_normas_juridicas_tipo_vinculo/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipo_vinculo/view'>
                        <input name='fil_sigla' id='fil_sigla' value='$fil_sigla' placeholder='Sigla'>
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>    
                </div>
                ";
                if ($rows > 0)
                {
                    echo "
                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                        <tr>                            
                            <td class='titulo_tabela'>Sigla</td>
                            <td class='titulo_tabela'>Descrição Ativa</td>                            
                            <td class='titulo_tabela'>Descrição Passiva</td>                            
                            <td class='titulo_tabela'>Revoga Integralmente</td>                            
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$result['sigla']."</td>
                                    <td>".$result['descricao_ativa']."</td>                                    
                                    <td>".$result['descricao_passiva']."</td>                                    
                                    <td>".$result['revoga_integralmente']."</td>                                
                                    <td align=center>
                                        <div class='g_excluir' title='Excluir' onclick=\"
                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_normas_juridicas_tipo_vinculo/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                            \">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_normas_juridicas_tipo_vinculo/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_normas_juridicas_tipo_vinculo  
                                WHERE ".$sigla_query." ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_sigla1', 	$fil_sigla1);
                            
                        $variavel = "&fil_sigla=$fil_sigla";              
                        include("../../core/mod_includes/php/paginacao.php");
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if($pagina == 'add')
            {
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipo_vinculo/view/adicionar' autocomplete='off'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                                                          
                                <p><label>Sigla *:</label> <input name='sigla' id='sigla' placeholder='Sigla' class='obg'>
                                <p><label>Descrição Ativa *:</label> <input name='descricao_ativa' id='descricao_ativa' placeholder='Descrição Ativa' class='obg'>
                                <p><label>Descrição Passiva *:</label> <input name='descricao_passiva' id='descricao_passiva' placeholder='Descrição Passiva' class='obg'>
                                <p><label>Revoga Integralmente *:</label> <select name='revoga_integralmente' id='revoga_integralmente' class='obg'>
                                    <option value=''>Revoga Integralmente</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>
                                </select>                                
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_normas_juridicas_tipo_vinculo/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT * FROM aux_normas_juridicas_tipo_vinculo 
                        WHERE aux_normas_juridicas_tipo_vinculo.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipo_vinculo/view/editar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                    
                                <p><label>Sigla *:</label> <input name='sigla' id='sigla' value='".$result['sigla']."' placeholder='Sigla'  class='obg'>
                                <p><label>Descrição Ativa *:</label> <input name='descricao_ativa' id='descricao_ativa' value='".$result['descricao_ativa']."' placeholder='Descrição Ativa'  class='obg'>
                                <p><label>Descrição Passiva *:</label> <input name='descricao_passiva' id='descricao_passiva' value='".$result['descricao_passiva']."' placeholder='Descrição Passiva'  class='obg'>
                                <p><label>Revoga Integralmente *:</label> <select name='revoga_integralmente' id='revoga_integralmente' class='obg'>
                                    <option value='".$result['revoga_integralmente']."'>".$result['revoga_integralmente']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>
                                </select>                                
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_normas_juridicas_tipo_vinculo/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>