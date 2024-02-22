<?php
    include("../../core/mod_includes/php/connect.php");
    // define( 'MYSQL_HOST', 'localhost' );
    // define( 'MYSQL_PORT', '3306' );
    // define( 'MYSQL_USER', 'audipamc_mogicomp' );
    // define( 'MYSQL_PASSWORD', 'M0507c1106#' );
    // define( 'MYSQL_DB_NAME', 'audipamc_sistema' );
    // try
    // {
    //     $PDO				= new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );    
    // }
    // catch ( PDOException $e )
    // {
    //     echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
    // }
    // $PDO->exec("SET CHARACTER SET utf8");
    // $PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
    // error_reporting(E_ALL);
   
    $sql = "SELECT * FROM cadastro_clientes";       
    $stmt = $PDO->prepare($sql);    
    $stmt->execute();
    $rows = $stmt->rowCount();    
    if($rows > 0)
    {
        while($result = $stmt->fetch())             
        {               
            define( 'MYSQL_DB_NAME_PL', 'audipamc_proclegis_'.$result['cli_url'] );
            $PDO_PROCLEGIS 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_PL, MYSQL_USER, MYSQL_PASSWORD );
            $PDO_PROCLEGIS->exec("SET CHARACTER SET utf8");
            $PDO_PROCLEGIS->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );    
            
            $sql_usu = "SELECT * FROM cadastro_usuarios 
            LEFT JOIN log_login_usuarios as log1 ON log1.log_usuario = cadastro_usuarios.usu_id
            WHERE usu_status = :usu_status AND ativo = :ativo AND log1.log_id = (SELECT MAX(log2.log_id) FROM log_login_usuarios log2 where log2.log_usuario = log1.log_usuario)";
            $stmt_usu = $PDO_PROCLEGIS->prepare($sql_usu); 
            $stmt_usu->bindValue(':usu_status', 1);
            $stmt_usu->bindValue(':ativo', 1);
            $stmt_usu->execute();
            $rows_usu = $stmt_usu->rowCount();
            if($rows_usu > 0)
            {   
                while ($result_usu = $stmt_usu->fetch()){
                    if ($result_usu['log_observacao'] != 'Usuário desconectado por inatividade'){

                        echo "aaaaaaa"; 
                        if($result_usu['usu_tempo'] != '' || $result_usu['usu_tempo'] != Null){
                            $sql_log = "SELECT * FROM log_operacoes  WHERE lop_id_usuario = :lop_id_usuario
                            ORDER BY lop_id DESC";
                            $stmt_log = $PDO_PROCLEGIS->prepare($sql_log); 
                            $stmt_log->bindParam(':lop_id_usuario', $result_usu['usu_id']);
                            $stmt_log->execute();   
                            $rows_log = $stmt_log->rowCount();
                            if($rows_log > 0)
                            {   
                                $result_log = $stmt_log->fetch();
                                $ultimo_acesso = strtotime($result_log['lop_data']); 
                                $hora_atual  = strtotime(date("Y-m-d H:i:s")); 
                                $tempo = $result_usu['usu_tempo'] * 60;
                                $diferenca = $hora_atual - $ultimo_acesso;
                                if ($diferenca > $tempo){

                                    $log_hash = hash('sha512', $result_usu['usu_id'].date("Y-m-d H:i:s"));
                                    $log_observacao = "Usuário desconectado por inatividade"; 
                                    $sql_desativar = "INSERT INTO log_login_usuarios ( log_usuario, log_hash, log_observacao ) VALUES (:log_usuario, :log_hash, :log_observacao )";
                                    $stmt_desativar = $PDO_PROCLEGIS->prepare($sql_desativar); 
                                    $stmt_desativar->bindParam(':log_usuario', $result_usu['usu_id']);
                                    $stmt_desativar->bindParam(':log_hash', $log_hash);
                                    $stmt_desativar->bindParam(':log_observacao', $log_observacao);
                                    $stmt_desativar->execute();           
                                }
                            }     
                        }
                    }
                }
            }
        }
    }
    exit;
?>    