<?php
session_start();
include_once("mod_includes_portal/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");

?>
<!DOCTYPE html
    PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
    //include("header.php"); 
    include_once('url.php');
    ?>

</head>

<body>
    <main class="cd-main-content">
        <?php
        $id = $_GET['id'];

        # PEGA NOME SISTEMA E CLIENTE #
        $sql = "SELECT * FROM cadastro_clientes
                INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
                LEFT JOIN end_uf ON end_uf.uf_id = cadastro_clientes.cli_uf
                LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_clientes.cli_municipio
                WHERE cli_url = :cli_url AND sis_url = :sis_url AND cli_status = :cli_status";
        $stmt = $PDO->prepare($sql);
        $sistema = "proclegis";
        $stmt->bindParam(':cli_url', $cli_url);
        $stmt->bindParam(':sis_url', $sis_url);
        $stmt->bindValue(':cli_status', 1);
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $result = $stmt->fetch();
            $sis_nome = $result['sis_nome'];
            $sis_logo = $result['sis_logo'];
            $sis_dominio = $result['sis_dominio'];
            $cli_id = $result['cli_id'];
            $cli_nome = $result['cli_nome'];
            $cli_foto = $result['cli_foto'];
            $cli_cep = $result['cli_cep'];
            $uf_sigla = $result['uf_sigla'];
            $mun_nome = $result['mun_nome'];
            $cli_bairro = $result['cli_bairro'];
            $cli_endereco = $result['cli_endereco'];
            $cli_numero = $result['cli_numero'];
            $cli_comp = $result['cli_comp'];
            $cli_telefone = $result['cli_telefone'];
            $cli_site = $result['cli_site'];

        }



        $sql = "SELECT conteudo
                    FROM cadastro_normas_juridicas 
                WHERE id = :id	
                ";
        $stmt = $PDO_PROCLEGIS->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if ($rows > 0) {

            while ($result = $stmt->fetch()) {
                //echo "<center><img src='" . $cli_foto . "' width='90' align='center'></center>";

                echo $result['conteudo'];
            }

        }

        ?>
    </main> <!-- .cd-main-content -->
</body>

</html>