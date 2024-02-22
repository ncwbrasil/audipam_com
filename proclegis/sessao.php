<?php
session_start();

?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<head>
	<?php 
		include('header.php'); 
		$pagina = 'home'; 
	?>

</head>
<body>
    <header>
		<?php 
        
			#region MOD INCLUDES
			include('mod_topo_portal/topo.php');
			include('banner.php');
            include('mod_includes_portal/php/funcoes-jquery.php');			
			#endregion
		?>
        <div class='naveg'>
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Sessão
        </div>
	</header>
    <main>
        <div id='sessoes'>
    	    <div class="wrapper">         
                    
                <?php
                $id = $_GET['id'];
                $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                                , cadastro_sessoes_plenarias.nome as nome
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
                        WHERE cadastro_sessoes_plenarias.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    echo "
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#abertura' id='abertura-tab'>Abertura</a></li>        
                            <li><a data-toggle='tab' href='#ordem_dia' id='ordem_dia-tab'>Ordem do Dia</a></li>    
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' >
                                <div style='display:table; width:100%'>
                                        <div class='exib_label'><span class='bold'>Nome Sessão:</span> ".$result['nome']."</div>
                                        <div class='exib_label'><span class='bold'>Legislatura:</span> ".$result['numero_legislatura']." (".$result['data_inicio_legislatura']." - ".$result['data_fim_legislatura'].") &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Sessão:</span> ".$result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].") &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Tipo de Sessão:</span> ".$result['descricao']."&nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Número:</span> ".$result['numero']." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Data abertura:</span> ".reverteData($result['data_abertura'])." às ".substr($result['hora_abertura'],0,5)." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Iniciada?</span> ".$result['iniciada']." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Data encerramento:</span> ".reverteData($result['data_encerramento'])." às ".substr($result['hora_encerramento'],0,5)." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Finalizada? </span> ".$result['finalizada']." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Pauta:</span> "; ;if($result['pauta'] != ''){ echo "<a href='".$result['pauta']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Ata:</span> "; ;if($result['ata'] != ''){ echo "<a href='".$result['ata']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>Anexo:</span> "; ;if($result['anexo'] != ''){ echo "<a href='".$result['anexo']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>URL Áudio:</span> ".$result['url_audio']." &nbsp;</div>
                                        <div class='exib_label'><span class='bold'>URL Vídeo:</span> ".$result['url_video']." &nbsp;</div>";
                                        if($result['tema_solene'] != "")
                                        {
                                            echo "<div class='exib_label'><span class='bold'>Tema Solene:</span> ".$result['tema_solene']." &nbsp;</div>";
                                        }
                                    echo "
                                </div>                                                                                                                                              
                            </div>                        
                            <div id='abertura' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <select id='modulo' name='modulo'>
                                        <option value='mesa'> Mesa </option>
                                        <option value='presenca'> Presença </option>
                                        <option value='justificativas'> Justificativas de Ausências </option>
                                        <option value='oradores'> Oradores das Explicações Pessoais </option>
                                        <option value='ocorrencia'> Ocorrências da Sessão </option>
                                        <option value='retirada'> Retirada de Pauta </option>
                                    </select>
                                </div>
                                <div class='bloco1' id='mesa1' style='display:table'>
                                    <center class='bold'> Mesa </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_mesa.id as id                            
                                            FROM cadastro_sessoes_plenarias_mesa
                                            LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_mesa.sessao_plenaria                     
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_mesa.parlamentar                     
                                            LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_sessoes_plenarias_mesa.cargo
                                            WHERE sessao_plenaria = :sessao_plenaria		
                                            ORDER BY cadastro_sessoes_plenarias_mesa.id ASC";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                    $stmt->bindParam(':sessao_plenaria', 	$id);
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if($rows > 0)
                                    {
                                        while($result = $stmt->fetch())
                                        {
                                            echo $result['nome'];
                                        }
                                    }
                                    else {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }
                                echo"</div>

                                <div class='bloco1' id='presenca1'>
                                    <center class='bold'> Presença </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_presenca.id as id                            
                                    FROM cadastro_sessoes_plenarias_presenca
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_presenca.sessao_plenaria 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_presenca.parlamentar                    
                                    WHERE sessao_plenaria = :sessao_plenaria	
                                    ORDER BY cadastro_sessoes_plenarias_presenca.id ASC";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                            
                                    $stmt_int->bindParam(':sessao_plenaria', 	$id);
                                    $stmt_int->execute();
                                    $rows_int = $stmt_int->rowCount();
                                    if($rows_int > 0)
                                    {
                                        while($result_int =  $stmt_int->fetch()){
                                            echo "<p>".$result_int['nome']."</p>";
                                        }
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }
                                echo "</div>

                                <div class='bloco1' id='justificativas1'>
                                    <center class='bold'> Justificativas de Ausências </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_ab_ausencias.id as id
                                    FROM cadastro_sessoes_plenarias_ab_ausencias 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_ab_ausencias.parlamentar                     
                                    LEFT JOIN aux_sessoes_plenarias_tipo_justificativa ON aux_sessoes_plenarias_tipo_justificativa.id = cadastro_sessoes_plenarias_ab_ausencias.tipo_justificativa                     
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_ab_ausencias.sessao_plenaria                     
                                    WHERE cadastro_sessoes_plenarias_ab_ausencias.sessao_plenaria = :sessao_plenaria				
                                    ORDER BY cadastro_sessoes_plenarias_ab_ausencias.id DESC";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);   
                                    $stmt->bindParam(':sessao_plenaria', 	$id); 
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if ($rows > 0)
                                    {
                                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Parlamentar</td>
                                                <td class='titulo_tabela'>Justificativa</td>                                            
                                                <td class='titulo_tabela'>Data</td>
                                                <td class='titulo_tabela'>Observação</td>
                                            </tr>
                                            ";
                                            $c=0;
                                            while($result = $stmt->fetch())
                                            {
                                                $id_ausencias = $result['id'];
                                                $parlamentar = $result['parlamentar'];
                                                $nome = $result['nome'];
                                                $tipo_justificativa = $result['tipo_justificativa'];
                                                $descricao = $result['descricao'];
                                                $data = reverteData($result['data']);
                                                $horario = substr($result['horario'],0,5);
                                                $observacao = $result['observacao'];
                                            
                                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                echo "<tr class='$c1'>
                                                        <td>".$result['nome']."</td>
                                                        <td>".$result['descricao']."</td>
                                                        <td>".reverteData($result['data'])."<br>".substr($result['horario'],0,5)."</td>
                                                        <td>".$result['observacao']."</td>                                                                    
                                                    </tr>";
                                            }
                                            echo "</table>";                                            
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }
                    
                                echo"</div>

                                <div class='bloco1' id='oradores1'>
                                    <center class='bold'> Oradores das Explicações Pessoais </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_oradores_explicacoes.id as id
                                    , cadastro_sessoes_plenarias_oradores_explicacoes.url_video as url_video    
                                    FROM cadastro_sessoes_plenarias_oradores_explicacoes 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_oradores_explicacoes.parlamentar                     
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_oradores_explicacoes.sessao_plenaria                     
                                    WHERE cadastro_sessoes_plenarias_oradores_explicacoes.sessao_plenaria = :sessao_plenaria	
                                    ORDER BY cadastro_sessoes_plenarias_oradores_explicacoes.id DESC ";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                    $stmt->bindParam(':sessao_plenaria', 	$id); 
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if ($rows > 0)
                                    {
                                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Parlamentar</td>
                                                <td class='titulo_tabela'>Ordem pronunciamento</td>                                            
                                                <td class='titulo_tabela'>URL vídeo</td>
                                                <td class='titulo_tabela'>Observação</td>
                                            </tr>
                                            ";
                                            $c=0;
                                            while($result = $stmt->fetch())
                                            {
                                                $id_oradores_explicacoes = $result['id'];
                                                $parlamentar = $result['parlamentar'];
                                                $nome = $result['nome'];
                                                $ordem = $result['ordem'];
                                                $url_video = $result['url_video'];
                                                $observacao = $result['observacao'];
                                            
                                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                echo "<tr class='$c1'>
                                                        <td>".$result['nome']."</td>
                                                        <td>".$ordem."</td>
                                                        <td>".$url_video."</td>
                                                        <td>".$observacao."</td>                                                                    
                                                    </tr>";
                                            }
                                        echo "</table>";
                                            
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }
                                                                
                                echo"</div>

                                <div class='bloco1' id='ocorrencia1'>";
                                
                                echo"</div>

                                <div class='bloco1' id='retirada1'>
                                    <center class='bold'> Retirada de Pauta </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_ab_retirada_pauta.id as id
                                    , t1.numero as numero_od
                                    , t1.ano as ano_od
                                    , t2.numero as numero_exp
                                    , t2.ano as ano_exp
                                    , b1.nome as nome_od
                                    , b2.nome as nome_exp
                                    , cadastro_parlamentares.nome as nome
                                    FROM cadastro_sessoes_plenarias_ab_retirada_pauta 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_ab_retirada_pauta.parlamentar                     
                                    LEFT JOIN aux_sessoes_plenarias_tipo_retirada_pauta ON aux_sessoes_plenarias_tipo_retirada_pauta.id = cadastro_sessoes_plenarias_ab_retirada_pauta.tipo_retirada                     
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_ab_retirada_pauta.sessao_plenaria                     
                                    LEFT JOIN (cadastro_materias t1 
                                        LEFT JOIN aux_materias_tipos b1 ON b1.id = t1.tipo)
                                    ON t1.id = cadastro_sessoes_plenarias_ab_retirada_pauta.materia_ordem_dia
                                    LEFT JOIN (cadastro_materias t2 
                                        LEFT JOIN aux_materias_tipos b2 ON b2.id = t2.tipo)
                                    ON t2.id = cadastro_sessoes_plenarias_ab_retirada_pauta.materia_expediente                   
                                    WHERE cadastro_sessoes_plenarias_ab_retirada_pauta.sessao_plenaria = :sessao_plenaria	
                                    ORDER BY cadastro_sessoes_plenarias_ab_retirada_pauta.id DESC";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                    $stmt->bindParam(':sessao_plenaria', 	$id);    
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if ($rows > 0)
                                    {
                                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Matéria</td>
                                                <td class='titulo_tabela'>Requerente</td>
                                                <td class='titulo_tabela'>Tipo de retirada</td>                                            
                                                <td class='titulo_tabela'>Data</td>
                                                <td class='titulo_tabela'>Observação</td>
                                                <td class='titulo_tabela' align='right'>Gerenciar</td>
                                            </tr>
                                            ";
                                            $c=0;
                                            while($result = $stmt->fetch())
                                            {
                                                $id_retirada_pauta = $result['id'];
                                                $parlamentar = $result['parlamentar'];
                                                $nome = $result['nome'];
                                                $tipo_retirada = $result['tipo_retirada'];
                                                $descricao = $result['descricao'];
                                                $data = reverteData($result['data']);                    
                                                $materia_ordem_dia = $result['materia_ordem_dia'];
                                                $materia_expediente = $result['materia_expediente'];
                                                $materia_od = $materia_exp = "";        
                                                if($result['numero_od'] != "" && $result['ano_od'] != "")
                                                {
                                                    $materia_od = $result['nome_od']. " Nº ".$result['numero_od']." de ".$result['ano_od'];
                                                }
                                                elseif($result['numero_exp'] != "" && $result['ano_exp'] != "")
                                                {
                                                    $materia_exp = $result['nome_exp']. " Nº ".$result['numero_exp']. " de ".$result['ano_exp'];
                                                }
                                                $observacao = $result['observacao'];
                                            
                                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                echo "<tr class='$c1'>
                                                        <td>".$materia_od.$materia_exp."</td>
                                                        <td>".$result['nome']."</td>
                                                        <td>".$result['descricao']."</td>
                                                        <td>".reverteData($result['data'])."<br>".substr($result['horario'],0,5)."</td>
                                                        <td>".$result['observacao']."</td>                                                                    
                                                    </tr>";
                                            }
                                        echo "</table>";
                                            
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }   
                                
                                echo"</div>
                            </div>

                            <div id='ordem_dia' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <select id='modulo' name='modulo'>
                                        <option value='m_ordens'> Matérias Ordem do Dia </option>
                                        <option value='p_ordens'> Presença Ordem do Dia </option>
                                        <option value='o_ordens'> Oradores Ordem Do Dia </option>
                                        <option value='v_ordens'> Votação em Bloco</option>
                                    </select>
                                </div>

                                <div class='bloco1' id='m_ordens1' style='display:table'>
                                    <center class='bold'>  Matérias Ordem do Dia </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_od_materias.id as id 
                                    , cadastro_materias.numero as numero , aux_materias_tipos.nome as tipo_materia
                                    FROM cadastro_sessoes_plenarias_od_materias 
                                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_sessoes_plenarias_od_materias.tipo_materia                     
                                    LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_sessoes_plenarias_od_materias.materia                     
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_od_materias.sessao_plenaria                     
                                    WHERE cadastro_sessoes_plenarias_od_materias.sessao_plenaria = :sessao_plenaria		
                                    ORDER BY cadastro_sessoes_plenarias_od_materias.ordem ASC";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                    $stmt->bindParam(':sessao_plenaria', 	$id); 
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if ($rows > 0)
                                    {
                                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Ordem</td>                                            
                                                <td class='titulo_tabela'>Matéria</td>
                                                <td class='titulo_tabela'>Tipo de votação</td>
                                                <td class='titulo_tabela'>Observação</td>
                                                <td class='titulo_tabela'>Resultado</td>
                                            </tr>
                                            ";
                                            $c=0;
                                            while($result = $stmt->fetch())
                                            {
                                                $id_od_materias = $result['id'];
                                                $tipo_materia = $result['tipo_materia'];
                                                $sigla = $result['sigla'];
                                                $nome = $result['nome'];
                                                $materia = $result['materia'];
                                                $numero = $result['numero'];
                                                $ano = $result['ano'];
                                            
                                                $ordem = $result['ordem'];
                                                $tipo_votacao = $result['tipo_votacao'];
                                                $observacao = $result['observacao'];
                    
                                                // AUTORES
                                                $autor=array();
                                                $sql = "SELECT *
                                                        FROM cadastro_materias_autoria
                                                        LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
                                                        WHERE cadastro_materias_autoria.materia = :materia	";
                                                $stmt_aut = $PDO_PROCLEGIS->prepare($sql);                                
                                                $stmt_aut->bindParam(':materia', 	$materia);                                
                                                $stmt_aut->execute();
                                                $rows_aut = $stmt_aut->rowCount();
                                                if($rows_aut > 0)
                                                {
                                                    while($result_aut = $stmt_aut->fetch())
                                                    {
                                                        $autor[] = $result_aut['nome'];
                                                    }
                                                }
                                            
                                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                echo "<tr class='$c1'>
                                                    <td>".$ordem."</td>
                                                    <td>
                                                        <a href='materia_legislativa/" . $result['id'] . "'>
                                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag&fil_tipo=$fil_tipo\");'>
                                                            " . $result['tipo_materia'] . " " . $result['numero'] . "/" . $result['ano'] . " 
                                                        </p>
                                                        <span class='bold'>Ementa:</span> ".substr($result['ementa'],0,100)."...<p>                                        
                                                        <span class='bold'>Autor(es):</span> ".implode(", ",$autor)."<p>
                                                        </a>
                                                    </td>
                                                    <td>".$tipo_votacao."</td>
                                                    <td>".$observacao."</td> 
                                                    <td>".$resultado."</td>                                                                
                                                </tr>";
                                            }
                                            echo "</table>";
                                            
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }
    
                                echo"</div>

                                <div class='bloco1' id='p_ordens1'>
                                    <center class='bold'>  Presença Ordem do Dia </center>";   
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_od_presenca.id as id                            
                                    FROM cadastro_sessoes_plenarias_od_presenca
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_od_presenca.sessao_plenaria 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_od_presenca.parlamentar                    
                                    WHERE sessao_plenaria = :sessao_plenaria		
                                    ORDER BY cadastro_sessoes_plenarias_od_presenca.id ASC";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                            
                                    $stmt_int->bindParam(':sessao_plenaria', 	$id);
                                    $stmt_int->execute();
                                    $rows_int = $stmt_int->rowCount();
                                    if($rows_int > 0)
                                    {
                                        while($result = $stmt_int->fetch()){
                                            echo "<p class='bold'> ".$result['nome']." </p>";
                                        }
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }

                                echo"</div>

                                <div class='bloco1' id='o_ordens1'>
                                    <center class='bold'>  Oradores Ordem Do Dia </center>";
                                    $sql = "SELECT *, cadastro_sessoes_plenarias_od_oradores.id as id
                                    , cadastro_sessoes_plenarias_od_oradores.url_video as url_video    
                                    FROM cadastro_sessoes_plenarias_od_oradores 
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_od_oradores.parlamentar                     
                                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_od_oradores.sessao_plenaria                     
                                    WHERE cadastro_sessoes_plenarias_od_oradores.sessao_plenaria = :sessao_plenaria	
                                    ORDER BY cadastro_sessoes_plenarias_od_oradores.id DESC";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                    $stmt->bindParam(':sessao_plenaria', 	$id); 
                                    $stmt->execute();
                                    $rows = $stmt->rowCount();
                                    if ($rows > 0)
                                    {   
                                        echo "
                                        <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                            <tr>
                                                <td class='titulo_tabela'>Parlamentar</td>
                                                <td class='titulo_tabela'>Ordem pronunciamento</td>                                            
                                                <td class='titulo_tabela'>Observação</td>
                                            </tr>
                                            ";
                                            $c=0;
                                            while($result = $stmt->fetch())
                                            {
                                                $id_od_oradores = $result['id'];
                                                $parlamentar = $result['parlamentar'];
                                                $nome = $result['nome'];
                                                $ordem = $result['ordem'];
                                                $url_video = $result['url_video'];
                                                $observacao = $result['observacao'];
                                            
                                                if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                                echo "<tr class='$c1'>
                                                        <td>".$result['nome']."</td>
                                                        <td>".$ordem."</td>
                                                        <td>".$observacao."</td>                                                                    
                                                    </tr>";
                                            }
                                            echo "</table>";                                            
                                    }
                                    else
                                    {
                                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                    }                            
                                echo"</div>

                                <div class='bloco1' id='v_ordens1'>
                                <center class='bold'>  Votação em Bloco </center>";
                                
                                echo"</div>
                            </div>
                        </div>
                    ";
                }
                ?>                  
            </div>  
        </div>
        <br>
        <?php
		    include('mod_rodape_portal/rodape.php');
		?>
    </main>

</body>
</html>


<script>
	$('select[name=modulo]').change(function(){
        var modulo = $(this).val(); 
        $('#sessoes .bloco1').css('display','none'); 
        $('#'+modulo+'1').css('display','table'); 
    })				
</script>


