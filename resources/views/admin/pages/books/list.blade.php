@extends("admin.admin_app")

@section("content")
<style type="text/css">
 
  .color-drops{
    width: 24px;
    height: 24px;
    border-radius: 50%;
    float: left;
    margin: 5px -5px 5px 0px;
    text-align:center;
    box-shadow:1px 0px 10px #000;
    transition: all linear .2s;
  }
  .color-drops:hover{
    transform: scale(1.2);
  }


</style>
  
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row"> 
                 <div class="col-md-3">
                     {!! Form::open(array('url' => 'admin/books','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}   
                      <input type="text" name="s" placeholder="{{trans('words.search_by_title')}}" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    {!! Form::close() !!}
                </div>
                <div class="col-sm-6">
                  &nbsp;
                </div> 
 
                <div class="col-md-3">
                  <a href="{{URL::to('admin/books/add')}}" class="btn btn-success btn-md waves-effect waves-light m-b-20 mt-2 pull-right" data-toggle="tooltip" title="{{trans('words.add_book')}}"><i class="fa fa-plus"></i> {{trans('words.add_book')}}</a>
                </div>
              </div>
              <div class="row">   
                  <div class="wall-filter-block">  
                    <div class="row" style="align-items: center;justify-content: space-between;">            
                    
                    <div class="col-sm-5">
                        <div class="radio radio-success form-check-inline pl-2 mr-1">
                            <input class="filter_radio" id="inlineRadio1" type="radio" name="filter" value="Slider" @if(isset($_GET['filter']) AND $_GET['filter']=="Slider" ) checked @endif>
                            <label for="inlineRadio1" class="mb-0">Slider Books</label>
                        </div>

                        <div class="radio radio-success form-check-inline pl-2 mr-1">
                              <input class="filter_radio" id="inlineRadio2" type="radio" name="filter" value="Paid" @if(isset($_GET['filter']) AND $_GET['filter']=="Paid" ) checked @endif>
                              <label for="inlineRadio2" class="mb-0">Paid</label>
                          </div>

                          <div class="radio radio-success form-check-inline pl-2 mr-1">
                              <input class="filter_radio" id="inlineRadio3" type="radio" name="filter" value="Free" @if(isset($_GET['filter']) AND $_GET['filter']=="Free" ) checked @endif>
                              <label for="inlineRadio3" class="mb-0">Free</label>
                          </div>

                          <div class="radio radio-success form-check-inline pl-2 mr-1">
                              <input class="filter_radio" id="inlineRadio4" type="radio" name="filter" value="Active" @if(isset($_GET['filter']) AND $_GET['filter']=="Active" ) checked @endif>
                              <label for="inlineRadio4" class="mb-0">{{trans('words.active')}}</label>
                          </div>

                          <div class="radio radio-success form-check-inline pl-2 mr-0">
                              <input class="filter_radio" id="inlineRadio5" type="radio" name="filter" value="Inactive" @if(isset($_GET['filter']) AND $_GET['filter']=="Inactive" ) checked @endif>
                              <label for="inlineRadio5" class="mb-0">{{trans('words.inactive')}}</label>
                          </div>
                         
                      </div>
                       
                      <?php 
                         if(isset($_GET['cat_id']))
                         {
                          $cat_id = $_GET['cat_id'];
                         }
                         else
                         {
                          $cat_id =0;
                         }

                         if(isset($_GET['author_id']))
                         {
                          $author_id = $_GET['author_id'];
                         }
                         else
                         {
                          $author_id =0;
                         }
                         ?>
 
                      <div class="col-sm-2 pr-1"> 
                         <select class="form-control select2" name="cat_select" id="cat_id">
                            <option value="?cat_id=0&author_id={{$author_id}}">{{trans('words.categories_text')}}</option>
                             @foreach($cat_list as $cat_data)
                              <option value="?cat_id={{$cat_data->id}}&author_id={{$author_id}}" @if(isset($_GET['cat_id']) AND $_GET['cat_id']==$cat_data->id) selected @endif>{{$cat_data->category_name}}</option>
                             @endforeach
                         </select>                         
                      </div>
                      <div class="col-sm-2 pl-1">
                         <select class="form-control select2" name="author_select" id="author_id">
                            <option value="?author_id=0&cat_id={{$cat_id}}">{{trans('words.authors_text')}}</option>
                             @foreach($authors_list as $author_data)
                              <option value="?author_id={{$author_data->id}}&cat_id={{$cat_id}}" @if(isset($_GET['author_id']) AND $_GET['author_id']==$author_data->id) selected @endif>{{$author_data->name}}</option>
                             @endforeach
                         </select>                         
                      </div>
                       
                      <div class="col-sm-3">
                        <div class="checkbox checkbox-success pull-right">
                            <input id="sellect_all" type="checkbox" name="sellect_all">
                            <label for="sellect_all">{{trans('words.select_all')}}</label>
                            &nbsp;&nbsp;
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">{{trans('words.action')}}<span class="caret"></span></button>
                                <div class="dropdown-menu">
                                  <a href="javascript:void(0);" class="dropdown-item" data-action="enable" id="data_enable_selected">{{trans('words.active')}}</a>
                                  <a href="javascript:void(0);" class="dropdown-item" data-action="disable" id="data_disable_selected">{{trans('words.inactive')}}</a>
                                  <a href="javascript:void(0);" class="dropdown-item" data-action="delete" id="data_remove_selected">{{trans('words.delete')}}</a>                                                                  
                                </div>
                            </div>
                        </div>
                      </div>
                    </div> 
                  </div>                
                </div>

                <br/>

                <div class="row">
                  @foreach($list as $i => $data)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6" id="card_box_id_{{$data->id}}">

                        <!-- Simple card -->
                        <div class="card m-b-20">
                            <div class="wall-list-item">
                              <div class="checkbox checkbox-success wall_check">
                                <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i; ?>" value="<?php echo $data->id; ?>" class="post_ids">
                                <label for="checkbox<?php echo $i; ?>"></label>
                              </div> 
							  <p class="wall_sub_text">{{ \App\Category::getCategoryInfo($data->cat_id,'category_name') }} - {{ \App\SubCategory::getSubCategoryInfo($data->sub_cat_id,'sub_category_name') }}</p>
                               
                              @include('admin.partials.book_cover', ['book' => $data])
                            </div>
 
                            <div class="card-body p-3">
                                <h4 class="card-title book_title mb-3 align-items-center">@if(isset($data->content_type) && $data->content_type=='video')<span style="background:#e53935;color:#fff;font-size:10px;padding:2px 7px;border-radius:4px;margin-right:6px;vertical-align:middle"><i class="fa fa-play"></i> VIDEO</span>@endif{{ stripslashes($data->title) }}</h4>
								<div class="d-flex wall_preview_item">
                                  <ul>
                                    <li><a href="javascript:void(0)" data-toggle="tooltip" title="{{$data->featured?'Remove Slider':'Set Slider'}}" data-id="{{$data->id}}" data-value="{{$data->featured?'false':'true'}}" class="slider_enable_disable" style="{{$data->featured?'color: green':''}}" id="slider_id{{$data->id}}"><i class="fa fa-sliders"></i></a></li> 
                                    <li><a href="javascript:void(0)" data-toggle="tooltip" title="{{post_views_count($data->id,"Book")}} Views"><i class="fa fa-eye"></i></a></li>            
                                    <li><a href="javascript:void(0)" data-toggle="tooltip" title="{{\App\PostRatings::getPostTotalRatings($data->id,"Book")}} Rating"><i class="fa fa-star"></i></a></li>
                                    <li><a href="javascript:void(0)" data-toggle="tooltip" title="{{post_download_count($data->id,"Book")}} Download"><i class="fa fa-download"></i></a></li>
                                  </ul>
                                </div>
                                <a href="{{ url('admin/books/edit/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="{{trans('words.edit')}}"> <i class="fa fa-edit"></i> </a>
                                <a href="#" class="btn btn-icon waves-effect waves-light btn-danger data_remove" data-toggle="tooltip" title="{{trans('words.remove')}}" data-id="{{$data->id}}"> <i class="fa fa-remove"></i> </a>
                                <a class="ml-2" href="Javascript:void(0);" data-toggle="tooltip" title="@if($data->status==1){{ trans('words.active')}} @else {{trans('words.inactive')}} @endif"><input type="checkbox" name="category_on_off" id="book_enable_disable{{$data->id}}" value="1" data-plugin="switchery" data-color="#28a745" data-size="small" class="enable_disable"  data-id="{{$data->id}}" @if($data->status==1) {{ 'checked' }} @endif/></a>    
                            </div>
                        </div>

                    </div>
                   @endforeach      

                </div>
  
                <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $list]) 
                </nav>
           
              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright") 
    </div>    

<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
 
<!-- SweetAlert2 -->
<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>


<script type="text/javascript">
 
$(".enable_disable").on("change",function(e){      
       
      var post_id = $(this).data("id");
      
      var status_set = $(this).prop("checked"); 

      var action_name='book_status';
      //alert($(this).is(":checked"));
      //alert(status_set);

      $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_id, value: status_set, action_for: action_name},
        success: function(res) {

          if(res.status=='1')
          {
            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.status_changed')}}',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
             
          } 
          else
          { 
            Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
          }
          
        }
      });
}); 

