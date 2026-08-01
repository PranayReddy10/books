<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{getcong('app_name')}} Admin">
  <meta name="author" content="Viaviwebtech">
  @if(getcong('app_logo'))
  <link rel="shortcut icon" href="{{ URL::asset('/'.getcong('app_logo')) }}">
  @else
  <link rel="shortcut icon" href="{{ URL::asset('site_assets/images/favicon.png') }}">
  @endif
  <title>{{getcong('app_name')}} Admin</title>

  <!--Morris Chart CSS -->
 <link rel="stylesheet" href="{{ URL::asset('admin_assets/plugins/morris/morris.css') }}">

        
   
     <link href="{{ URL::asset('admin_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ URL::asset('admin_assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ URL::asset('admin_assets/plugins/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ URL::asset('admin_assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ URL::asset('admin_assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

     <link href="{{ URL::asset('admin_assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet">

     <link href="{{ URL::asset('admin_assets/css/style.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('admin_assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />  

     <link href="{{ URL::asset('admin_assets/css/admin-light.css') }}?v=15" rel="stylesheet" type="text/css" />

     <script src="{{ URL::asset('admin_assets/js/modernizr.min.js') }}"></script>
     
     <link href="{{ URL::asset('admin_assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
  <!-- App css -->
 
  <!-- SweetAlert2 -->
  <script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>


 
</head>
  <body class="fixed-left">
    <div id="wrapper">
   
    @include("admin.topbar") 

    @include("admin.sidebar")

    @yield("content")

    </div>

  <!-- jQuery  -->

   
  <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/popper.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/bootstrap.min.js') }}"></script>  
   
  <script src="{{ URL::asset('admin_assets/js/detect.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/fastclick.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.blockUI.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/waves.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.nicescroll.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.slimscroll.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.scrollTo.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/plugins/switchery/switchery.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/plugins/tinymce/tinymce.min.js') }}"></script>
  
  <script src="{{ URL::asset('admin_assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}" type="text/javascript"></script>

  <script src="{{ URL::asset('admin_assets/plugins/jquery-knob/jquery.knob.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
 
  <script type="text/javascript" src="{{ URL::asset('admin_assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
  
  @if(classActivePath('dashboard'))
  <!-- Counter Up  -->
  <script src="{{ URL::asset('admin_assets/plugins/waypoints/jquery.waypoints.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
  @endif

  <!-- App js -->
   <script src="{{ URL::asset('admin_assets/js/jquery.core.js') }}"></script>
   <script src="{{ URL::asset('admin_assets/js/jquery.app.js') }}"></script>


<script type="text/javascript">
    $(document).ready(function () {
      if ($("#elm1").length > 0) {
        tinymce.init({
          selector: "textarea#elm1",           
          height: 300,
          plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
           toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor",
          style_formats: [
            { title: 'Bold text', inline: 'b' },
            { title: 'Red text', inline: 'span', styles: { color: '#ff0000' } },
            { title: 'Red header', block: 'h1', styles: { color: '#ff0000' } },
            { title: 'Example 1', inline: 'span', classes: 'example1' },
            { title: 'Example 2', inline: 'span', classes: 'example2' },
            { title: 'Table styles' },
            { title: 'Table row 1', selector: 'tr', classes: 'tablerow1' }
          ]
        });
      }

      if ($(".elm1_editor").length > 0) {
        tinymce.init({
          selector: "textarea.elm1_editor",           
          height: 300,
          plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
           toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor",
          style_formats: [
            { title: 'Bold text', inline: 'b' },
            { title: 'Red text', inline: 'span', styles: { color: '#ff0000' } },
            { title: 'Red header', block: 'h1', styles: { color: '#ff0000' } },
            { title: 'Example 1', inline: 'span', classes: 'example1' },
            { title: 'Example 2', inline: 'span', classes: 'example2' },
            { title: 'Table styles' },
            { title: 'Table row 1', selector: 'tr', classes: 'tablerow1' }
          ]
        });
      }
    });
  </script>
