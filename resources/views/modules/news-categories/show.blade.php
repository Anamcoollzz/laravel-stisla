@extends('layouts.app')

@section('title', __('Detail Kategori: ') . $newsCategory->name)

@section('content')
  <div class="section-header">
    <div class="section-header-back">
      <a href="{{ route('news-categories.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
    </div>
    <h1>{{ __('Detail Kategori Berita') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item"><a href="{{ route('news-categories.index') }}">{{ __('News Categories') }}</a></div>
      <div class="breadcrumb-item">{{ $newsCategory->name }}</div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-md-5">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Informasi Kategori') }}</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Nama Kategori') }}</label>
              <div class="font-weight-bold" style="font-size: 16px;">{{ $newsCategory->name }}</div>
            </div>
            <div class="form-group mb-0">
              <label class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Deskripsi') }}</label>
              <div>{{ $newsCategory->description ?: '-' }}</div>
            </div>
          </div>
          <div class="card-footer bg-whitesmoke text-right">
            @can('news-categories.edit')
              <a href="{{ route('news-categories.edit', $newsCategory) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt mr-1"></i> {{ __('Edit') }}
              </a>
            @endcan
          </div>
        </div>
      </div>

      <div class="col-12 col-md-7">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('Berita dalam Kategori Ini') }} ({{ $newsCategory->news->count() }})</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Judul') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Aksi') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($newsCategory->news as $item)
                    <tr>
                      <td class="font-weight-600">{{ $item->title }}</td>
                      <td>
                        <span class="badge {{ $item->status === 'published' ? 'badge-success' : ($item->status === 'archived' ? 'badge-secondary' : 'badge-warning') }}">
                          {{ ucfirst($item->status) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-3">{{ __('Belum ada berita pada kategori ini.') }}</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
