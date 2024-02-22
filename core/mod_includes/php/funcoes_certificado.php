<?php
use ddn\sapp\PDFDoc;

require_once('../../vendor/autoload.php');

function recuperaDadosCertificado($cert, $pass)
{
    $certfilecontent = file_get_contents($cert);
    openssl_pkcs12_read($certfilecontent, $certificate, $pass);
    $CertPriv   = openssl_x509_parse(openssl_x509_read($certificate['cert']));
    return $CertPriv;
}

function ghostScript($doc, $doc_old)
{
    if(php_uname('s') == "Windows NT")
    {    
        //gs -dPDFA -dBATCH -dNOPAUSE -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -sPDFACompatibilityPolicy=1 -sOutputFile=output_filename.pdf input_filename.pd
        //$command = shell_exec('"c:\Program Files\gs\gs9.54.0\bin\gswin64c" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile="'. $doc.'" "'.$doc_old.'" 2>&1');
        $command = shell_exec('"c:\Program Files\gs\gs9.54.0\bin\gswin64c" -dPDFA -dBATCH -dNOPAUSE -dNOOUTERSAVE  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite  -sOutputFile="'. $doc.'" PDFA_def.ps  "'.$doc_old.'" 2>&1');
    }
    else
    {
        // APENAS CONVERTE
        //$command = exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile="'. $doc.'" "'.$doc_old.'" 2>&1'); 
        
        // CONVERTE PARA PDF/A (PESQUISAVEL)
        $command = exec('gs -dPDFA -dBATCH -dNOPAUSE -dUseCIEColor -dNOOUTERSAVE -sDEVICE=pdfwrite  -dPDFACompatibilityPolicy=2 -sOutputFile="'. $doc.'" PDFA_def.ps "'.$doc_old.'" ');    
   
    }
    return $command;
}