<script>
jQuery(document).ready(function () {
  $(".select2, .select3, .select4, .select5, .select6, .select7, .select8").select2();
  $(".select2-limiting, .select3-limiting, .select4-limiting, .select5-limiting, .select6-limiting, .select7-limiting, .select8-limiting").select2({
    maximumSelectionLength: 2
  });
});

jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
jQuery('.datepicker_trans').datepicker({
                autoclose: true,
                todayHighlight: true
            });            
</script> 

<script>
 $(function(){
      
    //Category , Author
    $('#cat_id,#author_id').on('change', function () {
        var url = $(this).val(); // get selected value
       
        if (url) { // require a URL
            window.location = url; // redirect
        }
        return false;
    });
 
    //gateway
    $('#gateway_select').on('change', function () {
        var url = $(this).val(); // get selected value
       
        if (url) { // require a URL
            window.location = url; // redirect
        }
        return false;
    });
 
   
  });

 

$("#admin_usertype").change(function(){         
   var type=$("#admin_usertype").val();

       if(type=="Admin")
       {
          $("#master_admin_id").show();
          $("#sub_admin_id").hide();
       }
       else
       {
          $("#master_admin_id").hide();
          $("#sub_admin_id").show();
       }

 });

 
</script>

<script type="text/javascript">
  $("#home_section_type").change(function(){         
   var type=$("#home_section_type").val();

       if(type=="category")
       {
          $("#category_list_sec").show();
          $("#sub_category_list_sec").hide();
          $("#wallpaper_list_sec").hide();
          
       }
       else if(type=="subcategory")
       {
          $("#category_list_sec").hide();
          $("#sub_category_list_sec").show();
          $("#authors_list_sec").hide();
          $("#book_list_sec").hide();
          
       }
       else if(type=="author")
       {
          $("#category_list_sec").hide();
          $("#sub_category_list_sec").hide();
          $("#authors_list_sec").show();
          $("#book_list_sec").hide();         
          
       }
       else if(type=="book")
       {
          $("#category_list_sec").hide();
          $("#sub_category_list_sec").hide();
          $("#authors_list_sec").hide();
          $("#book_list_sec").show();
          
       }        
       else
       {
          $("#category_list_sec").hide();
          $("#sub_category_list_sec").hide();
          $("#authors_list_sec").hide();
          $("#book_list_sec").hide();          
       }

 });
</script>


<script type="text/javascript">
  $("#notification_section_type").change(function(){         
   var type=$("#notification_section_type").val();

       if(type=="category")
       {
          $("#category_list_sec").show();
          $("#authors_list_sec").hide();
          $("#books_list_sec").hide();
           
       }
       else if(type=="authors")
       {
          $("#category_list_sec").hide();
          $("#authors_list_sec").show();
          $("#books_list_sec").hide();
       }       
       else if(type=="books")
       {
          $("#category_list_sec").hide();
          $("#authors_list_sec").hide();
          $("#books_list_sec").show();
       }       
       else
       {
          $("#category_list_sec").hide();
          $("#authors_list_sec").hide();
          $("#books_list_sec").hide();
       }

 });
</script>
 
<script type="text/javascript">
  
  //$("select").select2();

$("select").on("select2:select", function (evt) {
  var element = evt.params.data.element;
  var $element = $(element);
  
  $element.detach();
  $(this).append($element);
  $(this).trigger("change");
});

</script>
  

<link rel="stylesheet" href="{{url('packages')}}/barryvdh/elfinder/css/colorbox.css">

 
<script src="{{url('packages')}}/barryvdh/elfinder/js/jquery.colorbox.js"></script>
<script type="text/javascript" src="{{url('packages')}}/barryvdh/elfinder/js/jquery.colorbox-min.js"></script>


<script type="text/javascript">
     
     $(document).on('click','.popup_selector',function (event) {
    event.preventDefault();
    var updateID = $(this).attr('data-inputid'); // Btn id clicked
    var elfinderUrl = "{{ URL::to('elfinder/popup') }}/";

    // trigger the reveal modal with elfinder inside
    var triggerUrl = elfinderUrl + updateID;
    $.colorbox({
        href: triggerUrl,
        fastIframe: true,
        iframe: true,
        width: '70%',
        height: '80%'
    });

});
 

 </script>       
  
    </body>
</html>