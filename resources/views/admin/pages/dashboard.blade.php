@extends("admin.admin_app")

@section("content")

{{-- ============================================================
     Modern SaaS light dashboard (scoped styles).
     Styles are namespaced under .saas-dash so they do not
     affect other admin pages.
============================================================ --}}
<style>
.saas-dash { --accent:#4a7dff; --ink:#1f2937; --muted:#6b7280; --line:#eef1f6; --bg:#f6f8fb; }
.saas-dash { background: var(--bg); margin:-20px -12px 0; padding:24px 20px 8px; }
.saas-dash .dash-head { margin:0 4px 20px; }
.saas-dash .dash-head h3 { font-size:22px; font-weight:700; color:var(--ink); margin:0; }
.saas-dash .dash-head p { color:var(--muted); margin:4px 0 0; font-size:13px; }
.saas-dash .stat-card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:18px; display:flex; align-items:center; gap:14px; transition:transform .15s ease, box-shadow .15s ease; height:100%; }
.saas-dash a:hover .stat-card { transform:translateY(-3px); box-shadow:0 10px 24px rgba(31,41,55,.08); }
.saas-dash a { text-decoration:none !important; }
.saas-dash .stat-ico { width:48px; height:48px; border-radius:12px; flex:0 0 48px; display:flex; align-items:center; justify-content:center; font-size:20px; color:#fff; }
.saas-dash .stat-num { font-size:26px; font-weight:700; color:var(--ink); line-height:1.1; }
.saas-dash .stat-lbl { font-size:12.5px; color:var(--muted); margin-top:3px; font-weight:500; }
.saas-dash .ic-blue{background:linear-gradient(135deg,#4a7dff,#6f9bff);} .saas-dash .ic-purple{background:linear-gradient(135deg,#7c5cff,#a084ff);}
.saas-dash .ic-green{background:linear-gradient(135deg,#16b981,#3ddc97);} .saas-dash .ic-orange{background:linear-gradient(135deg,#ff9f43,#ffbe76);}
.saas-dash .ic-teal{background:linear-gradient(135deg,#0eb5c6,#4fd6e3);} .saas-dash .ic-pink{background:linear-gradient(135deg,#ff5e93,#ff8fb3);}
.saas-dash .ic-red{background:linear-gradient(135deg,#f45b5b,#ff8080);} .saas-dash .ic-indigo{background:linear-gradient(135deg,#5b6ef4,#8a97ff);}
.saas-dash .ic-slate{background:linear-gradient(135deg,#64748b,#94a3b8);} .saas-dash .ic-cyan{background:linear-gradient(135deg,#2bc0e4,#64d2f0);}
.saas-dash .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; height:100%; }
.saas-dash .panel h4 { font-size:15px; font-weight:700; color:var(--ink); margin:0 0 2px; }
.saas-dash .panel .sub { font-size:12px; color:var(--muted); margin:0 0 16px; }
.saas-dash .list-row { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:1px solid var(--line); font-size:13px; color:var(--ink); }
.saas-dash .list-row:last-child { border-bottom:0; }
.saas-dash .pill { background:#eef3ff; color:var(--accent); border-radius:20px; padding:3px 10px; font-size:11.5px; font-weight:600; white-space:nowrap; }
.saas-dash .pill-green { background:#e7f9f1; color:#16b981; }
.saas-dash .scroll-area { max-height:320px; overflow:auto; }
.saas-dash .scroll-area::-webkit-scrollbar { width:6px; }
.saas-dash .scroll-area::-webkit-scrollbar-thumb { background:#dfe4ee; border-radius:6px; }
.saas-dash table.clean { width:100%; font-size:13px; }
.saas-dash table.clean th { color:var(--muted); font-weight:600; font-size:11.5px; text-transform:uppercase; padding:8px; border-bottom:1px solid var(--line); }
.saas-dash table.clean td { padding:10px 8px; border-bottom:1px solid var(--line); color:var(--ink); vertical-align:middle; }
.saas-dash .avatar-sm { width:34px; height:34px; border-radius:50%; object-fit:cover; }
.saas-dash .mb-gap { margin-bottom:22px; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="saas-dash">

        <div class="dash-head">
          <h3>{{ $page_title }}</h3>
          <p>Overview of your library, users and activity</p>
        </div>

        @php
          $stat_cards = [
            ['url'=>'admin/category',     'ico'=>'fa-list',           'cls'=>'ic-blue',   'num'=>$category,     'lbl'=>trans('words.categories_text')],
            ['url'=>'admin/sub_category', 'ico'=>'fa-sitemap',        'cls'=>'ic-purple', 'num'=>$sub_category, 'lbl'=>trans('words.sub_categories_text')],
            ['url'=>'admin/authors',      'ico'=>'fa-pencil',         'cls'=>'ic-green',  'num'=>$authors,     'lbl'=>trans('words.authors_text')],
            ['url'=>'admin/books',        'ico'=>'fa-book',           'cls'=>'ic-orange', 'num'=>$books,       'lbl'=>trans('words.books_text')],
            ['url'=>'admin/department',   'ico'=>'fa-university',     'cls'=>'ic-indigo', 'num'=>$departments_count, 'lbl'=>'Departments'],
            ['url'=>'admin/college',      'ico'=>'fa-graduation-cap', 'cls'=>'ic-cyan',   'num'=>$colleges_count,    'lbl'=>'Colleges'],
            ['url'=>'admin/users',        'ico'=>'fa-users',          'cls'=>'ic-teal',   'num'=>$users,       'lbl'=>trans('words.users')],
            ['url'=>'admin/transactions', 'ico'=>'fa-credit-card',    'cls'=>'ic-red',    'num'=>$transactions,'lbl'=>trans('words.transactions')],
            ['url'=>'admin/reviews',      'ico'=>'fa-star',           'cls'=>'ic-pink',   'num'=>$reviews,     'lbl'=>trans('words.reviews')],
            ['url'=>'admin/reports',      'ico'=>'fa-flag',           'cls'=>'ic-slate',  'num'=>$reports,     'lbl'=>trans('words.reports')],
            ['url'=>'admin/posts',        'ico'=>'fa-th-large',       'cls'=>'ic-blue',   'num'=>$media_total, 'lbl'=>'Posts'],
            ['url'=>'admin/media?filter=approved', 'ico'=>'fa-photo', 'cls'=>'ic-green',  'num'=>$media_photos,'lbl'=>'Photos'],
            ['url'=>'admin/media?filter=approved', 'ico'=>'fa-video-camera','cls'=>'ic-purple','num'=>$media_videos,'lbl'=>'Videos'],
          ];
        @endphp

        {{-- Pending-approval highlight row --}}
        <div class="row mb-gap">
          <div class="col-xl-6 col-md-6 mb-3">
            <a href="{{ URL::to('admin/media?filter=pending') }}">
              <div class="stat-card" style="border-left:4px solid #f0ad4e;">
                <div class="stat-ico ic-orange"><i class="fa fa-clock-o"></i></div>
                <div>
                  <div class="stat-num" data-plugin="counterup">{{ $media_pending }}</div>
                  <div class="stat-lbl">Posts pending approval</div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xl-6 col-md-6 mb-3">
            <a href="{{ URL::to('admin/user_books?filter=pending') }}">
              <div class="stat-card" style="border-left:4px solid #f0ad4e;">
                <div class="stat-ico ic-pink"><i class="fa fa-clock-o"></i></div>
                <div>
                  <div class="stat-num" data-plugin="counterup">{{ $books_pending }}</div>
                  <div class="stat-lbl">Books pending approval</div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <div class="row mb-gap">
          @foreach($stat_cards as $c)
            <div class="col-xl-3 col-md-4 col-6 mb-3">
              <a href="{{ URL::to($c['url']) }}">
                <div class="stat-card">
                  <div class="stat-ico {{ $c['cls'] }}"><i class="fa {{ $c['ico'] }}"></i></div>
                  <div>
                    <div class="stat-num" data-plugin="counterup">{{ $c['num'] }}</div>
                    <div class="stat-lbl">{{ $c['lbl'] }}</div>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        @php
          $user_cards = [
            ['ico'=>'fa-user-plus','cls'=>'ic-green', 'num'=>$users_today,        'lbl'=>'New Today'],
            ['ico'=>'fa-calendar', 'cls'=>'ic-blue',  'num'=>$users_week,         'lbl'=>'New This Week'],
            ['ico'=>'fa-line-chart','cls'=>'ic-indigo','num'=>$users_month,       'lbl'=>'New This Month'],
            ['ico'=>'fa-check-circle','cls'=>'ic-teal','num'=>$active_subscribers, 'lbl'=>'Active Subscribers'],
            ['ico'=>'fa-clock-o',  'cls'=>'ic-orange','num'=>$recently_active,    'lbl'=>'Active Today'],
          ];
        @endphp
        <div class="row mb-gap">
          @foreach($user_cards as $c)
            <div class="col-xl col-md-4 col-6 mb-3">
              <div class="stat-card">
                <div class="stat-ico {{ $c['cls'] }}"><i class="fa {{ $c['ico'] }}"></i></div>
                <div>
                  <div class="stat-num" data-plugin="counterup">{{ $c['num'] }}</div>
                  <div class="stat-lbl">{{ $c['lbl'] }}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="row mb-gap">
          <div class="col-12">
            <div class="panel">
              <h4>Books per Department</h4>
              <p class="sub">How many books are assigned to each department</p>
              @if(count($books_per_department) > 0)
                <canvas id="deptChart" height="90"></canvas>
              @else
                <p class="text-muted" style="padding:20px 0;">No departments yet. Add departments and assign books to see this chart.</p>
              @endif
            </div>
          </div>
        </div>

        <div class="row mb-gap">
          <div class="col-xl-4 col-md-6 mb-3">
            <div class="panel">
              <h4>{{ trans('words.trending_now') }}</h4>
              <p class="sub">{{ trans('words.based_on_30_days') }}</p>
              <div class="scroll-area">
                @foreach($trending_now as $trending_data)
                  <div class="list-row">
                    <span>{{ Str::limit(stripslashes(\App\Books::getBookInfo($trending_data->post_id,'title')), 26) }}</span>
                    <span class="pill">{{ number_format_short($trending_data->total_views) }} {{ trans('words.views') }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-md-6 mb-3">
            <div class="panel">
              <h4>{{ trans('words.latest_books') }}</h4>
              <p class="sub">Recently added</p>
              <div class="scroll-area">
                @foreach($latest_books as $latest_data)
                  <div class="list-row">
                    <span>{{ Str::limit(stripslashes($latest_data->title), 26) }}</span>
                    <span class="pill pill-green">{{ number_format_short(post_views_count($latest_data->id,"Book")) }} {{ trans('words.views') }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-md-6 mb-3">
            <div class="panel">
              <h4>{{ trans('words.top_country') }}</h4>
              <p class="sub">{{ trans('words.based_on_30_days') }}</p>
              <div class="scroll-area">
                @foreach($top_country as $country_data)
                  <div class="list-row">
                    <span>
                      <img src="{{ URL::asset('admin_assets/country_icons/').'/'.strtolower(countryNameToISO3166($country_data->country,'US')) }}.png" style="width:18px;margin-right:8px;">
                      {{ $country_data->country }}
                    </span>
                    <span class="pill">{{ number_format_short($country_data->count_row) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-gap">
          <div class="col-xl-4 col-md-6 mb-3">
            <div class="panel">
              <h4>{{ trans('words.latest_reviews') }}</h4>
              <p class="sub">Newest user feedback</p>
              <div class="scroll-area">
                @foreach($latest_review as $review_data)
                  <div class="list-row" style="align-items:flex-start;">
                    <span style="display:flex;gap:10px;align-items:center;">
                      @if(isset(\App\User::getUserInfo($review_data->user_id)->user_image))
                        <img src="{{ user_image_url(\App\User::getUserInfo($review_data->user_id,'user_image')) }}" class="avatar-sm">
                      @else
                        <img src="{{ URL::asset('admin_assets/images/users/avatar-10.jpg') }}" class="avatar-sm">
                      @endif
                      <span>
                        <strong style="display:block;">{{ \App\User::getUserInfo($review_data->user_id,'name') }}</strong>
                        <small style="color:#6b7280;">{{ Str::limit(stripslashes($review_data->review_text), 34) }}</small>
                      </span>
                    </span>
                    <small style="color:#9ca3af;white-space:nowrap;">{{ date('M d',$review_data->date) }}</small>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="col-xl-8 col-md-6 mb-3">
            <div class="panel">
              <h4>{{ trans('words.latest_reports') }}</h4>
              <p class="sub">Flagged by users</p>
              <div class="scroll-area">
                <table class="clean">
                  <thead>
                    <tr>
                      <th>{{ trans('words.name') }}</th>
                      <th>{{ trans('words.message') }}</th>
                      <th style="text-align:right;">Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($reports_list as $reports_data)
                      <tr>
                        <td>
                          <div style="display:flex;gap:10px;align-items:center;">
                            @if(isset(\App\User::getUserInfo($reports_data->user_id)->user_image))
                              <img src="{{ user_image_url(\App\User::getUserInfo($reports_data->user_id)->user_image) }}" class="avatar-sm">
                            @else
                              <img src="{{ URL::to('upload/profile.jpg') }}" class="avatar-sm">
                            @endif
                            <span>{{ \App\User::getUserFullname($reports_data->user_id) }}</span>
                          </div>
                        </td>
                        <td>{{ Str::limit($reports_data->message,60) }}</td>
                        <td style="text-align:right;"><span class="pill">{{ date('m-d-Y',$reports_data->date) }}</span></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

@if(count($books_per_department) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var ctx = document.getElementById('deptChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($books_per_department->pluck('department_name')->map(function($n){ return stripslashes($n); })) !!},
      datasets: [{
        label: 'Books',
        data: {!! json_encode($books_per_department->pluck('book_count')) !!},
        backgroundColor: '#4a7dff',
        borderRadius: 6,
        maxBarThickness: 46
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef1f6' } },
        x: { grid: { display: false } }
      }
    }
  });
});
</script>
@endif

@endsection
