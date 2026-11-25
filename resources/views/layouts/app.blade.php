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
          @php
            $searchableMenus = collect();
            foreach ($sidebarMenus ?? collect() as $menu) {
                if ($menu->type === 'header') {
                    continue;
                }
                if ($menu->permission && ! auth()->user()?->can($menu->permission)) {
                    continue;
                }
                if ($menu->type === 'item') {
                    $href = $menu->route && \Illuminate\Support\Facades\Route::has($menu->route)
                        ? route($menu->route)
                        : ($menu->url ?: '#');
                    if ($href !== '#') {
                        $searchableMenus->push([
                            'label' => __($menu->name),
                            'parent' => null,
                            'icon' => $menu->icon ?: 'fas fa-circle',
                            'url' => $href,
                        ]);
                    }
                } elseif ($menu->type === 'dropdown') {
                    foreach ($menu->children ?? collect() as $child) {
                        if ($child->permission && ! auth()->user()?->can($child->permission)) {
                            continue;
                        }
                        $href = $child->route && \Illuminate\Support\Facades\Route::has($child->route)
                            ? route($child->route)
                            : ($child->url ?: '#');
                        if ($href !== '#') {
                            $searchableMenus->push([
                                'label' => __($child->name),
                                'parent' => __($menu->name),
                                'icon' => $child->icon ?: ($menu->icon ?: 'fas fa-circle'),
                                'url' => $href,
                            ]);
                        }
                    }
                }
            }
          @endphp
          <div class="search-element">
            <input id="navbar-menu-search" class="form-control" type="search" placeholder="{{ __('Search menu...') }}" aria-label="{{ __('Search menu...') }}" data-width="250" autocomplete="off">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            <div class="search-backdrop"></div>
            <div class="search-result">
              <div class="search-header">
                {{ __('Menus') }}
              </div>
              @forelse ($searchableMenus as $item)
                <div class="search-item navbar-menu-search-item" data-search="{{ strtolower($item['label'].' '.($item['parent'] ?? '')) }}">
                  <a href="{{ $item['url'] }}">
                    <div class="search-icon bg-primary text-white mr-3">
                      <i class="{{ $item['icon'] }}"></i>
                    </div>
                    <div>
                      <div>{{ $item['label'] }}</div>
                      @if ($item['parent'])
                        <div class="text-muted small">{{ $item['parent'] }}</div>
                      @endif
                    </div>
                  </a>
                </div>
              @empty
                <div class="search-item text-muted small px-3 py-2">
                  {{ __('No matching menu found.') }}
                </div>
              @endforelse
              <div id="navbar-menu-search-empty" class="search-item text-muted small px-3 py-2 d-none">
                {{ __('No matching menu found.') }}
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
                    <a href="{{ route('chat.index', ['user' => $navMessage->sender?->uuid]) }}" @class(['dropdown-item', 'dropdown-item-unread' => ! $navMessage->is_read])>
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
          <li class="dropdown dropdown-list-toggle">
            <a href="#" data-toggle="dropdown" @class(['nav-link notification-toggle nav-link-lg', 'beep' => ($navNotificationUnreadCount ?? 0) > 0])>
              <i class="far fa-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">{{ __('Notifications') }}
                @if (($navNotificationUnreadCount ?? 0) > 0)
                  <div class="float-right">
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-link btn-sm p-0 text-primary">{{ __('Mark All As Read') }}</button>
                    </form>
                  </div>
                @endif
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                @forelse ($navNotifications ?? collect() as $notif)
                  @php
                    $isRead = auth()->check() && $notif->isReadBy((int) auth()->id());
                    $bgClass = match ($notif->type) {
                        'primary' => 'bg-primary',
                        'success' => 'bg-success',
                        'warning' => 'bg-warning',
                        'danger' => 'bg-danger',
                        default => 'bg-info',
                    };
                  @endphp
                  <a href="{{ route('notifications.read', $notif) }}" @class(['dropdown-item', 'dropdown-item-unread' => ! $isRead])>
                    <div class="dropdown-item-icon {{ $bgClass }} text-white">
                      <i class="{{ $notif->icon ?: 'fas fa-bell' }}"></i>
                    </div>
                    <div class="dropdown-item-desc">
                      <div class="font-weight-600">{{ $notif->title }}</div>
                      <div class="small text-muted">{{ \Illuminate\Support\Str::limit($notif->message, 70) }}</div>
                      <div class="time {{ ! $isRead ? 'text-primary' : '' }}">
                        {{ $notif->created_at?->diffForHumans() }}
                        @if ($notif->is_broadcast)
                          &middot; {{ __('Broadcast') }}
                        @endif
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="dropdown-item text-center text-muted py-4">
                    {{ __('No notifications yet.') }}
                  </div>
                @endforelse
              </div>
              @can('notifications.view')
                <div class="dropdown-footer text-center">
                  <a href="{{ route('notifications.index') }}">{{ __('View All') }} <i class="fas fa-chevron-right"></i></a>
                </div>
              @endcan
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
              @if (session()->has('impersonator_id'))
                <form action="{{ route('users.stop-impersonate') }}" method="POST" id="stop-impersonate-form" class="d-none">
                  @csrf
                </form>
                <a href="{{ route('users.stop-impersonate') }}" class="dropdown-item has-icon text-warning" onclick="event.preventDefault(); document.getElementById('stop-impersonate-form').submit();">
                  <i class="fas fa-user-shield"></i> {{ __('Return to Admin') }}
                </a>
              @endif
              @if (config('stisla.is_demo'))
                <a href="{{ route('features-activities.html') }}" class="dropdown-item has-icon">
                  <i class="fas fa-bolt"></i> {{ __('Activities') }}
                </a>
              @endif
              @can('settings.view')
                <a href="{{ route('settings.index') }}" class="dropdown-item has-icon">
                  <i class="fas fa-cog"></i> {{ __('Settings') }}
                </a>
              @endcan
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
        @if (config('stisla.is_demo_menu'))
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
                @php
                  $menuList = $sidebarMenus->values();
                @endphp
                @foreach ($menuList as $index => $menu)
                  @if (! $menu->canAccess())
                    @continue
                  @endif

                  @if ($menu->type === 'header')
                    @php
                      $hasVisibleItemBelow = false;
                      for ($i = $index + 1; $i < $menuList->count(); $i++) {
                          $nextMenu = $menuList[$i];
                          if ($nextMenu->type === 'header') {
                              break;
                          }
                          if ($nextMenu->canAccess()) {
                              $hasVisibleItemBelow = true;
                              break;
                          }
                      }
                    @endphp
                    @if (! $hasVisibleItemBelow)
                      @continue
                    @endif
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
                      @if (\Illuminate\Support\Facades\Route::has('classes.index'))
                        @can('classes.view')
                          <li @class(['active' => request()->routeIs('classes.*')])><a class="nav-link" href="{{ route('classes.index') }}">{{ __('Classes') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('teachers.index'))
                        @can('teachers.view')
                          <li @class(['active' => request()->routeIs('teachers.*')])><a class="nav-link" href="{{ route('teachers.index') }}">{{ __('Teachers') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('students.index'))
                        @can('students.view')
                          <li @class(['active' => request()->routeIs('students.*')])><a class="nav-link" href="{{ route('students.index') }}">{{ __('Students') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('subjects.index'))
                        @can('subjects.view')
                          <li @class(['active' => request()->routeIs('subjects.*')])><a class="nav-link" href="{{ route('subjects.index') }}">{{ __('Subjects') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('fields.index'))
                        @can('fields.view')
                          <li @class(['active' => request()->routeIs('fields.*')])><a class="nav-link" href="{{ route('fields.index') }}">{{ __('Fields') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('spp-payments.index'))
                        @can('spp-payments.view')
                          <li @class(['active' => request()->routeIs('spp-payments.*')])><a class="nav-link" href="{{ route('spp-payments.index') }}">{{ __('SPP Payments') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('tickets.index'))
                        @can('tickets.view')
                          <li @class(['active' => request()->routeIs('tickets.*')])><a class="nav-link" href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('chat.index'))
                        @can('chat.view')
                          <li @class(['active' => request()->routeIs('chat.*')])><a class="nav-link" href="{{ route('chat.index') }}">{{ __('Chat Box') }}</a></li>
                        @endcan
                      @endif
                    </ul>
                  </li>
                @endcanany

                @canany(['products.view', 'product-categories.view', 'product-brands.view', 'product-vendors.view', 'product-sizes.view', 'product-colors.view', 'product-banners.view', 'our-teams.view'])
                  <li @class(['nav-item dropdown', 'active' => request()->routeIs('products.*', 'product-categories.*', 'product-brands.*', 'product-vendors.*', 'product-sizes.*', 'product-colors.*', 'product-banners.*', 'our-teams.*')])>
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-shopping-bag"></i> <span>{{ __('Shop / Products') }}</span></a>
                    <ul class="dropdown-menu">
                      @if (\Illuminate\Support\Facades\Route::has('products.index'))
                        @can('products.view')
                          <li @class(['active' => request()->routeIs('products.*')])><a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-categories.index'))
                        @can('product-categories.view')
                          <li @class(['active' => request()->routeIs('product-categories.*')])><a class="nav-link" href="{{ route('product-categories.index') }}">{{ __('Categories') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-brands.index'))
                        @can('product-brands.view')
                          <li @class(['active' => request()->routeIs('product-brands.*')])><a class="nav-link" href="{{ route('product-brands.index') }}">{{ __('Brands') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-vendors.index'))
                        @can('product-vendors.view')
                          <li @class(['active' => request()->routeIs('product-vendors.*')])><a class="nav-link" href="{{ route('product-vendors.index') }}">{{ __('Vendors') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-sizes.index'))
                        @can('product-sizes.view')
                          <li @class(['active' => request()->routeIs('product-sizes.*')])><a class="nav-link" href="{{ route('product-sizes.index') }}">{{ __('Sizes') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-colors.index'))
                        @can('product-colors.view')
                          <li @class(['active' => request()->routeIs('product-colors.*')])><a class="nav-link" href="{{ route('product-colors.index') }}">{{ __('Colors') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('product-banners.index'))
                        @can('product-banners.view')
                          <li @class(['active' => request()->routeIs('product-banners.*')])><a class="nav-link" href="{{ route('product-banners.index') }}">{{ __('Banners') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('our-teams.index'))
                        @can('our-teams.view')
                          <li @class(['active' => request()->routeIs('our-teams.*')])><a class="nav-link" href="{{ route('our-teams.index') }}">{{ __('Our Team') }}</a></li>
                        @endcan
                      @endif
                    </ul>
                  </li>
                @endcanany
                @canany(['users.view', 'roles.view', 'permissions.view', 'menus.view', 'backup.view'])
                  <li class="menu-header">{{ __('User Management') }}</li>
                  <li @class(['nav-item dropdown', 'active' => request()->routeIs('users.*', 'roles.*', 'permissions.*', 'menus.*', 'backups.*')])>
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users-cog"></i> <span>{{ __('Access Control') }}</span></a>
                    <ul class="dropdown-menu">
                      @if (\Illuminate\Support\Facades\Route::has('users.index'))
                        @can('users.view')
                          <li @class(['active' => request()->routeIs('users.*')])><a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('roles.index'))
                        @can('roles.view')
                          <li @class(['active' => request()->routeIs('roles.*')])><a class="nav-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('permissions.index'))
                        @can('permissions.view')
                          <li @class(['active' => request()->routeIs('permissions.*')])><a class="nav-link" href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('menus.index'))
                        @can('menus.view')
                          <li @class(['active' => request()->routeIs('menus.*')])><a class="nav-link" href="{{ route('menus.index') }}">{{ __('Menu Management') }}</a></li>
                        @endcan
                      @endif
                      @if (\Illuminate\Support\Facades\Route::has('backups.index'))
                        @can('backup.view')
                          <li @class(['active' => request()->routeIs('backups.*')])><a class="nav-link" href="{{ route('backups.index') }}">{{ __('Database Backup') }}</a></li>
                        @endcan
                      @endif
                    </ul>
                  </li>
                @endcanany
                @if (\Illuminate\Support\Facades\Route::has('settings.index'))
                  @can('settings.view')
                    <li class="menu-header">{{ __('Settings') }}</li>
                    <li @class(['active' => request()->routeIs('settings.*')])>
                      <a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> <span>{{ __('Settings') }}</span></a>
                    </li>
                  @endcan
                @endif
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
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
  @stack('js')

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>
  <script>
    window.StislaCrud = {
      title: @json(__('Are you sure?')),
      text: @json(__('Once deleted, you will not be able to recover this data!')),
      confirmButtonText: @json(__('Delete')),
      cancelButtonText: @json(__('Cancel')),
      successTitle: @json(__('Success')),
      errorTitle: @json(__('Error')),
      flashSuccess: @json(session('success')),
      flashError: @json(session('error'))
    };
  </script>
  <script src="{{ asset('assets/js/stisla-crud.js') }}"></script>

  <script>
    (function() {
      var $input = $('#navbar-menu-search');
      if (! $input.length) {
        return;
      }

      function filterNavbarMenus() {
        var keyword = ($input.val() || '').toLowerCase().trim();
        var visibleCount = 0;

        $('.navbar-menu-search-item').each(function() {
          var text = ($(this).data('search') || '').toString();
          var matched = keyword === '' || text.indexOf(keyword) !== -1;
          $(this).toggleClass('d-none', ! matched);
          if (matched) {
            visibleCount++;
          }
        });

        $('#navbar-menu-search-empty').toggleClass('d-none', visibleCount > 0);
      }

      $input.on('input keyup focus', filterNavbarMenus);

      $input.closest('form').on('submit', function(e) {
        e.preventDefault();
        var $firstLink = $('.navbar-menu-search-item:not(.d-none) a').first();
        if ($firstLink.length && $firstLink.attr('href')) {
          window.location.href = $firstLink.attr('href');
        }
      });
    })();
  </script>

  <!-- Page Specific JS File -->
  @stack('page-js')
</body>

</html>
