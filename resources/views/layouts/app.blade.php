<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  @include('partials.seo')

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  @stack('css')

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
          <div class="search-element">
            <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            <div class="search-backdrop"></div>
            <div class="search-result">
              <div class="search-header">
                Histories
              </div>
              <div class="search-item">
                <a href="#">How to hack NASA using CSS</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-item">
                <a href="#">Kodinger.com</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-item">
                <a href="#">#Stisla</a>
                <a href="#" class="search-close"><i class="fas fa-times"></i></a>
              </div>
              <div class="search-header">
                Result
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('assets/img/products/product-3-50.png') }}" alt="product">
                  oPhone S9 Limited Edition
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('assets/img/products/product-2-50.png') }}" alt="product">
                  Drone X2 New Gen-7
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <img class="mr-3 rounded" width="30" src="{{ asset('assets/img/products/product-1-50.png') }}" alt="product">
                  Headphone Blitz
                </a>
              </div>
              <div class="search-header">
                Projects
              </div>
              <div class="search-item">
                <a href="#">
                  <div class="search-icon bg-danger text-white mr-3">
                    <i class="fas fa-code"></i>
                  </div>
                  Stisla Admin Template
                </a>
              </div>
              <div class="search-item">
                <a href="#">
                  <div class="search-icon bg-primary text-white mr-3">
                    <i class="fas fa-laptop"></i>
                  </div>
                  Create a new Homepage Design
                </a>
              </div>
            </div>
          </div>
        </form>
        <ul class="navbar-nav navbar-right">
          @can('chat.view')
            <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" @class(['nav-link nav-link-lg message-toggle', 'beep' => $navUnreadCount > 0])><i class="far fa-envelope"></i></a>
              <div class="dropdown-menu dropdown-list dropdown-menu-right">
                <div class="dropdown-header">{{ __('Messages') }}
                  @if ($navUnreadCount > 0)
                    <div class="float-right">
                      <a href="#" onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">{{ __('Mark All As Read') }}</a>
                    </div>
                  @endif
                </div>
                <div class="dropdown-list-content dropdown-list-message">
                  @forelse ($navMessages as $navMessage)
                    <a href="{{ route('chat.index', ['user' => $navMessage->sender_id]) }}" @class(['dropdown-item', 'dropdown-item-unread' => ! $navMessage->is_read])>
                      <div class="dropdown-item-avatar">
                        <img alt="image" src="{{ $navMessage->sender?->avatar_url ?? asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle">
                        @if ($navMessage->sender?->isOnline())
                          <div class="is-online"></div>
                        @endif
                      </div>
                      <div class="dropdown-item-desc">
                        <b>{{ $navMessage->sender?->name ?? __('Unknown') }}</b>
                        <p>{{ \Illuminate\Support\Str::limit($navMessage->message, 60) }}</p>
                        <div class="time">{{ $navMessage->created_at?->diffForHumans() }}</div>
                      </div>
                    </a>
                  @empty
                    <div class="dropdown-item text-center text-muted">{{ __('No messages yet') }}</div>
                  @endforelse
                </div>
                <div class="dropdown-footer text-center">
                  <a href="{{ route('chat.index') }}">{{ __('View All') }} <i class="fas fa-chevron-right"></i></a>
                </div>
              </div>
              <form id="mark-all-read-form" action="{{ route('chat.readAll') }}" method="POST" class="d-none">@csrf</form>
            </li>
          @endcan
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notifications
                <div class="float-right">
                  <a href="#">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-icon bg-primary text-white">
                    <i class="fas fa-code"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Template update is available now!
                    <div class="time text-primary">2 Min Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="far fa-user"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>You</b> and <b>Dedik Sugiharto</b> are now friends
                    <div class="time">10 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-success text-white">
                    <i class="fas fa-check"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Kusnaedi</b> has moved task <b>Fix bug header</b> to <b>Done</b>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Low disk space. Let's clean it!
                    <div class="time">17 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    Welcome to Stisla template!
                    <div class="time">Yesterday</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user" title="{{ __('Switch Language') }}">
              <i class="fas fa-globe mr-1"></i>
              <div class="d-none d-md-inline-block font-weight-bold text-uppercase">{{ app()->getLocale() }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">{{ __('Select Language') }}</div>
              <a href="{{ route('lang.switch', 'id') }}" class="dropdown-item has-icon d-flex align-items-center justify-content-between {{ app()->getLocale() === 'id' ? 'active font-weight-bold' : '' }}">
                <span><span class="mr-2">🇮🇩</span> Bahasa Indonesia</span>
                @if (app()->getLocale() === 'id')
                  <i class="fas fa-check text-primary"></i>
                @endif
              </a>
              <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item has-icon d-flex align-items-center justify-content-between {{ app()->getLocale() === 'en' ? 'active font-weight-bold' : '' }}">
                <span><span class="mr-2">🇬🇧</span> English</span>
                @if (app()->getLocale() === 'en')
                  <i class="fas fa-check text-primary"></i>
                @endif
              </a>
            </div>
          </li>
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ auth()->user()?->avatar_url ?? asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1" style="width: 30px; height: 30px; object-fit: cover;">
              <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()?->name ?? 'User' }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">
                @if (auth()->user()?->last_logged_in)
                  {{ __('Logged in :time', ['time' => auth()->user()->last_logged_in->diffForHumans()]) }}
                @else
                  {{ __('Logged in just now') }}
                @endif
              </div>
              <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> {{ __('Profile') }}
              </a>
              <a href="{{ route('features-activities.html') }}" class="dropdown-item has-icon">
                <i class="fas fa-bolt"></i> Activities
              </a>
              <a href="{{ route('settings.index') }}" class="dropdown-item has-icon">
                <i class="fas fa-cog"></i> Settings
              </a>
              <div class="dropdown-divider"></div>
              <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                @csrf
              </form>
              <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        @if (config('app.is_stisla_demo'))
          @include('partials.sidebar-pages')
        @else
          <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
              <a href="{{ route('dashboard') }}">{{ \App\Models\Setting::getValue('site_title', 'Stisla') }}</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
              <a href="{{ route('dashboard') }}">{{ strtoupper(substr(\App\Models\Setting::getValue('site_title', 'Stisla'), 0, 2)) }}</a>
            </div>
            <ul class="sidebar-menu">
              @if (isset($sidebarMenus) && $sidebarMenus->isNotEmpty())
                @foreach ($sidebarMenus as $menu)
                  @if (! $menu->canAccess())
                    @continue
                  @endif

                  @if ($menu->type === 'header')
                    <li class="menu-header">{{ __($menu->name) }}</li>
                  @elseif ($menu->type === 'dropdown')
                    <li @class(['nav-item dropdown', 'active' => $menu->isActiveRoute()])>
                      <a href="#" class="nav-link has-dropdown"><i class="{{ $menu->icon ?: 'fas fa-plug' }}"></i> <span>{{ __($menu->name) }}</span></a>
                      <ul class="dropdown-menu">
                        @foreach ($menu->children as $child)
                          @if ($child->canAccess())
                            <li @class(['active' => $child->isActiveRoute()])>
                              <a class="nav-link" href="{{ $child->resolvedUrl() }}">
                                @if ($child->icon)
                                  <i class="{{ $child->icon }} mr-1"></i>
                                @endif
                                {{ __($child->name) }}
                              </a>
                            </li>
                          @endif
                        @endforeach
                      </ul>
                    </li>
                  @else
                    <li @class(['active' => $menu->isActiveRoute()])>
                      <a class="nav-link" href="{{ $menu->resolvedUrl() }}">
                        <i class="{{ $menu->icon ?: 'fas fa-circle' }}"></i> <span>{{ __($menu->name) }}</span>
                      </a>
                    </li>
                  @endif
                @endforeach
              @else
                <li class="menu-header">{{ __('Dashboard') }}</li>
                <li @class(['active' => request()->routeIs('dashboard')])>
                  <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fire"></i> <span>{{ __('Dashboard') }}</span></a>
                </li>
                @canany(['classes.view', 'teachers.view', 'students.view', 'subjects.view', 'fields.view', 'spp-payments.view', 'tickets.view', 'chat.view'])
                  <li class="menu-header">{{ __('Modules') }}</li>
                  <li @class(['nav-item dropdown', 'active' => request()->routeIs('classes.*', 'teachers.*', 'students.*', 'subjects.*', 'fields.*', 'spp-payments.*', 'tickets.*', 'chat.*')])>
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>{{ __('Modules') }}</span></a>
                    <ul class="dropdown-menu">
                      @can('classes.view')
                        <li @class(['active' => request()->routeIs('classes.*')])><a class="nav-link" href="{{ route('classes.index') }}">{{ __('Classes') }}</a></li>
                      @endcan
                      @can('teachers.view')
                        <li @class(['active' => request()->routeIs('teachers.*')])><a class="nav-link" href="{{ route('teachers.index') }}">{{ __('Teachers') }}</a></li>
                      @endcan
                      @can('students.view')
                        <li @class(['active' => request()->routeIs('students.*')])><a class="nav-link" href="{{ route('students.index') }}">{{ __('Students') }}</a></li>
                      @endcan
                      @can('subjects.view')
                        <li @class(['active' => request()->routeIs('subjects.*')])><a class="nav-link" href="{{ route('subjects.index') }}">{{ __('Subjects') }}</a></li>
                      @endcan
                      @can('fields.view')
                        <li @class(['active' => request()->routeIs('fields.*')])><a class="nav-link" href="{{ route('fields.index') }}">{{ __('Fields') }}</a></li>
                      @endcan
                      @can('spp-payments.view')
                        <li @class(['active' => request()->routeIs('spp-payments.*')])><a class="nav-link" href="{{ route('spp-payments.index') }}">{{ __('SPP Payments') }}</a></li>
                      @endcan
                      @can('tickets.view')
                        <li @class(['active' => request()->routeIs('tickets.*')])><a class="nav-link" href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a></li>
                      @endcan
                      @can('chat.view')
                        <li @class(['active' => request()->routeIs('chat.*')])><a class="nav-link" href="{{ route('chat.index') }}">{{ __('Chat Box') }}</a></li>
                      @endcan
                    </ul>
                  </li>
                @endcanany

                @canany(['products.view', 'product-categories.view', 'product-brands.view', 'product-vendors.view', 'product-sizes.view', 'product-colors.view', 'product-banners.view'])
                  <li @class(['nav-item dropdown', 'active' => request()->routeIs('products.*', 'product-categories.*', 'product-brands.*', 'product-vendors.*', 'product-sizes.*', 'product-colors.*', 'product-banners.*')])>
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-shopping-bag"></i> <span>{{ __('Shop / Products') }}</span></a>
                    <ul class="dropdown-menu">
                      @can('products.view')
                        <li @class(['active' => request()->routeIs('products.*')])><a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                      @endcan
                      @can('product-categories.view')
                        <li @class(['active' => request()->routeIs('product-categories.*')])><a class="nav-link" href="{{ route('product-categories.index') }}">{{ __('Categories') }}</a></li>
                      @endcan
                      @can('product-brands.view')
                        <li @class(['active' => request()->routeIs('product-brands.*')])><a class="nav-link" href="{{ route('product-brands.index') }}">{{ __('Brands') }}</a></li>
                      @endcan
                      @can('product-vendors.view')
                        <li @class(['active' => request()->routeIs('product-vendors.*')])><a class="nav-link" href="{{ route('product-vendors.index') }}">{{ __('Vendors') }}</a></li>
                      @endcan
                      @can('product-sizes.view')
                        <li @class(['active' => request()->routeIs('product-sizes.*')])><a class="nav-link" href="{{ route('product-sizes.index') }}">{{ __('Sizes') }}</a></li>
                      @endcan
                      @can('product-colors.view')
                        <li @class(['active' => request()->routeIs('product-colors.*')])><a class="nav-link" href="{{ route('product-colors.index') }}">{{ __('Colors') }}</a></li>
                      @endcan
                      @can('product-banners.view')
                        <li @class(['active' => request()->routeIs('product-banners.*')])><a class="nav-link" href="{{ route('product-banners.index') }}">{{ __('Banners') }}</a></li>
                      @endcan
                    </ul>
                  </li>
                @endcanany
                @canany(['users.view', 'roles.view', 'permissions.view', 'menus.view', 'backup.view'])
                  <li class="menu-header">{{ __('User Management') }}</li>
                  <li @class(['nav-item dropdown', 'active' => request()->routeIs('users.*', 'roles.*', 'permissions.*', 'menus.*', 'backups.*')])>
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users-cog"></i> <span>{{ __('Access Control') }}</span></a>
                    <ul class="dropdown-menu">
                      @can('users.view')
                        <li @class(['active' => request()->routeIs('users.*')])><a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a></li>
                      @endcan
                      @can('roles.view')
                        <li @class(['active' => request()->routeIs('roles.*')])><a class="nav-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a></li>
                      @endcan
                      @can('permissions.view')
                        <li @class(['active' => request()->routeIs('permissions.*')])><a class="nav-link" href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a></li>
                      @endcan
                      @can('menus.view')
                        <li @class(['active' => request()->routeIs('menus.*')])><a class="nav-link" href="{{ route('menus.index') }}">{{ __('Menu Management') }}</a></li>
                      @endcan
                      @can('backup.view')
                        <li @class(['active' => request()->routeIs('backups.*')])><a class="nav-link" href="{{ route('backups.index') }}">{{ __('Database Backup') }}</a></li>
                      @endcan
                    </ul>
                  </li>
                @endcanany
                @can('settings.view')
                  <li class="menu-header">{{ __('Settings') }}</li>
                  <li @class(['active' => request()->routeIs('settings.*')])>
                    <a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> <span>{{ __('Settings') }}</span></a>
                  </li>
                @endcan
              @endif
            </ul>
          </aside>
        @endif
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          @yield('content')
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; {{ \App\Models\Setting::getValue('year', date('Y')) }} <div class="bullet"></div> {{ \App\Models\Setting::getValue('site_title', 'Stisla') }}
        </div>
        <div class="footer-right">
          2.3.0
        </div>
      </footer>
    </div>
  </div>

  @stack('modals')

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- JS Libraies -->
  @stack('js')

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- Page Specific JS File -->
  @stack('page-js')
</body>

</html>
