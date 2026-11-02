<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title') &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  @stack('css')

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
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
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle beep"><i class="far fa-envelope"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Messages
                <div class="float-right">
                  <a href="#">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-message">
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Kusnaedi</b>
                    <p>Hello, Bro!</p>
                    <div class="time">10 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-2.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Dedik Sugiharto</b>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-3.png') }}" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Agung Ardiansyah</b>
                    <p>Sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-4.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Ardian Rahardiansyah</b>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit ess</p>
                    <div class="time">16 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-5.png') }}" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Alfa Zulkarnain</b>
                    <p>Exercitation ullamco laboris nisi ut aliquip ex ea commodo</p>
                    <div class="time">Yesterday</div>
                  </div>
                </a>
              </div>
              <div class="dropdown-footer text-center">
                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </li>
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
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
              <div class="d-sm-none d-lg-inline-block">Hi, Ujang Maman</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">Logged in 5 min ago</div>
              <a href="{{ route('features-profile.html') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <a href="{{ route('features-activities.html') }}" class="dropdown-item has-icon">
                <i class="fas fa-bolt"></i> Activities
              </a>
              <a href="{{ route('features-settings.html') }}" class="dropdown-item has-icon">
                <i class="fas fa-cog"></i> Settings
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{ route('index.html') }}">Stisla</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('index.html') }}">St</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('index-0.html', 'index.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('index-0.html')])><a class="nav-link" href="{{ route('index-0.html') }}">General Dashboard</a></li>
                <li @class(['active' => request()->routeIs('index.html')])><a class="nav-link" href="{{ route('index.html') }}">Ecommerce Dashboard</a></li>
              </ul>
            </li>
            <li class="menu-header">Starter</li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('layout-default.html', 'layout-transparent.html', 'layout-top-navigation.html')])>
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Layout</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('layout-default.html')])><a class="nav-link" href="{{ route('layout-default.html') }}">Default Layout</a></li>
                <li @class(['active' => request()->routeIs('layout-transparent.html')])><a class="nav-link" href="{{ route('layout-transparent.html') }}">Transparent Sidebar</a></li>
                <li @class(['active' => request()->routeIs('layout-top-navigation.html')])><a class="nav-link" href="{{ route('layout-top-navigation.html') }}">Top Navigation</a></li>
              </ul>
            </li>
            <li @class(['active' => request()->routeIs('blank.html')])><a class="nav-link" href="{{ route('blank.html') }}"><i class="far fa-square"></i> <span>Blank Page</span></a></li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('bootstrap-alert.html', 'bootstrap-badge.html', 'bootstrap-breadcrumb.html', 'bootstrap-buttons.html', 'bootstrap-card.html', 'bootstrap-carousel.html', 'bootstrap-collapse.html', 'bootstrap-dropdown.html', 'bootstrap-form.html', 'bootstrap-list-group.html', 'bootstrap-media-object.html', 'bootstrap-modal.html', 'bootstrap-nav.html', 'bootstrap-navbar.html', 'bootstrap-pagination.html', 'bootstrap-popover.html', 'bootstrap-progress.html', 'bootstrap-table.html', 'bootstrap-tooltip.html', 'bootstrap-typography.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Bootstrap</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('bootstrap-alert.html')])><a class="nav-link" href="{{ route('bootstrap-alert.html') }}">Alert</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-badge.html')])><a class="nav-link" href="{{ route('bootstrap-badge.html') }}">Badge</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-breadcrumb.html')])><a class="nav-link" href="{{ route('bootstrap-breadcrumb.html') }}">Breadcrumb</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-buttons.html')])><a class="nav-link" href="{{ route('bootstrap-buttons.html') }}">Buttons</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-card.html')])><a class="nav-link" href="{{ route('bootstrap-card.html') }}">Card</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-carousel.html')])><a class="nav-link" href="{{ route('bootstrap-carousel.html') }}">Carousel</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-collapse.html')])><a class="nav-link" href="{{ route('bootstrap-collapse.html') }}">Collapse</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-dropdown.html')])><a class="nav-link" href="{{ route('bootstrap-dropdown.html') }}">Dropdown</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-form.html')])><a class="nav-link" href="{{ route('bootstrap-form.html') }}">Form</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-list-group.html')])><a class="nav-link" href="{{ route('bootstrap-list-group.html') }}">List Group</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-media-object.html')])><a class="nav-link" href="{{ route('bootstrap-media-object.html') }}">Media Object</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-modal.html')])><a class="nav-link" href="{{ route('bootstrap-modal.html') }}">Modal</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-nav.html')])><a class="nav-link" href="{{ route('bootstrap-nav.html') }}">Nav</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-navbar.html')])><a class="nav-link" href="{{ route('bootstrap-navbar.html') }}">Navbar</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-pagination.html')])><a class="nav-link" href="{{ route('bootstrap-pagination.html') }}">Pagination</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-popover.html')])><a class="nav-link" href="{{ route('bootstrap-popover.html') }}">Popover</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-progress.html')])><a class="nav-link" href="{{ route('bootstrap-progress.html') }}">Progress</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-table.html')])><a class="nav-link" href="{{ route('bootstrap-table.html') }}">Table</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-tooltip.html')])><a class="nav-link" href="{{ route('bootstrap-tooltip.html') }}">Tooltip</a></li>
                <li @class(['active' => request()->routeIs('bootstrap-typography.html')])><a class="nav-link" href="{{ route('bootstrap-typography.html') }}">Typography</a></li>
              </ul>
            </li>
            <li class="menu-header">Stisla</li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('components-article.html', 'components-avatar.html', 'components-chat-box.html', 'components-empty-state.html', 'components-gallery.html', 'components-hero.html', 'components-multiple-upload.html', 'components-pricing.html', 'components-statistic.html', 'components-tab.html', 'components-table.html', 'components-user.html', 'components-wizard.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Components</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('components-article.html')])><a class="nav-link" href="{{ route('components-article.html') }}">Article</a></li>
                <li @class(['active' => request()->routeIs('components-avatar.html')])><a class="nav-link beep beep-sidebar" href="{{ route('components-avatar.html') }}">Avatar</a></li>
                <li @class(['active' => request()->routeIs('components-chat-box.html')])><a class="nav-link" href="{{ route('components-chat-box.html') }}">Chat Box</a></li>
                <li @class(['active' => request()->routeIs('components-empty-state.html')])><a class="nav-link beep beep-sidebar" href="{{ route('components-empty-state.html') }}">Empty State</a></li>
                <li @class(['active' => request()->routeIs('components-gallery.html')])><a class="nav-link" href="{{ route('components-gallery.html') }}">Gallery</a></li>
                <li @class(['active' => request()->routeIs('components-hero.html')])><a class="nav-link beep beep-sidebar" href="{{ route('components-hero.html') }}">Hero</a></li>
                <li @class(['active' => request()->routeIs('components-multiple-upload.html')])><a class="nav-link" href="{{ route('components-multiple-upload.html') }}">Multiple Upload</a></li>
                <li @class(['active' => request()->routeIs('components-pricing.html')])><a class="nav-link beep beep-sidebar" href="{{ route('components-pricing.html') }}">Pricing</a></li>
                <li @class(['active' => request()->routeIs('components-statistic.html')])><a class="nav-link" href="{{ route('components-statistic.html') }}">Statistic</a></li>
                <li @class(['active' => request()->routeIs('components-tab.html')])><a class="nav-link" href="{{ route('components-tab.html') }}">Tab</a></li>
                <li @class(['active' => request()->routeIs('components-table.html')])><a class="nav-link" href="{{ route('components-table.html') }}">Table</a></li>
                <li @class(['active' => request()->routeIs('components-user.html')])><a class="nav-link" href="{{ route('components-user.html') }}">User</a></li>
                <li @class(['active' => request()->routeIs('components-wizard.html')])><a class="nav-link beep beep-sidebar" href="{{ route('components-wizard.html') }}">Wizard</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('forms-advanced-form.html', 'forms-editor.html', 'forms-validation.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Forms</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('forms-advanced-form.html')])><a class="nav-link" href="{{ route('forms-advanced-form.html') }}">Advanced Form</a></li>
                <li @class(['active' => request()->routeIs('forms-editor.html')])><a class="nav-link" href="{{ route('forms-editor.html') }}">Editor</a></li>
                <li @class(['active' => request()->routeIs('forms-validation.html')])><a class="nav-link" href="{{ route('forms-validation.html') }}">Validation</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('gmaps-advanced-route.html', 'gmaps-draggable-marker.html', 'gmaps-geocoding.html', 'gmaps-geolocation.html', 'gmaps-marker.html', 'gmaps-multiple-marker.html', 'gmaps-route.html', 'gmaps-simple.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-map-marker-alt"></i> <span>Google Maps</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('gmaps-advanced-route.html')])><a href="{{ route('gmaps-advanced-route.html') }}">Advanced Route</a></li>
                <li @class(['active' => request()->routeIs('gmaps-draggable-marker.html')])><a href="{{ route('gmaps-draggable-marker.html') }}">Draggable Marker</a></li>
                <li @class(['active' => request()->routeIs('gmaps-geocoding.html')])><a href="{{ route('gmaps-geocoding.html') }}">Geocoding</a></li>
                <li @class(['active' => request()->routeIs('gmaps-geolocation.html')])><a href="{{ route('gmaps-geolocation.html') }}">Geolocation</a></li>
                <li @class(['active' => request()->routeIs('gmaps-marker.html')])><a href="{{ route('gmaps-marker.html') }}">Marker</a></li>
                <li @class(['active' => request()->routeIs('gmaps-multiple-marker.html')])><a href="{{ route('gmaps-multiple-marker.html') }}">Multiple Marker</a></li>
                <li @class(['active' => request()->routeIs('gmaps-route.html')])><a href="{{ route('gmaps-route.html') }}">Route</a></li>
                <li @class(['active' => request()->routeIs('gmaps-simple.html')])><a href="{{ route('gmaps-simple.html') }}">Simple</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('modules-calendar.html', 'modules-chartjs.html', 'modules-datatables.html', 'modules-flag.html', 'modules-font-awesome.html', 'modules-ion-icons.html', 'modules-owl-carousel.html', 'modules-sparkline.html', 'modules-sweet-alert.html', 'modules-toastr.html', 'modules-vector-map.html', 'modules-weather-icon.html', 'classes.*')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>Modules</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('classes.*')])><a class="nav-link" href="{{ route('classes.index') }}">{{ __('Classes') }}</a></li>
                <li @class(['active' => request()->routeIs('modules-calendar.html')])><a class="nav-link" href="{{ route('modules-calendar.html') }}">Calendar</a></li>
                <li @class(['active' => request()->routeIs('modules-chartjs.html')])><a class="nav-link" href="{{ route('modules-chartjs.html') }}">ChartJS</a></li>
                <li @class(['active' => request()->routeIs('modules-datatables.html')])><a class="nav-link" href="{{ route('modules-datatables.html') }}">DataTables</a></li>
                <li @class(['active' => request()->routeIs('modules-flag.html')])><a class="nav-link" href="{{ route('modules-flag.html') }}">Flag</a></li>
                <li @class(['active' => request()->routeIs('modules-font-awesome.html')])><a class="nav-link" href="{{ route('modules-font-awesome.html') }}">Font Awesome</a></li>
                <li @class(['active' => request()->routeIs('modules-ion-icons.html')])><a class="nav-link" href="{{ route('modules-ion-icons.html') }}">Ion Icons</a></li>
                <li @class(['active' => request()->routeIs('modules-owl-carousel.html')])><a class="nav-link" href="{{ route('modules-owl-carousel.html') }}">Owl Carousel</a></li>
                <li @class(['active' => request()->routeIs('modules-sparkline.html')])><a class="nav-link" href="{{ route('modules-sparkline.html') }}">Sparkline</a></li>
                <li @class(['active' => request()->routeIs('modules-sweet-alert.html')])><a class="nav-link" href="{{ route('modules-sweet-alert.html') }}">Sweet Alert</a></li>
                <li @class(['active' => request()->routeIs('modules-toastr.html')])><a class="nav-link" href="{{ route('modules-toastr.html') }}">Toastr</a></li>
                <li @class(['active' => request()->routeIs('modules-vector-map.html')])><a class="nav-link" href="{{ route('modules-vector-map.html') }}">Vector Map</a></li>
                <li @class(['active' => request()->routeIs('modules-weather-icon.html')])><a class="nav-link" href="{{ route('modules-weather-icon.html') }}">Weather Icon</a></li>
              </ul>
            </li>
            <li class="menu-header">Pages</li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('auth-forgot-password.html', 'auth-login.html', 'auth-login-2.html', 'auth-register.html', 'auth-reset-password.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Auth</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('auth-forgot-password.html')])><a href="{{ route('auth-forgot-password.html') }}">Forgot Password</a></li>
                <li @class(['active' => request()->routeIs('auth-login.html')])><a href="{{ route('auth-login.html') }}">Login</a></li>
                <li @class(['active' => request()->routeIs('auth-login-2.html')])><a class="beep beep-sidebar" href="{{ route('auth-login-2.html') }}">Login 2</a></li>
                <li @class(['active' => request()->routeIs('auth-register.html')])><a href="{{ route('auth-register.html') }}">Register</a></li>
                <li @class(['active' => request()->routeIs('auth-reset-password.html')])><a href="{{ route('auth-reset-password.html') }}">Reset Password</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('errors-503.html', 'errors-403.html', 'errors-404.html', 'errors-500.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-exclamation"></i> <span>Errors</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('errors-503.html')])><a class="nav-link" href="{{ route('errors-503.html') }}">503</a></li>
                <li @class(['active' => request()->routeIs('errors-403.html')])><a class="nav-link" href="{{ route('errors-403.html') }}">403</a></li>
                <li @class(['active' => request()->routeIs('errors-404.html')])><a class="nav-link" href="{{ route('errors-404.html') }}">404</a></li>
                <li @class(['active' => request()->routeIs('errors-500.html')])><a class="nav-link" href="{{ route('errors-500.html') }}">500</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('features-activities.html', 'features-post-create.html', 'features-posts.html', 'features-profile.html', 'features-settings.html', 'features-setting-detail.html', 'features-tickets.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-bicycle"></i> <span>Features</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('features-activities.html')])><a class="nav-link" href="{{ route('features-activities.html') }}">Activities</a></li>
                <li @class(['active' => request()->routeIs('features-post-create.html')])><a class="nav-link" href="{{ route('features-post-create.html') }}">Post Create</a></li>
                <li @class(['active' => request()->routeIs('features-posts.html')])><a class="nav-link" href="{{ route('features-posts.html') }}">Posts</a></li>
                <li @class(['active' => request()->routeIs('features-profile.html')])><a class="nav-link" href="{{ route('features-profile.html') }}">Profile</a></li>
                <li @class(['active' => request()->routeIs('features-settings.html')])><a class="nav-link" href="{{ route('features-settings.html') }}">Settings</a></li>
                <li @class(['active' => request()->routeIs('features-setting-detail.html')])><a class="nav-link" href="{{ route('features-setting-detail.html') }}">Setting Detail</a></li>
                <li @class(['active' => request()->routeIs('features-tickets.html')])><a class="nav-link" href="{{ route('features-tickets.html') }}">Tickets</a></li>
              </ul>
            </li>
            <li @class(['nav-item dropdown', 'active' => request()->routeIs('utilities-contact.html', 'utilities-invoice.html', 'utilities-subscribe.html')])>
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-ellipsis-h"></i> <span>Utilities</span></a>
              <ul class="dropdown-menu">
                <li @class(['active' => request()->routeIs('utilities-contact.html')])><a href="{{ route('utilities-contact.html') }}">Contact</a></li>
                <li @class(['active' => request()->routeIs('utilities-invoice.html')])><a class="nav-link" href="{{ route('utilities-invoice.html') }}">Invoice</a></li>
                <li @class(['active' => request()->routeIs('utilities-subscribe.html')])><a href="{{ route('utilities-subscribe.html') }}">Subscribe</a></li>
              </ul>
            </li>
            <li @class(['active' => request()->routeIs('credits.html')])><a class="nav-link" href="{{ route('credits.html') }}"><i class="fas fa-pencil-ruler"></i> <span>Credits</span></a></li>
          </ul>

          <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
              <i class="fas fa-rocket"></i> Documentation
            </a>
          </div>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          @yield('content')
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2018 <div class="bullet"></div> Design By <a href="https://nauval.in/">Muhamad Nauval Azhar</a>
        </div>
        <div class="footer-right">
          2.3.0
        </div>
      </footer>
    </div>
  </div>

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
