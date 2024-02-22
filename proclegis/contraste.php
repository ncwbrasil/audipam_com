<?php    
    session_start();
    $contraste = $_POST['contraste'];
    $_SESSION['contraste'] = $contraste; 
    echo $_SESSION['contraste']."aaaa";
?>  