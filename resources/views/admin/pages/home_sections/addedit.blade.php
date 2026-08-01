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
                 <div class="row">
                 <div class="col-sm-6">
                      <a href="{{ URL::to('admin/home_sections') }}"><h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;"><i class="fa fa-arrow-left"></i> {{trans('words.back')}}</h4></a>
                 </div>                  
               </div> 
                 
                 
                 {!! Form::open(array('url' => array('admin/home_sections/add_edit'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')) !!}  
                  
                  <input type="hidden" name="id" value="{{ isset($info->id) ? $info->id : null }}">
  
                   
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.section_title')}}*</label>
                    <div class="col-sm-8">
                      <input type="text" name="section_name" value="{{ isset($info->section_name) ? stripslashes($info->section_name) : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.type')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="post_type" id="home_section_type">                               
                                <option value="" >{{trans('words.select')}}</option>
                                <option value="category" @if(isset($info->post_type) AND $info->post_type=="category") selected @endif>{{trans('words.category')}}</option>
                                <option value="subcategory" @if(isset($info->post_type) AND $info->post_type=="subcategory") selected @endif>{{trans('words.sub_category')}}</option>
                                <option value="author" @if(isset($info->post_type) AND $info->post_type=="author") selected @endif>{{trans('words.authors_text')}}</option>
                                <option value="book" @if(isset($info->post_type) AND $info->post_type=="book") selected @endif>{{trans('words.books_text')}}</option>                                     
                            </select>
                      </div>
                  </div>

                  <div class="form-group row" id="category_list_sec" @if(isset($info->post_type) AND $info->post_type!="category") style="display:none;" @endif @if(!isset($info->id)) style="display:none;" @endif>
                    <label class="col-sm-3 col-form-label">{{trans('words.category')}} </label> 
                      <div class="col-sm-8">
                            <select name="category[]" class="select2 select2-multiple" multiple="multiple" multiple id="home_cat_id" data-placeholder="{{trans('words.select_category')}}"> 
                                 @foreach($category_list as $category_data)
                                  <option value="{{$category_data->id}}" @if(isset($info->id) && in_array($category_data->id, explode(",",$info->post_ids))) selected @endif>{{stripslashes($category_data->category_name)}}</option>
                                @endforeach
                            </select>                             
                      </div>
                  </div>

                  <div class="form-group row" id="sub_category_list_sec" @if(isset($info->post_type) AND $info->post_type!="subcategory") style="display:none;" @endif @if(!isset($info->id)) style="display:none;" @endif>
                    <label class="col-sm-3 col-form-label">{{trans('words.sub_category')}} </label> 
                      <div class="col-sm-8">
                            <select name="sub_category[]" class="select2 select2-multiple" multiple="multiple" multiple id="home_sub_cat_id" data-placeholder="{{trans('words.select_sub_category')}}">
 
                                 @foreach($sub_cat_list as $sub_cat_data)
                                  <option value="{{$sub_cat_data->id}}" @if(isset($info->id) && in_array($sub_cat_data->id, explode(",",$info->post_ids))) selected @endif>{{stripslashes($sub_cat_data->sub_category_name)}}</option>
                                @endforeach
                            </select>                             
                      </div>
                  </div>

                  <div class="form-group row" id="authors_list_sec" @if(isset($info->post_type) AND $info->post_type!="author") style="display:none;" @endif @if(!isset($info->id)) style="display:none;" @endif>
                    <label class="col-sm-3 col-form-label">{{trans('words.authors_text')}}</label> 
                      <div class="col-sm-8">
                            <select name="authors[]" class="select2 select2-multiple" multiple="multiple" multiple id="home_authors_id" data-placeholder="{{trans('words.select_author')}}">
                                 @foreach($authors_list as $authors_data)
                                  <option value="{{$authors_data->id}}" @if(isset($info->id) && in_array($authors_data->id, explode(",",$info->post_ids))) selected @endif>{{stripslashes($authors_data->name)}}</option>
                                @endforeach
                            </select>
                      </div>
                  </div>

                  <div class="form-group row" id="book_list_sec" @if(isset($info->post_type) AND $info->post_type!="book") style="display:none;" @endif @if(!isset($info->id)) style="display:none;" @endif>
                    <label class="col-sm-3 col-form-label">{{trans('words.books_text')}}</label> 
                      <div class="col-sm-8">
                            <select name="books[]" class="select2 select2-multiple" multiple="multiple" multiple id="home_books_id" data-placeholder="{{trans('words.select_book')}}">
                                 @foreach($books_list as $books_data)
                                  <option value="{{$books_data->id}}" @if(isset($info->id) && in_array($books_data->id, explode(",",$info->post_ids))) selected @endif>{{stripslashes($books_data->title)}}</option>
                                @endforeach
                            </select>
                      </div>
                  </div>

                   
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.status')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="status">                               
                                <option value="1" @if(isset($info->status) AND $info->status==1) selected @endif>{{trans('words.active')}}</option>
                                <option value="0" @if(isset($info->status) AND $info->status==0) selected @endif>{{trans('words.inactive')}}</option>                            
                            </select>
                      </div>
                  </div>
                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save')}}</button>                      
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

    if(requestingField=="playlist_image")
    {
      var target_preview = $('#image_holder');
      target_preview.html('');
      target_preview.append(
              $('<img>').css('height', '5rem').attr('src', elfinderUrl + filePath.replace(/\\/g,"/"))
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