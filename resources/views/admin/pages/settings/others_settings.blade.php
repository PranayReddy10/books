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
                  
                 {!! Form::open(array('url' => array('admin/others_settings'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')) !!} 
                  
                  
                  <input type="hidden" name="id" value="{{ isset($settings->id) ? $settings->id : null }}">

                  <div class="row">

                    <div class="col-md-6"> 

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.cat_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="cat_by_name_id">                                                                
                              <option value="category_name" @if($settings->cat_by_name_id=="category_name") selected @endif>Name</option>
                              <option value="id" @if($settings->cat_by_name_id=="id") selected @endif>ID</option>                                  
                          </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.cat_order_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="cat_order_by">                                                                
                              <option value="ASC" @if($settings->cat_order_by=="ASC") selected @endif>ASC (Ascending)</option>
                              <option value="DESC" @if($settings->cat_order_by=="DESC") selected @endif>DESC (Descending)</option>                                  
                          </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.subcat_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="subcat_by_name_id">                                                                
                              <option value="sub_category_name" @if($settings->subcat_by_name_id=="sub_category_name") selected @endif>Name</option>
                              <option value="id" @if($settings->subcat_by_name_id=="id") selected @endif>ID</option>                                  
                          </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.subcat_order_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="subcat_order_by">                                                                
                              <option value="ASC" @if($settings->subcat_order_by=="ASC") selected @endif>ASC (Ascending)</option>
                              <option value="DESC" @if($settings->subcat_order_by=="DESC") selected @endif>DESC (Descending)</option>                                  
                          </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.author_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="author_by_name_id">                                                                
                              <option value="name" @if($settings->author_by_name_id=="name") selected @endif>Name</option>
                              <option value="id" @if($settings->author_by_name_id=="id") selected @endif>ID</option>                                  
                          </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.author_order_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="author_order_by">                                                                
                              <option value="ASC" @if($settings->author_order_by=="ASC") selected @endif>ASC (Ascending)</option>
                              <option value="DESC" @if($settings->author_order_by=="DESC") selected @endif>DESC (Descending)</option>                                  
                          </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.book_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="book_by_name_id">                                                                
                              <option value="title" @if($settings->book_by_name_id=="title") selected @endif>Title</option>
                              <option value="id" @if($settings->book_by_name_id=="id") selected @endif>ID</option>                                  
                          </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.book_order_by')}}</label>
                    <div class="col-sm-8">
                          <select class="form-control" name="book_order_by">                                                                
                              <option value="ASC" @if($settings->book_order_by=="ASC") selected @endif>ASC (Ascending)</option>
                              <option value="DESC" @if($settings->book_order_by=="DESC") selected @endif>DESC (Descending)</option>                                  
                          </select>
                    </div>
                  </div>

                </div>  

                <div class="col-md-6"> 
    
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.pagination_limit')}}</label>
                    <div class="col-sm-8">
                      <input type="number" name="pagination_limit" value="{{ isset($settings->pagination_limit) ? $settings->pagination_limit : null }}" class="form-control" placeholder="10" min="10">
                    </div>
                  </div>
 
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.continue_read_limit')}}

                    <small id="emailHelp" class="form-text text-muted">(Home Screen Only)</small>
                    </label>
                    <div class="col-sm-8">
                      <input type="number" name="continue_read_limit" value="{{ isset($settings->continue_read_limit) ? $settings->continue_read_limit : null }}" class="form-control" placeholder="5" min="1">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.trending_limit')}}
                    <small id="emailHelp" class="form-text text-muted">(Home Screen Only)</small>
                    </label>
                    <div class="col-sm-8">
                      <input type="number" name="trending_limit" value="{{ isset($settings->trending_limit) ? $settings->trending_limit : null }}" class="form-control" placeholder="10" min="1">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.latest_limit')}}</label>
                    <div class="col-sm-8">
                      <input type="number" name="latest_limit" value="{{ isset($settings->latest_limit) ? $settings->latest_limit : null }}" class="form-control" placeholder="10" min="10">
                    </div>
                  </div>
                    
                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save_settings')}} </button>                      
                    </div>
                  </div>
                
                </div>

                {!! Form::close() !!} 
            </div>  

                
              </div>
            </div>            
          </div>              
        </div>
      </div>
      @include("admin.copyright") 
    </div> 
 
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