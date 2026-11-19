@extends('layouts.app')

@section('title', __('Detail FAQ'))

@section('content')
  <div class="section-header">
    <h1>{{ __('Detail FAQ') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('lp-faqs.index') }}">{{ __('FAQs') }}</a></div>
      <div class="breadcrumb-item active">{{ __('Detail') }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle mr-2 text-info"></i> {{ __('Detail FAQ') }}</h4>
        <div class="card-header-action">
          @can('lp-faqs.edit')
            <a href="{{ route('lp-faqs.edit', $lpFaq) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
            </a>
          @endcan
          <a href="{{ route('lp-faqs.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered mb-0">
          <tr>
            <th width="220">{{ __('Pertanyaan') }}</th>
            <td><strong>{{ $lpFaq->question }}</strong></td>
          </tr>
          <tr>
            <th>{{ __('Jawaban') }}</th>
            <td>{{ $lpFaq->answer }}</td>
          </tr>
          <tr>
            <th>{{ __('Urutan') }}</th>
            <td>{{ $lpFaq->order }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}</th>
            <td>
              @if ($lpFaq->is_active)
                <span class="badge badge-success">{{ __('Aktif') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Nonaktif') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Dibuat Pada') }}</th>
            <td>{{ $lpFaq->created_at?->format('d M Y H:i') }}</td>
          </tr>
          <tr>
            <th>{{ __('Diperbarui Pada') }}</th>
            <td>{{ $lpFaq->updated_at?->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
