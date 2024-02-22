<?php 



?>

<div class='rodape'  style="background: <?php echo $cor_rodape ?>">

    <div id="rodape">



        <div class="bloco1">

           <img src='<?php echo $cli_foto;?>' >

        </div>



        <div class="bloco2">

            <p class="title"><?php echo $cli_nome ?></p>

            <p><?php echo $cli_endereco ?>, <?php echo $cli_numero ?><br>

            <?php echo $cli_bairro ?> - <?php echo $mun_nome ?>/<?php echo $uf_sigla ?> - CEP: <?php echo $cli_cep ?>

            </p>



            <p>Fone/Fax:  <?php echo $cli_telefone ?><br>
            <?php
            if($cli_whats){
                echo "Whatsapp: $cli_whats" ; 
            } ?><br>
           

        </div>



        <!-- <div class="bloco3">

        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3658.373727013234!2d-46.18650758484033!3d-23.519056965916896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94cdd83be1666c69%3A0xb9397e12be62f753!2sC%C3%A2mara%20Municipal%20de%20Mogi%20das%20Cruzes!5e0!3m2!1spt-BR!2sbr!4v1614148201873!5m2!1spt-BR!2sbr" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

        </div> -->



    </div>

    <div id="copy">

        <p>© <?php echo date('Y'); ?> Copyright - Todos os direitos reservados</p>

    </div>

</div>

