<div class="topbar">
      <div class="topbar-left"> 
        <a href="{{ URL::to('admin/dashboard') }}" class="logo">
        <span>
        @if(getcong('admin_logo'))
         <img src="{{ URL::asset('/'.getcong('admin_logo')) }}" alt="Admin Logo" width="190">  
        @else
          <span>APP Admin</span>
        @endif  
        </span>         
          <i class="mdi mdi-layers"></i>
        </a> 
      </div>
      <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
          <ul class="nav navbar-nav list-inline navbar-left">
            <li class="list-inline-item">
              <button class="button-menu-mobile open-left"> <i class="mdi mdi-menu"></i> </button>
            </li>
            <li class="list-inline-item">
              <h4 class="page-title">{{$page_title}}</h4>
            </li>
          </ul>
          <nav class="navbar-custom">
            <ul class="list-unstyled topbar-right-menu float-right mb-0">
               
              <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect nav-user topbar-user" data-toggle="dropdown" href="#"
                  role="button" aria-haspopup="false" aria-expanded="false">
                  @if(Auth::user()->user_image!='')
                  <img src="{{ user_image_url(Auth::user()->user_image) }}" alt="" class="rounded-circle" />
                  @else
                  <img src="{{ URL::asset('admin_assets/images/users/avatar-10.jpg') }}" alt="" class="rounded-circle" />
                  @endif
                  <span class="topbar-user-name">{{ Auth::user()->name }}
                    <span class="topbar-user-role">{{ Auth::user()->usertype=="Admin" ? 'Administrator' : 'Sub Admin' }}</span>
                  </span>
                  <i class="fa fa-angle-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                  <a href="{{ URL::to('admin/profile') }}" class="dropdown-item notify-item">
                    <i class="fa fa-user-o"></i> {{trans('words.profile')}}
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="{{ URL::to('admin/logout') }}" class="dropdown-item notify-item text-danger">
                    <i class="fa fa-sign-out"></i> {{trans('words.logout')}}
                  </a>
                </div>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>