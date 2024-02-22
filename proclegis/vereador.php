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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Vereador
        </div>
	</header>
    <main>
        <div id='vereadores'>
    	    <div class="wrapper">
                
                    
                <?php
                $id = $_GET['id'];
                $sql = "SELECT *,cadastro_parlamentares.id as parlamentar_id, cadastro_parlamentares.nome as parlamentar_nome FROM cadastro_parlamentares
                        LEFT JOIN ( cadastro_parlamentares_mandatos 
                            LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_parlamentares_mandatos.legislatura )
                        ON cadastro_parlamentares_mandatos.parlamentar =  cadastro_parlamentares.id 
                        LEFT JOIN ( cadastro_parlamentares_filiacoes 
                            LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido )
                        ON cadastro_parlamentares_filiacoes.parlamentar =  cadastro_parlamentares.id 
                        WHERE cadastro_parlamentares.id  = :id
                        GROUP BY cadastro_parlamentares.id
                        ";
                $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                $hoje = date("Y");                                                     
                $stmt_int->bindParam(":id", $id);                                                           
                
                $stmt_int->execute();
                $rows_int = $stmt_int->rowCount();
                if($rows_int > 0)
                {
                    echo "
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#mandatos' id='mandatos-tab'>Mandatos</a></li>        
                            <li><a data-toggle='tab' href='#filiacoes' id='filiacoes-tab'>Filiações</a></li>
                            <li><a data-toggle='tab' href='#proposituras' id='proposituras-tab'>Proposituras</a></li>
                            <li><a data-toggle='tab' href='#materias' id='materias-tab'>Matérias Legislativas</a></li>
                            <li><a data-toggle='tab' href='#leis' id='leis-tab'>Leis</a></li>                 
                            <li><a data-toggle='tab' href='#comissoes' id='comissoes-tab'>Comissões</a></li>    
                            <li><a data-toggle='tab' href='#mesa' id='mesa-tab'>Mesa Diretora</a></li>  
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                <div style='display:table; width:100%;'>";
                                while($result_int = $stmt_int->fetch())
                                {
                                    
                                    echo "
                                    <div class='vereadores-foto' style='background:url(".str_replace("../","",$result_int['foto']).") top center no-repeat; background-size: 80%; border-radius:60px; width:80px; height:80px; border:1px solid #CCC;' border='0'></div>
                                    <label class='lab'>Nome: </label>           <div class='info'> ".$result_int['parlamentar_nome']." &nbsp;</div><p>
                                    <label class='lab'>Nome popular: </label>   <div class='info'> ".$result_int['apelido']." &nbsp;</div><p>
                                    <label class='lab'>Partido: </label>        <div class='info'> ".$result_int['sigla']." &nbsp;</div><p>
                                    <label class='lab'>Data nascimento: </label><div class='info'> ".reverteData($result_int['data_nasc'])." &nbsp;</div><p>
                                    <label class='lab'>Profissão: </label>      <div class='info'> ".$result_int['profissao']." &nbsp;</div><p>
                                    <label class='lab'>Site: </label>           <div class='info'> ".$result_int['site']." &nbsp;</div><p>
                                    <label class='lab'>E-mail: </label>         <div class='info'> ".$result_int['email']." &nbsp;</div><p>
                                    <label class='lab'>Telefone: </label>       <div class='info'> ".$result_int['telefone']." &nbsp;</div><p>
                                    <label class='lab'>Nº gabinete: </label>    <div class='info'> ".$result_int['gabinete']." &nbsp;</div><p>   
                                    <label class='lab'>Biografia: </label>    <div class='info'> ".$result_int['biografia']." &nbsp;</div><p>                                         
                                    ";
                                }    
                                echo "
                                </div>
                            </div>
                            <div id='mandatos' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF); '>
                                ";
                                $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim,
                                        cadastro_parlamentares_mandatos.votos as votos,
                                        cadastro_parlamentares_mandatos.id as id_mandato
                                        FROM cadastro_parlamentares_mandatos 
                                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_parlamentares_mandatos.legislatura
                                        LEFT JOIN aux_parlamentares_coligacoes ON aux_parlamentares_coligacoes.id = cadastro_parlamentares_mandatos.coligacao
                                        LEFT JOIN aux_parlamentares_tipo_afastamento ON aux_parlamentares_tipo_afastamento.id = cadastro_parlamentares_mandatos.tipo_afastamento
                                        WHERE parlamentar = :parlamentar
                                        ORDER BY cadastro_parlamentares_mandatos.id DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                            
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Legislatura</td>
                                            <td class='titulo_tabela'>Votos</td>
                                            <td class='titulo_tabela'>Coligação</td>
                                            <td class='titulo_tabela'>Período</td>                                            
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_mandato = $result['id_mandato'];
                                            $votos = $result['votos'];
                                            $coligacao_id = $result['coligacao'];
                                            $tipo_afastamento = $result['tipo_afastamento'];
                                            $tipo_afastamento_n = $result['descricao'];
                                            $coligacao = $result['nome'];
                                            $legislatura_id = $result['legislatura'];
                                            $legislatura = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                                            $data_inicio_mandato = reverteData($result['data_inicio_mandato']);
                                            $data_fim_mandato = reverteData($result['data_fim_mandato']);
                                            $observacao = $result['observacao'];
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$legislatura</td>                                                    
                                                    <td>$votos</td>
                                                    <td>$coligacao</td>
                                                    <td>$data_inicio_mandato a $data_fim_mandato</td>                                                
                                                </tr>";                                                
                                        }                                        
                                        echo "</table>"; 
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }    

                                echo "
                            </div>
                            <div id='filiacoes' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                ";
                                $sql = "SELECT *,                                         
                                        cadastro_parlamentares_filiacoes.id as id_filiacoes
                                        FROM cadastro_parlamentares_filiacoes 
                                        LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido
                                        WHERE parlamentar = :parlamentar
                                        ORDER BY cadastro_parlamentares_filiacoes.id DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela' colspan='2'>Partido</td>
                                            <td class='titulo_tabela'>Data Filiação</td>
                                            <td class='titulo_tabela'>Data Desfiliação</td>                                            
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_filiacoes = $result['id_filiacoes'];
                                            $partido = $result['partido'];
                                            $partido_sigla = $result['sigla'];
                                            $partido_nome = $result['nome'];                                           
                                            $partido_logo = str_replace("../","",$result['logo']);                                           
                                            $data_filiacao = reverteData($result['data_filiacao']);
                                            $data_desfiliacao = reverteData($result['data_desfiliacao']);                                            
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td width='1'><div class='perfil' style='background:url($partido_logo) center center; background-size: cover; border-radius:50px; width:50px; height:50px;' border='0'></div> </td>
                                                    <td>$partido_sigla - $partido_nome</td>
                                                    <td>$data_filiacao</td>
                                                    <td>$data_desfiliacao</td>                                                    
                                                </tr>";                                            
                                        }
                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                            <div id='proposituras' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                            </div>
                            <div id='materias' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                ";
                                $sql = "SELECT *, cadastro_materias.id as materia_id
                                                , aux_materias_tipos.nome as tipo_materia
                                        FROM cadastro_materias 
                                        LEFT JOIN ( cadastro_materias_autoria 
                                            LEFT JOIN (aux_autoria_autores 
                                                LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                                            ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                                        ON cadastro_materias_autoria.materia = cadastro_materias.id
                                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo
                                        WHERE cadastro_parlamentares.id = :parlamentar
                                        ORDER BY cadastro_materias.ano DESC, cadastro_materias.numero DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                            
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                if ($rows > 0)
                                {
                                    echo "
                                    Total de <span class='bold'>$rows</span> matérias legislativas.
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        ";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                             // AUTORES
                                            $autor=array();
                                            $sql = "SELECT *
                                                    FROM cadastro_materias_autoria
                                                    LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
                                                    WHERE cadastro_materias_autoria.materia = :materia	";
                                            $stmt_aut = $PDO_PROCLEGIS->prepare($sql);                                
                                            $stmt_aut->bindParam(':materia', 	$result['materia_id']);                                
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
                                                    <td>
                                                        <a href='materia_legislativa/" . $result['id'] . "'>
                                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(" . $permissoes["view"] . ",\"" . $pagina_link . "/exib/$id?pag=$pag&fil_tipo=$fil_tipo\");'>
                                                            " . $result['tipo_materia'] . " " . $result['numero'] . " de " . $result['ano'] . " 
                                                        </p>
                                                        <span class='bold'>Ementa:</span> ".$result['ementa']."<br>
                                                        <span class='bold'>Data apresentação:</span> ".reverteData($result['data_apresentacao'])."<br>
                                                        <span class='bold'>Autor(es):</span> ".implode(", ",$autor)."<br>
                                                        "; if($result['texto_original']){ echo "<span class='bold'>Texto original:</span> <a href='".str_replace("../","",$result['texto_original'])."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                                        </a>
                                                    </td>                                                
                                                </tr>";                                                
                                        }                                        
                                        echo "</table>"; 
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }    

                                echo "
                            </div>
                            <div id='leis' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                            </div>
                            <div id='comissoes' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                ";
                                $sql = "SELECT *, cadastro_comissoes.nome as comissao_nome
                                                , cadastro_comissoes.sigla as comissao_sigla
                                                , aux_comissoes_tipos.nome as comissao_tipo
                                                , aux_comissoes_cargos.descricao as cargo_comissao
                                        FROM cadastro_comissoes_composicao 
                                        LEFT JOIN (cadastro_comissoes 
                                            LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo)
                                        ON cadastro_comissoes.id = cadastro_comissoes_composicao.comissao
                                        LEFT JOIN cadastro_parlamentares  ON cadastro_parlamentares.id = cadastro_comissoes_composicao.parlamentar
                                        LEFT JOIN aux_comissoes_cargos ON aux_comissoes_cargos.id = cadastro_comissoes_composicao.cargo                                                                                                                
                                        WHERE cadastro_comissoes_composicao.parlamentar = :parlamentar
                                        GROUP BY cadastro_comissoes.id
                                        ORDER BY data_designacao DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Cargo</td>
                                            <td class='titulo_tabela'>Comissão</td>
                                            <td class='titulo_tabela'>Tipo</td>
                                            <td class='titulo_tabela'>Data Designação</td>   
                                            <td class='titulo_tabela'>Data Desigamento</td>                                            
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $cargo_comissao = $result['cargo_comissao'];
                                            $comissao_nome        = $result['comissao_nome'];
                                            $comissao_tipo  = $result['comissao_tipo'];
                                            $comissao_sigla  = $result['comissao_sigla'];
                                            $data_designacao = reverteData($result['data_designacao']);
                                            $data_desligamento = reverteData($result['data_desligamento']);                                            
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$cargo_comissao</td>
                                                    <td>$comissao_nome - $comissao_sigla</td>
                                                    <td>$comissao_tipo</td>
                                                    <td>$data_designacao</td>
                                                    <td>$data_desligamento</td>                                                    
                                                </tr>";                                            
                                        }
                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                            <div id='mesa' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                ";
                                $sql = "SELECT *, aux_mesa_diretora_sessoes.tipo as mesa_tipo
                                                , aux_mesa_diretora_cargos.descricao as cargo_mesa
                                        FROM cadastro_mesa_diretora_composicao 
                                        LEFT JOIN (cadastro_mesa_diretora 
                                            LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
                                            LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao)
                                        ON cadastro_mesa_diretora.id = cadastro_mesa_diretora_composicao.mesa_diretora
                                        LEFT JOIN cadastro_parlamentares  ON cadastro_parlamentares.id = cadastro_mesa_diretora_composicao.parlamentar
                                        LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_mesa_diretora_composicao.cargo                                                                                                                
                                        WHERE cadastro_mesa_diretora_composicao.parlamentar = :parlamentar
                                        GROUP BY cadastro_mesa_diretora.id
                                        ORDER BY aux_mesa_diretora_sessoes.data_inicio DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Cargo</td>
                                            <td class='titulo_tabela'>Sessão Legislativa</td> 
                                            <td class='titulo_tabela'>Tipo</td>
                                                                                   
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $cargo_mesa = $result['cargo_mesa'];
                                            $mesa_tipo  = $result['mesa_tipo'];
                                            $comissao_sigla  = $result['comissao_sigla'];
                                            $data_inicio = reverteData($result['data_inicio']);
                                            $data_fim = reverteData($result['data_fim']);                                            
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$cargo_mesa</td>
                                                    <td>$data_inicio - $data_fim</td>
                                                    <td>$mesa_tipo</td>                                                  
                                                </tr>";                                            
                                        }
                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                        </div>";
                    
                    
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
