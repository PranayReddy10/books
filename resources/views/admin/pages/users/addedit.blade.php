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
                  
                 {!! Form::open(array('url' => array('admin/users/add_edit'),'class'=>'form-horizontal','name'=>'user_form','id'=>'user_form','role'=>'form','enctype' => 'multipart/form-data')) !!}  
                  
                  <input type="hidden" name="id" value="{{ isset($user->id) ? $user->id : null }}">
 
                   
                   
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.name')}}</label>
                    <div class="col-sm-8">
                      <input type="text" name="name" value="{{ isset($user->name) ? $user->name : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-8">
                      <input type="text" name="username" value="{{ isset($user->username) ? $user->username : null }}" class="form-control" placeholder="Auto-generated if left blank">
                      <small class="text-muted">Unique handle shown in the app (e.g. pranay_reddy_82). Leave blank to auto-generate.</small>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.email')}}</label>
                    <div class="col-sm-8">
                      <input type="text" name="email" value="{{ isset($user->email) ? $user->email : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.password')}}</label>
                    <div class="col-sm-8">
                      <input type="password" name="password" value="" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.phone')}}</label>
                    <div class="col-sm-8">
                      <input type="text" name="phone" value="{{ isset($user->phone) ? $user->phone : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Roll Number</label>
                    <div class="col-sm-8">
                      <input type="text" name="rollnumber" value="{{ isset($user->rollnumber) ? $user->rollnumber : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">University</label>
                    <div class="col-sm-8">
                      <select class="form-control" name="university">
                        <option value="">Select University</option>
                        @foreach($university_list as $uni)
                          <option value="{{ stripslashes($uni->university_name) }}" @if(isset($user->university) AND $user->university==$uni->university_name) selected @endif>{{ stripslashes($uni->university_name) }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Department</label>
                    <div class="col-sm-8">
                      <select class="form-control" name="department_id">
                        <option value="">Select Department</option>
                        @foreach($department_list as $dept)
                          <option value="{{$dept->id}}" @if(isset($user->department_id) AND $user->department_id==$dept->id) selected @endif>{{ stripslashes($dept->department_name) }}@if($dept->university_id) ({{ \App\University::getUniversityInfo($dept->university_id,'university_name') }})@endif</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">College</label>
                    <div class="col-sm-8">
                      <select class="form-control" name="college">
                        <option value="">Select College</option>
                        @foreach($college_list as $college)
                          <option value="{{ stripslashes($college->college_name) }}" @if(isset($user->college) AND $user->college==$college->college_name) selected @endif>{{ stripslashes($college->college_name) }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Gender</label>
                    <div class="col-sm-8">
                      <select class="form-control" name="gender">
                        <option value="">Select Gender</option>
                        @foreach(['Male','Female','Other'] as $g)
                          <option value="{{ $g }}" @if(isset($user->gender) AND $user->gender==$g) selected @endif>{{ $g }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                 
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.image')}}</label>
                    <div class="col-sm-8">
                      <input type="file" name="user_image" class="form-control">                     
                    </div>
                  </div>

                  @if(has_user_image($user->user_image)) 
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                    <div class="col-sm-8">
                      <img src="{{ user_image_url($user->user_image) }}" alt="video image" class="img-thumbnail" width="140">       
                    </div>
                  </div>
                  @endif    
                  
                  <div class="form-group row">
                    <label class="control-label col-sm-3">{{trans('words.expiry_date')}}</label>
                    <div class="col-sm-8">
                      <div class="input-group"> 
                        <input type="text" id="datepicker-autoclose" name="exp_date" value="{{ isset($user->exp_date) ? date('m/d/Y',$user->exp_date) : null }}" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off">
                         
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.subscription_plan')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="subscription_plan">                               
                                @foreach($plan_list as $plan_data)
                                  <option value="{{$plan_data->id}}" @if(isset($user->plan_id) AND $user->plan_id==$plan_data->id) selected @endif>{{$plan_data->plan_name}}</option>
                                @endforeach                            
                            </select>
                      </div>
                  </div>
                                  
                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{trans('words.status')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="status">                               
                                <option value="1" @if(isset($user->status) AND $user->status==1) selected @endif>{{trans('words.active')}}</option>
                                <option value="0" @if(isset($user->status) AND $user->status==0) selected @endif>{{trans('words.inactive')}}</option>                            
                            </select>
                      </div>
                  </div>

                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save')}} </button>                      
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