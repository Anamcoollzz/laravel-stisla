<aside id="sidebar-wrapper">
  @php
    $siteTitle = \App\Models\Setting::getValue('site_title', 'Stisla');
    $shortTitle = strtoupper(substr($siteTitle, 0, 2));
  @endphp
  <div class="sidebar-brand">
    <a href="{{ route('index.html') }}">{{ $siteTitle }}</a>
  </div>
  <div class="sidebar-brand sidebar-brand-sm">
    <a href="{{ route('index.html') }}">{{ $shortTitle }}</a>
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
    <li @class(['nav-item dropdown', 'active' => request()->routeIs('modules-calendar.html', 'modules-chartjs.html', 'modules-datatables.html', 'modules-flag.html', 'modules-font-awesome.html', 'modules-ion-icons.html', 'modules-owl-carousel.html', 'modules-sparkline.html', 'modules-sweet-alert.html', 'modules-toastr.html', 'modules-vector-map.html', 'modules-weather-icon.html', 'classes.*', 'teachers.*', 'students.*', 'subjects.*', 'spp-payments.*', 'tickets.*')])>
      <a href="#" class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>Modules</span></a>
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
        @can('spp-payments.view')
          <li @class(['active' => request()->routeIs('spp-payments.*')])><a class="nav-link" href="{{ route('spp-payments.index') }}">{{ __('SPP Payments') }}</a></li>
        @endcan
        @can('tickets.view')
          <li @class(['active' => request()->routeIs('tickets.*')])><a class="nav-link" href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a></li>
        @endcan
        @can('chat.view')
          <li @class(['active' => request()->routeIs('chat.*')])><a class="nav-link" href="{{ route('chat.index') }}">{{ __('Chat Box') }}</a></li>
        @endcan
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
    @canany(['users.view', 'roles.view', 'permissions.view', 'menus.view'])
      <li class="menu-header">{{ __('User Management') }}</li>
      <li @class(['nav-item dropdown', 'active' => request()->routeIs('users.*', 'roles.*', 'permissions.*', 'menus.*')])>
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
        </ul>
      </li>
    @endcanany
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

