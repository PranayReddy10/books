@extends("admin.admin_app")

@section("content")

{{-- One student's account: who they are, what they have done, what they paid.
     Styles are scoped under .uh and use the admin theme's tokens. --}}
<style>
.uh .gap{ margin-bottom:20px; }
.uh .panel{ background:#fff; border:1px solid var(--ad-line); border-radius:var(--ad-radius); box-shadow:var(--ad-shadow); padding:22px; height:100%; }
.uh .panel h4{ font-size:15px; font-weight:700; margin:0 0 14px; color:var(--ad-ink); }

/* Header */
.uh .hero{ display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
.uh .avatar{ width:84px; height:84px; border-radius:50%; object-fit:cover; flex:0 0 84px; border:3px solid #fff; box-shadow:0 0 0 1px var(--ad-line); }
.uh .avatar-initials{ display:flex; align-items:center; justify-content:center; background:var(--ad-accent-soft); color:var(--ad-accent); font-size:28px; font-weight:700; }
.uh .hero-main{ flex:1; min-width:240px; }
.uh .hero-name{ font-size:22px; font-weight:700; margin:0; color:var(--ad-ink); letter-spacing:-.02em; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.uh .hero-sub{ color:var(--ad-muted); font-size:13.5px; margin-top:4px; display:flex; gap:14px; flex-wrap:wrap; }
.uh .hero-sub i{ color:var(--ad-faint); margin-right:5px; }
.uh .hero-actions{ display:flex; gap:8px; flex-wrap:wrap; }

/* Stat strip */
.uh .stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; }
.uh .stat{ background:#fff; border:1px solid var(--ad-line); border-radius:12px; padding:14px 16px; }
.uh .stat-num{ font-size:22px; font-weight:700; color:var(--ad-ink); line-height:1.15; }
.uh .stat-lbl{ font-size:12.5px; color:var(--ad-muted); margin-top:2px; }

/* Details */
.uh .dl{ display:grid; grid-template-columns:150px 1fr; gap:0; margin:0; }
.uh .dl dt, .uh .dl dd{ padding:10px 0; border-bottom:1px solid var(--ad-line-2); margin:0; font-size:13.5px; }
.uh .dl dt{ color:var(--ad-muted); font-weight:500; }
.uh .dl dd{ color:var(--ad-ink); font-weight:500; word-break:break-word; }
.uh .dl dt:nth-last-child(2), .uh .dl dd:last-child{ border-bottom:0; }
.uh .none{ color:var(--ad-faint); font-weight:400; }
.uh .missing{ display:flex; gap:10px; align-items:flex-start; background:var(--ad-amber-soft); border:1px solid #f3dbb0; color:#92400e; border-radius:10px; padding:10px 12px; font-size:13px; margin-bottom:14px; }
.uh .missing i{ margin-top:2px; }

/* Results card */
.uh .rc{ display:flex; align-items:center; gap:22px; flex-wrap:wrap; }
.uh .rc-ico{ width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:var(--ad-green-soft); color:var(--ad-green); font-size:20px; flex:0 0 46px; }
.uh .rc-ico-muted{ background:var(--ad-surface-2); color:var(--ad-faint); }
.uh .rc-main{ flex:1; min-width:220px; }
.uh .rc-main h4{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.uh .rc-sub{ font-size:13px; color:var(--ad-muted); margin-top:4px; }
.uh .rc-num{ text-align:center; min-width:70px; }
.uh .rc-num b{ display:block; font-size:20px; color:var(--ad-ink); line-height:1.1; }
.uh .rc-num span{ font-size:12px; color:var(--ad-muted); }
.uh .rc-num.is-bad b{ color:var(--ad-red); }

/* Tabs */
.uh .tabs-panel{ padding:0; overflow:hidden; }
.uh .tabs-panel .nav-tabs{ padding:0 18px; background:var(--ad-surface-2); }
.uh .tabs-panel .nav-tabs .nav-link{ padding:14px 10px; margin-right:10px; }
.uh .tab-count{ display:inline-block; min-width:20px; padding:1px 7px; border-radius:999px; background:var(--ad-line); color:var(--ad-ink-2); font-size:11.5px; margin-left:6px; }
.uh .nav-link.active .tab-count{ background:var(--ad-accent-soft); color:var(--ad-accent); }
.uh .tab-content{ padding:18px; }
.uh .empty{ text-align:center; color:var(--ad-muted); padding:36px 10px; font-size:13.5px; }
.uh .empty i{ display:block; font-size:24px; color:var(--ad-faint); margin-bottom:8px; }
.uh .thumb{ width:44px; height:44px; object-fit:cover; border-radius:8px; }
</style>

@php
  $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(function ($w) { return mb_strtoupper(mb_substr($w, 0, 1)); })->implode('');
  $currency = html_entity_decode(getCurrencySymbols(getcong('currency_code')));
  $department = $user->department_id ? \App\Department::getDepartmentInfo($user->department_id,'department_name') : '';
  $plan_name = $user->plan_id ? \App\SubscriptionPlan::getSubscriptionPlanInfo($user->plan_id,'plan_name') : '';
  $plan_active = $user->exp_date && $user->exp_date >= time();
  $show = function ($v) { return trim((string) $v) !== '' ? e($v) : '<span class="none">Not given</span>'; };
@endphp

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="uh">

        <a href="{{ URL::to('admin/users') }}" class="btn btn-secondary btn-sm gap"><i class="fa fa-arrow-left"></i> All users</a>

        @if(Session::has('flash_message'))
          <div class="alert alert-info gap">{{ Session::get('flash_message') }}</div>
        @endif

        {{-- Header --}}
        <div class="panel gap">
          <div class="hero">
            @if(has_user_image($user->user_image))
              <img src="{{ user_image_url($user->user_image) }}" class="avatar" alt="" onerror="this.outerHTML='<div class=\'avatar avatar-initials\'>{{ $initials }}</div>'">
            @else
              <div class="avatar avatar-initials">{{ $initials }}</div>
            @endif

            <div class="hero-main">
              <h3 class="hero-name">
                {{ $user->name }}
                @if($user->status==1)<span class="badge badge-success">{{trans('words.active')}}</span>@else<span class="badge badge-danger">{{trans('words.inactive')}}</span>@endif
                @if($user->is_verified)<span class="badge badge-info"><i class="fa fa-check-circle"></i> Verified</span>@endif
                @if(count($missing))<span class="badge badge-warning">Incomplete profile</span>@endif
              </h3>
              <div class="hero-sub">
                @if($user->username)<span><i class="fa fa-at"></i>{{ $user->username }}</span>@endif
                <span><i class="fa fa-envelope-o"></i>{{ $user->email }}</span>
                @if($user->phone)<span><i class="fa fa-phone"></i>{{ $user->phone }}</span>@endif
                @if($user->rollnumber)<span><i class="fa fa-id-card-o"></i>{{ $user->rollnumber }}</span>@endif
              </div>
            </div>

            <div class="hero-actions">
              <a href="{{ url('admin/users/edit/'.$user->id) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
              <a href="{{ url('admin/users/verify/'.$user->id) }}" class="btn btn-secondary" title="{{ $user->is_verified ? 'Uploads will need approval again' : 'Uploads go live without approval' }}"><i class="fa fa-check-circle"></i> {{ $user->is_verified ? 'Un-verify' : 'Verify' }}</a>
              <a href="{{ url('admin/coins/user/'.$user->id) }}" class="btn btn-secondary"><i class="fa fa-money"></i> Coins</a>
              @if($result)<a href="{{ url('admin/results/view/'.$result->id) }}" class="btn btn-secondary"><i class="fa fa-graduation-cap"></i> Results</a>@endif
            </div>
          </div>
        </div>

        {{-- Stat strip --}}
        <div class="stats gap">
          <div class="stat"><div class="stat-num">{{ count($reading_history) }}</div><div class="stat-lbl">Books read</div></div>
          <div class="stat"><div class="stat-num">{{ count($uploaded_books) + count($uploaded_posts) }}</div><div class="stat-lbl">Uploads · {{ count($uploaded_books) }} {{ Str::plural('book', count($uploaded_books)) }}, {{ count($uploaded_posts) }} {{ Str::plural('post', count($uploaded_posts)) }}</div></div>
          <div class="stat"><div class="stat-num">{{ $favourites_count }}</div><div class="stat-lbl">Favourites</div></div>
          <div class="stat"><div class="stat-num">{{ $reviews_count }}</div><div class="stat-lbl">Reviews written</div></div>
          <div class="stat"><div class="stat-num">{{ number_format((int) $user->getAttribute('coin_balance')) }}</div><div class="stat-lbl">Coin balance</div></div>
          <div class="stat"><div class="stat-num">{{ $currency }} {{ number_format($total_spent, $total_spent == floor($total_spent) ? 0 : 2) }}</div><div class="stat-lbl">Total paid</div></div>
        </div>

        {{-- University results --}}
        <div class="panel gap results-card">
          @if($result)
            <div class="rc">
              <span class="rc-ico"><i class="fa fa-graduation-cap"></i></span>
              <div class="rc-main">
                <h4 style="margin:0;">University results
                  @if($result->verified)<span class="badge badge-success"><i class="fa fa-check"></i> From {{ strtoupper($result->source ?: 'JNTUH') }}</span>@else<span class="badge badge-secondary">Entered manually</span>@endif
                </h4>
                <div class="rc-sub">Hall ticket {{ $result->hall_ticket_no }}@if($result->student_name) · {{ $result->student_name }}@endif @if($result->regulation) · {{ $result->regulation }}@endif</div>
              </div>
              <div class="rc-num"><b>{{ $result->current_cgpa !== null ? number_format((float) $result->current_cgpa, 2) : '—' }}</b><span>CGPA</span></div>
              <div class="rc-num"><b>{{ $result->semesters()->count() }}</b><span>Semesters</span></div>
              <div class="rc-num {{ (int) $result->backlogs_count > 0 ? 'is-bad' : '' }}"><b>{{ (int) $result->backlogs_count }}</b><span>Backlogs</span></div>
              <a href="{{ url('admin/results/view/'.$result->id) }}" class="btn btn-primary">View results</a>
            </div>
          @else
            <div class="rc">
              <span class="rc-ico rc-ico-muted"><i class="fa fa-graduation-cap"></i></span>
              <div class="rc-main">
                <h4 style="margin:0;">University results</h4>
                <div class="rc-sub">
                  @if($results_pending)
                    Waiting on the JNTUH results service for hall ticket {{ $user->rollnumber }}. They are checked every 5 minutes and will appear here.
                  @elseif(!$user->rollnumber)
                    No hall ticket on this account yet.
                  @elseif(stripos((string) $user->university, 'JNTU') === false)
                    Automatic results are only available for JNTU students.
                  @else
                    No results found for hall ticket {{ $user->rollnumber }} yet.
                  @endif
                </div>
              </div>
              @if($user->rollnumber && stripos((string) $user->university, 'JNTU') !== false)
                <a href="{{ url('admin/users/sync_results/'.$user->id) }}" class="btn btn-secondary"><i class="fa fa-refresh"></i> Fetch now</a>
              @endif
            </div>
          @endif
        </div>

        <div class="row">
          {{-- Profile --}}
          <div class="col-xl-7 gap">
            <div class="panel">
              <h4>Profile</h4>
              @if(count($missing))
                <div class="missing"><i class="fa fa-exclamation-circle"></i><span>Missing {{ implode(', ', $missing) }}. The app asks for these the next time they sign in.</span></div>
              @endif
              <dl class="dl">
                <dt>Phone</dt><dd>{!! $show($user->phone) !!}</dd>
                <dt>Gender</dt><dd>{!! $show($user->gender) !!}</dd>
                <dt>Hall ticket</dt><dd>{!! $show($user->rollnumber) !!}</dd>
                <dt>University</dt><dd>{!! $show($user->university) !!}</dd>
                <dt>Department</dt><dd>{!! $show($department) !!}</dd>
                <dt>College</dt><dd>{!! $show($user->college) !!}</dd>
                <dt>Regulation</dt><dd>{!! $show($user->regulation) !!}</dd>
                <dt>Father's name</dt><dd>{!! $show($user->getAttribute('father_name')) !!}</dd>
              </dl>
            </div>
          </div>

          {{-- Account --}}
          <div class="col-xl-5 gap">
            <div class="panel">
              <h4>Account</h4>
              <dl class="dl">
                <dt>Joined</dt><dd>{{ $user->created_at ? date('d M Y', strtotime($user->created_at)) : '—' }}</dd>
                <dt>Signed up with</dt>
                <dd>
                  @if($user->social_login_type)<span class="badge badge-info">{{ ucfirst($user->social_login_type) }}</span>@else<span class="badge badge-secondary">Email</span>@endif
                  @if($user->registered_via == 'app')<span class="badge badge-primary">App</span>@elseif($user->registered_via == 'website')<span class="badge badge-warning">Website</span>@endif
                </dd>
                <dt>Last sign-in</dt>
                <dd>
                  @if($user->last_login_at)
                    {{ date('d M Y, h:i A', strtotime($user->last_login_at)) }}
                    @if($user->last_login_via)<span class="badge badge-secondary">{{ ucfirst($user->last_login_via) }}</span>@endif
                  @else
                    <span class="none">Not recorded</span>
                  @endif
                </dd>
                <dt>{{ trans('words.current_plan') }}</dt><dd>@if($plan_name){{ $plan_name }}@else<span class="none">No plan</span>@endif</dd>
                <dt>Plan expires</dt>
                <dd>
                  @if($user->exp_date)
                    {{ date('d M Y', $user->exp_date) }}
                    @if($plan_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Expired</span>@endif
                  @else
                    <span class="none">No plan</span>
                  @endif
                </dd>
              </dl>
            </div>
          </div>
        </div>

        {{-- Activity tabs --}}
        <div class="panel tabs-panel gap">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-reading" role="tab">Reading<span class="tab-count">{{ count($reading_history) }}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-books" role="tab">Uploaded books<span class="tab-count">{{ count($uploaded_books) }}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-posts" role="tab">Posts<span class="tab-count">{{ count($uploaded_posts) }}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-payments" role="tab">{{ trans('words.user_transactions') }}<span class="tab-count">{{ $transactions_list->total() }}</span></a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-reading" role="tabpanel">
              @if(count($reading_history))
              <div class="table-responsive">
                <table class="table">
                  <thead><tr><th>Book</th><th>Category</th><th>Last page</th></tr></thead>
                  <tbody>
                    @foreach($reading_history as $rh)
                      @php $rbook = \App\Books::find($rh->post_id); @endphp
                      <tr>
                        <td>
                          @if($rbook)<a href="{{ url('admin/books/edit/'.$rh->post_id) }}">{{ Str::limit(stripslashes($rbook->title), 60) }}</a>
                          @else<span class="text-muted">Book #{{ $rh->post_id }} (removed)</span>@endif
                        </td>
                        <td>{{ $rbook ? \App\Category::getCategoryInfo($rbook->cat_id,'category_name') : '—' }}</td>
                        <td>{{ $rh->page_num ?: '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
                <div class="empty"><i class="fa fa-book"></i>No reading activity recorded yet.</div>
              @endif
            </div>

            <div class="tab-pane fade" id="tab-books" role="tabpanel">
              @if(count($uploaded_books))
              <div class="table-responsive">
                <table class="table">
                  <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Review</th><th>Live</th></tr></thead>
                  <tbody>
                    @foreach($uploaded_books as $ub)
                    <tr>
                      <td><a href="{{ url('admin/books/edit/'.$ub->id) }}">{{ Str::limit(stripslashes($ub->title), 50) }}</a></td>
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
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
                <div class="empty"><i class="fa fa-upload"></i>This student hasn't uploaded any books.</div>
              @endif
            </div>

            <div class="tab-pane fade" id="tab-posts" role="tabpanel">
              @if(count($uploaded_posts))
              <div class="table-responsive">
                <table class="table">
                  <thead><tr><th>Preview</th><th>Title</th><th>Type</th><th>Date</th><th>Review</th><th>Live</th></tr></thead>
                  <tbody>
                    @foreach($uploaded_posts as $up)
                    <tr>
                      <td>
                        @php $prev = $up->media_type=='video' ? ($up->thumb_url ?: '') : $up->file_url; @endphp
                        @if($prev)<img src="{{ $prev }}" class="thumb" alt="" onerror="this.style.display='none'">@endif
                      </td>
                      <td><a href="{{ url('admin/media/edit/'.$up->id) }}">{{ Str::limit(stripslashes($up->title), 50) ?: 'Untitled' }}</a></td>
                      <td>{{ ucfirst($up->media_type) }}</td>
                      <td>{{ $up->created_at ? $up->created_at->format('d M Y') : '—' }}</td>
                      <td>
                        @if($up->upload_status=='approved')<span class="badge badge-success">Approved</span>
                        @elseif($up->upload_status=='rejected')<span class="badge badge-danger">Rejected</span>
                        @else<span class="badge badge-warning">Pending</span>@endif
                        @if($up->reject_reason)<br><small class="text-muted">{{ $up->reject_reason }}</small>@endif
                      </td>
                      <td>@if($up->status==1)<span class="badge badge-success">Yes</span>@else<span class="badge badge-secondary">No</span>@endif</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
                <div class="empty"><i class="fa fa-photo"></i>This student hasn't uploaded any posts.</div>
              @endif
            </div>

            <div class="tab-pane fade" id="tab-payments" role="tabpanel">
              @if($transactions_list->total())
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{trans('words.plan')}} / {{trans('words.book_on_rent')}}</th>
                      <th>{{trans('words.amount')}}</th>
                      <th>{{trans('words.payment_gateway')}}</th>
                      <th>{{trans('words.payment_id')}}</th>
                      <th>{{trans('words.payment_date')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($transactions_list as $transaction_data)
                    <tr>
                      <td>
                        @if($transaction_data->rent_id)
                          <span class="badge badge-warning">Rent</span> {{\App\Books::getBookInfo($transaction_data->rent_id,'title')}}
                        @else
                          {{\App\SubscriptionPlan::getSubscriptionPlanInfo($transaction_data->plan_id,'plan_name')}}
                        @endif
                      </td>
                      <td><b>{{ $currency }} {{ $transaction_data->payment_amount }}</b></td>
                      <td>{{ $transaction_data->gateway }}</td>
                      <td>{{ $transaction_data->payment_id }}</td>
                      <td>{{ date('d M Y, h:i A',$transaction_data->date) }}</td>
                    </tr>
                   @endforeach
                  </tbody>
                </table>
              </div>
              <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $transactions_list])
              </nav>
              @else
                <div class="empty"><i class="fa fa-credit-card"></i>No payments from this student.</div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script>
// Open the tab named in the URL (#tab-payments after paging), and keep the URL
// in step so a refresh or a shared link lands on the same tab.
document.addEventListener('DOMContentLoaded', function () {
  if (!window.jQuery) return;
  var hash = window.location.hash;
  if (hash && jQuery('.uh .nav-tabs a[href="' + hash + '"]').length) {
    jQuery('.uh .nav-tabs a[href="' + hash + '"]').tab('show');
  }
  jQuery('.uh .nav-tabs a').on('shown.bs.tab', function (e) {
    history.replaceState(null, '', e.target.getAttribute('href'));
  });
});
</script>

@endsection
