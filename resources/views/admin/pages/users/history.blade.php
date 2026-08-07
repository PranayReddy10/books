@extends("admin.admin_app")

@section("content")

 
 
  <div class="content-page">
      <div class="content">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="bg-picture card-box">

                                    <div class="p-t-10 pull-right">@if($user->status==1)<span class="badge badge-success">{{trans('words.active')}}</span> @else<span class="badge badge-danger">{{trans('words.inactive')}}</span>@endif</div>

                                    <div class="profile-info-name">
                                        
                                        @if($user->user_image)
                                          <img src="{{ URL::asset('upload/'.$user->user_image) }}" class="img-thumbnail" alt="profile_img" style="width: 155px">
                                        @else  
                                          <img src="{{ URL::asset('upload/profile.jpg') }}" class="img-thumbnail" alt="profile_img" style="width: 155px">
                                        @endif


                                        <div class="profile-info-detail">
                                            <h4 class="m-0">{{$user->name}}</h4>
                                            @if($user->username)<span class="text-primary" style="font-size:13px;">&#64;{{ $user->username }}</span>@endif
                                             <p class="text-muted m-b-20" style="margin-top:6px;">
                                               <b>{{trans('words.email')}}:</b> {{$user->email}} <br/>
                                               <b>{{trans('words.phone')}}:</b> {{$user->phone ?: '-'}} <br/>
                                               <b>Roll Number:</b> {{ $user->rollnumber ?: '-' }} <br/>
                                               <b>University:</b> {{ $user->university ?: '-' }} <br/>
                                               <b>Department:</b> {{ $user->department_id ? \App\Department::getDepartmentInfo($user->department_id,'department_name') : '-' }} <br/>
                                               <b>College:</b> {{ $user->college ?: '-' }} <br/>
                                               <b>Gender:</b> {{ $user->gender ?: '-' }} <br/>
                                               <b>Joined:</b> {{ $user->created_at ? date('M d, Y', strtotime($user->created_at)) : '-' }} <br/>
                                               <b>Signed up via:</b>
                                               @php
                                                 $src = $user->source_label;
                                               @endphp
                                               @if($user->social_login_type)
                                                 <span class="badge badge-info">{{ ucfirst($user->social_login_type) }}</span>
                                               @endif
                                               @if($user->registered_via == 'app')
                                                 <span class="badge badge-primary">App</span>
                                               @elseif($user->registered_via == 'website')
                                                 <span class="badge badge-warning">Website</span>
                                               @else
                                                 <span class="badge badge-secondary">Unknown</span>
                                               @endif
                                               <br/>
                                               <b>Last login:</b>
                                               @if($user->last_login_at)
                                                 {{ date('M d, Y h:i A', strtotime($user->last_login_at)) }}
                                                 @if($user->last_login_via)
                                                   <span class="badge badge-light">{{ ucfirst($user->last_login_via) }}</span>
                                                 @endif
                                               @else
                                                 <span class="text-muted">Never recorded</span>
                                               @endif
                                             </p>
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <!--/ meta -->

                                 
                            </div>

                            <div class="col-sm-4">
                                <div class="card-box">
                                     

                                    <h4 class="header-title m-t-0 m-b-30">{{trans('words.subscription_plan')}}</h4>

                                    <ul class="list-group m-b-0 user-list">
                                        <li class="list-group-item">
                                            <a href="#" class="user-list-item">
                                                <div class="avatar">
                                                    <i class="mdi mdi-circle text-primary"></i>
                                                </div>
                                                <div class="user-desc">
                                                    <span class="name">{{\App\SubscriptionPlan::getSubscriptionPlanInfo($user->plan_id,'plan_name')}}</span>
                                                    <span class="desc">{{trans('words.current_plan')}}</span>
                                                </div>
                                            </a>
                                        </li>

                                        <li class="list-group-item">
                                            <a href="#" class="user-list-item">
                                                <div class="avatar">
                                                    <i class="mdi mdi-circle text-success"></i>
                                                </div>
                                                <div class="user-desc">
                                                    <span class="name">@if($user->exp_date){{date('F,  d, Y',$user->exp_date)}}@endif</span>
                                                    <span class="desc">{{trans('words.subscription_expires_on')}}</span>
                                                </div>
                                            </a>
                                        </li>
 
                                    </ul>
                                </div>


                                 

                            </div>


                        </div>
                        <div class="row">
                          <div class="col-sm-12">
                               
                            <div class="card-box">

                              <h4 class="header-title m-t-0 m-b-30">{{trans('words.user_transactions')}}</h4>
                              <div class="table-responsive">
                               <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>{{trans('words.email')}}</th>
                                      <th>{{trans('words.plan')}} / {{trans('words.book_on_rent')}}</th>
                                      <th>{{trans('words.amount')}}</th>
                                      <th>{{trans('words.payment_gateway')}}</th>
                                      <th>{{trans('words.payment_id')}}</th>
                                      <th>{{trans('words.payment_date')}}</th>                      
                                       
                                    </tr>
                                  </thead>
                                  <tbody>
                                   @foreach($transactions_list as $i => $transaction_data)
                                    <tr>
                                      <td>{{ $transaction_data->email }}</td>
                                      <td>
                                      @if($transaction_data->rent_id)
                                        {{\App\Books::getBookInfo($transaction_data->rent_id,'title')}}
                                      @else
                                        {{\App\SubscriptionPlan::getSubscriptionPlanInfo($transaction_data->plan_id,'plan_name')}}
                                      @endif
                                      </td>
                                      <td>{{html_entity_decode(getCurrencySymbols(getcong('currency_code')))}} {{ $transaction_data->payment_amount }}</td>
                                      <td>{{ $transaction_data->gateway }}</td>
                                      <td>{{ $transaction_data->payment_id }}</td>
                                      <td>{{ date('M d Y h:i A',$transaction_data->date) }}</td>                                              
                                       
                                    </tr>
                                   @endforeach
                                     
                                     
                                     
                                  </tbody>
                                </table>
                              </div>  
                                <nav class="paging_simple_numbers">
                                @include('admin.pagination', ['paginator' => $transactions_list]) 
                                </nav>          
                            </div>

                            <div class="card-box">
                              <h4 class="header-title m-t-0 m-b-30">Uploaded Books ({{ count($uploaded_books) }})</h4>
                              <div class="table-responsive">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Title</th>
                                      <th>Category</th>
                                      <th>Type</th>
                                      <th>Upload Status</th>
                                      <th>Live?</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @forelse($uploaded_books as $ub)
                                    <tr>
                                      <td>{{ $ub->id }}</td>
                                      <td><a href="{{ url('admin/books/edit/'.$ub->id) }}">{{ Str::limit(stripslashes($ub->title), 40) }}</a></td>
                                      <td>{{ \App\Category::getCategoryInfo($ub->cat_id,'category_name') }}</td>
                                      <td>{{ $ub->url_type=='local' ? 'File' : 'Link' }}</td>
                                      <td>
                                        @if($ub->upload_status=='approved')<span class="badge badge-success">Approved</span>
                                        @elseif($ub->upload_status=='rejected')<span class="badge badge-danger">Rejected</span>
                                        @else<span class="badge badge-warning">Pending</span>@endif
                                        @if($ub->reject_reason)<br><small class="text-muted">{{ $ub->reject_reason }}</small>@endif
                                      </td>
                                      <td>@if($ub->status==1)<span class="badge badge-success">Yes</span>@else<span class="badge badge-secondary">No</span>@endif</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted" style="padding:18px;">This user hasn't uploaded any books.</td></tr>
                                    @endforelse
                                  </tbody>
                                </table>
                              </div>

                              <h4 class="header-title m-t-0 m-b-30" style="margin-top:30px;">Uploaded Posts ({{ count($uploaded_posts) }})</h4>
                              <div class="table-responsive">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Preview</th>
                                      <th>Title</th>
                                      <th>Type</th>
                                      <th>Date</th>
                                      <th>Upload Status</th>
                                      <th>Live?</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @forelse($uploaded_posts as $up)
                                    <tr>
                                      <td>{{ $up->id }}</td>
                                      <td>
                                        @php $prev = $up->media_type=='video' ? ($up->thumb_url ?: '') : $up->file_url; @endphp
                                        @if($prev)<img src="{{ $prev }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;" onerror="this.style.display='none'">@endif
                                      </td>
                                      <td><a href="{{ url('admin/media/edit/'.$up->id) }}">{{ Str::limit(stripslashes($up->title), 40) ?: 'Untitled' }}</a></td>
                                      <td>{{ ucfirst($up->media_type) }}</td>
                                      <td><small>{{ $up->created_at ? $up->created_at->format('d M Y') : '-' }}</small></td>
                                      <td>
                                        @if($up->upload_status=='approved')<span class="badge badge-success">Approved</span>
                                        @elseif($up->upload_status=='rejected')<span class="badge badge-danger">Rejected</span>
                                        @else<span class="badge badge-warning">Pending</span>@endif
                                        @if($up->reject_reason)<br><small class="text-muted">{{ $up->reject_reason }}</small>@endif
                                      </td>
                                      <td>@if($up->status==1)<span class="badge badge-success">Yes</span>@else<span class="badge badge-secondary">No</span>@endif</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted" style="padding:18px;">This user hasn't uploaded any posts.</td></tr>
                                    @endforelse
                                  </tbody>
                                </table>
                              </div>
                            </div>

                            <div class="card-box">
                              <h4 class="header-title m-t-0 m-b-30" style="margin-top:30px;">Books Read / Reading ({{ count($reading_history) }})</h4>
                              <div class="table-responsive">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Book</th>
                                      <th>Last Page</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @forelse($reading_history as $rh)
                                    <tr>
                                      <td>{{ $rh->post_id }}</td>
                                      <td>
                                        @php $rtitle = \App\Books::getBookInfo($rh->post_id,'title'); @endphp
                                        @if($rtitle)
                                          <a href="{{ url('admin/books/edit/'.$rh->post_id) }}">{{ Str::limit(stripslashes($rtitle), 50) }}</a>
                                        @else
                                          <span class="text-muted">Book #{{ $rh->post_id }} (removed)</span>
                                        @endif
                                      </td>
                                      <td>{{ $rh->page_num ?: '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted">No reading activity recorded yet.</td></tr>
                                    @endforelse
                                  </tbody>
                                </table>
                              </div>
                            </div>

                          </div> 
                        </div>

                    </div> <!-- container -->

                </div> <!-- content -->
      @include("admin.copyright") 
    </div> 
  

@endsection