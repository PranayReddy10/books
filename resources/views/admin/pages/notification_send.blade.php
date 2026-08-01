@extends("admin.admin_app")

@section("content")

<style type="text/css">
  .iframe-container {
  overflow: hidden;
  padding-top: 56.25% !important;
  position: relative;
}
 
.iframe-container iframe {
   border: 0;
   height: 100%;
   left: 0;
   position: absolute;
   top: 0;
   width: 100%;
}
</style>
 
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box">
                 
                
                 {!! Form::open(array('url' => array('admin/notification_send'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')) !!}  
                   
 
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.title')}}*</label>
                    <div class="col-sm-8">
                      <input type="text" name="notification_title" value="" class="form-control">
                    </div>
                  </div>
  
                   
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.android_notification_message')}}*</label>
                    <div class="col-sm-8">
                       
                      <textarea id="notification_msg" name="notification_msg" class="form-control"></textarea>
                     
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.image')}} <small id="emailHelp" class="form-text text-muted">({{trans('words.optional')}})</small></label>
                    <div class="col-sm-8">
                      <div class="input-group">
                        <input type="text" name="notification_image" id="notification_image" value="" class="form-control" readonly>                         
                        <div class="input-group-append">                           
                          <button type="button" class="btn btn-dark waves-effect waves-light popup_selector" data-input="notification_image" data-preview="holder_thumb" data-inputid="notification_image">Select</button>                        
                        </div>
                      </div>
                      <small id="emailHelp" class="form-text text-muted">( {{trans('words.recommended_resolution')}}: 600x293 or 650x317 or 700x342 or 750x366)</small>
                      <div id="image_holder" class="send_notification_img"></div>                     
                    </div>
                  </div>       
                  <h4 class="m-t-0 m-b-30 header-title" style="font-size: 18px;">{{trans('words.or')}}</h4>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.post_type')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="section_type" id="notification_section_type">     

                                 <option value="" >Select Type</option>                           
                                 <option value="category" @if(isset($info->section_type) AND $info->section_type=="category") selected @endif>Category</option>
                                <option value="authors" @if(isset($info->section_type) AND $info->section_type=="authors") selected @endif>Authors</option>
                                <option value="books" @if(isset($info->section_type) AND $info->section_type=="books") selected @endif>Books</option>
                                 
                            </select>
                      </div>
                  </div> 

                  <div class="form-group row" id="category_list_sec" style="display:none;">
                    <label class="col-sm-3 col-form-label">{{trans('words.categories_text')}} <small id="emailHelp" class="form-text text-muted">({{trans('words.optional')}})</small></label> 
                      <div class="col-sm-8">
                            <select name="category_id" class="select2" id="cat_id1" data-placeholder="{{trans('words.select_category')}}...">   
                                 <option value="" >{{trans('words.select_category')}}</option>

                                 @foreach($category_list as $category_data)
                                  <option value="{{$category_data->id}}">{{$category_data->category_name}}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">(To directly open category books when clicking on the notification)</small>
                      </div>
                  </div>

                  <div class="form-group row" id="authors_list_sec" style="display:none;">
                    <label class="col-sm-3 col-form-label">{{trans('words.authors_text')}} <small id="emailHelp" class="form-text text-muted">({{trans('words.optional')}})</small></label> 
                      <div class="col-sm-8">
                            <select name="author_id" class="select2" id="author_id1" data-placeholder="{{trans('words.select_author')}}...">   
                                 <option value="" >{{trans('words.select_author')}}</option>

                                 @foreach($authors_list as $authors_data)
                                  <option value="{{$authors_data->id}}">{{$authors_data->name}}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">(To directly open author's books when clicking on the notification)</small>
                      </div>
                  </div>

                  <div class="form-group row" id="books_list_sec" style="display:none;">
                    <label class="col-sm-3 col-form-label">{{trans('words.books_text')}} <small id="emailHelp" class="form-text text-muted">({{trans('words.optional')}})</small></label> 
                      <div class="col-sm-8">
                            <select name="book_id" class="select2" id="book_id1" data-placeholder="{{trans('words.select_book')}}...">   
                                 <option value="" >{{trans('words.select_book')}}</option>

                                 @foreach($books_list as $books_data)
                                  <option value="{{$books_data->id}}">{{$books_data->title}}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">(To directly open single post when clicking on the notification)</small>
                      </div>
                  </div>

                  

                  <h4 class="m-t-0 m-b-30 header-title" style="font-size: 18px;">{{trans('words.or')}}</h4>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.android_external_link')}} <small id="emailHelp" class="form-text text-muted">({{trans('words.optional')}})</small></label>
                    <div class="col-sm-8">
                      <input type="text" name="external_link" value="" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-send"></i> {{trans('words.send')}} </button>                      
                    </div>
                   </div>

                
                   
                {!! Form::close() !!} 
              </div>
            </div>            
          </div>              
        </div>
      </div>
      @include("admin.copyright") 
    </div> 
  
<script type="text/javascript">
     
     
// function to update the file selected by elfinder
function processSelectedFile(filePath, requestingField) {

    //alert(requestingField);

    var elfinderUrl = "{{ URL::to('/') }}/";

    if(requestingField=="notification_image")
    {
      var target_preview = $('#image_holder');
      target_preview.html('');
      target_preview.append(
              $('<img class="noti_preview_img">').attr('src', elfinderUrl + filePath.replace(/\\/g,"/"))
            );
      target_preview.trigger('change');
    }
 
    //$('#' + requestingField).val(filePath.split('\\').pop()).trigger('change'); //For only filename
    $('#' + requestingField).val(filePath.replace(/\\/g,"/")).trigger('change');
 
}
 
 </script>

<script type="text/javascript">
    
    @if(Session::has('flash_message'))     
 
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

      Toast.fire({
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
      })     
     
  @endif

  @if (count($errors) > 0)
                  
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background:"#1a2234",
            color:"#fff"
           }) 
  @endif

  </script>

@endsection