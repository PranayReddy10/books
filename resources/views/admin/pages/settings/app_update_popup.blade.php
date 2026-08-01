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
                 
                
                

                 {!! Form::open(array('url' => array('admin/app_update_popup'),'class'=>'form-horizontal','name'=>'settings_form','id'=>'settings_form','role'=>'form','enctype' => 'multipart/form-data')) !!}  
                  
                  <input type="hidden" name="id" value="{{ isset($settings->id) ? $settings->id : null }}">
   
  
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.app_update_popup')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="app_update_hide_show">                               
                                <option value="true" @if(isset($settings->app_update_hide_show) AND $settings->app_update_hide_show=='true') selected @endif>True</option>
                                <option value="false" @if(isset($settings->app_update_hide_show) AND $settings->app_update_hide_show=='false') selected @endif>False</option>                            
                            </select>
                      </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.app_update_new_version')}}</label>
                    <div class="col-sm-8">
                      <input type="text" name="app_update_version_code" value="{{ isset($settings->app_update_version_code) ? $settings->app_update_version_code : null }}" class="form-control" placeholder="1.1">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.description')}}</label>
                    <div class="col-sm-8">
                      <textarea name="app_update_desc" class="form-control" placeholder="Please update new app">{{ isset($settings->app_update_desc) ? stripslashes($settings->app_update_desc) : null }}</textarea>
                      
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.app_update_link')}}</label>
                    <div class="col-sm-8">
                      <input type="text" name="app_update_link" value="{{ isset($settings->app_update_link) ? $settings->app_update_link : null }}" class="form-control" placeholder="https://play.google.com/store/apps/details?id=com.posts365.brandingapp">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.app_update_cancel_option')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="app_update_cancel_option">                               
                                <option value="true" @if(isset($settings->app_update_cancel_option) AND $settings->app_update_cancel_option=='true') selected @endif>True</option>
                                <option value="false" @if(isset($settings->app_update_cancel_option) AND $settings->app_update_cancel_option=='false') selected @endif>False</option>                            
                            </select>
                      </div>
                  </div> 
                    
                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save_settings')}} </button>                      
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