</script>

<script type="text/javascript">
 
$(".slider_enable_disable").on("click",function(e){      
      
 
      var post_id = $(this).data("id");
      
      var status_set = $(this).data("value"); 

      var action_name='book_featured';
      //alert($(this).is(":checked"));
      //alert(status_set);

      $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_id, value: status_set, action_for: action_name},
        success: function(res) {

          if(res.status=='1')
          {

            //slider_id
            var s_post_id= '#slider_id'+post_id;

            $(s_post_id).css({'color':res.set_color,'background':'#fff'});
            $(s_post_id).attr('data-original-title', res.set_title);            
            $(s_post_id).data('value',res.re_set_value);

            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: res.res_msg,
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
             
          } 
          else
          { 
            Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
          }
          
        }
      });
}); 

</script>

<script type="text/javascript">
//Single
$(".data_remove").click(function () {  
  
  var post_id = $(this).data("id");
  var action_name='book_delete';

  Swal.fire({
  title: '{{trans('words.dlt_warning')}}',
  text: "{{trans('words.dlt_warning_text')}}",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: '{{trans('words.dlt_confirm')}}',
  cancelButtonText: "{{trans('words.btn_cancel')}}",
  background:"#1a2234",
  color:"#fff"

}).then((result) => {

  //alert(post_id);

  //alert(JSON.stringify(result));

    if(result.isConfirmed) { 

        $.ajax({
            type: 'post',
            url: "{{ URL::to('admin/ajax_delete') }}",
            dataType: 'json',
            data: {"_token": "{{ csrf_token() }}",id: post_id, action_for: action_name},
            success: function(res) {

              if(res.status=='1')
              {  

                  var selector = "#card_box_id_"+post_id;
                    $(selector ).fadeOut(1000);
                    setTimeout(function(){
                            $(selector ).remove()
                        }, 1000);

                  Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.deleted')}}!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
                
              } 
              else
              { 
                Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: true,
                        confirmButtonColor: '#10c469',
                        background:"#1a2234",
                        color:"#fff"
                       })
              }
              
            }
        });
    }
 
})

});

