<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Laporan FAQ') }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
    h2 { margin: 0 0 4px 0; font-size: 16px; color: #222; }
    p.meta { margin: 0 0 14px 0; font-size: 10px; color: #666; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; }
    th { background-color: #6777ef; color: #fff; font-weight: bold; }
    tr:nth-child(even) td { background-color: #f9f9fb; }
  </style>
</head>
<body>
  <h2>{{ __('Laporan FAQ') }}</h2>
  <p class="meta">{{ __('Dicetak pada') }}: {{ now()->format('d M Y H:i') }}</p>

  <table>
    <thead>
      <tr>
        <th width="30">#</th>
        <th width="45">{{ __('Urutan') }}</th>
        <th width="180">{{ __('Pertanyaan') }}</th>
        <th>{{ __('Jawaban') }}</th>
        <th width="60">{{ __('Status') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($faqs as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->order }}</td>
          <td><strong>{{ $item->question }}</strong></td>
          <td>{{ $item->answer }}</td>
          <td>{{ $item->is_active ? __('Aktif') : __('Nonaktif') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="text-align: center;">{{ __('Tidak ada data FAQ.') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
