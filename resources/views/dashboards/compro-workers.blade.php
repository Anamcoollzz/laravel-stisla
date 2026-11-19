@extends('layouts.app')

@section('title', __('Dashboard Company Profile'))

@push('js')
  <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    $(document).ready(function() {
      var catCanvas = document.getElementById("workersCategoryChart");
      if (catCanvas) {
        new Chart(catCanvas.getContext('2d'), {
          type: 'bar',
          data: {
            labels: @json($categoryLabels),
            datasets: [{
              label: '{{ __("Jumlah Pekerja") }}',
              data: @json($categoryTotals),
              backgroundColor: [
                'rgba(103, 119, 239, 0.85)',
                'rgba(71, 195, 99, 0.85)',
                'rgba(58, 186, 244, 0.85)',
                'rgba(255, 164, 38, 0.85)',
                'rgba(252, 84, 75, 0.85)',
                'rgba(134, 142, 150, 0.85)'
              ],
              borderRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
              yAxes: [{
                ticks: { beginAtZero: true, stepSize: 1 },
                gridLines: { color: '#f2f2f2', drawBorder: false }
              }],
              xAxes: [{
                gridLines: { display: false }
              }]
            }
          }
        });
      }

      var statusCanvas = document.getElementById("workersStatusChart");
      if (statusCanvas) {
        new Chart(statusCanvas.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: ['Available', 'In Training', 'Placed'],
            datasets: [{
              data: [
                {{ $workersByStatus['Available'] }},
                {{ $workersByStatus['In Training'] }},
                {{ $workersByStatus['Placed'] }}
              ],
              backgroundColor: ['#47c363', '#ffa426', '#6777ef'],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom' },
            cutoutPercentage: 65
          }
        });
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Dashboard Company Profile') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active">{{ __('Dashboard') }}</div>
      <div class="breadcrumb-item">lp-compro-workers</div>
    </div>
  </div>

  <div class="section-body">
    @include('partials.dashboard-announcements')

    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Total Pekerja') }}</h4></div>
            <div class="card-body">
              {{ $counts['workers'] }}
              <small class="text-success font-weight-normal d-block" style="font-size: 12px;">{{ $counts['workers_available'] }} Available</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"><i class="fas fa-briefcase"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Layanan Aktif') }}</h4></div>
            <div class="card-body">
              {{ $counts['services'] }}
              <small class="text-muted font-weight-normal d-block" style="font-size: 12px;">{{ $counts['steps'] }} {{ __('Tahapan') }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i class="fas fa-envelope-open-text"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Pesan Kontak') }}</h4></div>
            <div class="card-body">
              {{ $counts['inquiries'] }}
              <small class="text-warning font-weight-normal d-block" style="font-size: 12px;">{{ $counts['inquiries_unread'] }} {{ __('Belum Dibaca') }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i class="fas fa-images"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>{{ __('Galeri & Testimoni') }}</h4></div>
            <div class="card-body">
              {{ $counts['galleries'] }} / {{ $counts['testimonials'] }}
              <small class="text-muted font-weight-normal d-block" style="font-size: 12px;">{{ $counts['faqs'] }} FAQ</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 col-md-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-chart-bar mr-2 text-primary"></i> {{ __('Distribusi Pekerja per Kategori') }}</h4>
          </div>
          <div class="card-body">
            <div style="height: 280px;">
              <canvas id="workersCategoryChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-chart-pie mr-2 text-success"></i> {{ __('Status Pekerja') }}</h4>
          </div>
          <div class="card-body">
            <div style="height: 280px;">
              <canvas id="workersStatusChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-bolt mr-2 text-warning"></i> {{ __('Akses Cepat Konten Company Profile') }}</h4>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap" style="gap: 8px;">
              @can('lp-workers.view')
                <a href="{{ route('lp-workers.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-users mr-1"></i> {{ __('Workers') }} ({{ $counts['workers'] }})</a>
              @endcan
              @can('lp-services.view')
                <a href="{{ route('lp-services.index') }}" class="btn btn-info btn-sm"><i class="fas fa-briefcase mr-1"></i> {{ __('Services') }} ({{ $counts['services'] }})</a>
              @endcan
              @can('lp-contact-inquiries.view')
                <a href="{{ route('lp-contact-inquiries.index') }}" class="btn btn-warning btn-sm"><i class="fas fa-inbox mr-1"></i> {{ __('Contact Inquiries') }} ({{ $counts['inquiries_unread'] }})</a>
              @endcan
              @can('lp-company-profile.edit')
                <a href="{{ route('lp-company-profile.edit') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-address-card mr-1"></i> {{ __('Contact & Company Info') }}</a>
              @endcan
              @can('lp-company-introduction.edit')
                <a href="{{ route('lp-company-introduction.edit') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-home mr-1"></i> {{ __('Company Introduction') }}</a>
              @endcan
              @can('lp-vision-mission.edit')
                <a href="{{ route('lp-vision-mission.edit') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-bullseye mr-1"></i> {{ __('Vision & Mission') }}</a>
              @endcan
              @can('lp-company-stats.view')
                <a href="{{ route('lp-company-stats.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Company Stats') }} ({{ $counts['stats'] }})</a>
              @endcan
              @can('lp-why-choose-us.view')
                <a href="{{ route('lp-why-choose-us.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Why Choose Us') }} ({{ $counts['why_choose_us'] }})</a>
              @endcan
              @can('lp-milestones.view')
                <a href="{{ route('lp-milestones.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Milestones') }} ({{ $counts['milestones'] }})</a>
              @endcan
              @can('lp-company-values.view')
                <a href="{{ route('lp-company-values.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Company Values') }} ({{ $counts['values'] }})</a>
              @endcan
              @can('lp-galleries.view')
                <a href="{{ route('lp-galleries.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Galleries') }} ({{ $counts['galleries'] }})</a>
              @endcan
              @can('lp-testimonials.view')
                <a href="{{ route('lp-testimonials.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Testimonials') }} ({{ $counts['testimonials'] }})</a>
              @endcan
              @can('lp-faqs.view')
                <a href="{{ route('lp-faqs.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('FAQs') }} ({{ $counts['faqs'] }})</a>
              @endcan
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 col-md-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-inbox mr-2 text-warning"></i> {{ __('Pesan Kontak Terbaru') }}</h4>
            <div class="card-header-action">
              @can('lp-contact-inquiries.view')
                <a href="{{ route('lp-contact-inquiries.index') }}" class="btn btn-primary btn-sm">{{ __('Lihat Semua') }}</a>
              @endcan
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Pengirim') }}</th>
                    <th>{{ __('Subjek') }}</th>
                    <th>{{ __('Status') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($recentInquiries as $inquiry)
                    <tr>
                      <td>
                        <div class="font-weight-bold">{{ $inquiry->full_name }}</div>
                        <small class="text-muted">{{ $inquiry->created_at?->diffForHumans() }}</small>
                      </td>
                      <td>
                        <a href="{{ route('lp-contact-inquiries.show', $inquiry) }}">{{ \Illuminate\Support\Str::limit($inquiry->subject, 35) }}</a>
                      </td>
                      <td>
                        @if ($inquiry->is_read)
                          <span class="badge badge-success">{{ __('Sudah Dibaca') }}</span>
                        @else
                          <span class="badge badge-warning">{{ __('Belum Dibaca') }}</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-4">{{ __('Belum ada pesan kontak.') }}</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-md-12">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-users mr-2 text-primary"></i> {{ __('Kandidat Pekerja Terbaru') }}</h4>
            <div class="card-header-action">
              @can('lp-workers.view')
                <a href="{{ route('lp-workers.index') }}" class="btn btn-primary btn-sm">{{ __('Lihat Semua') }}</a>
              @endcan
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Kandidat') }}</th>
                    <th>{{ __('Kategori') }}</th>
                    <th>{{ __('Status') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($recentWorkers as $worker)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="{{ $worker->photo_url }}" alt="{{ $worker->name }}" class="rounded mr-2" style="width: 34px; height: 34px; object-fit: cover;">
                          <div>
                            <a href="{{ route('lp-workers.show', $worker) }}" class="font-weight-bold">{{ $worker->name }}</a>
                            <small class="d-block text-muted">{{ $worker->code }}</small>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge badge-info">{{ $worker->category }}</span></td>
                      <td>
                        <span class="badge {{ $worker->status === 'Available' ? 'badge-success' : ($worker->status === 'In Training' ? 'badge-warning' : 'badge-secondary') }}">
                          {{ $worker->status }}
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-4">{{ __('Belum ada data pekerja.') }}</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