//Multiple Delete
$("#data_remove_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_delete_selected';

  Swal.fire({
  title: '{{trans('words.dlt_warning')}}',
  text: "{{trans('words.dlt_warning_text')}}",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: '{{trans('words.dlt_confirm')}}',
  cancelButtonText: "{{trans('words.btn_cancel')}}",
  background:"#1a2234",
  color:"#fff"

}).then((result) => {
 
    if(result.isConfirmed) { 

        $.ajax({
            type: 'post',
            url: "{{ URL::to('admin/ajax_delete') }}",
            dataType: 'json',
            data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
            success: function(res) {

              if(res.status=='1')
              {  
                  $.map($('.post_ids:checked'), function(c) {
                    
                    var post_id= c.value;
                    
                    var selector = "#card_box_id_"+post_id;
                      $(selector ).fadeOut(1000);
                      setTimeout(function(){
                              $(selector ).remove()
                          }, 1000);

                    return c.value;
                  });
 
                  Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.deleted')}}!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
                
              } 
              else
              { 
                Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: true,
                        confirmButtonColor: '#10c469',
                        background:"#1a2234",
                        color:"#fff"
                       })
              }
              
            }
        });
    }
 
})

});


//Multiple Enable
$("#data_enable_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_enable_multiple';

  $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
        success: function(res) {

          if(res.status=='1')
          {
 
            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.status_changed')}}',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  }).then((result) => {

                    var url="{{url()->full()}}";
                    window.location = url;

                    });
             
          } 
          else
          { 
            Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
          }
          
        }
      });
 

});


//Multiple Disable
$("#data_disable_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_disable_multiple';

  $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
        success: function(res) {

          if(res.status=='1')
          {
 
            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.status_changed')}}',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  }).then((result) => {

                    var url="{{url()->full()}}";
                    window.location = url;
     
                  });
             
          } 
          else
          { 
            Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
          }
          
        }
      });
 

});

</script>
<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
<script type="text/javascript">

  
$(".filter_radio").on( "click",(function() {


    var filter_value = $("input[name='filter']:checked").val();

   
    if(filter_value!="")
    {
      var url="{{URL::to('admin/books')}}?filter="+filter_value;
    }
    else
    {
      var url="{{URL::to('admin/books')}}?filter=none";
    } 
 
     if (url) { // require a URL
            window.location = url; // redirect
      }
      return false; 
})
);

   

  var totalItems = 0;
 // $("#sellect_all").on("click", function(e) {
  $(document).on("click", "#sellect_all", function() {
      
    totalItems = 0;

    $("input[name='post_ids[]']").not(this).prop('checked', this.checked);
    $.each($("input[name='post_ids[]']:checked"), function() {
      totalItems = totalItems + 1;       
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
        /*didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }*/
      })

    
    if ($("input[name='post_ids[]']").prop("checked") == true) {
        
      Toast.fire({
      icon: 'success',
      title: totalItems + ' {{trans('words.item_checked')}}'
    })

    } else if ($("input[name='post_ids[]']").prop("checked") == false) {
      totalItems = 0;
      
      Toast.fire({
      icon: 'success',
      title: totalItems + ' {{trans('words.item_checked')}}'
    })
      
    }
 
});

$(document).on("click", ".post_ids", function(e) {
 
if ($(this).prop("checked") == true) {
  totalItems = totalItems + 1;
} else if ($(this).prop("checked") == false) {
  totalItems = totalItems - 1;
}

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
        /*didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }*/
      })

    if (totalItems == 0) {
      Toast.fire({
        icon: 'success',
        title: totalItems + ' {{trans('words.item_checked')}}'
      })

      return true;
    }
 
    Toast.fire({
      icon: 'success',
      title: totalItems + ' {{trans('words.item_checked')}}'
    })

 
});

</script> 
 
@endsection

