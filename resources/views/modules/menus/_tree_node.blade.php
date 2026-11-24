@php
  $isDropdown = $menu->type === 'dropdown';
  $isHeader = $menu->type === 'header';
  $nodeApps = (array) ($menu->app ?? ['online-shop']);
  $typeColor = match ($menu->type) {
      'header' => 'border-left: 4px solid #6777ef; background: #f8f9fa;',
      'dropdown' => 'border-left: 4px solid #3abaf4; background: #ffffff;',
      default => 'border-left: 4px solid #47c363; background: #ffffff;',
  };
@endphp

<li class="menu-tree-node mb-2" data-id="{{ $menu->id }}" data-type="{{ $menu->type }}">
  <div class="card mb-0 shadow-sm menu-node-card" style="{{ $typeColor }}">
    <div class="card-body py-2 px-3">
      <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          @can('menus.edit')
            <span class="menu-drag-handle text-muted px-1" style="cursor: grab;" title="{{ __('Drag to reorder') }}">
              <i class="fas fa-grip-vertical"></i>
            </span>
          @endcan

          @if ($isDropdown)
            <button type="button" class="btn btn-xs btn-light btn-toggle-children px-2 py-1" title="{{ __('Toggle children') }}">
              <i class="fas fa-chevron-down"></i>
            </button>
          @endif

          @if ($menu->icon)
            <span class="menu-node-icon text-primary" style="width: 20px; text-align: center;">
              <i class="{{ $menu->icon }}"></i>
            </span>
          @endif

          <span class="font-weight-bold {{ $isHeader ? 'text-uppercase text-primary small' : 'text-dark' }}">
            {{ __($menu->name) }}
          </span>

          <span class="badge {{ $menu->typeBadgeClass() }}">{{ __(ucfirst($menu->type)) }}</span>

          @if ($menu->route)
            <code class="small">{{ $menu->route }}</code>
          @elseif ($menu->url)
            <span class="text-muted small">{{ $menu->url }}</span>
          @endif

          @if ($menu->permission)
            <span class="badge badge-warning text-dark">{{ $menu->permission }}</span>
          @endif

          @foreach ($nodeApps as $appBadge)
            <span class="badge badge-light border text-muted">{{ $appBadge }}</span>
          @endforeach
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
          @can('menus.edit')
            <label class="custom-switch mb-0 pl-0 mr-1" title="{{ __('Toggle Active') }}">
              <input type="checkbox"
                     class="custom-switch-input js-toggle-menu-active"
                     data-url="{{ route('menus.toggleActive', $menu) }}"
                     @checked($menu->is_active)>
              <span class="custom-switch-indicator"></span>
            </label>
          @endcan

          @if ($isDropdown)
            @can('menus.create')
              <button type="button"
                      class="btn btn-sm btn-outline-success js-open-inline-child"
                      data-parent-id="{{ $menu->id }}"
                      title="{{ __('Add Child') }}">
                <i class="fas fa-plus mr-1"></i>{{ __('Child') }}
              </button>
            @endcan
          @endif

          @can('menus.edit')
            <button type="button"
                    class="btn btn-sm btn-outline-primary js-toggle-inline-edit"
                    title="{{ __('Inline Edit') }}">
              <i class="fas fa-pencil-alt"></i>
            </button>
          @endcan

          @can('menus.create')
            <button type="button"
                    class="btn btn-sm btn-outline-warning js-duplicate-menu"
                    data-url="{{ route('menus.duplicate', $menu) }}"
                    title="{{ __('Duplicate') }}">
              <i class="fas fa-copy"></i>
            </button>
          @endcan

          @can('menus.delete')
            <button type="button"
                    class="btn btn-sm btn-outline-danger js-delete-menu"
                    data-url="{{ route('menus.destroy', $menu) }}"
                    data-name="{{ __($menu->name) }}"
                    title="{{ __('Delete') }}">
              <i class="fas fa-trash"></i>
            </button>
          @endcan
        </div>
      </div>

      {{-- Inline Edit Form --}}
      @can('menus.edit')
        <div class="inline-edit-panel mt-3 pt-3 border-top d-none">
          <form class="js-inline-menu-form" action="{{ route('menus.update', $menu) }}" method="POST" data-method="PUT">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $menu->parent_id }}">
            <input type="hidden" name="order" value="{{ $menu->order }}">
            <div class="row">
              <div class="col-md-3 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('Menu Name') }} *</label>
                <input type="text" name="name" class="form-control form-control-sm" value="{{ $menu->name }}" required>
              </div>
              <div class="col-md-2 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('Type') }} *</label>
                <select name="type" class="form-control form-control-sm">
                  <option value="item" @selected($menu->type === 'item')>{{ __('Item') }}</option>
                  <option value="dropdown" @selected($menu->type === 'dropdown')>{{ __('Dropdown') }}</option>
                  <option value="header" @selected($menu->type === 'header')>{{ __('Header') }}</option>
                </select>
              </div>
              <div class="col-md-2 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('Icon') }}</label>
                <input type="text" name="icon" class="form-control form-control-sm" value="{{ $menu->icon }}" placeholder="fas fa-circle">
              </div>
              <div class="col-md-2 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('Route') }}</label>
                <select name="route" class="form-control form-control-sm">
                  <option value="">-- {{ __('None') }} --</option>
                  @foreach ($availableRoutes as $routeName)
                    <option value="{{ $routeName }}" @selected($menu->route === $routeName)>{{ $routeName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('URL') }}</label>
                <input type="text" name="url" class="form-control form-control-sm" value="{{ $menu->url }}" placeholder="/custom-url">
              </div>
              <div class="col-md-4 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('Permission') }}</label>
                <select name="permission" class="form-control form-control-sm select2-permission">
                  <option value="">{{ __('Public (No Permission)') }}</option>
                  @foreach ($permissions as $perm)
                    <option value="{{ $perm }}" @selected($menu->permission === $perm)>{{ $perm }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-5 mb-2">
                <label class="small font-weight-bold mb-1">{{ __('App') }} *</label>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                  @foreach ($apps as $appOpt)
                    <label class="mb-0 small d-flex align-items-center" style="gap: 4px;">
                      <input type="checkbox" name="app[]" value="{{ $appOpt }}" @checked(in_array($appOpt, $nodeApps, true))>
                      <span>{{ $appOpt }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
              <div class="col-md-3 mb-2 d-flex align-items-end justify-content-end" style="gap: 6px;">
                <input type="hidden" name="is_active" value="{{ $menu->is_active ? '1' : '0' }}">
                <button type="button" class="btn btn-sm btn-secondary js-cancel-inline-edit">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save mr-1"></i>{{ __('Save') }}</button>
              </div>
            </div>
          </form>
        </div>
      @endcan
    </div>
  </div>

  @if ($isDropdown)
    <div class="menu-children-wrapper pl-4 mt-2" style="border-left: 2px dashed #cbd5e1; margin-left: 14px;">
      {{-- Inline Add Child Form --}}
      @can('menus.create')
        <div class="inline-child-create card mb-2 border-success d-none" data-parent-id="{{ $menu->id }}">
          <div class="card-body py-2 px-3 bg-light">
            <form class="js-inline-menu-form" action="{{ route('menus.store') }}" method="POST" data-method="POST">
              @csrf
              <input type="hidden" name="parent_id" value="{{ $menu->id }}">
              <input type="hidden" name="type" value="item">
              <input type="hidden" name="is_active" value="1">
              <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                  <label class="small font-weight-bold mb-1">{{ __('Child Name') }} *</label>
                  <input type="text" name="name" class="form-control form-control-sm" placeholder="{{ __('Menu Name') }}" required>
                </div>
                <div class="col-md-2 mb-2">
                  <label class="small font-weight-bold mb-1">{{ __('Icon') }}</label>
                  <input type="text" name="icon" class="form-control form-control-sm" placeholder="fas fa-circle">
                </div>
                <div class="col-md-3 mb-2">
                  <label class="small font-weight-bold mb-1">{{ __('Route') }}</label>
                  <select name="route" class="form-control form-control-sm">
                    <option value="">-- {{ __('None') }} --</option>
                    @foreach ($availableRoutes as $routeName)
                      <option value="{{ $routeName }}">{{ $routeName }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 mb-2">
                  <label class="small font-weight-bold mb-1">{{ __('Permission') }}</label>
                  <select name="permission" class="form-control form-control-sm select2-permission">
                    <option value="">{{ __('Public') }}</option>
                    @foreach ($permissions as $perm)
                      <option value="{{ $perm }}">{{ $perm }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 mb-2 d-flex justify-content-end" style="gap: 4px;">
                  @foreach ($nodeApps as $appOpt)
                    <input type="hidden" name="app[]" value="{{ $appOpt }}">
                  @endforeach
                  <button type="button" class="btn btn-sm btn-secondary js-close-inline-child">{{ __('Cancel') }}</button>
                  <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-plus mr-1"></i>{{ __('Add') }}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      @endcan

      <ul class="list-unstyled menu-tree-list menu-tree-children mb-0" data-parent-id="{{ $menu->id }}" style="min-height: 24px;">
        @foreach ($menu->children as $child)
          @include('modules.menus._tree_node', ['menu' => $child])
        @endforeach
      </ul>
    </div>
  @endif
</li>
