<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Laporan Kandidat Pekerja') }}</title>
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
  <h2>{{ __('Laporan Kandidat Pekerja') }}</h2>
  <p class="meta">{{ __('Dicetak pada') }}: {{ now()->format('d M Y H:i') }}</p>

  <table>
    <thead>
      <tr>
        <th width="25">#</th>
        <th width="85">{{ __('Kode') }}</th>
        <th width="120">{{ __('Nama') }}</th>
        <th width="90">{{ __('Kategori') }}</th>
        <th width="45">{{ __('Usia') }}</th>
        <th width="75">{{ __('Pengalaman') }}</th>
        <th width="120">{{ __('Asal') }}</th>
        <th>{{ __('Keahlian') }}</th>
        <th width="65">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($workers as $worker)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $worker->code }}</td>
          <td><strong>{{ $worker->name }}</strong></td>
          <td>{{ $worker->category }}</td>
          <td>{{ $worker->age }} {{ __('thn') }}</td>
          <td>{{ $worker->experience_label }}</td>
          <td>{{ $worker->origin ?: '-' }}</td>
          <td>{{ implode(', ', $worker->skills ?? []) ?: '-' }}</td>
          <td>{{ $worker->status }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="9" style="text-align: center;">{{ __('Tidak ada data pekerja.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
