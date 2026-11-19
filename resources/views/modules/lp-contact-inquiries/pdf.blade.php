<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Laporan Pesan Kontak') }}</title>
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
  <h2>{{ __('Laporan Pesan Kontak') }}</h2>
  <p class="meta">{{ __('Dicetak pada') }}: {{ now()->format('d M Y H:i') }}</p>

  <table>
    <thead>
      <tr>
        <th width="25">#</th>
        <th width="110">{{ __('Pengirim') }}</th>
        <th width="110">{{ __('Email') }}</th>
        <th width="85">{{ __('Telepon') }}</th>
        <th width="120">{{ __('Subjek') }}</th>
        <th>{{ __('Pesan') }}</th>
        <th width="65">{{ __('Status') }}</th>
        <th width="85">{{ __('Waktu') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($inquiries as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td><strong>{{ $item->full_name }}</strong></td>
          <td>{{ $item->email }}</td>
          <td>{{ $item->phone }}</td>
          <td>{{ $item->subject }}</td>
          <td>{{ $item->message }}</td>
          <td>{{ $item->is_read ? __('Sudah Dibaca') : __('Belum Dibaca') }}</td>
          <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center;">{{ __('Tidak ada data pesan kontak.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
