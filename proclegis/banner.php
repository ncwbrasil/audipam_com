<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="mod_includes_portal/css/banner/style.css" />

<div class="g_slide" id="slides1">
  <div class="switch_main bnn">
    <a class="item switch_item" target="_parent"><img src="uploads/banner/01.jpg"></a>
  </div>
</div>

<!--<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>-->
<script src="mod_includes_portal/js/switchable.js"></script>

<script>
  $(function() {
    switchable({
      $element: $('#slides1'),
      interval: 2000,
      effect: 'fade'
    });
  });
</script>