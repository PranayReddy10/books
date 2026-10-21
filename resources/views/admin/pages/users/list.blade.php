@extends("admin.admin_app")

@section("content")

  
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                @php
                  $active_filters = array_filter(\Illuminate\Support\Arr::except($filters, ['s']), 'strlen');
                  $sel = function ($key, $value) use ($filters) { return (string) $filters[$key] === (string) $value ? 'selected' : ''; };
                @endphp

                <div class="row">
                  <div class="col-md-5">
                    <form method="get" action="{{ URL::to('admin/users') }}" class="app-search" role="form">
                      @foreach($active_filters as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                      <input type="text" name="s" value="{{ $filters['s'] }}" placeholder="Search name, email, phone, hall ticket…" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                  </div>
                  <div class="col-md-7 users-toolbar-actions">
                    <button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#userFilters" aria-expanded="{{ count($active_filters) ? 'true' : 'false' }}">
                      <i class="fa fa-sliders"></i> Filters @if(count($active_filters))<span class="filter-count">{{ count($active_filters) }}</span>@endif
                    </button>
                    <a href="{{ URL::to('admin/users/export').(count(array_filter($filters,'strlen')) ? '?'.http_build_query(array_filter($filters,'strlen')) : '') }}" class="btn btn-info" title="{{trans('words.export_user')}}"><i class="fa fa-file-excel-o"></i> {{ count(array_filter($filters,'strlen')) ? 'Export filtered' : trans('words.export_user') }}</a>
                    <a href="{{URL::to('admin/users/add')}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('words.add_user')}}</a>
                  </div>
                </div>

                <form method="get" action="{{ URL::to('admin/users') }}" id="userFilters" class="collapse user-filters {{ count($active_filters) ? 'show' : '' }}">
                  @if($filters['s'] !== '')<input type="hidden" name="s" value="{{ $filters['s'] }}">@endif
                  <div class="user-filters-grid">
                    <label>University
                      <select name="university" class="form-control">
                        <option value="">All</option>
                        @foreach($universities as $u)<option value="{{ $u }}" {{ $sel('university', $u) }}>{{ $u }}</option>@endforeach
                      </select>
                    </label>
                    <label>Department
                      <select name="department_id" class="form-control">
                        <option value="">All</option>
                        @foreach($departments as $d)<option value="{{ $d->id }}" {{ $sel('department_id', $d->id) }}>{{ stripslashes($d->department_name) }}</option>@endforeach
                      </select>
                    </label>
                    <label>College
                      <select name="college" class="form-control">
                        <option value="">All</option>
                        @foreach($colleges as $c)<option value="{{ $c }}" {{ $sel('college', $c) }}>{{ $c }}</option>@endforeach
                      </select>
                    </label>
                    <label>Gender
                      <select name="gender" class="form-control">
                        <option value="">All</option>
                        @foreach(['Male','Female','Other'] as $g)<option value="{{ $g }}" {{ $sel('gender', $g) }}>{{ $g }}</option>@endforeach
                      </select>
                    </label>
                    <label>Status
                      <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="active" {{ $sel('status','active') }}>Active</option>
                        <option value="inactive" {{ $sel('status','inactive') }}>Inactive</option>
                      </select>
                    </label>
                    <label>Verified
                      <select name="verified" class="form-control">
                        <option value="">All</option>
                        <option value="yes" {{ $sel('verified','yes') }}>Verified</option>
                        <option value="no" {{ $sel('verified','no') }}>Not verified</option>
                      </select>
                    </label>
                    <label>Signed up with
                      <select name="signup" class="form-control">
                        <option value="">All</option>
                        <option value="google" {{ $sel('signup','google') }}>Google</option>
                        <option value="email" {{ $sel('signup','email') }}>Email</option>
                      </select>
                    </label>
                    <label>Profile
                      <select name="profile" class="form-control">
                        <option value="">All</option>
                        <option value="complete" {{ $sel('profile','complete') }}>Complete</option>
                        <option value="incomplete" {{ $sel('profile','incomplete') }}>Missing details</option>
                      </select>
                    </label>
                    <label>Joined
                      <select name="joined" class="form-control">
                        <option value="">Any time</option>
                        <option value="today" {{ $sel('joined','today') }}>Today</option>
                        <option value="7" {{ $sel('joined','7') }}>Last 7 days</option>
                        <option value="30" {{ $sel('joined','30') }}>Last 30 days</option>
                        <option value="90" {{ $sel('joined','90') }}>Last 90 days</option>
                      </select>
                    </label>
                  </div>
                  <div class="user-filters-actions">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Apply</button>
                    @if(count($active_filters))<a href="{{ URL::to('admin/users').($filters['s'] !== '' ? '?s='.urlencode($filters['s']) : '') }}" class="btn btn-secondary">Reset</a>@endif
                  </div>
                </form>

                <div class="user-results">
                  <span><b>{{ number_format($user_list->total()) }}</b> {{ $user_list->total() == 1 ? 'student' : 'students' }}@if(count(array_filter($filters,'strlen'))) match @endif</span>
                  @if(count(array_filter($filters,'strlen')))<a href="{{ URL::to('admin/users') }}"><i class="fa fa-times"></i> Clear all</a>@endif
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif
                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{{trans('words.name')}}</th>
                      <th>{{trans('words.email')}}</th>
                      <th>{{trans('words.phone')}}</th>
                      <th>Roll No</th>
                      <th>University</th>
                      <th>Department</th>
                      <th>College</th>
                      <th>Gender</th>
                      <th>{{trans('words.status')}}</th>                        
                      <th>{{trans('words.action')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                   @if($user_list->isEmpty())
                    <tr><td colspan="10">No students match these filters.</td></tr>
                   @endif
                   @foreach($user_list as $i => $user_data)
                    <tr id="post_id_{{$user_data->id}}">
                      <td><a href="{{ url('admin/users/history/'.$user_data->id) }}" data-toggle="tooltip" title="User History">{{ $user_data->name }}</a>@if($user_data->username)<br><small class="text-muted">&#64;{{ $user_data->username }}</small>@endif</td>
                      <td>{{ $user_data->email }}</td>
                      <td>{{ $user_data->phone }}</td>
                      <td>{{ $user_data->rollnumber ?: '-' }}</td>
                      <td>{{ $user_data->university ?: '-' }}</td>
                      <td>{{ $user_data->department_id ? \App\Department::getDepartmentInfo($user_data->department_id,'department_name') : '-' }}</td>
                      <td>{{ $user_data->college ?: '-' }}</td>
                      <td>{{ $user_data->gender ?: '-' }}</td>
                      <td>@if($user_data->status==1)<span class="badge badge-success">{{trans('words.active')}}</span> @else<span class="badge badge-danger">{{trans('words.inactive')}}</span>@endif
                      @if(isset($user_data->is_verified) && $user_data->is_verified)<br><span class="badge badge-info" style="margin-top:4px;"><i class="fa fa-check-circle"></i> Verified</span>@endif
                      @if(!$user_data->phone || !$user_data->gender || !$user_data->university || !$user_data->department_id || !$user_data->college)<br><span class="badge badge-warning" style="margin-top:4px;" title="Missing phone, gender or college details">Incomplete</span>@endif</td>
                                             
                      <td>
                       
                      <a href="{{ url('admin/users/edit/'.$user_data->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="{{trans('words.edit')}}"> <i class="fa fa-edit"></i> </a>

                      <a href="{{ url('admin/users/verify/'.$user_data->id) }}" class="btn btn-icon waves-effect waves-light {{ (isset($user_data->is_verified) && $user_data->is_verified) ? 'btn-info' : 'btn-secondary' }} m-b-5 m-r-5" data-toggle="tooltip" title="{{ (isset($user_data->is_verified) && $user_data->is_verified) ? 'Un-verify (uploads will need approval)' : 'Verify (uploads auto-approve)' }}"> <i class="fa fa-check-circle"></i> </a>
                      
                      <a href="#" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 data_remove" data-toggle="tooltip" title="{{trans('words.remove')}}" data-id="{{$user_data->id}}"> <i class="fa fa-remove"></i> </a>
                                  
                      </td>
                    </tr>
                   @endforeach
                     
                     
                     
                  </tbody>
                </table>
              </div>
                <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $user_list]) 
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
 
 $(".data_remove").click(function () {  
   
   var post_id = $(this).data("id");
   var action_name='user_delete';
 
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
             data: {"_token": "{{ csrf_token() }}",id: post_id, action_for: action_name},
             success: function(res) {
 
               if(res.status=='1')
               {  
 
                   var selector = "#post_id_"+post_id;
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
 
 </script>    
    

@endsection