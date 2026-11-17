@extends('layouts.app')

@section('title', __('Detail Log Request #') . $logRequest->id)

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
        title: @json(__('Apakah Anda yakin?')),
        text: @json(__('Log request ini akan dihapus permanen!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          form.submit();
        }
      });
    });

    $(document).on('click', '#btnCopyJson', function() {
      var jsonText = $('#codePayload').text();
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(jsonText);
      } else {
        var temp = $('<textarea>');
        $('body').append(temp);
        temp.val(jsonText).select();
        document.execCommand('copy');
        temp.remove();
      }

      var btn = $(this);
      var originalHtml = btn.html();
      btn.html('<i class="fas fa-check mr-1 text-success"></i> {{ __("Tersalin!") }}').addClass('btn-success text-white').removeClass('btn-outline-primary');
      setTimeout(function() {
        btn.html(originalHtml).removeClass('btn-success text-white').addClass('btn-outline-primary');
      }, 1800);
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('log-requests.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Detail Log Request #') }}{{ $logRequest->id }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('log-requests.index') }}">{{ __('Log Requests') }}</a></div>
      <div class="breadcrumb-item">#{{ $logRequest->id }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <!-- Left Column: Request Details & Payload -->
      <div class="col-12 col-lg-7">
        <!-- Request Info Card -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-network-wired mr-2 text-primary"></i> {{ __('Informasi Request') }}</h4>
            <div class="card-header-action">
              @php
                $methodBadges = [
                  'GET' => 'badge-success',
                  'POST' => 'badge-primary',
                  'PUT' => 'badge-warning',
                  'PATCH' => 'badge-warning',
                  'DELETE' => 'badge-danger',
                ];
                $badgeClass = $methodBadges[$logRequest->method] ?? 'badge-secondary';
              @endphp
              <span class="badge {{ $badgeClass }} px-3 py-1 font-weight-bold" style="font-size: 13px;">{{ $logRequest->method }}</span>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <td class="font-weight-bold" style="width: 170px;">{{ __('URI') }}</td>
                    <td><code class="font-weight-bold" style="font-size: 14px;">{{ $logRequest->uri }}</code></td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Query String') }}</td>
                    <td>
                      @if ($logRequest->query_string)
                        <code>?{{ $logRequest->query_string }}</code>
                      @else
                        <span class="text-muted"><em>Tidak ada query string</em></span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Waktu Request') }}</td>
                    <td>
                      <strong>{{ $logRequest->created_at->format('d F Y, H:i:s') }} WIB</strong>
                      <span class="text-muted ml-2">({{ $logRequest->created_at->diffForHumans() }})</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Tipe Request') }}</td>
                    <td>
                      @if ($logRequest->is_ajax)
                        <span class="badge badge-info"><i class="fas fa-bolt mr-1"></i> AJAX / XMLHttpRequest</span>
                      @else
                        <span class="badge badge-light border text-dark">Standard HTTP Request</span>
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Payload Card -->
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-code mr-2 text-primary"></i> {{ __('Request Data (Payload)') }}</h4>
            @if (!empty($logRequest->request_data))
              <div class="card-header-action">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnCopyJson">
                  <i class="fas fa-copy mr-1"></i> {{ __('Salin JSON') }}
                </button>
              </div>
            @endif
          </div>
          <div class="card-body">
            @if (!empty($logRequest->request_data))
              <pre class="bg-dark text-white p-3 rounded" style="max-height: 420px; overflow-y: auto; font-size: 13px;"><code id="codePayload">{{ json_encode($logRequest->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
            @else
              <div class="alert alert-light border text-muted mb-0">
                <i class="fas fa-info-circle mr-1"></i> {{ __('Tidak ada data payload yang dikirim pada request ini.') }}
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Right Column: User & Client Environment -->
      <div class="col-12 col-lg-5">
        <!-- User Info Card -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-user mr-2 text-primary"></i> {{ __('Informasi Pengguna') }}</h4>
          </div>
          <div class="card-body">
            @if ($logRequest->user)
              <div class="d-flex align-items-center mb-3">
                @if ($logRequest->user->avatar_url)
                  <img src="{{ $logRequest->user->avatar_url }}" alt="{{ $logRequest->user->name }}" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                @else
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-weight: bold; font-size: 18px;">
                    {{ strtoupper(substr($logRequest->user->name, 0, 1)) }}
                  </div>
                @endif
                <div>
                  <h6 class="font-weight-bold mb-0 text-dark">{{ $logRequest->user->name }}</h6>
                  <small class="text-muted">{{ $logRequest->user->email }}</small>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                  <tr>
                    <td class="font-weight-bold text-muted" style="width: 100px;">User ID</td>
                    <td>#{{ $logRequest->user_id }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold text-muted">Role(s)</td>
                    <td>
                      @if (!empty($logRequest->roles))
                        @foreach ($logRequest->roles as $role)
                          <span class="badge badge-primary px-2 py-1 small">{{ $role }}</span>
                        @endforeach
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                  </tr>
                </table>
              </div>
            @else
              <div class="text-center py-3">
                <i class="fas fa-user-secret fa-3x text-secondary mb-2"></i>
                <h6 class="font-weight-bold mb-1">{{ __('Tamu / Unauthenticated') }}</h6>
                <p class="text-muted small mb-0">{{ __('Request dilakukan oleh pengguna yang belum login (Guest).') }}</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Client & Device Info Card -->
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-laptop mr-2 text-primary"></i> {{ __('Klien & Perangkat') }}</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <tbody>
                  <tr>
                    <td class="font-weight-bold" style="width: 130px;">{{ __('IP Address') }}</td>
                    <td><span class="font-weight-bold">{{ $logRequest->ip ?? '-' }}</span></td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Device') }}</td>
                    <td>
                      @if ($logRequest->device === 'Mobile')
                        <span class="badge badge-success"><i class="fas fa-mobile-alt mr-1"></i> Mobile</span>
                      @elseif ($logRequest->device === 'Tablet')
                        <span class="badge badge-warning"><i class="fas fa-tablet-alt mr-1"></i> Tablet</span>
                      @elseif ($logRequest->device === 'Bot / Crawler')
                        <span class="badge badge-dark"><i class="fas fa-robot mr-1"></i> Bot / Crawler</span>
                      @else
                        <span class="badge badge-info"><i class="fas fa-desktop mr-1"></i> Desktop</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Browser') }}</td>
                    <td>{{ $logRequest->browser ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">{{ __('Platform (OS)') }}</td>
                    <td>{{ $logRequest->platform ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold align-top">{{ __('User Agent') }}</td>
                    <td>
                      <small class="text-muted font-monospace d-block" style="word-break: break-all; line-height: 1.4;">
                        {{ $logRequest->user_agent ?? '-' }}
                      </small>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Action Card -->
        @can('log-requests.delete')
          <div class="card">
            <div class="card-body">
              <form action="{{ route('log-requests.destroy', $logRequest->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-block btn-delete">
                  <i class="fas fa-trash-alt mr-1"></i> {{ __('Hapus Log Request Ini') }}
                </button>
              </form>
            </div>
          </div>
        @endcan
      </div>
    </div>
  </div>
@endsection
