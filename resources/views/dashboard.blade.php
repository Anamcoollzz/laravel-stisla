@extends('layouts.app')

@section('title', __('Dashboard'))

@push('js')
  <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    $(document).ready(function() {
      // 1. Line Chart: Tren Penerimaan SPP Bulanan
      var sppTrendCanvas = document.getElementById("sppTrendChart");
      if (sppTrendCanvas) {
        var ctxTrend = sppTrendCanvas.getContext('2d');
        var gradientTrend = ctxTrend.createLinearGradient(0, 0, 0, 300);
        gradientTrend.addColorStop(0, 'rgba(103, 119, 239, 0.45)');
        gradientTrend.addColorStop(1, 'rgba(103, 119, 239, 0.02)');

        new Chart(ctxTrend, {
          type: 'line',
          data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
              label: '{{ __("Amount") }}',
              data: @json($monthlyChartData),
              borderWidth: 3,
              borderColor: '#6777ef',
              backgroundColor: gradientTrend,
              pointBackgroundColor: '#ffffff',
              pointBorderColor: '#6777ef',
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBorderWidth: 2,
              fill: true,
              tension: 0.35
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              display: false
            },
            tooltips: {
              backgroundColor: 'rgba(32, 34, 42, 0.9)',
              titleFontSize: 13,
              bodyFontSize: 13,
              xPadding: 12,
              yPadding: 10,
              displayColors: false,
              callbacks: {
                label: function(tooltipItem) {
                  var val = Number(tooltipItem.yLabel);
                  return 'Rp ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
              }
            },
            scales: {
              yAxes: [{
                gridLines: {
                  drawBorder: false,
                  color: '#f2f2f2'
                },
                ticks: {
                  beginAtZero: true,
                  callback: function(value) {
                    if (value >= 1000000) {
                      return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                      return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                    }
                    return 'Rp ' + value;
                  }
                }
              }],
              xAxes: [{
                gridLines: {
                  display: false
                }
              }]
            }
          }
        });
      }

      // 2. Doughnut Chart: Rasio Status Pembayaran SPP
      var sppStatusCanvas = document.getElementById("sppStatusChart");
      if (sppStatusCanvas) {
        var ctxStatus = sppStatusCanvas.getContext('2d');
        var paidCount = {{ $sppStats['paid_count'] }};
        var unpaidCount = {{ $sppStats['unpaid_count'] }};

        new Chart(ctxStatus, {
          type: 'doughnut',
          data: {
            datasets: [{
              data: [paidCount, unpaidCount],
              backgroundColor: [
                '#47c363',
                '#ffa426'
              ],
              borderWidth: 2,
              borderColor: '#ffffff'
            }],
            labels: [
              '{{ __("Paid") }}',
              '{{ __("Unpaid") }}'
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              position: 'bottom',
              labels: {
                boxWidth: 14,
                padding: 16
              }
            },
            tooltips: {
              callbacks: {
                label: function(tooltipItem, data) {
                  var label = data.labels[tooltipItem.index] || '';
                  var value = data.datasets[0].data[tooltipItem.index] || 0;
                  return label + ': ' + value + ' {{ __("Transactions") }}';
                }
              }
            },
            cutoutPercentage: 70
          }
        });
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Dashboard') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Overview') ?? 'Overview' }}</div>
    </div>
  </div>

  <div class="section-body">
    {{-- Welcome Hero Widget --}}
    <div class="row">
      <div class="col-12 mb-4">
        <div class="hero text-white shadow-sm" style="background: linear-gradient(135deg, #6777ef 0%, #3f51b5 100%); border-radius: 12px; padding: 28px 32px;">
          <div class="row align-items-center">
            <div class="col-lg-8 col-md-12">
              <span class="badge badge-light text-primary font-weight-bold text-uppercase mb-2 px-3 py-1">
                <i class="fas fa-shield-alt mr-1"></i> {{ auth()->user()?->role ?? 'admin' }}
              </span>
              <h2 class="mb-2 font-weight-bold" style="letter-spacing: -0.5px;">
                {{ __('Welcome to Stisla School System') }}, {{ auth()->user()?->name ?? 'Administrator' }}! 👋
              </h2>
              <p class="lead mb-0 text-white-50 font-weight-normal" style="font-size: 15px;">
                {{ __('Manage academic activities, students, teachers, and SPP payments smoothly.') }}
              </p>
            </div>
            <div class="col-lg-4 col-md-12 text-lg-right mt-3 mt-lg-0">
              @can('spp-payments.create')
                <a href="{{ route('spp-payments.create') }}" class="btn btn-warning btn-icon icon-left shadow-sm mr-2 mb-2 font-weight-600">
                  <i class="fas fa-file-invoice-dollar"></i> {{ __('Record SPP') }}
                </a>
              @endcan
              @can('students.create')
                <a href="{{ route('students.create') }}" class="btn btn-outline-white btn-icon icon-left mb-2">
                  <i class="fas fa-user-plus"></i> {{ __('Add Student') }}
                </a>
              @endcan
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 4 Primary Stat Cards --}}
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1 shadow-sm">
          <div class="card-icon bg-primary">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Students') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($counts['students']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1 shadow-sm">
          <div class="card-icon bg-success">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Teachers') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($counts['teachers']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1 shadow-sm">
          <div class="card-icon bg-warning">
            <i class="fas fa-school"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Classes') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($counts['classes']) }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1 shadow-sm">
          <div class="card-icon bg-danger">
            <i class="fas fa-book-open"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total Subjects') }}</h4>
            </div>
            <div class="card-body">
              {{ number_format($counts['subjects']) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SPP Financial Summary (3 Cards) --}}
    <div class="row">
      <div class="col-lg-4 col-md-6 col-12">
        <div class="card card-statistic-2 shadow-sm">
          <div class="card-icon shadow-success bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total SPP Paid') }}</h4>
            </div>
            <div class="card-body text-success font-weight-bold">
              Rp {{ number_format($sppStats['total_paid'], 0, ',', '.') }}
            </div>
            <div class="text-muted small mt-1">
              <i class="fas fa-receipt mr-1"></i> {{ $sppStats['paid_count'] }} {{ __('Transactions') }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-12">
        <div class="card card-statistic-2 shadow-sm">
          <div class="card-icon shadow-warning bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('Total SPP Unpaid') }}</h4>
            </div>
            <div class="card-body text-warning font-weight-bold">
              Rp {{ number_format($sppStats['total_unpaid'], 0, ',', '.') }}
            </div>
            <div class="text-muted small mt-1">
              <i class="fas fa-hourglass-half mr-1"></i> {{ $sppStats['unpaid_count'] }} {{ __('Transactions') }}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 col-12">
        <div class="card card-statistic-2 shadow-sm">
          <div class="card-icon shadow-info bg-info">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>{{ __('This Month Income') }}</h4>
            </div>
            <div class="card-body text-info font-weight-bold">
              Rp {{ number_format($sppStats['current_month_paid'], 0, ',', '.') }}
            </div>
            <div class="text-muted small mt-1">
              <i class="fas fa-calendar-day mr-1"></i> {{ now()->translatedFormat('F Y') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Interactive Visual Charts Row --}}
    <div class="row">
      <div class="col-lg-8 col-md-12 col-12">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4>
              <i class="fas fa-chart-area text-primary mr-2"></i> {{ __('Monthly SPP Income Trend') }} ({{ $currentYear }})
            </h4>
            <span class="badge badge-light text-primary font-weight-600">
              <i class="fas fa-coins mr-1"></i> {{ __('Paid') }}
            </span>
          </div>
          <div class="card-body">
            <div style="height: 310px;">
              <canvas id="sppTrendChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 col-12">
        <div class="card shadow-sm">
          <div class="card-header">
            <h4>
              <i class="fas fa-chart-pie text-warning mr-2"></i> {{ __('SPP Payment Status Ratio') }}
            </h4>
          </div>
          <div class="card-body">
            <div style="height: 210px;" class="mb-3">
              <canvas id="sppStatusChart"></canvas>
            </div>
            @php
              $totalTransactions = $sppStats['paid_count'] + $sppStats['unpaid_count'];
              $paidPercent = $totalTransactions > 0 ? round(($sppStats['paid_count'] / $totalTransactions) * 100) : 0;
              $unpaidPercent = $totalTransactions > 0 ? (100 - $paidPercent) : 0;
            @endphp
            <div class="row text-center mt-3 pt-2 border-top">
              <div class="col-6 border-right">
                <div class="text-muted small text-uppercase font-weight-bold">{{ __('Paid') }}</div>
                <div class="h5 mb-0 text-success font-weight-bold">{{ $paidPercent }}%</div>
                <small class="text-muted">{{ $sppStats['paid_count'] }} trx</small>
              </div>
              <div class="col-6">
                <div class="text-muted small text-uppercase font-weight-bold">{{ __('Unpaid') }}</div>
                <div class="h5 mb-0 text-warning font-weight-bold">{{ $unpaidPercent }}%</div>
                <small class="text-muted">{{ $sppStats['unpaid_count'] }} trx</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Bottom Panels: Recent Payments & Recent Students + Quick Actions --}}
    <div class="row">
      <div class="col-lg-8 col-md-12 col-12">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4>
              <i class="fas fa-receipt text-primary mr-2"></i> {{ __('Recent SPP Payments') }}
            </h4>
            @can('spp-payments.view')
              <a href="{{ route('spp-payments.index') }}" class="btn btn-sm btn-outline-primary">
                {{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i>
              </a>
            @endcan
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-md mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Invoice Number') }}</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-center">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($recentPayments as $payment)
                    <tr>
                      <td class="font-weight-600 text-dark">
                        {{ $payment->invoice_number }}
                      </td>
                      <td>
                        <span class="font-weight-600">{{ $payment->student?->name ?? '-' }}</span>
                        @if ($payment->student?->schoolClass)
                          <div class="text-muted text-small">{{ $payment->student->schoolClass->class_name }}</div>
                        @endif
                      </td>
                      <td>
                        {{ $payment->monthName() }} {{ $payment->year }}
                      </td>
                      <td class="font-weight-bold">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                      </td>
                      <td>
                        @if ($payment->status === 'paid')
                          <span class="badge badge-success px-2">{{ __('Paid') }}</span>
                        @else
                          <span class="badge badge-warning px-2">{{ __('Unpaid') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @can('spp-payments.view')
                          <a href="{{ route('spp-payments.show', $payment) }}" class="btn btn-sm btn-icon btn-info" data-toggle="tooltip" title="{{ __('Detail') }}">
                            <i class="fas fa-eye"></i>
                          </a>
                        @endcan
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle mr-1"></i> {{ __('No payment records found.') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 col-12">
        {{-- Recent Students Widget --}}
        <div class="card shadow-sm mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4>
              <i class="fas fa-users text-primary mr-2"></i> {{ __('Recent Students') }}
            </h4>
            @can('students.view')
              <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">
                {{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i>
              </a>
            @endcan
          </div>
          <div class="card-body p-0">
            <ul class="list-unstyled list-unstyled-border mb-0 px-3 py-2">
              @forelse ($recentStudents as $student)
                <li class="media py-2 align-items-center">
                  <div class="avatar bg-primary-light text-primary mr-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(103, 119, 239, 0.12);">
                    <i class="fas {{ $student->gender === 'female' ? 'fa-female text-danger' : 'fa-male text-primary' }}"></i>
                  </div>
                  <div class="media-body">
                    @can('students.view')
                      <div class="float-right">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-icon btn-light" data-toggle="tooltip" title="{{ __('Detail') }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      </div>
                    @endcan
                    <div class="media-title mb-0 font-weight-600">
                      {{ $student->name }}
                    </div>
                    <div class="text-small text-muted">
                      <span class="badge badge-light mr-1">{{ $student->nis }}</span>
                      {{ $student->schoolClass?->class_name ?? '-' }}
                    </div>
                  </div>
                </li>
              @empty
                <li class="py-4 text-center text-muted">
                  <i class="fas fa-info-circle mr-1"></i> {{ __('No student records found.') }}
                </li>
              @endforelse
            </ul>
          </div>
        </div>

        {{-- Quick Actions Card --}}
        <div class="card shadow-sm">
          <div class="card-header">
            <h4>
              <i class="fas fa-bolt text-warning mr-2"></i> {{ __('Quick Actions') }}
            </h4>
          </div>
          <div class="card-body">
            <div class="row">
              @can('spp-payments.create')
                <div class="col-6 mb-2">
                  <a href="{{ route('spp-payments.create') }}" class="btn btn-outline-primary btn-block text-left text-truncate btn-sm">
                    <i class="fas fa-plus mr-1"></i> {{ __('Record SPP') }}
                  </a>
                </div>
              @endcan
              @can('students.create')
                <div class="col-6 mb-2">
                  <a href="{{ route('students.create') }}" class="btn btn-outline-success btn-block text-left text-truncate btn-sm">
                    <i class="fas fa-user-plus mr-1"></i> {{ __('Add Student') }}
                  </a>
                </div>
              @endcan
              @can('teachers.create')
                <div class="col-6 mb-2">
                  <a href="{{ route('teachers.create') }}" class="btn btn-outline-info btn-block text-left text-truncate btn-sm">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> {{ __('Add Teacher') }}
                  </a>
                </div>
              @endcan
              @can('classes.create')
                <div class="col-6 mb-2">
                  <a href="{{ route('classes.create') }}" class="btn btn-outline-warning btn-block text-left text-truncate btn-sm">
                    <i class="fas fa-school mr-1"></i> {{ __('Add Class') }}
                  </a>
                </div>
              @endcan
              @can('subjects.create')
                <div class="col-12">
                  <a href="{{ route('subjects.create') }}" class="btn btn-outline-danger btn-block text-left text-truncate btn-sm">
                    <i class="fas fa-book mr-1"></i> {{ __('Add Subject') }}
                  </a>
                </div>
              @endcan
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

