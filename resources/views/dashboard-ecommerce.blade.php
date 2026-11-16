@extends('layouts.app')

@section('title', __('Ecommerce Dashboard'))

@push('js')
  <script src="{{ asset('node_modules/chart.js/dist/Chart.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    $(document).ready(function() {
      var revenueCanvas = document.getElementById("ecommerceRevenueChart");
      if (revenueCanvas) {
        var ctxRevenue = revenueCanvas.getContext('2d');
        var gradientRevenue = ctxRevenue.createLinearGradient(0, 0, 0, 300);
        gradientRevenue.addColorStop(0, 'rgba(103, 119, 239, 0.45)');
        gradientRevenue.addColorStop(1, 'rgba(103, 119, 239, 0.02)');

        new Chart(ctxRevenue, {
          type: 'line',
          data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
              label: '{{ __("Paid Revenue") }}',
              data: @json($monthlyChartData),
              borderWidth: 3,
              borderColor: '#6777ef',
              backgroundColor: gradientRevenue,
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
            legend: { display: false },
            tooltips: {
              backgroundColor: 'rgba(32, 34, 42, 0.9)',
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
                gridLines: { drawBorder: false, color: '#f2f2f2' },
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
                gridLines: { display: false }
              }]
            }
          }
        });
      }

      var statusCanvas = document.getElementById("orderStatusChart");
      if (statusCanvas) {
        new Chart(statusCanvas.getContext('2d'), {
          type: 'doughnut',
          data: {
            datasets: [{
              data: [
                {{ $orderStats['pending_count'] }},
                {{ $orderStats['processing_count'] }},
                {{ $orderStats['completed_count'] }},
                {{ $orderStats['cancelled_count'] }}
              ],
              backgroundColor: ['#ffa426', '#3abaf4', '#47c363', '#fc544b'],
              label: 'Order Status'
            }],
            labels: {!! json_encode([__('Pending'), __('Processing / Shipped'), __('Completed'), __('Cancelled')]) !!}
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom' }
          }
        });
      }
    });
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Ecommerce Dashboard') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Ecommerce') }}</div>
    </div>
  </div>

  <!-- Top KPI Cards -->
  <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-primary">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>{{ __('Total Orders') }}</h4>
          </div>
          <div class="card-body">
            {{ $counts['orders'] }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-success">
          <i class="fas fa-wallet"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>{{ __('Paid Revenue') }}</h4>
          </div>
          <div class="card-body" style="font-size: 16px;">
            Rp {{ number_format($orderStats['total_revenue'], 0, ',', '.') }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-info">
          <i class="fas fa-box-open"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>{{ __('Active Products') }}</h4>
          </div>
          <div class="card-body">
            {{ $counts['products'] }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-warning">
          <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>{{ __('Need Verification') }}</h4>
          </div>
          <div class="card-body">
            {{ $orderStats['waiting_verification_count'] }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row">
    <div class="col-lg-8 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>{{ __('Monthly Paid Revenue') }} ({{ $currentYear }})</h4>
          <div class="card-header-action">
            <span class="badge badge-primary">
              {{ __('This Month:') }} Rp {{ number_format($orderStats['current_month_revenue'], 0, ',', '.') }}
            </span>
          </div>
        </div>
        <div class="card-body">
          <div style="height: 290px;">
            <canvas id="ecommerceRevenueChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>{{ __('Order Status Ratio') }}</h4>
        </div>
        <div class="card-body">
          <div style="height: 230px;">
            <canvas id="orderStatusChart"></canvas>
          </div>
          <div class="mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
            <span>{{ __('Open Tickets:') }} <strong>{{ $counts['open_tickets'] }}</strong></span>
            <span>{{ __('Pending Reviews:') }} <strong>{{ $counts['pending_reviews'] }}</strong></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables Row -->
  <div class="row">
    <div class="col-lg-7 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>{{ __('Recent Orders') }}</h4>
          <div class="card-header-action">
            <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">{{ __('View All') }}</a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>{{ __('Order #') }}</th>
                  <th>{{ __('Customer') }}</th>
                  <th>{{ __('Total') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Payment') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($recentOrders as $order)
                  <tr>
                    <td>
                      <a href="{{ route('orders.show', $order) }}" class="font-weight-bold">#{{ $order->order_number }}</a>
                    </td>
                    <td>{{ $order->recipient_name }}</td>
                    <td>{{ $order->formatted_grand_total }}</td>
                    <td><span class="badge badge-info">{{ ucfirst($order->status) }}</span></td>
                    <td><span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">{{ __('No orders yet.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>{{ __('Top Selling Products') }}</h4>
          <div class="card-header-action">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">{{ __('Products') }}</a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>{{ __('Product') }}</th>
                  <th class="text-center">{{ __('Sold') }}</th>
                  <th class="text-center">{{ __('Stock') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($topProducts as $product)
                  <tr>
                    <td>
                      <div class="font-weight-600">{{ \Illuminate\Support\Str::limit($product->title, 28) }}</div>
                      <small class="text-muted">{{ $product->category?->name ?? '-' }}</small>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-success">{{ (int) ($product->total_sold ?? 0) }}</span>
                    </td>
                    <td class="text-center">
                      <span class="badge {{ $product->stock <= 10 ? 'badge-danger' : 'badge-light' }}">{{ $product->stock }}</span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">{{ __('No products found.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
