<script type="text/javascript" src="mod_includes/js/webcamjs-master/webcam.min.js"></script>
<script language="JavaScript">

    function bater_foto()
    {
    Webcam.snap(function(data_uri)
    {
    document.getElementById('results').innerHTML = '<img id="base64image" src="'+data_uri+'"/><input name="caminho_foto" type="hidden" value="'+data_uri+'">';
    });
    }

    function mostrar_camera()
    {
    Webcam.set({
        width: 320,
        height: 240,
        dest_width: 320,
        dest_height: 240,
        crop_width: 320,
        crop_height: 240,
        image_format: 'jpeg',
        jpeg_quality: 100,
        flip_horiz: true
    });
    Webcam.attach('#minha_camera');
    }

    function salvar_foto()
    {
    document.getElementById("carregando").innerHTML="Salvando, aguarde...";
    var file = document.getElementById("base64image").src;
    var formdata = new FormData();
    formdata.append("base64image", file);
    var ajax = new XMLHttpRequest();
    ajax.addEventListener("load", function(event) { upload_completo(event);}, false);
    ajax.open("POST", "upload.php");
    ajax.send(formdata);
    }

    function upload_completo(event)
    {
    document.getElementById("carregando").innerHTML="";
    var image_return=event.target.responseText;
    var showup=document.getElementById("completado").src=image_return;
    var showup2=document.getElementById("carregando").innerHTML='<b>Upload feito:</b>';
    }
    window.onload= mostrar_camera;
</script>