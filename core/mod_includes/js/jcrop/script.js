/**
 *
 * HTML5 Image uploader with Jcrop
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2012, Script Tutorials
 * http://www.script-tutorials.com/
 */

// convert bytes into friendly format
function bytesToSize_1(bytes) {
    var sizes = ['Bytes', 'KB', 'MB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};

// check for selected crop region
function checkForm_1() {
    if (parseInt($('#w_1').val())) return true;
    $('.error').html('Please select a crop region and then press Upload').show();
    return false;
};

// update info by cropping (onChange and onSelect events handler)
function updateInfo_1(e) {
    $('#x1_1').val(e.x);
    $('#y1_1').val(e.y);
    $('#x2_1').val(e.x2);
    $('#y2_1').val(e.y2);
    $('#w_1').val(e.w);
    $('#h_1').val(e.h);
};

// clear info by cropping (onRelease event handler)
function clearInfo_1() {
    $('.info #w_1').val('');
    $('.info #h_1').val('');
};

function fileSelectHandler_1() {
	if(document.getElementById('jcrop_banner').src != "")
	{
		jQuery("#jcrop_banner").remove();
		jQuery("#img1").html("<img id='jcrop_banner'/>");
	}
	 // get selected file
    var oFile = $('#loj_logo')[0].files[0];

    // hide all errors
    $('.error').hide();

    // check for image type (jpg and png are allowed)
    var rFilter = /^(image\/jpeg|image\/png)$/i;
    if (! rFilter.test(oFile.type)) {
        $('.error').html('Please select a valid image file (jpg and png are allowed)').show();
        return;
    }

    // check for file size
    if (oFile.size > 1024 * 1024) {
        $('.error').html('You have selected too big file, please select a one smaller image file').show();
        return;
    }

    // preview element
    var oImage = document.getElementById('jcrop_banner');

    // prepare HTML5 FileReader
    var oReader = new FileReader();
        oReader.onload = function(e) {

        // e.target.result contains the DataURL which we can use as a source of the image
        oImage.src = e.target.result;
        oImage.onload = function () { // onload event handler

            // display step 2
            $('.step2').fadeIn(500);

            // display some basic image info
            var sResultFileSize = bytesToSize_1(oFile.size);
            $('#filesize_1').val(sResultFileSize);
            $('#filetype_1').val(oFile.type);
            $('#filedim_1').val(oImage.naturalWidth + ' x ' + oImage.naturalHeight);
			
            // Create variables (in this scope) to hold the Jcrop API and image size
            var jcrop_api, boundx, boundy;

            // destroy Jcrop if it is existed
            if (typeof jcrop_api != 'undefined') 
                jcrop_api.destroy();

            // initialize Jcrop
            $('#jcrop_banner').Jcrop({
                minSize: [200, 200], // min crop size`
				setSelect:   [ 100, 100, 50, 50 ],
                aspectRatio : 1,
                bgFade: true, // use fade effect
                bgOpacity: .3, // fade opacity
                bgColor: 'transparent',
				onChange: showPreview_1,
                onSelect: updateInfo_1,
                onRelease: clearInfo_1,
				boxWidth: 400,
				boxHeight: 400
            }, function(){

                // use the Jcrop API to get the real image size
                var bounds = this.getBounds();
                boundx = bounds[0];
                boundy = bounds[1];

                // Store the Jcrop API in the jcrop_api variable
                jcrop_api = this;
            });
			function showPreview_1(coords) {
			   if (parseInt(coords.w) > 0) {
				  var rx = 80 / coords.w;
				  var ry = 80 / coords.h;
			 
				  var img_height = $("#jcrop_banner").height();
				  var img_width = $("#jcrop_banner").width();
			 
				  jQuery('#jcrop_banner').css({
					 width: Math.round(rx * img_width) + 'px',
					 height: Math.round(ry * img_height) + 'px',
					 marginLeft: '-' + Math.round(rx * coords.x) + 'px',
					 marginTop: '-' + Math.round(ry * coords.y) + 'px'
				 });
			   }
			};
        };
    };

    // read selected file as DataURL
    oReader.readAsDataURL(oFile);
}