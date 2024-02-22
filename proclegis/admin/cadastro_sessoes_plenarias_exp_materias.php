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
                             
            $page = "Cadastro  &raquo; <a href='cadastro_sessoes_plenarias/view'>Sessões Plenárias</a> &raquo;  <a href='cadastro_sessoes_plenarias/exib/$id'>Exibir</a> ";
            
            $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                            , cadastro_sessoes_plenarias.numero as numero
                            , aux_parlamentares_legislaturas.numero as numero_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                            , aux_mesa_diretora_sessoes.numero as numero_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                            , aux_parlamentares_legislaturas.id as id_legislatura
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
                $id_legislatura = $result['id_legislatura'];
                $n = $result['numero'];
                $d = $result['descricao'];
                $n_s = $result['numero_sessao'];
                $n_l = $result['numero_legislatura'];
            }

            if($action == "adicionar_exp_materias")
            {            
                           
                $tipo_materia   = $_POST['tipo_materia'];
                $materia   = $_POST['materia'];
                $ordem   = $_POST['ordem'];
                $tipo_votacao   = $_POST['tipo_votacao'];if($tipo_votacao == ""){$tipo_votacao = null;}
                $observacao   = $_POST['observacao'];if($observacao == ""){$observacao = null;}

                if($tipo_votacao == "Apenas leitura")
                {
                    $status = "Matéria não lida";
                    $botao = "Abrir para leitura";
                }
                elseif($tipo_votacao == "Simbólica" || $tipo_votacao == "Secreta" || $tipo_votacao == "Nominal")
                {
                    $status = "Matéria não votada";
                    $botao = "Abrir votação";
                }

                $dados = array(
                    'sessao_plenaria'   => $id,
                    'tipo_materia' 		=> $tipo_materia,                    
                    'materia' 		    => $materia,                    
                    'ordem' 		    => $ordem,
                    'tipo_votacao' 		=> $tipo_votacao,
                    'observacao' 		=> $observacao,
                    'status' 		    => $status,
                    'botao' 		    => $botao
                    );
                $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {	
                    log_operacao($id, $PDO_PROCLEGIS); 	

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
            if($action == "editar_exp_materias")
            {                       
                $id_exp_materias   = $_POST['id_exp_materias'];
                $tipo_materia   = $_POST['tipo_materia'];
                $materia   = $_POST['materia'];
                $ordem   = $_POST['ordem'];
                $tipo_votacao   = $_POST['tipo_votacao'];if($tipo_votacao == ""){$tipo_votacao = null;}
                $observacao   = $_POST['observacao'];if($observacao == ""){$observacao = null;}
               
                if($tipo_votacao == "Apenas leitura")
                {
                    $status = "Matéria não lida";
                    $botao = "Abrir para leitura";
                }
                elseif($tipo_votacao == "Simbólica" || $tipo_votacao == "Secreta" || $tipo_votacao == "Nominal")
                {
                    $status = "Matéria não votada";
                    $botao = "Abrir votação";
                }
                

                $dados = array(
                    'sessao_plenaria'   => $id,
                    'tipo_materia' 		=> $tipo_materia,                    
                    'materia' 		    => $materia,                    
                    'ordem' 		    => $ordem,
                    'tipo_votacao' 		=> $tipo_votacao,
                    'observacao' 		=> $observacao,
                    'status' 		    => $status,
                    'botao' 		    => $botao
                    );
                    
                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_exp_materias;
                if($stmt->execute($dados))
                {	
                    
                    log_operacao($id, $PDO_PROCLEGIS); 
                   
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
            if($action == "abrir_leitura")
            {                     
                $id_sub   = $_GET['id_sub'];
                $status   = "Aberta para leitura";
                $botao   = "Registrar leitura";
               
                $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_materias WHERE sessao_plenaria = :sessao_plenaria AND status LIKE :status AND ativo = :ativo ";
                $sts = "%Aberta%";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $stmt->bindParam(':sessao_plenaria', 	$id);    
                $stmt->bindParam(':status', 	$sts);     
                $stmt->bindValue(':ativo', 	1);  
                $stmt->execute();
                $rows = $stmt->rowCount();               
                if($rows > 0)
                {	
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Já existe uma matéria aberta para leitura/votação.");
                    </script>
                    <?php 
                }	
                else
                {
                    $dados = array(
                        'status' 		    => $status,
                        'botao' 		    => $botao
                        );
                        
                    $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $dados['id'] =  $id_sub;
                    if($stmt->execute($dados))
                    {	
                        
                        log_operacao($id, $PDO_PROCLEGIS); 
                    
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
            if($action == "abrir_votacao")
            {                     
                $id_sub   = $_GET['id_sub'];
                $status   = "Aberta para votação";
                $botao   = "Registrar votação";
               
                $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_materias WHERE sessao_plenaria = :sessao_plenaria AND status LIKE :status ";
                $sts = "%aberta%";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $stmt->bindParam(':sessao_plenaria', 	$id);    
                $stmt->bindParam(':status', 	$sts);     
                $stmt->execute();
                $rows = $stmt->rowCount();               
                if($rows > 0)
                {	
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Já existe uma matéria aberta para leitura/votação.");
                    </script>
                    <?php 
                }	
                else
                {
                    $dados = array(
                        'status' 		    => $status,
                        'botao' 		    => $botao
                        );
                        
                    $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $dados['id'] =  $id_sub;
                    if($stmt->execute($dados))
                    {		

                        log_operacao($id, $PDO_PROCLEGIS); 
                    
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
            if($action == "registrar_leitura")
            {                       
                
                $id_exp_materias   = $_POST['id_exp_materias'];
                
                $status = "Matéria lida";
                $botao = "Ver resultado";
                
                $dados = array(                    
                    'status' 		    => $status,
                    'botao' 		    => $botao
                    );
                    
                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_exp_materias;
                if($stmt->execute($dados))
                {		      
                    // VERIFICAR SE JÁ TEM LEITURA CADASTRADA   
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_materias_leitura WHERE materia_exp = :materia_exp ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':materia_exp', 	$id_exp_materias);       
                    $stmt->execute();
                    $rows = $stmt->rowCount();              
                    if($rows > 0)
                    {		
                        $result = $stmt->fetch();
                        // REGISTRA LEITURA
                        $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias_leitura SET 
                                       observacao = :observacao 
                                WHERE id = :id";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':id', 	$result['id']);    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {
                            
                            log_operacao($id, $PDO_PROCLEGIS); 

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
                    else
                    {
                        // REGISTRA LEITURA
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_materias_leitura SET 
                                materia_exp = :materia_exp,
                                observacao = :observacao ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':materia_exp', 	$id_exp_materias);    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {		
                            log_operacao($id, $PDO_PROCLEGIS); 
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
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }

            if($action == "registrar_votacao")
            {                       
                
                $id_exp_materias   = $_POST['id_exp_materias'];
                
                $status = "Matéria votada";
                $botao = "Ver resultado votação";
                
                $dados = array(                    
                    'status' 		    => $status,
                    'botao' 		    => $botao
                    );
                    
                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_exp_materias;
                if($stmt->execute($dados))
                {		      
                    // VERIFICAR SE JÁ TEM VOTACAO CADASTRADA   
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_materias_votacao WHERE materia_exp = :materia_exp ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':materia_exp', 	$id_exp_materias);       
                    $stmt->execute();
                    $rows = $stmt->rowCount();              
                    if($rows > 0)
                    {		
                        $result = $stmt->fetch();
                        // REGISTRA VOTACAO
                        $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias_votacao SET 
                                       sim = :sim,
                                       nao = :nao,
                                       abstencao = :abstencao,
                                       inclui_presidente = :inclui_presidente,
                                       resultado = :resultado,
                                       observacao = :observacao
                                WHERE id = :id";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':id', 	$result['id']);    
                        $stmt->bindParam(':sim', 	$_POST['sim']);    
                        $stmt->bindParam(':nao', 	$_POST['nao']);    
                        $stmt->bindParam(':abstencao', 	$_POST['abstencao']);    
                        $stmt->bindParam(':inclui_presidente', 	$_POST['inclui_presidente']);    
                        $stmt->bindParam(':resultado', 	$_POST['resultado']);                                    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {		

                            log_operacao($id, $PDO_PROCLEGIS); 
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
                    else
                    {
                        // REGISTRA VOTACAO
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_materias_votacao SET 
                                materia_exp = :materia_exp,
                                sim = :sim,
                                nao = :nao,
                                abstencao = :abstencao,
                                inclui_presidente = :inclui_presidente,
                                resultado = :resultado,
                                observacao = :observacao ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':materia_exp', 	$id_exp_materias);  
                        $stmt->bindParam(':sim', 	$_POST['sim']);    
                        $stmt->bindParam(':nao', 	$_POST['nao']);    
                        $stmt->bindParam(':abstencao', 	$_POST['abstencao']);    
                        $stmt->bindParam(':inclui_presidente', 	$_POST['inclui_presidente']);    
                        $stmt->bindParam(':resultado', 	$_POST['resultado']);    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {		
                            log_operacao($id, $PDO_PROCLEGIS); 

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
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == "registrar_votacao_nominal")
            {                       
                
                $id_exp_materias   = $_POST['id_exp_materias'];
                
                $status = "Matéria votada";
                $botao = "Ver resultado votação";
                
                $dados = array(                    
                    'status' 		    => $status,
                    'botao' 		    => $botao
                    );
                    
                $votos = $_POST['voto'];
                $sim = $nao = $abs = 0;
                foreach($votos as $val)
                {
                    if($val == "Sim")
                    {
                        $sim++;
                    }
                    elseif($val == "Não")
                    {
                        $nao++;
                    }
                    elseif($val == "Abstenção")
                    {
                        $abs++;
                    }
                }
                
                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_exp_materias;
                if($stmt->execute($dados))
                {		      
                    
                    // VERIFICAR SE JÁ TEM VOTACAO CADASTRADA   
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_materias_votacao WHERE materia_exp = :materia_exp ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':materia_exp', 	$id_exp_materias);       
                    $stmt->execute();
                    $rows = $stmt->rowCount();              
                    if($rows > 0)
                    {		
                        $result = $stmt->fetch();                    
                        // REGISTRA VOTACAO
                        $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias_votacao SET 
                                       sim = :sim,
                                       nao = :nao,
                                       abstencao = :abstencao,                                       
                                       resultado = :resultado,
                                       observacao = :observacao
                                WHERE id = :id";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':id', 	$result['id']);    
                        $stmt->bindParam(':sim', 	$sim);    
                        $stmt->bindParam(':nao', 	$nao);    
                        $stmt->bindParam(':abstencao', 	$abs);                              
                        $stmt->bindParam(':resultado', 	$_POST['resultado']);                                    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {		

                            log_operacao($id, $PDO_PROCLEGIS); 
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
                    else
                    {
                        // REGISTRA VOTACAO
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_materias_votacao SET 
                                materia_exp = :materia_exp,
                                sim = :sim,
                                nao = :nao,
                                abstencao = :abstencao,                                
                                resultado = :resultado,
                                observacao = :observacao ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':materia_exp', 	$id_exp_materias);  
                        $stmt->bindParam(':sim', 	$sim);    
                        $stmt->bindParam(':nao', 	$nao);    
                        $stmt->bindParam(':abstencao', 	$abs);      
                        $stmt->bindParam(':resultado', 	$_POST['resultado']);    
                        $stmt->bindParam(':observacao', 	$_POST['observacao']);    
                        if($stmt->execute())
                        {		
                            $id_votacao = $PDO_PROCLEGIS->lastInsertId();
                            $parlamentar = $_POST['id_parlamentar'];
                            $voto = $_POST['voto'];
                            $votos = array_combine($parlamentar, $voto);
                            foreach($votos as $key => $val)
                            {
                                $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_materias_votacao_nominal SET 
                                                    votacao = :votacao,
                                                    parlamentar = :parlamentar,
                                                    voto = :voto";
                                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                                $stmt->bindParam(':votacao', 	$id_votacao);  
                                $stmt->bindParam(':parlamentar', 	$key);    
                                $stmt->bindParam(':voto', 	$val);                                    
                                if($stmt->execute())
                                {		
                                }
                                else { $erro = 1;}
                            }
                            log_operacao($id, $PDO_PROCLEGIS); 
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
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == 'excluir_exp_materias')
            {
                $id_sub = $_GET['id_sub'];

                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue (':ativo',0);
                if($stmt->execute())
                {
                    log_operacao($id, $PDO_PROCLEGIS);
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

            if($action == 'excluir_leitura')
            {
                $id_sub = $_GET['id_sub'];

                $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias_leitura set ativo = :ativo WHERE materia_exp = :materia_exp";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':materia_exp',$id_sub);
                $stmt->bindValue (':ativo',0);
                if($stmt->execute())
                {
                     // VOLTA STATUS LEITURA
                    $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET 
                                    status = :status,
                                    botao = :botao 
                            WHERE id = :id";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $status = "Matéria não lida";
                    $botao = "Abrir para leitura";
                    $stmt->bindParam(':id', 	$id_sub);    
                    $stmt->bindParam(':status', 	$status);    
                    $stmt->bindParam(':botao', 	$botao);    
                    if($stmt->execute())
                    {		
                        log_operacao($id, $PDO_PROCLEGIS); 
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
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }            
            }

            if($action == 'excluir_votacao')
            {
                $id_sub = $_GET['id_sub'];

                $sql = "DELETE FROM cadastro_sessoes_plenarias_exp_materias_votacao WHERE materia_exp = :materia_exp";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':materia_exp',$id_sub);                
                if($stmt->execute())
                {
                     // VOLTA STATUS LEITURA
                    $sql = "UPDATE cadastro_sessoes_plenarias_exp_materias SET 
                                    status = :status,
                                    botao = :botao 
                            WHERE id = :id";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $status = "Matéria não votada";
                    $botao = "Abrir votação";
                    $stmt->bindParam(':id', 	$id_sub);    
                    $stmt->bindParam(':status', 	$status);    
                    $stmt->bindParam(':botao', 	$botao);    
                    if($stmt->execute())
                    {		
                        
                        log_operacao($id, $PDO_PROCLEGIS);
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
            $fil_nome = $_REQUEST['fil_nome'];
            if($fil_nome == '')
            {
                $nome_query = " 1 = 1 ";
            }
            else
            {
                $fil_nome1 = $fil_nome2 = $fil_nome3 = "%".$fil_nome."%";
                $nome_query = " (cadastro_materias.nome LIKE :fil_nome1 ) ";
            }            
            $sql = "SELECT *, cadastro_sessoes_plenarias_exp_materias.id as id 
                            , cadastro_materias.numero as numero
                            , cadastro_sessoes_plenarias_exp_materias.observacao as observacao
                            , cadastro_sessoes_plenarias_exp_materias_leitura.observacao as observacao_leitura
                            , cadastro_sessoes_plenarias_exp_materias_votacao.observacao as observacao_votacao
                    FROM cadastro_sessoes_plenarias_exp_materias 
                    LEFT JOIN cadastro_sessoes_plenarias_exp_materias_leitura ON cadastro_sessoes_plenarias_exp_materias_leitura.materia_exp = cadastro_sessoes_plenarias_exp_materias.id                     
                    LEFT JOIN ( cadastro_sessoes_plenarias_exp_materias_votacao 
                        LEFT JOIN aux_sessoes_plenarias_tipo_resultado ON aux_sessoes_plenarias_tipo_resultado.id = cadastro_sessoes_plenarias_exp_materias_votacao.resultado)
                    ON cadastro_sessoes_plenarias_exp_materias_votacao.materia_exp = cadastro_sessoes_plenarias_exp_materias.id                     
                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_exp_materias.tipo_materia                     
                    LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_exp_materias.materia                     
                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_exp_materias.sessao_plenaria                     
                    WHERE cadastro_sessoes_plenarias_exp_materias.ativo = :ativo and ".$nome_query." AND cadastro_sessoes_plenarias_exp_materias.sessao_plenaria = :sessao_plenaria		
                    ORDER BY cadastro_sessoes_plenarias_exp_materias.ordem ASC
                   ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
             
            $stmt->bindParam(':sessao_plenaria', 	$id);    
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->bindValue(':ativo', 	1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                include("../mod_includes/modal/exp_materiasAdd.php");
                echo "
                <div class='titulo'> $page  &raquo; Matérias do Expediente </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#exp_materiasAdd'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_exp_materias/$id/view'>
                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
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
                            <td class='titulo_tabela'>Ordem</td>                                            
                            <td class='titulo_tabela'>Matéria</td>
                            <td class='titulo_tabela'>Tipo de votação</td>
                            <td class='titulo_tabela'>Observação</td>
                            <td class='titulo_tabela'  align='center'>Resultado</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>
                        ";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id_exp_materias = $result['id'];                            
                            $tipo_materia = $result['tipo_materia'];
                            $sigla = $result['sigla'];
                            $nome = $result['nome'];
                            $materia = $result['materia'];
                            $numero = $result['numero'];
                            $ementa = $result['ementa'];
                            $ano = $result['ano'];
                            $status = $result['status'];
                            $botao = $result['botao'];
                            // AUTORES
                            $autor=array();
                            $sql = "SELECT *
                                    FROM cadastro_sessoes_plenarias_presenca 
                                    WHERE sessao_plenaria = :sessao_plenaria	";
                            $stmt_aut = $PDO_PROCLEGIS->prepare($sql);                                
                            $stmt_aut->bindParam(':sessao_plenaria', 	$id);                                
                            $stmt_aut->execute();
                            $rows_aut = $stmt_aut->rowCount();
                            if($rows_aut > 0)
                            {
                               $total_presenca = $rows_aut;
                            }
                           
                            $ordem = $result['ordem'];
                            $tipo_votacao = $result['tipo_votacao'];
                            $observacao = $result['observacao'];
                            $observacao_leitura = $result['observacao_leitura'];
                            $observacao_votacao = $result['observacao_votacao'];
                          
                            $sim = $total_votos_sim = $result['sim'];
                            $nao = $total_votos_nao= $result['nao'];
                            $abstencao = $total_votos_abstencao = $result['abstencao'];
                            $total_votos = $sim + $nao +$abstencao;
                            $inclui_presidente = $result['inclui_presidente'];
                            $resultado = $result['resultado'];
                            $descricao = $result['descricao'];
                            


                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$ordem."</td>
                                    <td>
                                        <span class='bold'>".$result['nome']." Nº ".$result['numero']." de ".$result['ano']."</span><p>
                                        <span class='bold'>Ementa:</span> ".substr($result['ementa'],0,100)."...<p>                                        
                                        <span class='bold'>Autor(es):</span> ".implode(", ",$autor)."<p>
                                    </td>
                                    <td>".$tipo_votacao."</td>
                                    <td>".$observacao."</td> 
                                    <td align='center'><span class='bold'>".$status."</span><p>
                                        ";
                                        $cor = "style='background:#63C;'";
                                        if($botao == "Abrir para leitura")
                                        {
                                            $function = "onclick=verificaPermissao(".$permissoes["edit"].",'cadastro_sessoes_plenarias_exp_materias/$id/view/abrir_leitura/$id_exp_materias');";                                            
                                            $cor = "style='background:#F23B3B;'";
                                              
                                        }
                                        elseif($botao == "Registrar leitura" || $botao == "Ver resultado" )
                                        {
                                            $function = "data-toggle='modal' data-target='#exp_materiasLeitura".$id_exp_materias."'";  
                                            if($botao == "Ver resultado")
                                            {
                                                $cor = "style='background:#1DBA9B;'";
                                            }                                  
                                        }

                                        if($tipo_votacao != "Nominal")
                                        {
                                            if($botao == "Abrir votação")
                                            {
                                                $function = "onclick=verificaPermissao(".$permissoes["edit"].",'cadastro_sessoes_plenarias_exp_materias/$id/view/abrir_votacao/$id_exp_materias');";                                            
                                                $cor = "style='background:#F23B3B;'";
                                                
                                            }
                                        
                                            elseif($botao == "Registrar votação" || $botao == "Ver resultado votação" )
                                            {
                                                $function = "data-toggle='modal' data-target='#exp_materiasVotacao".$id_exp_materias."'";  
                                                if($botao == "Ver resultado votação")
                                                {
                                                    $cor = "style='background:#1DBA9B;'";
                                                }                                                                  
                                            }
                                        }
                                        else if($tipo_votacao == "Nominal")
                                        {
                                            if($botao == "Abrir votação")
                                            {
                                                $function = "onclick=verificaPermissao(".$permissoes["edit"].",'cadastro_sessoes_plenarias_exp_materias/$id/view/abrir_votacao/$id_exp_materias');";                                            
                                                $cor = "style='background:#F23B3B;'";
                                                
                                            }
                                        
                                            elseif($botao == "Registrar votação" || $botao == "Ver resultado votação" )
                                            {
                                                $function = "data-toggle='modal' data-target='#exp_materiasVotacaoNominal".$id_exp_materias."'";  
                                                if($botao == "Ver resultado votação")
                                                {
                                                    $cor = "style='background:#1DBA9B;'";
                                                }                                                                  
                                            }
                                        }
                                        
                                       
                                        echo "<div class='botao hand ' $cor $function >".$botao."</div>
                                    </td>                                                                                                        
                                    <td align=center width='150'>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_sessoes_plenarias_exp_materias/$id/view/excluir_exp_materias/$id_exp_materias\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#exp_materiasEdit".$id_exp_materias."'><i class='fas fa-pencil-alt'></i></div> 
                                            
                                    </td>
                                </tr>";
                                include("../mod_includes/modal/exp_materiasEdit.php");
                                include("../mod_includes/modal/exp_materiasLeitura.php");
                                include("../mod_includes/modal/exp_materiasVotacao.php");
                                include("../mod_includes/modal/exp_materiasVotacaoNominal.php");
                        }
                        echo "</table>";
                        
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }            
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
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