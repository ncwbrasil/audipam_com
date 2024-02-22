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
            <a href='./'>Início</a> <i style='font-size:22px; margin:0 3px; vertical-align:middle;' class="fas fa-caret-right"></i> Comissão
        </div>
	</header>
    <main>
        <div id='comissao'>
    	    <div class="wrapper">
                
                    
                <?php
                $id = $_GET['id'];
                $sql = "SELECT *, aux_comissoes_tipos.nome as tipo_nome,
                                  aux_comissoes_tipos.natureza as tipo_natureza,
                                    cadastro_comissoes.nome as nome,
                                    cadastro_comissoes.sigla as sigla 
                        FROM cadastro_comissoes 
                        LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo
                        WHERE cadastro_comissoes.id = :id 
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
                            <li><a data-toggle='tab' href='#composicao' id='composicao-tab'>Composição</a></li>        
                            <li><a data-toggle='tab' href='#reunioes' id='reunioes-tab'>Reuniões</a></li>
                            <li><a data-toggle='tab' href='#materias' id='materias-tab'>Matérias em tramitação</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                <div style='display:table; width:100%;'>";
                                while($result_int = $stmt_int->fetch())
                                {
                                    
                                    echo "
                                    <label class='lab'>Nome: </label>           <div class='info'> ".$result_int['nome']." &nbsp;</div><p>
                                    <label class='lab'>Sigla: </label>   <div class='info'> ".$result_int['sigla']." &nbsp;</div><p>
                                    <label class='lab'>Tipo: </label>        <div class='info'> ".$result_int['tipo_nome']." &nbsp;</div><p>
                                    <label class='lab'>Natureza: </label>        <div class='info'> ".$result_int['tipo_natureza']." &nbsp;</div><p>
                                    <label class='lab'>Unidade deliberativa: </label><div class='info'> ".$result_int['unidade_deliberativa']." &nbsp;</div><p>
                                    <label class='lab'>Data criação: </label>      <div class='info'> ".reverteData($result_int['data_criacao'])." &nbsp;</div><p>
                                    <label class='lab'>Data extinção: </label>           <div class='info'> ".reverteData($result_int['data_extincao'])." &nbsp;</div><p>
                                    ";
                                }    
                                echo "
                                </div>
                            </div>
                            <div id='composicao' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF); '>
                                <div class='bloco'>";
                                $sql = "SELECT *, cadastro_comissoes_composicao.id as id_composicao
                                                , cadastro_parlamentares.id as parlamentar_id
                                                , cadastro_parlamentares.nome as parlamentar_nome
                                                , aux_comissoes_cargos.descricao as descricao_cargo
                                        FROM cadastro_comissoes_composicao 
                                        LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo
                                        LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_comissoes_composicao.parlamentar
                                        LEFT JOIN aux_comissoes_cargos ON aux_comissoes_cargos.id = cadastro_comissoes_composicao.cargo
                                        WHERE comissao = :comissao
                                        ORDER BY cadastro_comissoes_composicao.id ASC
                                    ";
                                $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                            
                                $stmt_int->bindParam(':comissao', 	$id);                                    
                                $stmt_int->execute();
                                $rows_int = $stmt_int->rowCount();
                                if ($rows_int > 0)
                                {
                                    $c=0;
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "
                                        <a href='vereador/".$result_int['parlamentar_id']."'>
                                        <div class='blocos'>
                                            <div class='vereadores-foto' style='background:url(".str_replace("../","",$result_int['foto']).") top center no-repeat; background-size: 80%; border-radius:60px; width:80px; height:80px; border:1px solid #CCC;' border='0'></div>
                                            <div class='vereadores-nome'><span class='bold'>".$result_int['parlamentar_nome']."</span><br>".$result_int['descricao_cargo']."</div>
                                        </div>
                                        </a>";                                
                                    }                                        
                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }    

                                echo "
                                </div>
                            </div>
                            <div id='reunioes' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
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
                            <div id='materias' class='tab-pane fade in' style='background:linear-gradient(to top, #F6F6F6, #FFF);'>
                                ";
                                $sql = "SELECT *, cadastro_materias.id as materia_id
                                                , aux_materias_tipos.nome as tipo_materia
                                        FROM cadastro_materias 
                                        LEFT JOIN ( cadastro_materias_tramitacao 
                                            LEFT JOIN aux_materias_unidade_tramitacao ON aux_materias_unidade_tramitacao.id = cadastro_materias_tramitacao.unidade_destino)
                                        ON cadastro_materias_tramitacao.materia = cadastro_materias.id
                                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo
                                        LEFT JOIN cadastro_materias_tramitacao h1 ON h1.materia = cadastro_materias.id 
			                            WHERE h1.id = (SELECT MAX(h2.id) FROM cadastro_materias_tramitacao h2 where h2.materia = h1.materia) AND
                                              aux_materias_unidade_tramitacao.comissao = :comissao
                                        ORDER BY cadastro_materias.ano DESC, cadastro_materias.numero DESC
                                    ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                            
                                $stmt->bindParam(':comissao', 	$id);                                    
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
                                                        <span class='bold'>".$result['tipo_materia']." ".$result['numero']."/".$result['ano']."</span><br>
                                                        <span class='bold'>Ementa:</span> ".$result['ementa']."<br>
                                                        <span class='bold'>Data apresentação:</span> ".reverteData($result['data_apresentacao'])."<br>
                                                        <span class='bold'>Autor(es):</span> ".implode(", ",$autor)."<br>
                                                        "; if($result['texto_original']){ echo "<span class='bold'>Texto original:</span> <a href='".str_replace("../","",$result['texto_original'])."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        
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
