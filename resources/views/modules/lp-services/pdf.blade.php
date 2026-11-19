<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Laporan Layanan Rekrutmen') }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
    h2 { margin: 0 0 4px 0; font-size: 15px; color: #222; }
    p.meta { margin: 0 0 12px 0; font-size: 9px; color: #666; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 5px 7px; text-align: left; vertical-align: top; }
    th { background-color: #6777ef; color: #fff; font-weight: bold; }
    tr:nth-child(even) td { background-color: #f9f9fb; }
  </style>
</head>
<body>
  <h2>{{ __('Laporan Layanan Rekrutmen') }}</h2>
  <p class="meta">{{ __('Dicetak pada') }}: {{ now()->format('d M Y H:i') }}</p>

  <table>
    <thead>
      <tr>
        <th width="25">#</th>
        <th width="40">{{ __('Urutan') }}</th>
        <th width="70">{{ __('Category Key') }}</th>
        <th width="130">{{ __('Judul Layanan') }}</th>
        <th width="110">{{ __('Subjudul') }}</th>
        <th>{{ __('Deskripsi Singkat') }}</th>
        <th width="140">{{ __('Keahlian') }}</th>
        <th width="55">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($services as $service)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $service->order }}</td>
          <td>{{ $service->category_key }}</td>
          <td><strong>{{ $service->title }}</strong></td>
          <td>{{ $service->subtitle ?: '-' }}</td>
          <td>{{ $service->short_description ?: '-' }}</td>
          <td>{{ implode(', ', $service->skills ?? []) ?: '-' }}</td>
          <td>{{ $service->is_active ? __('Aktif') : __('Nonaktif') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center;">{{ __('Tidak ada data layanan.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
