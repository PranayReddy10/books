@extends("admin.admin_app")

@section("content")

{{-- Dashboard. Styles are scoped under .dash and use the admin theme's tokens
     (admin-light.css), so it matches every other page. Charts are plain HTML
     bars: nothing to load from a CDN, nothing to break when it is blocked. --}}
<style>
.dash .dash-intro{ display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin:0 0 20px; }
.dash .dash-intro h2{ font-size:22px; font-weight:700; margin:0; color:var(--ad-ink); letter-spacing:-.02em; }
.dash .dash-intro p{ margin:4px 0 0; color:var(--ad-muted); font-size:13.5px; }
.dash .quick{ display:flex; gap:8px; flex-wrap:wrap; }

.dash .panel{ background:#fff; border:1px solid var(--ad-line); border-radius:var(--ad-radius); box-shadow:var(--ad-shadow); padding:20px; height:100%; }
.dash .panel-head{ display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin-bottom:14px; }
.dash .panel-head h4{ font-size:15px; font-weight:700; margin:0; color:var(--ad-ink); }
.dash .panel-head .sub{ font-size:12.5px; color:var(--ad-muted); margin:2px 0 0; }
.dash .panel-head a{ font-size:12.5px; font-weight:600; white-space:nowrap; }
.dash .gap{ margin-bottom:20px; }

/* Needs attention */
.dash .attention{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; }
.dash .att{ display:flex; align-items:center; gap:14px; padding:14px 16px; border-radius:12px; border:1px solid var(--ad-line); background:#fff; color:var(--ad-ink) !important; transition:border-color .15s ease, box-shadow .15s ease; }
.dash .att:hover{ border-color:#cfd6e2; box-shadow:var(--ad-shadow); }
.dash .att-ico{ width:40px; height:40px; border-radius:10px; flex:0 0 40px; display:flex; align-items:center; justify-content:center; font-size:17px; }
.dash .att-num{ font-size:20px; font-weight:700; line-height:1.1; }
.dash .att-lbl{ font-size:12.5px; color:var(--ad-muted); }
.dash .att .fa-angle-right{ margin-left:auto; color:var(--ad-faint); }
.dash .att.is-warn{ border-color:#f3dbb0; background:#fffbf3; }
.dash .att.is-warn .att-ico{ background:var(--ad-amber-soft); color:var(--ad-amber); }
.dash .att.is-ok .att-ico{ background:var(--ad-green-soft); color:var(--ad-green); }
.dash .att.is-ok .att-num{ color:var(--ad-muted); }

/* Headline numbers */
.dash .kpis{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; }
.dash .kpi{ background:#fff; border:1px solid var(--ad-line); border-radius:var(--ad-radius); box-shadow:var(--ad-shadow); padding:18px 20px; color:var(--ad-ink) !important; display:block; }
.dash a.kpi:hover{ border-color:#cfd6e2; }
.dash .kpi-top{ display:flex; align-items:center; justify-content:space-between; }
.dash .kpi-lbl{ font-size:12.5px; font-weight:600; color:var(--ad-muted); text-transform:uppercase; letter-spacing:.05em; }
.dash .kpi-ico{ width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:15px; }
.dash .kpi-num{ font-size:28px; font-weight:700; letter-spacing:-.02em; margin:10px 0 4px; line-height:1.1; }
.dash .kpi-foot{ font-size:12.5px; color:var(--ad-muted); }
.dash .kpi-foot b{ color:var(--ad-green); font-weight:600; }
.dash .t-blue{ background:var(--ad-accent-soft); color:var(--ad-accent); }
.dash .t-green{ background:var(--ad-green-soft); color:var(--ad-green); }
.dash .t-cyan{ background:var(--ad-cyan-soft); color:var(--ad-cyan); }
.dash .t-amber{ background:var(--ad-amber-soft); color:var(--ad-amber); }

/* Sign-ups column chart */
.dash .cols{ display:flex; align-items:flex-end; gap:6px; height:170px; padding-top:18px; }
.dash .dcol{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%; min-width:0; }
.dash .col-bar{ width:100%; max-width:34px; background:var(--ad-accent); border-radius:6px 6px 2px 2px; min-height:3px; position:relative; }
.dash .col-bar.is-zero{ background:var(--ad-line); }
.dash .col-val{ font-size:11px; font-weight:600; color:var(--ad-ink-2); margin-bottom:4px; }
.dash .col-lbl{ font-size:10.5px; color:var(--ad-faint); margin-top:6px; white-space:nowrap; }
.dash .dcol:nth-child(odd) .col-lbl{ visibility:hidden; }

/* Library summary */
.dash .lib{ display:grid; grid-template-columns:1fr 1fr; gap:4px 18px; }
.dash .lib a{ display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--ad-line-2); color:var(--ad-ink-2) !important; font-size:13.5px; }
.dash .lib a:hover{ color:var(--ad-accent) !important; }
.dash .lib i{ width:16px; color:var(--ad-faint); text-align:center; }
.dash .lib b{ margin-left:auto; color:var(--ad-ink); }

/* Horizontal bars (books per department) */
.dash .hbar{ display:grid; grid-template-columns:minmax(120px,38%) 1fr 34px; align-items:center; gap:10px; padding:6px 0; font-size:13px; }
.dash .hbar-name{ color:var(--ad-ink-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dash .hbar-track{ height:8px; border-radius:99px; background:var(--ad-surface-2); overflow:hidden; }
.dash .hbar-fill{ height:100%; border-radius:99px; background:var(--ad-accent); }
.dash .hbar-num{ text-align:right; font-weight:600; color:var(--ad-ink); }

/* Lists */
.dash .rows{ max-height:330px; overflow:auto; margin:0 -4px; padding:0 4px; }
.dash .row-item{ display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--ad-line-2); font-size:13.5px; color:var(--ad-ink-2); }
.dash .row-item:last-child{ border-bottom:0; }
.dash .row-item .grow{ flex:1; min-width:0; }
.dash .row-item .title{ display:block; color:var(--ad-ink); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dash .row-item small{ color:var(--ad-muted); }
.dash .rank{ width:22px; height:22px; border-radius:6px; background:var(--ad-surface-2); color:var(--ad-muted); font-size:11.5px; font-weight:700; display:flex; align-items:center; justify-content:center; flex:0 0 22px; }
.dash .pill{ background:var(--ad-accent-soft); color:var(--ad-accent); border-radius:999px; padding:3px 10px; font-size:11.5px; font-weight:600; white-space:nowrap; }
.dash .avatar{ width:34px; height:34px; border-radius:50%; object-fit:cover; flex:0 0 34px; }
.dash .empty{ text-align:center; color:var(--ad-muted); font-size:13px; padding:34px 10px; }
.dash .empty i{ display:block; font-size:22px; color:var(--ad-faint); margin-bottom:8px; }
.dash .rows::-webkit-scrollbar{ width:6px; }
.dash .rows::-webkit-scrollbar-thumb{ background:#dfe4ee; border-radius:6px; }
</style>

@php
  $currency = html_entity_decode(getCurrencySymbols(getcong('currency_code')));
  $money = function ($v) use ($currency) { return $currency.' '.number_format($v, $v == floor($v) ? 0 : 2); };
  $signup_max = max(1, max(array_column($signups_daily, 'count')));
  $signups_14 = array_sum(array_column($signups_daily, 'count'));
  $dept_max = max(1, (int) $books_per_department->max('book_count'));
  $hour = (int) date('G');
  $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $first_name = explode(' ', trim(Auth::user()->name))[0];

  $attention = [
    ['url'=>'admin/media?filter=pending',      'ico'=>'fa-photo',  'num'=>$media_pending,       'lbl'=>'Posts waiting for approval'],
    ['url'=>'admin/user_books?filter=pending', 'ico'=>'fa-book',   'num'=>$books_pending,       'lbl'=>'Book uploads waiting for approval'],
    ['url'=>'admin/reports',                   'ico'=>'fa-flag',   'num'=>$reports,             'lbl'=>'Reports from users'],
    ['url'=>'admin/users?profile=incomplete',  'ico'=>'fa-user-times','num'=>$profiles_incomplete,'lbl'=>'Students with missing details'],
  ];
  $library = [
    ['url'=>'admin/books',        'ico'=>'fa-book',           'lbl'=>trans('words.books_text'),          'num'=>$books],
    ['url'=>'admin/category',     'ico'=>'fa-list',           'lbl'=>trans('words.categories_text'),     'num'=>$category],
    ['url'=>'admin/sub_category', 'ico'=>'fa-sitemap',        'lbl'=>trans('words.sub_categories_text'), 'num'=>$sub_category],
    ['url'=>'admin/authors',      'ico'=>'fa-pencil',         'lbl'=>trans('words.authors_text'),        'num'=>$authors],
    ['url'=>'admin/department',   'ico'=>'fa-university',     'lbl'=>'Departments',                      'num'=>$departments_count],
    ['url'=>'admin/college',      'ico'=>'fa-graduation-cap', 'lbl'=>'Colleges',                         'num'=>$colleges_count],
    ['url'=>'admin/posts',        'ico'=>'fa-th-large',       'lbl'=>'Posts',                            'num'=>$media_total],
    ['url'=>'admin/media?filter=approved', 'ico'=>'fa-photo', 'lbl'=>'Photos',                           'num'=>$media_photos],
    ['url'=>'admin/media?filter=approved', 'ico'=>'fa-video-camera','lbl'=>'Videos',                     'num'=>$media_videos],
    ['url'=>'admin/reviews',      'ico'=>'fa-star',           'lbl'=>trans('words.reviews'),             'num'=>$reviews],
  ];
@endphp

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="dash">

        <div class="dash-intro">
          <div>
            <h2>{{ $greeting }}, {{ $first_name }}</h2>
            <p>Here's what's happening with your library and students today.</p>
          </div>
          <div class="quick">
            <a href="{{ URL::to('admin/books/add') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Book</a>
            <a href="{{ URL::to('admin/notification_send') }}" class="btn btn-secondary"><i class="fa fa-bell"></i> Send Notification</a>
          </div>
        </div>

        {{-- Needs attention --}}
        <div class="attention gap">
          @foreach($attention as $a)
            <a href="{{ URL::to($a['url']) }}" class="att {{ $a['num'] > 0 ? 'is-warn' : 'is-ok' }}">
              <span class="att-ico"><i class="fa {{ $a['num'] > 0 ? $a['ico'] : 'fa-check' }}"></i></span>
              <span>
                <span class="att-num d-block">{{ number_format($a['num']) }}</span>
                <span class="att-lbl">{{ $a['lbl'] }}</span>
              </span>
              <i class="fa fa-angle-right"></i>
            </a>
          @endforeach
        </div>

        {{-- Headline numbers --}}
        <div class="kpis gap">
          <a href="{{ URL::to('admin/users') }}" class="kpi">
            <div class="kpi-top"><span class="kpi-lbl">Students</span><span class="kpi-ico t-blue"><i class="fa fa-users"></i></span></div>
            <div class="kpi-num">{{ number_format($users) }}</div>
            <div class="kpi-foot"><b>+{{ number_format($users_week) }}</b> this week · {{ number_format($users_today) }} today</div>
          </a>
          <div class="kpi">
            <div class="kpi-top"><span class="kpi-lbl">Active today</span><span class="kpi-ico t-green"><i class="fa fa-bolt"></i></span></div>
            <div class="kpi-num">{{ number_format($recently_active) }}</div>
            <div class="kpi-foot">Signed in on the app or website</div>
          </div>
          <a href="{{ URL::to('admin/subscription_plan') }}" class="kpi">
            <div class="kpi-top"><span class="kpi-lbl">Subscribers</span><span class="kpi-ico t-cyan"><i class="fa fa-check-circle"></i></span></div>
            <div class="kpi-num">{{ number_format($active_subscribers) }}</div>
            <div class="kpi-foot">With an active plan</div>
          </a>
          <a href="{{ URL::to('admin/transactions') }}" class="kpi">
            <div class="kpi-top"><span class="kpi-lbl">Revenue · 30 days</span><span class="kpi-ico t-amber"><i class="fa fa-inr"></i></span></div>
            <div class="kpi-num">{{ $money($revenue_month) }}</div>
            <div class="kpi-foot">{{ $money($revenue_total) }} all time · {{ number_format($transactions) }} payments</div>
          </a>
        </div>

        <div class="row">
          {{-- Sign-ups --}}
          <div class="col-xl-8 gap">
            <div class="panel">
              <div class="panel-head">
                <div><h4>New students</h4><p class="sub">Sign-ups per day, last 14 days · {{ number_format($signups_14) }} in total · {{ number_format($users_month) }} in 30 days</p></div>
                <a href="{{ URL::to('admin/users?joined=30') }}">View</a>
              </div>
              <div class="cols">
                @foreach($signups_daily as $d)
                  <div class="dcol" title="{{ $d['label'] }}: {{ $d['count'] }}">
                    @if($d['count'])<span class="col-val">{{ $d['count'] }}</span>@endif
                    <div class="col-bar {{ $d['count'] ? '' : 'is-zero' }}" style="height:{{ $d['count'] ? max(4, round($d['count'] / $signup_max * 120)) : 3 }}px"></div>
                    <span class="col-lbl">{{ $d['label'] }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Library --}}
          <div class="col-xl-4 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>Library</h4><p class="sub">Everything in the app</p></div></div>
              <div class="lib">
                @foreach($library as $l)
                  <a href="{{ URL::to($l['url']) }}"><i class="fa {{ $l['ico'] }}"></i>{{ $l['lbl'] }}<b>{{ number_format($l['num']) }}</b></a>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          {{-- Books per department --}}
          <div class="col-xl-6 gap">
            <div class="panel">
              <div class="panel-head">
                <div><h4>Books per department</h4><p class="sub">Books linked to each department come first for its students</p></div>
                <a href="{{ URL::to('admin/department') }}">Departments</a>
              </div>
              @if(count($books_per_department) > 0)
                @foreach($books_per_department as $dep)
                  <div class="hbar">
                    <span class="hbar-name" title="{{ stripslashes($dep->department_name) }}">{{ stripslashes($dep->department_name) }}</span>
                    <span class="hbar-track"><span class="hbar-fill d-block" style="width:{{ round($dep->book_count / $dept_max * 100) }}%"></span></span>
                    <span class="hbar-num">{{ $dep->book_count }}</span>
                  </div>
                @endforeach
              @else
                <div class="empty"><i class="fa fa-university"></i>No departments yet. Add departments and link books to them.</div>
              @endif
            </div>
          </div>

          {{-- Popular books --}}
          <div class="col-xl-6 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>{{ trans('words.trending_now') }}</h4><p class="sub">Most read in the last 30 days</p></div></div>
              <div class="rows">
                @forelse($trending_now as $i => $trending_data)
                  <div class="row-item">
                    <span class="rank">{{ $i + 1 }}</span>
                    <span class="grow"><span class="title">{{ stripslashes(\App\Books::getBookInfo($trending_data->post_id,'title')) }}</span></span>
                    <span class="pill">{{ number_format_short($trending_data->total_views) }} {{ trans('words.views') }}</span>
                  </div>
                @empty
                  <div class="empty"><i class="fa fa-line-chart"></i>No reads in the last 30 days yet.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          {{-- Latest books --}}
          <div class="col-xl-4 col-md-6 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>{{ trans('words.latest_books') }}</h4><p class="sub">Recently added</p></div><a href="{{ URL::to('admin/books') }}">All books</a></div>
              <div class="rows">
                @forelse($latest_books as $latest_data)
                  <div class="row-item">
                    <span class="grow">
                      <a class="title" href="{{ URL::to('admin/books/edit/'.$latest_data->id) }}">{{ stripslashes($latest_data->title) }}</a>
                      <small>{{ \App\Category::getCategoryInfo($latest_data->cat_id,'category_name') }}</small>
                    </span>
                    <span class="pill">{{ number_format_short(post_views_count($latest_data->id,"Book")) }} {{ trans('words.views') }}</span>
                  </div>
                @empty
                  <div class="empty"><i class="fa fa-book"></i>No books yet.</div>
                @endforelse
              </div>
            </div>
          </div>

          {{-- Latest reviews --}}
          <div class="col-xl-4 col-md-6 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>{{ trans('words.latest_reviews') }}</h4><p class="sub">Newest feedback on books</p></div><a href="{{ URL::to('admin/reviews') }}">All reviews</a></div>
              <div class="rows">
                @forelse($latest_review as $review_data)
                  @php $reviewer = \App\User::getUserInfo($review_data->user_id); @endphp
                  <div class="row-item">
                    <img src="{{ $reviewer && has_user_image($reviewer->user_image) ? user_image_url($reviewer->user_image) : URL::asset('admin_assets/images/users/avatar-10.jpg') }}" class="avatar" alt="">
                    <span class="grow">
                      <span class="title">{{ $reviewer ? $reviewer->name : 'Deleted user' }} <small>· {{ $review_data->rate }}★</small></span>
                      <small>{{ Str::limit(stripslashes($review_data->review_text), 60) }}</small>
                    </span>
                    <small>{{ date('d M', $review_data->date) }}</small>
                  </div>
                @empty
                  <div class="empty"><i class="fa fa-star-o"></i>No reviews yet.</div>
                @endforelse
              </div>
            </div>
          </div>

          {{-- Latest reports --}}
          <div class="col-xl-4 col-md-12 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>{{ trans('words.latest_reports') }}</h4><p class="sub">Problems flagged by users</p></div><a href="{{ URL::to('admin/reports') }}">All reports</a></div>
              <div class="rows">
                @forelse($reports_list as $reports_data)
                  <div class="row-item">
                    <span class="grow">
                      <span class="title">{{ \App\User::getUserFullname($reports_data->user_id) }}</span>
                      <small>{{ Str::limit(stripslashes($reports_data->message), 70) }}</small>
                    </span>
                    <small>{{ date('d M', $reports_data->date) }}</small>
                  </div>
                @empty
                  <div class="empty"><i class="fa fa-flag-o"></i>Nothing reported. All clear.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        @if(count($top_country) > 0)
        <div class="row">
          <div class="col-xl-4 col-md-6 gap">
            <div class="panel">
              <div class="panel-head"><div><h4>{{ trans('words.top_country') }}</h4><p class="sub">Where readers are</p></div></div>
              <div class="rows">
                @foreach($top_country as $country_data)
                  <div class="row-item">
                    <img src="{{ URL::asset('admin_assets/country_icons/').'/'.strtolower(countryNameToISO3166($country_data->country,'US')) }}.png" style="width:20px;" alt="">
                    <span class="grow">{{ $country_data->country }}</span>
                    <span class="pill">{{ number_format_short($country_data->count_row) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

@endsection
