@extends('layouts.app')

@section('title', __('Shop › Member Details'))

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";
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
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('our-teams.index') }}" class="btn btn-icon btn-sm" data-toggle="tooltip" title="{{ __('Back') }}"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Member Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('our-teams.index') }}">{{ __('Shop') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('our-teams.index') }}">{{ __('Our Team') }}</a></div>
      <div class="breadcrumb-item">{{ $team->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ $team->name }}</h2>
    <p class="section-lead">
      {{ __('Details and social links for this team member.') }}
    </p>

    <div class="row">
      <div class="col-12 col-md-5 col-lg-4">
        <div class="card profile-widget">
          <div class="profile-widget-header text-center pt-4">
            <img alt="{{ $team->name }}" src="{{ $team->image_url }}" class="rounded-circle profile-widget-picture" style="width: 140px; height: 140px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,.1);">
          </div>
          <div class="profile-widget-description text-center">
            <div class="profile-widget-name font-weight-bold h5 mb-1">{{ $team->name }}</div>
            <div class="text-muted mb-3"><span class="badge badge-info">{{ $team->position }}</span></div>

            <div class="mt-3">
              @if ($team->twitter_url)
                <a href="{{ $team->twitter_url }}" target="_blank" class="btn btn-social-icon btn-twitter mr-1" title="Twitter"><i class="fab fa-twitter"></i></a>
              @endif
              @if ($team->facebook_url)
                <a href="{{ $team->facebook_url }}" target="_blank" class="btn btn-social-icon btn-facebook mr-1" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              @endif
              @if ($team->linkedin_url)
                <a href="{{ $team->linkedin_url }}" target="_blank" class="btn btn-social-icon btn-linkedin mr-1" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
              @endif
            </div>
          </div>
          <div class="card-footer text-center">
            @if ($team->is_active)
              <span class="badge badge-success px-3 py-2"><i class="fas fa-check"></i> {{ __('Active on Website') }}</span>
            @else
              <span class="badge badge-danger px-3 py-2"><i class="fas fa-times"></i> {{ __('Inactive') }}</span>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12 col-md-7 col-lg-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Member Information') }}</h4>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 30%;">{{ __('Full Name') }}</th>
                  <td><strong>{{ $team->name }}</strong></td>
                </tr>
                <tr>
                  <th>{{ __('Position / Role') }}</th>
                  <td>{{ $team->position }}</td>
                </tr>
                <tr>
                  <th>{{ __('Display Order') }}</th>
                  <td><span class="badge badge-light">#{{ $team->order }}</span></td>
                </tr>
                <tr>
                  <th>{{ __('Status') }}</th>
                  <td>
                    @if ($team->is_active)
                      <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                      <span class="badge badge-danger">{{ __('Inactive') }}</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Twitter URL') }}</th>
                  <td>
                    @if ($team->twitter_url)
                      <a href="{{ $team->twitter_url }}" target="_blank">{{ $team->twitter_url }} <i class="fas fa-external-link-alt small ml-1"></i></a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Facebook URL') }}</th>
                  <td>
                    @if ($team->facebook_url)
                      <a href="{{ $team->facebook_url }}" target="_blank">{{ $team->facebook_url }} <i class="fas fa-external-link-alt small ml-1"></i></a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('LinkedIn URL') }}</th>
                  <td>
                    @if ($team->linkedin_url)
                      <a href="{{ $team->linkedin_url }}" target="_blank">{{ $team->linkedin_url }} <i class="fas fa-external-link-alt small ml-1"></i></a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ __('Created At') }}</th>
                  <td>{{ $team->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                </tr>
                <tr>
                  <th>{{ __('Last Updated') }}</th>
                  <td>{{ $team->updated_at?->format('d M Y, H:i') ?? '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('our-teams.index') }}" class="btn btn-secondary mr-2">{{ __('Back') }}</a>
            @can('our-teams.create')
              <form action="{{ route('our-teams.duplicate', $team) }}" method="POST" class="d-inline mr-2">
                @csrf
                <button type="submit" class="btn btn-secondary"><i class="fas fa-copy mr-1"></i> {{ __('Duplicate') }}</button>
              </form>
            @endcan
            @can('our-teams.edit')
              <a href="{{ route('our-teams.edit', $team) }}" class="btn btn-warning mr-2"><i class="fas fa-pencil-alt mr-1"></i> {{ __('Edit') }}</a>
            @endcan
            @can('our-teams.delete')
              <form action="{{ route('our-teams.destroy', $team) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete"><i class="fas fa-trash mr-1"></i> {{ __('Delete') }}</button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

