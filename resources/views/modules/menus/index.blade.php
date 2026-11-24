@extends('layouts.app')

@section('title', __('Modules › Menu Management'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/select2/dist/css/select2.min.css') }}">
  <style>
    .sortable-ghost > .menu-node-card {
      opacity: 0.45;
      background: #eef2ff !important;
      border: 1px dashed #6777ef !important;
    }
    .menu-drag-handle:active {
      cursor: grabbing !important;
    }
    .menu-node-card {
      transition: box-shadow 0.15s ease;
    }
    .menu-node-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var reorderUrl = @json(route('menus.reorder'));

    function setTreeStatus(text, badgeClass) {
      var $badge = $('#menu-tree-status');
      $badge.removeClass('badge-light badge-warning badge-success badge-danger').addClass(badgeClass).text(text);
    }

    function showSwalSuccess(message) {
      if (typeof window.swal === 'function') {
        window.swal({
          title: @json(__('Success')),
          text: message,
          icon: 'success',
          timer: 1800,
          buttons: false
        });
      }
    }

    function collectTreeItems() {
      var items = [];
      $('#menu-tree-root > li.menu-tree-node').each(function(rootIndex) {
        var rootId = Number($(this).data('id'));
        items.push({
          id: rootId,
          parent_id: null,
          order: rootIndex + 1
        });

        $(this).find('> .menu-children-wrapper > ul.menu-tree-children > li.menu-tree-node').each(function(childIndex) {
          items.push({
            id: Number($(this).data('id')),
            parent_id: rootId,
            order: childIndex + 1
          });
        });
      });
      return items;
    }

    function initMenuSortables() {
      @can('menus.edit')
        var containers = document.querySelectorAll('#menu-tree-root, .menu-tree-children');
        containers.forEach(function(el) {
          if (el._sortableInstance) {
            el._sortableInstance.destroy();
          }
          el._sortableInstance = new Sortable(el, {
            group: 'menus',
            handle: '.menu-drag-handle',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function() {
              var items = collectTreeItems();
              if (!items.length) {
                return;
              }
              setTreeStatus(@json(__('Saving...')), 'badge-warning');
              $.ajax({
                url: reorderUrl,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                contentType: 'application/json',
                data: JSON.stringify({ items: items }),
                success: function(res) {
                  var msg = res.message || @json(__('Saved'));
                  setTreeStatus(msg, 'badge-success');
                  showSwalSuccess(msg);
                },
                error: function(xhr) {
                  var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Failed to save order'));
                  setTreeStatus(msg, 'badge-danger');
                }
              });
            }
          });
        });
      @endcan
    }

    function initMenuSelect2() {
      $('.select2-permission').each(function() {
        var $el = $(this);
        if ($el.hasClass('select2-hidden-accessible')) {
          $el.select2('destroy');
        }
        $el.select2({
          width: '100%'
        });
      });
    }

    function reloadMenuTree() {
      $.get(window.location.href, function(html) {
        var $doc = $('<div>').html(html);
        var $newTree = $doc.find('#menu-tree-wrapper').html();
        if ($newTree) {
          $('#menu-tree-wrapper').html($newTree);
          initMenuSortables();
          initMenuSelect2();
        }
      });
    }

    $(document).ready(function() {
      initMenuSortables();
      initMenuSelect2();
    });

    $(document).on('click', '#btn-toggle-root-create', function() {
      $('#inline-root-create-card').toggleClass('d-none');
      initMenuSelect2();
    });

    $(document).on('click', '.btn-toggle-children', function() {
      var $btn = $(this);
      var $wrapper = $btn.closest('li.menu-tree-node').children('.menu-children-wrapper');
      $wrapper.toggleClass('d-none');
      $btn.find('i').toggleClass('fa-chevron-down fa-chevron-right');
    });

    $(document).on('click', '.js-open-inline-child', function() {
      var $node = $(this).closest('li.menu-tree-node');
      var $wrapper = $node.children('.menu-children-wrapper');
      $wrapper.removeClass('d-none');
      $node.find('.btn-toggle-children i').removeClass('fa-chevron-right').addClass('fa-chevron-down');
      $wrapper.children('.inline-child-create').toggleClass('d-none');
      initMenuSelect2();
    });

    $(document).on('click', '.js-close-inline-child', function() {
      $(this).closest('.inline-child-create').addClass('d-none');
    });

    $(document).on('click', '.js-toggle-inline-edit', function() {
      $(this).closest('.menu-node-card').find('.inline-edit-panel').toggleClass('d-none');
      initMenuSelect2();
    });

    $(document).on('click', '.js-cancel-inline-edit', function() {
      $(this).closest('.inline-edit-panel').addClass('d-none');
    });

    $(document).on('submit', '.js-inline-menu-form', function(e) {
      e.preventDefault();
      var $form = $(this);
      var method = $form.data('method') || 'POST';
      var formData = $form.serializeArray();
      if (method === 'PUT') {
        formData.push({ name: '_method', value: 'PUT' });
      }

      setTreeStatus(@json(__('Saving...')), 'badge-warning');

      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: $.param(formData),
        success: function(res) {
          var msg = res.message || @json(__('Saved'));
          setTreeStatus(msg, 'badge-success');
          showSwalSuccess(msg);
          if ($form.closest('#inline-root-create-card').length) {
            $form[0].reset();
            $('#inline-root-create-card').addClass('d-none');
          }
          reloadMenuTree();
        },
        error: function(xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : @json(__('Validation failed.'));
          setTreeStatus(msg, 'badge-danger');
          swal(@json(__('Error')), msg, 'error');
        }
      });
    });

    $(document).on('change', '.js-toggle-menu-active', function() {
      var $input = $(this);
      $.ajax({
        url: $input.data('url'),
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        data: { _method: 'PATCH' },
        success: function(res) {
          var msg = res.message || @json(__('Saved'));
          setTreeStatus(msg, 'badge-success');
          showSwalSuccess(msg);
        },
        error: function() {
          $input.prop('checked', !$input.prop('checked'));
          setTreeStatus(@json(__('Failed to update status')), 'badge-danger');
        }
      });
    });

    $(document).on('click', '.js-duplicate-menu', function() {
      var url = $(this).data('url');
      setTreeStatus(@json(__('Saving...')), 'badge-warning');
      $.ajax({
        url: url,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        success: function(res) {
          var msg = res.message || @json(__('Duplicated'));
          setTreeStatus(msg, 'badge-success');
          showSwalSuccess(msg);
          reloadMenuTree();
        },
        error: function() {
          setTreeStatus(@json(__('Failed to duplicate')), 'badge-danger');
        }
      });
    });

    $(document).on('click', '.js-delete-menu', function() {
      var url = $(this).data('url');
      var name = $(this).data('name');
      swal({
        title: @json(__('Delete Menu?')),
        text: @json(__('Delete :name and its child menus?', ['name' => '__NAME__'])).replace('__NAME__', name),
        icon: 'warning',
        buttons: [@json(__('Cancel')), @json(__('Delete'))],
        dangerMode: true
      }).then(function(willDelete) {
        if (!willDelete) {
          return;
        }
        setTreeStatus(@json(__('Deleting...')), 'badge-warning');
        $.ajax({
          url: url,
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          data: { _method: 'DELETE' },
          success: function(res) {
            var msg = res.message || @json(__('Deleted'));
            setTreeStatus(msg, 'badge-success');
            showSwalSuccess(msg);
            reloadMenuTree();
          },
          error: function() {
            setTreeStatus(@json(__('Failed to delete')), 'badge-danger');
          }
        });
      });
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Menu Management') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('menus.index') }}">{{ __('Access Control') }}</a></div>
      <div class="breadcrumb-item">{{ __('Menu Management') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Menu Work Tree') }}</h2>
    <p class="section-lead">
      {{ __('Drag and drop to reorder menus, move items into dropdowns, and add or edit menus inline.') }}
    </p>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="d-flex align-items-center" style="gap: 10px;">
              <h4 class="mb-0">{{ __('Menu Tree') }}</h4>
              <span id="menu-tree-status" class="badge badge-light">{{ __('Ready') }}</span>
            </div>
            <div class="card-header-action d-flex align-items-center flex-wrap" style="gap: 6px;">
              <form method="GET" action="{{ route('menus.index') }}" class="form-inline">
                <select name="app" class="form-control form-control-sm" onchange="this.form.submit()">
                  <option value="">{{ __('Semua App') }}</option>
                  @foreach ($apps as $appOption)
                    <option value="{{ $appOption }}" @selected($selectedApp === $appOption)>{{ $appOption }}</option>
                  @endforeach
                </select>
              </form>
              @can('menus.view')
                <a href="{{ route('menus.exportPdf') }}" class="btn btn-outline-danger btn-sm">
                  <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                </a>
              @endcan
              @can('menus.create')
                <button type="button" class="btn btn-primary btn-sm" id="btn-toggle-root-create">
                  <i class="fas fa-plus mr-1"></i> {{ __('Add Inline Menu') }}
                </button>
              @endcan
            </div>
          </div>

          <div class="card-body">
            {{-- Inline Root Create Card --}}
            @can('menus.create')
              <div id="inline-root-create-card" class="card border-primary mb-4 d-none">
                <div class="card-body bg-light">
                  <h6 class="font-weight-bold text-primary mb-3">
                    <i class="fas fa-plus-circle mr-1"></i> {{ __('Add New Root Menu') }}
                  </h6>
                  <form class="js-inline-menu-form" action="{{ route('menus.store') }}" method="POST" data-method="POST">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    <div class="row">
                      <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('Menu Name') }} *</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="{{ __('Menu Name') }}" required>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('Type') }} *</label>
                        <select name="type" class="form-control form-control-sm">
                          <option value="item">{{ __('Item') }}</option>
                          <option value="dropdown">{{ __('Dropdown') }}</option>
                          <option value="header">{{ __('Header') }}</option>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('Icon') }}</label>
                        <input type="text" name="icon" class="form-control form-control-sm" placeholder="fas fa-circle">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('Route') }}</label>
                        <select name="route" class="form-control form-control-sm">
                          <option value="">-- {{ __('None') }} --</option>
                          @foreach ($availableRoutes as $routeName)
                            <option value="{{ $routeName }}">{{ $routeName }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('URL') }}</label>
                        <input type="text" name="url" class="form-control form-control-sm" placeholder="/custom-url">
                      </div>
                      <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('Permission') }}</label>
                        <select name="permission" class="form-control form-control-sm select2-permission">
                          <option value="">{{ __('Public (No Permission)') }}</option>
                          @foreach ($permissions as $perm)
                            <option value="{{ $perm }}">{{ $perm }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold mb-1">{{ __('App') }} *</label>
                        <div class="d-flex flex-wrap" style="gap: 8px;">
                          @foreach ($apps as $appOpt)
                            <label class="mb-0 small d-flex align-items-center" style="gap: 4px;">
                              <input type="checkbox" name="app[]" value="{{ $appOpt }}" @checked($selectedApp !== '' ? $selectedApp === $appOpt : $appOpt === config('stisla.menu_app', 'online-shop'))>
                              <span>{{ $appOpt }}</span>
                            </label>
                          @endforeach
                        </div>
                      </div>
                      <div class="col-md-3 mb-2 d-flex align-items-end justify-content-end" style="gap: 6px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="$('#inline-root-create-card').addClass('d-none')">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save mr-1"></i>{{ __('Add Menu') }}</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            @endcan

            <div id="menu-tree-wrapper">
              @if ($menus->isEmpty())
                <div class="text-center text-muted py-5">
                  {{ __('No menus found.') }}
                </div>
              @else
                <ul class="list-unstyled menu-tree-list mb-0" id="menu-tree-root">
                  @foreach ($menus as $menu)
                    @include('modules.menus._tree_node', ['menu' => $menu])
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
