@extends('layouts.app')

@section('title', __('Shop › Our Team'))

@push('css')
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endpush

@push('js')
  <script src="{{ asset('node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    $("#table-teams").dataTable({
      "pageLength": 10,
      "columnDefs": [
        { "sortable": false, "targets": [1, 4, 6] }
      ]
    });

    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      var form = $(this).closest('form');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This team member will be deleted!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Our Team') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('our-teams.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item">{{ __('Our Team') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Team Management') }}</h2>
    <p class="section-lead">
      {{ __('Manage team members displayed on the About Us page.') }}
    </p>

    <!-- Stats -->
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Members') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['total'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Active Members') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['active'] }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-secondary">
            <i class="fas fa-eye-slash"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Inactive') }}</h4>
            </div>
            <div class="card-body">
              {{ $stats['inactive'] }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('All Team Members') }}</h4>
            <div class="card-header-action">
              @can('our-teams.create')
                <a href="{{ route('our-teams.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> {{ __('Add Member') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover" id="table-teams">
                <thead>
                  <tr>
                    <th style="width: 60px;">#</th>
                    <th style="width: 80px;">{{ __('Photo') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Position') }}</th>
                    <th>{{ __('Social Links') }}</th>
                    <th style="width: 100px;">{{ __('Status') }}</th>
                    <th style="width: 160px;">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($teams as $index => $team)
                    <tr>
                      <td>
                        <span class="badge badge-light">#{{ $team->order }}</span>
                      </td>
                      <td>
                        <img src="{{ $team->image_url }}" alt="{{ $team->name }}" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                      </td>
                      <td>
                        <a href="{{ route('our-teams.show', $team) }}" class="font-weight-bold">
                          {{ $team->name }}
                        </a>
                      </td>
                      <td>
                        <span class="badge badge-info">{{ $team->position }}</span>
                      </td>
                      <td>
                        @if ($team->twitter_url)
                          <a href="{{ $team->twitter_url }}" target="_blank" class="badge badge-light mr-1" title="Twitter"><i class="fab fa-twitter text-info"></i></a>
                        @endif
                        @if ($team->facebook_url)
                          <a href="{{ $team->facebook_url }}" target="_blank" class="badge badge-light mr-1" title="Facebook"><i class="fab fa-facebook text-primary"></i></a>
                        @endif
                        @if ($team->linkedin_url)
                          <a href="{{ $team->linkedin_url }}" target="_blank" class="badge badge-light mr-1" title="LinkedIn"><i class="fab fa-linkedin text-primary"></i></a>
                        @endif
                        @if (!$team->twitter_url && !$team->facebook_url && !$team->linkedin_url)
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                      <td>
                        @if ($team->is_active)
                          <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Active') }}</span>
                        @else
                          <span class="badge badge-danger"><i class="fas fa-times"></i> {{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('our-teams.show', $team) }}" class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                          @can('our-teams.create')
                            <form action="{{ route('our-teams.duplicate', $team) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('Duplicate') }}">
                                <i class="fas fa-copy"></i>
                              </button>
                            </form>
                          @endcan
                          @can('our-teams.edit')
                            <a href="{{ route('our-teams.edit', $team) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                          @endcan
                          @can('our-teams.delete')
                            <form action="{{ route('our-teams.destroy', $team) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-danger btn-delete" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          @endcan
                        </div>
                      </td>
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
@endsection