function assinaDocumento($cert, $pass, $doc, $dados_ass, $data_ass, $x_ass = 1)
{    
    //exit;
    //error_reporting(_E_ALL);
    $return = array();
    $doc_old = $doc;

    // LÊ DOCUMENTO
   
    $file_content = file_get_contents($doc);    
    $obj = PDFDoc::from_string($file_content);          
    $doc = substr($doc_old,0,-4).'_cvt'.substr($doc_old,-4);
    $command = shell_exec('gs -dPDFA -dBATCH -dNOPAUSE -dUseCIEColor -dNOOUTERSAVE -sDEVICE=pdfwrite  -dPDFACompatibilityPolicy=2 -sOutputFile="'. $doc.'" PDFA_def.ps "'.$doc_old.'" ');    
    
    //$command = shell_exec('"c:\Program Files\gs\gs9.54.0\bin\gswin64c" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile="'. $doc.'" "'.$doc_old.'" 2>&1');    
    //$doc2 = substr($doc,0,-4).'_cvt'.substr($doc,-4);
    //-dColorConversionStrategy=RGB
    
    // VERIFICA ERROS
    if ($obj === false)
    {            
        $doc_old = $doc;
        $doc = substr($doc_old,0,-4).'_cvt'.substr($doc_old,-4);

        // UTILIZA GHOSTSCRIPT PARA CONVERTER PARA VERSÃO 1.4 DO PDF E CORRIGIR ERROS.
        if(ghostScript($doc, $doc_old))
        {            
            // LÊ DOCUMENTO NOVAMENTE
            $file_content = file_get_contents($doc);
            $obj = PDFDoc::from_string($file_content);
            //$pagesize = $obj->get_page_size(0);    
            //echo "aaa";
            
            if ($obj === false)
            {                                
                $retorno['result'] = "Não foi possível encontrar o documento.";
                return $retorno;
            }
        }
        else
        {
            $retorno['result'] = "Não foi possível converter o documento. Verifique se o mesmo está no formato PDF ou se está protegido contra edições.";
            return $retorno;
        }        
    }
      
    if ($obj === false)
    {            
        $retorno['result'] =  "Não foi possível ler o documento. Verifique se o mesmo está no formato PDF ou se está protegido contra edições.";
        return $retorno;
    }
    else 
    {                              
        # Insere bloco assinatura no PDF
        $img = imagecreatetruecolor(800, 25);
        imagealphablending($img, false);
        $transparency = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparency);
        imagesavealpha($img, true);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, 0, 0, $black);
        //$white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        //imagefilledrectangle($img, 0, 0, 800, 25, $white);
        imagestring($img, 5, 5, 5, "Assinado digitalmente por: ".$dados_ass." - ".$data_ass, $black);
        $img = imagerotate($img, 90, imagecolorallocatealpha($img, 0, 0, 0, 127));
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagepng($img, substr($doc,0,-4).".png");

        $position = [ ];
        $image = substr($doc,0,-4).".png";
        
        $imagesize = @getimagesize($image);
        
        if ($imagesize === false) 
        {            
            //fwrite(STDERR, "failed to open the image $image");
            $retorno['result'] = "Erro ao abrir imagem.";  
            return $retorno;                      
        }
        
        $pagesize = $obj->get_page_size(0);
        if ($pagesize === false)
        {
            $doc_old = $doc;
            $doc = substr($doc_old,0,-4).'_cvt'.substr($doc_old,-4);
            if(ghostScript($doc, $doc_old))
            {
                // LÊ DOCUMENTO NOVAMENTE
                $file_content = file_get_contents($doc);
                $obj = PDFDoc::from_string($file_content);
                $pagesize = $obj->get_page_size(0);   
                
                if ($obj === false)
                {                                
                    $retorno['result'] = "Não foi possível encontrar o documento.";
                    return $retorno;
                }
            }
            else
            {
                $retorno['result'] =  "Não foi possível converter o documento. Verifique se o mesmo está no formato PDF ou se está protegido contra edições.";     
                return $retorno;           
            }
        }
        
        $pagesize = explode(" ", $pagesize[0]->val());
        // Calculate the position of the image according to its size and the size of the page;
        //   the idea is to keep the aspect ratio and center the image in the page with a size
        //   of 1/3 of the size of the page.
        $p_x = intval("". $pagesize[0]);
        $p_y = intval("". $pagesize[1]);
        $p_w = intval("". $pagesize[2]) - $p_x;
        $p_h = intval("". $pagesize[3]) - $p_y;
        $i_w = $imagesize[0];
        $i_h = $imagesize[1];

        $ratio_x = $p_w / $i_w;
        $ratio_y = $p_h / $i_h;
        $ratio = min($ratio_x, $ratio_y);

        $i_w = ($i_w * $ratio) / 2.5;
        $i_h = ($i_h * $ratio) / 2.5;

        $x = $x_ass * 0.015;        
        $p_x = $p_w / (1.06 + $x);
        $p_y = $p_h / 3;
        
        // Set the image appearance and the certificate file
        $obj->set_signature_appearance(0, [ $p_x, $p_y, $p_x + $i_w, $p_y + $i_h ], $image);

       
        $obj->set_signature_certificate($cert, $pass);
        $docsigned = $obj->to_pdf_file_s();
        if ($docsigned === false)
        {            
            //fwrite(STDERR, "could not sign the document");
        }
        else
        {         
            # Salva PDF assinado   
           
            if(strpos($doc_old,"_assign") === false)   
            {
                $doc_ass = substr($doc_old,0,-4)."_assign".substr($doc_old,-4);
            }
            else
            {
                $a = explode("/",$doc_old);
                $b = array_pop($a);
                $c = implode("/",$a);
                $doc_ass = $c."/anexo_".md5(mt_rand(1,10000).$b.date('YmdHis'))."_assign".substr($doc_old,-4);
            }
            
            if(file_put_contents($doc_ass, $docsigned))
            {                
                unlink(substr($doc_old,0,-4).".png");                
                $retorno['result'] = "Documento assinado com sucesso!";
                $retorno['file']  = $doc_ass;
                return $retorno;     
            }
            else
            {
                
                $retorno['result'] =  "Não foi possível salvar o documento assinado.";     
                return $retorno;             
                # Erro ao salvar arquivo
            }
        }            
    }
}

function pdfVersion($filename)
{ 
    $fp = @fopen($filename, 'rb');
    
    if (!$fp) {
        return 0;
    }
    
    /* Reset file pointer to the start */
    fseek($fp, 0);
    /* Read 20 bytes from the start of the PDF */
    preg_match('/\d\.\d/',fread($fp,20),$match);
    
    fclose($fp);
    
    if (isset($match[0])) {
        return $match[0];
    } else {
        return 0;
    }
}
?>