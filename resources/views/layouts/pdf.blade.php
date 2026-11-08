<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <title>@yield('title', config('app.name'))</title>
  <style>
    @page {
      margin: 25px 25px 35px 25px;
    }
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 11px;
      color: #333333;
      line-height: 1.4;
      margin: 0;
      padding: 0;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      border-bottom: 2px solid #6777ef;
      padding-bottom: 10px;
    }
    .header-table td {
      vertical-align: middle;
      border: none;
    }
    .header-title h2 {
      margin: 0 0 4px 0;
      color: #34395e;
      font-size: 18px;
      font-weight: bold;
    }
    .header-title p {
      margin: 0;
      color: #8c8c8c;
      font-size: 10px;
    }
    .header-meta {
      text-align: right;
      font-size: 10px;
      color: #6c757d;
    }
    .header-meta strong {
      color: #34395e;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .data-table th {
      background-color: #6777ef;
      color: #ffffff;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 10px;
      letter-spacing: 0.5px;
      padding: 7px 8px;
      border: 1px solid #6777ef;
      text-align: left;
    }
    .data-table th.text-center, .data-table td.text-center {
      text-align: center;
    }
    .data-table th.text-right, .data-table td.text-right {
      text-align: right;
    }
    .data-table td {
      padding: 6px 8px;
      border-bottom: 1px solid #e4e6fc;
      border-left: 1px solid #f0f1f5;
      border-right: 1px solid #f0f1f5;
      font-size: 10px;
      vertical-align: top;
    }
    .data-table tbody tr:nth-child(even) {
      background-color: #fbfbfb;
    }
    .badge {
      display: inline-block;
      padding: 2px 6px;
      font-size: 9px;
      font-weight: bold;
      border-radius: 3px;
      text-align: center;
    }
    .badge-primary { background-color: #6777ef; color: #fff; }
    .badge-secondary { background-color: #cdd3d8; color: #333; }
    .badge-success { background-color: #47c363; color: #fff; }
    .badge-danger { background-color: #fc544b; color: #fff; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-info { background-color: #3abaf4; color: #fff; }
    .badge-light { background-color: #e3eaef; color: #1f2d3d; }
    .badge-dark { background-color: #191d21; color: #fff; }

    .footer {
      position: fixed;
      bottom: -20px;
      left: 0;
      right: 0;
      height: 20px;
      font-size: 9px;
      color: #8c8c8c;
      border-top: 1px solid #e4e6fc;
      padding-top: 4px;
      text-align: right;
    }
    .footer-left {
      float: left;
      text-align: left;
    }
  </style>
  @yield('styles')
</head>
<body>
  <table class="header-table">
    <tr>
      <td class="header-title">
        <h2>@yield('title')</h2>
        <p>{{ config('app.name', 'Laravel Stisla') }} &bull; {{ __('System Report') }}</p>
      </td>
      <td class="header-meta">
        <div><strong>{{ __('Exported At') }}:</strong> {{ now()->format('Y-m-d H:i:s') }}</div>
        <div><strong>{{ __('Exported By') }}:</strong> {{ auth()->user()?->name ?? 'System' }}</div>
        <div><strong>{{ __('Total Records') }}:</strong> @yield('total', '0')</div>
      </td>
    </tr>
  </table>

  @yield('content')

  <div class="footer">
    <div class="footer-left">{{ config('app.name', 'Laravel Stisla') }} &copy; {{ date('Y') }}</div>
    <div>{{ __('Generated automatically by system') }}</div>
  </div>
</body>
</html>

