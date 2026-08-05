<div class="left side-menu">
  <div class="sidebar-inner slimscrollleft">
    <div id="sidebar-menu" class="saas-sidebar">

      <ul>
        <li class="menu-section">Main</li>
        <li><a href="{{ URL::to('admin/dashboard') }}" class="waves-effect {{classActivePath('dashboard')}}"><span class="nav-ic ic-blue"><i class="fa fa-dashboard"></i></span><span>{{trans('words.dashboard_text')}}</span></a></li>

        @if(admin_can_module('category') || admin_can_module('subcategory') || admin_can_module('university') || admin_can_module('department') || admin_can_module('college') || admin_can_module('authors') || admin_can_module('books') || admin_can_module('user_books') || admin_can_module('media') || admin_can_module('posts') || admin_can_module('home_sections'))
        <li class="menu-section">Catalog</li>
        @endif
        @if(admin_can_module('category'))<li><a href="{{ URL::to('admin/category') }}" class="waves-effect {{classActivePath('category')}}"><span class="nav-ic ic-blue"><i class="fa fa-list"></i></span><span>{{trans('words.categories_text')}}</span></a></li>@endif
        @if(admin_can_module('subcategory'))<li><a href="{{ URL::to('admin/sub_category') }}" class="waves-effect {{classActivePath('sub_category')}}"><span class="nav-ic ic-purple"><i class="fa fa-sitemap"></i></span><span>{{trans('words.sub_categories_text')}}</span></a></li>@endif
        @if(admin_can_module('university'))<li><a href="{{ URL::to('admin/university') }}" class="waves-effect {{classActivePath('university')}}"><span class="nav-ic ic-blue"><i class="fa fa-institution"></i></span><span>Universities</span></a></li>@endif
        @if(admin_can_module('department'))<li><a href="{{ URL::to('admin/department') }}" class="waves-effect {{classActivePath('department')}}"><span class="nav-ic ic-indigo"><i class="fa fa-university"></i></span><span>Departments</span></a></li>@endif
        @if(admin_can_module('college'))<li><a href="{{ URL::to('admin/college') }}" class="waves-effect {{classActivePath('college')}}"><span class="nav-ic ic-cyan"><i class="fa fa-graduation-cap"></i></span><span>Colleges</span></a></li>@endif
        @if(admin_can_module('authors'))<li><a href="{{ URL::to('admin/authors') }}" class="waves-effect {{classActivePath('authors')}}"><span class="nav-ic ic-green"><i class="fa fa-user"></i></span><span>{{trans('words.authors_text')}}</span></a></li>@endif
        @if(admin_can_module('books'))<li><a href="{{ URL::to('admin/books') }}" class="waves-effect {{classActivePath('books')}}"><span class="nav-ic ic-orange"><i class="fa fa-book"></i></span><span>{{trans('words.books_text')}}</span></a></li>@endif
        @if(admin_can_module('user_books'))<li><a href="{{ URL::to('admin/user_books') }}" class="waves-effect {{classActivePath('user_books')}}"><span class="nav-ic ic-pink"><i class="fa fa-upload"></i></span><span>User Uploads</span></a></li>@endif
        @if(admin_can_module('media'))<li><a href="{{ URL::to('admin/media') }}" class="waves-effect {{classActivePath('media')}}"><span class="nav-ic ic-red"><i class="fa fa-photo"></i></span><span>Media Feed</span></a></li>@endif
        @if(admin_can_module('posts'))<li><a href="{{ URL::to('admin/posts') }}" class="waves-effect {{classActivePath('posts')}}"><span class="nav-ic ic-blue"><i class="fa fa-th-large"></i></span><span>Posts</span></a></li>@endif
        @if(admin_can_module('home_sections'))<li><a href="{{ URL::to('admin/home_sections') }}" class="waves-effect {{classActivePath('home_sections')}}"><span class="nav-ic ic-teal"><i class="fa fa-th-list"></i></span><span>{{trans('words.home_sections')}}</span></a></li>@endif

        @if(admin_can_module('users') || admin_can_module('roles') || admin_can_module('reviews') || admin_can_module('reports'))
        <li class="menu-section">Users</li>
        @endif
        @if(admin_can_module('users') || admin_can_module('roles'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect"><span class="nav-ic ic-teal"><i class="fa fa-users"></i></span><span>{{trans('words.users')}}</span><span class="menu-arrow"></span></a>
          <ul class="list-unstyled">
            @if(admin_can_module('users'))<li class="{{classActivePath('users')}}"><a href="{{ URL::to('admin/users') }}" class="{{classActivePath('users')}}"><i class="fa fa-users"></i><span>{{trans('words.users')}}</span></a></li>@endif
            @if(admin_can_module('roles'))<li class="{{classActivePath('sub_admin')}}"><a href="{{ URL::to('admin/sub_admin') }}" class="{{classActivePath('sub_admin')}}"><i class="fa fa-user-secret"></i><span>{{trans('words.admin')}}</span></a></li>@endif
            @if(admin_can_module('roles'))<li class="{{classActivePath('roles')}}"><a href="{{ URL::to('admin/roles') }}" class="{{classActivePath('roles')}}"><i class="fa fa-shield"></i><span>Roles &amp; Permissions</span></a></li>@endif
          </ul>
        </li>
        @endif
        @if(admin_can_module('reviews'))<li><a href="{{ URL::to('admin/reviews') }}" class="waves-effect {{classActivePath('reviews')}}"><span class="nav-ic ic-pink"><i class="fa fa-star"></i></span><span>{{trans('words.reviews')}}</span></a></li>@endif
        @if(admin_can_module('reports'))<li><a href="{{ URL::to('admin/reports') }}" class="waves-effect {{classActivePath('reports')}}"><span class="nav-ic ic-slate"><i class="fa fa-bug"></i></span><span>{{trans('words.reports')}}</span></a></li>@endif

        @if(admin_can_module('subscription') || admin_can_module('payment') || admin_can_module('transactions'))
        <li class="menu-section">Commerce</li>
        @endif
        @if(admin_can_module('subscription'))<li><a href="{{ URL::to('admin/subscription_plan') }}" class="waves-effect {{classActivePath('subscription_plan')}}"><span class="nav-ic ic-green"><i class="fa fa-dollar"></i></span><span>{{trans('words.subscription_plan')}}</span></a></li>@endif
        @if(admin_can_module('payment'))<li><a href="{{ URL::to('admin/payment_gateway') }}" class="waves-effect {{classActivePath('payment_gateway')}}"><span class="nav-ic ic-indigo"><i class="fa fa-credit-card-alt"></i></span><span>{{trans('words.payment_gateway')}}</span></a></li>@endif
        @if(admin_can_module('transactions'))<li><a href="{{ URL::to('admin/transactions') }}" class="waves-effect {{classActivePath('transactions')}}"><span class="nav-ic ic-blue"><i class="fa fa-list"></i></span><span>{{trans('words.transactions')}}</span></a></li>@endif

        @if(admin_can_module('pages') || admin_can_module('notification') || admin_can_module('app_ads') || admin_can_module('settings'))
        <li class="menu-section">System</li>
        @endif
        @if(admin_can_module('pages'))<li><a href="{{ URL::to('admin/pages') }}" class="waves-effect {{classActivePath('pages')}}"><span class="nav-ic ic-purple"><i class="fa fa-edit"></i></span><span>{{trans('words.pages')}}</span></a></li>@endif
        @if(admin_can_module('notification'))<li><a href="{{ URL::to('admin/notification_send') }}" class="waves-effect {{classActivePath('notification_send')}}"><span class="nav-ic ic-orange"><i class="fa fa-bell"></i></span><span>{{trans('words.notification_send')}}</span></a></li>@endif
        @if(admin_can_module('app_ads'))<li><a href="{{ URL::to('admin/ad_list') }}" class="waves-effect {{classActivePath('ad_list')}}"><span class="nav-ic ic-cyan"><i class="fa fa-buysellads"></i></span><span>{{trans('words.ad_settings')}}</span></a></li>@endif
        @if(admin_can_module('settings'))
        <li class="has_sub">
          <a href="javascript:void(0);" class="waves-effect"><span class="nav-ic ic-slate"><i class="fa fa-cog"></i></span><span>{{trans('words.settings')}}</span><span class="menu-arrow"></span></a>
          <ul class="list-unstyled">
            <li class="{{classActivePath('general_settings')}}"><a href="{{ URL::to('admin/general_settings') }}" class="{{classActivePath('general_settings')}}"><i class="fa fa-cog"></i><span>{{trans('words.general')}}</span></a></li>
            <li class="{{classActivePath('email_settings')}}"><a href="{{ URL::to('admin/email_settings') }}" class="{{classActivePath('email_settings')}}"><i class="fa fa-envelope"></i><span>{{trans('words.smtp_email')}}</span></a></li>
            <li class="{{classActivePath('onesignal_notification')}}"><a href="{{ URL::to('admin/onesignal_notification') }}" class="{{classActivePath('onesignal_notification')}}"><i class="fa fa-podcast"></i><span>{{trans('words.onesignal_notification')}}</span></a></li>
            <li class="{{classActivePath('app_update_popup')}}"><a href="{{ URL::to('admin/app_update_popup') }}" class="{{classActivePath('app_update_popup')}}"><i class="fa fa-external-link"></i><span>{{trans('words.app_update_popup')}}</span></a></li>
            <li class="{{classActivePath('others_settings')}}"><a href="{{ URL::to('admin/others_settings') }}" class="{{classActivePath('others_settings')}}"><i class="fa fa-asterisk"></i><span>{{trans('words.others_settings')}}</span></a></li>
          </ul>
        </li>
        @endif
        @if(Auth::User()->usertype=="Admin")
        <li><a href="{{ URL::to('admin/verify_purchase_app') }}" class="waves-effect {{classActivePath('verify_purchase_app')}}"><span class="nav-ic ic-red"><i class="fa fa-lock"></i></span><span>{{trans('words.app_verify')}}</span></a></li>
        <li><a href="{{ URL::to('admin/api_urls') }}" class="waves-effect {{classActivePath('api_urls')}}"><span class="nav-ic ic-slate"><i class="fa fa-align-justify"></i></span><span>{{trans('words.app_api')}}</span></a></li>
        @endif
      </ul>
    </div>
  </div>
</div>
