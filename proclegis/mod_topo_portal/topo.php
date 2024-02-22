<?php 


?>

<div class="topo" style="background: <?php echo $cor_topo ?>">

    <div class="faixa">

        <div id='faixa'>

            <div class='acess'>

                <div class='accessBloco hand'><a href='index/<?php echo $cli_url;?>' accesskey='0' title='Página inicial [ALT + 0]'>Página inicial [0]</a></div>
                <?php 
                    if($_SESSION['cliente']!='cmmc'){
                ?>
                <div class='accessBloco hand'><a href='vereadores-exercicio/' accesskey='1' title='Vereadores [ALT + 1]'>Vereadores [1]</a></div>

                <div class='accessBloco hand'><a href='sessoes/' accesskey='2' title='Sessões [ALT + 2]'>Sessões [2]</a></div>
                <?php } ?>
                <div class='accessBloco hand'><a href='materias/' accesskey='3' title='Matérias [ALT + 3]'>Matérias [3]</a></div>

                <div class='accessBloco hand'><a href='normas/' accesskey='4' title='Normas [ALT + 4]'>Normas [4]</a></div>

                <div class='accessBloco hand'><a accesskey='5' title='Contraste [ALT + 5]' onclick="modContrast(1)" >Contraste [5]</a></div>

                <div class='accessBloco hand'><a accesskey='6' title='Cor original [ALT + 6]' onclick="modContrast(2)">Cor original [6]</a></div>

                <div class='accessBloco hand'><a accesskey='7' title='Aumentar fonte [ALT + 7]' onClick="fonte('a');">A+ [7]</a></div>

                <div class='accessBloco hand'><a accesskey='8' title='Diminuir fonte [ALT + 8]' onClick="fonte('d');">A- [8]</a></div>

                <div class='accessBloco hand'><a accesskey='9' title='Fonte padrão [ALT + 9]' onClick="fonte('n');">Fonte padrão [9]</a></div>                

            </div>

        </div>

    </div>

    <div id='topo'>

        <div class='logo'>

            <a href='index/'><img src='<?php echo $cli_foto;?>' alt="Brasão" class='logo_c'></a>

            <a href='index/'><img src='<?php echo $cli_foto;?>' alt="<?php echo $cli_nome;?>" class='logo_w'></a>         

        </div>   

        <div class='title'>

            <b class='bold'><?php echo $cli_nome;?></b>

            <p class='normal'> Processo Legislativo</p>       

        </div>       

    </div>

    <div class='menu'>

        <div id='menu'>

            <?php include("mod_menu_portal/menu_site.php");  ?>   

    

        </div>

    </div>

</div>

