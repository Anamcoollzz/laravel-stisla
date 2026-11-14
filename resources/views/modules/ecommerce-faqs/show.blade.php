@extends('layouts.app')

@section('title', __('Shop › FAQ Details'))

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('ecommerce-faqs.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('FAQ Details') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('ecommerce-faqs.index') }}">{{ __('FAQs') }}</a></div>
      <div class="breadcrumb-item">{{ Str::limit($faq->question, 30) }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card">
          <div class="card-header">
            <h4><i class="fas fa-question-circle text-primary mr-2"></i> {{ __('Question & Answer') }}</h4>
            <div class="card-header-action">
              @can('ecommerce-faqs.edit')
                <a href="{{ route('ecommerce-faqs.edit', $faq) }}" class="btn btn-warning">
                  <i class="fas fa-pencil-alt"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
          <div class="card-body">
            <h5 class="font-weight-bold text-dark mb-3">{{ $faq->question }}</h5>
            <div class="p-3 bg-light rounded mb-4" style="border-left: 4px solid #6777ef;">
              {!! nl2br(e($faq->answer)) !!}
            </div>

            <table class="table table-bordered">
              <tr>
                <th style="width: 200px;">{{ __('Display Column') }}</th>
                <td>
                  @if ($faq->column === 'left')
                    <span class="badge badge-info">{{ __('Left Column') }}</span>
                  @else
                    <span class="badge badge-primary">{{ __('Right Column') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Display Order') }}</th>
                <td>{{ $faq->order }}</td>
              </tr>
              <tr>
                <th>{{ __('Status') }}</th>
                <td>
                  @if ($faq->is_active)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('Created At') }}</th>
                <td>{{ $faq->created_at?->format('d M Y H:i') }}</td>
              </tr>
              <tr>
                <th>{{ __('Last Updated') }}</th>
                <td>{{ $faq->updated_at?->format('d M Y H:i') }}</td>
              </tr>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('ecommerce-faqs.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
            @can('ecommerce-faqs.delete')
              <form action="{{ route('ecommerce-faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('{{ __('Delete this FAQ?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
