<div class="modal fade" id="modalCustom<?php echo $log_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">

    <!--Content-->
    <div class="modal-content">

      <!--Body-->
      <div class="modal-body"  style="padding-left:0; padding-right:0;">

        <!--Google map-->
            <center>
          <iframe src="https://maps.google.com/?q=<?php echo $log_lat;?>,<?php echo $log_long;?>&z=15&iwloc=&output=embed"
            frameborder="0" height="400" width="600" style="border:0; margin:0; padding-top:10px; padding-bottom:10px;" allowfullscreen></iframe></center>
    

      </div>

      <!--Footer-->
      <div class="modal-footer justify-content-center">            
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>                     
        </div>

    </div>
    <!--/.Content-->

  </div>
</div>