<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembaruan Pesanan #{{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 24px; color: #313b50;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb;">
    <div style="background: #3a4ee5; color: #ffffff; padding: 20px 24px;">
      <h2 style="margin: 0; font-size: 20px;">
        @if ($updateType === 'payment')
          Pembaruan Status Pembayaran
        @elseif ($updateType === 'tracking')
          Pembaruan Resi Pengiriman
        @else
          Pembaruan Status Pesanan
        @endif
      </h2>
      <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.9;">
        No. Pesanan: <strong>#{{ $order->order_number }}</strong>
      </p>
    </div>

    <div style="padding: 24px;">
      <p style="margin-top: 0;">Halo <strong>{{ $order->user?->name ?? $order->recipient_name }}</strong>,</p>
      <p style="line-height: 1.6;">
        @if ($updateType === 'payment')
          Status pembayaran untuk pesanan Anda telah diperbarui oleh admin.
        @elseif ($updateType === 'tracking')
          Nomor resi / bukti pengiriman untuk pesanan Anda telah diperbarui oleh admin.
        @else
          Status pesanan Anda telah diperbarui oleh admin.
        @endif
      </p>

      <table style="width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 14px;">
        <tr>
          <td style="padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb; width: 42%;"><strong>No. Pesanan</strong></td>
          <td style="padding: 10px; border: 1px solid #e5e7eb;">#{{ $order->order_number }}</td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb;"><strong>Status Pesanan</strong></td>
          <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb;"><strong>Status Pembayaran</strong></td>
          <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</td>
        </tr>
        @if ($order->tracking_number)
          <tr>
            <td style="padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb;"><strong>No. Resi</strong></td>
            <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ $order->tracking_number }}</td>
          </tr>
        @endif
        <tr>
          <td style="padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb;"><strong>Total Pembayaran</strong></td>
          <td style="padding: 10px; border: 1px solid #e5e7eb;"><strong>{{ $order->formatted_grand_total }}</strong></td>
        </tr>
      </table>

      <div style="margin-top: 24px; text-align: center;">
        <a href="{{ route('ecommerce.checkout.detail', $order->order_number) }}"
           style="display: inline-block; background: #3a4ee5; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 14px;">
          Lihat Detail Pesanan
        </a>
      </div>
    </div>

    <div style="padding: 16px 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center;">
      Email ini dikirim otomatis oleh sistem {{ \App\Models\Setting::getValue('site_title') ?: config('app.name', 'Stisla') }}.
    </div>
  </div>
</body>
</html>
