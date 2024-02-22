<?php 
$pagina_link = 'aux_configuracao_paginas';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include('header.php'); ?>
</head>
<body>	
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        <script>
            function fundo() {
                var cor_fundo = document.getElementById("cor_fundo").value;
                var lbl = document.getElementById('lbl1');
                lbl.innerText = cor_fundo;
            }
              function topo() {
                var cor_topo = document.getElementById("cor_topo").value;
                var lbl = document.getElementById('lbl2');
                lbl.innerText = cor_topo;
            }
              function fonte() {
                var cor_fonte = document.getElementById("cor_fonte").value;
                var lbl = document.getElementById('lbl3');
                lbl.innerText = cor_fonte;
            }
              function rodape() {
                var cor_rodape = document.getElementById("cor_rodape").value;
                var lbl = document.getElementById('lbl4');
                lbl.innerText = cor_rodape;
            }
        </script>

        <div id='janela' class='janela' style='display:none;'> </div>
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Auxiliares &raquo; <a href='aux_configuracao_paginas/view'>Configuração de Paginas</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}            
            $cor_fundo = $_POST['cor_fundo'];
            $cor_topo = $_POST['cor_topo'];            
            $cor_rodape = $_POST['cor_rodape'];
            $cliente = $_SESSION['cliente_id']; 
            $usuario  = $_SESSION['usuario_id']; 
            
           
            $dados = array(
                'clientes'           => $cliente,
                'usuario'           => $usuario, 
                'cor_fundo' 		=> $cor_fundo,
                'cor_topo' 		    => $cor_topo,                
                'cor_fonte' 		=> $cor_fonte,
                'cor_rodape'        => $cor_rodape
            );


            if($action == "salvar")
            {      
                $sql_consulta = "SELECT * FROM aux_configuracao_paginas WHERE clientes = :clientes";
                $stmt_consulta = $PDO_PROCLEGIS->prepare($sql_consulta);	
                $stmt_consulta->bindParam(':clientes', $cliente); 
                $stmt_consulta->execute(); 
               
                $rows = $stmt_consulta->rowCount();	

             if ($rows>0) {
                    $cor_fundo = $_POST['cor_fundo'];
                    $cor_topo = $_POST['cor_topo'];            
                    $cor_rodape = $_POST['cor_rodape'];
                    $cliente = $_SESSION['cliente_id']; 
                    
                    $sql = "UPDATE aux_configuracao_paginas SET cor_fundo = :cor_fundo, cor_topo = :cor_topo, cor_rodape= :cor_rodape WHERE clientes = :clientes";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);	
                    $stmt->bindParam(':cor_fundo', $cor_fundo); 
                    $stmt->bindParam(':cor_topo', $cor_topo);
                    $stmt->bindParam(':cor_rodape', $cor_rodape );
                    $stmt->bindParam(':clientes', $cliente); 
                             
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
                        $err = $stmt->errorInfo();
                        print_r($err);
                        ?>
                        <script>
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                        <?php 
                    }
                 # code...
                }else {
                    # code...
                    $sql = "INSERT INTO aux_configuracao_paginas SET ".bindFields($dados);
                    $stmt = $PDO_PROCLEGIS->prepare($sql);	
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
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                        <?php 
                    }	
                }
             }   

            if($pagina == "view")
            {
                $sql_consulta = "SELECT * FROM aux_configuracao_paginas WHERE clientes = :clientes";
                $stmt_consulta = $PDO_PROCLEGIS->prepare($sql_consulta);	
                $stmt_consulta->bindParam(':clientes', $_SESSION['cliente_id']); 
                $stmt_consulta->execute();
                $rows =  $stmt_consulta->rowCount();
                $result= $stmt_consulta->fetch(); 

                if($result['cor_fundo']){
                    $cor_fundo = $result["cor_fundo"];
                }else{
                        $cor_fundo = "#00000";
                }

                if($result['cor_topo']){
                    $cor_topo = $result["cor_topo"];
                }else{
                        $cor_topo = "#00000";
                }

                if($result['cor_rodape']){
                    $cor_rodape = $result["cor_rodape"];
                }else{
                        $cor_rodape = "#00000";
                }



                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_configuracao_paginas/view/salvar'>
                    <div class='titulo'> $page </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>           
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                                            
                            <p style = 'display:flex;'><label for='cor_fundo'>Cor de Fundo:</label> <input name='cor_fundo' type = 'color' id='cor_fundo' value= '$cor_fundo' placeholder='Cor do fundo'  class='caixa_cor' onchange='fundo()'> <label id='lbl1' class = 'hex'>$cor_fundo</label>
                            <p style = 'display:flex;'><label for='cor_topo'>Cor do Topo:</label> <input name='cor_topo' type = 'color' id='cor_topo' value='$cor_topo' placeholder='Cor do Topo'  class='caixa_cor' onchange='topo()'> <label id='lbl2' class = 'hex'>$cor_topo</label>			
                            <p style = 'display:flex;'><label for='cor_rodape'>Cor do Rodapé :</label> <input name='cor_rodape' type = 'color' id='cor_rodape' value='$cor_rodape' placeholder='Cor do Rodapé' class='caixa_cor' onchange='rodape()'> <label id='lbl4' class = 'hex'>$cor_rodape</label>";
                            echo "     
                            <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_configuracao_paginas/view'; value='Cancelar'/></center>
                            </center>                                                     
                        </div>                        
                    </div>
                </form>
                ";
            } 
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    
</body>
</html>