@if (isset($dashboardAnnouncements) && $dashboardAnnouncements->isNotEmpty())
  <div class="row mb-3">
    <div class="col-12">
      <div class="card card-primary mb-0">
        <div class="card-header">
          <h4><i class="fas fa-bullhorn mr-2 text-primary"></i>{{ __('Announcements') }}</h4>
          @can('announcements.view')
            <div class="card-header-action">
              <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
            </div>
          @endcan
        </div>
        <div class="card-body py-3">
          <div class="list-group list-group-flush">
            @foreach ($dashboardAnnouncements as $announcement)
              @php
                $badgeClass = match ($announcement->type) {
                    'warning' => 'badge-warning',
                    'urgent' => 'badge-danger',
                    'event' => 'badge-primary',
                    default => 'badge-info',
                };
              @endphp
              <div class="list-group-item px-0 py-2 border-light">
                <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 8px;">
                  <div>
                    <div class="font-weight-600 text-dark">
                      @if ($announcement->is_pinned)
                        <i class="fas fa-thumbtack text-warning mr-1" title="{{ __('Pinned') }}"></i>
                      @endif
                      {{ $announcement->title }}
                      <span class="badge {{ $badgeClass }} ml-1">{{ __(\App\Models\Announcement::types()[$announcement->type] ?? ucfirst($announcement->type)) }}</span>
                    </div>
                    <div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 160) }}</div>
                  </div>
                  <div class="text-muted small text-nowrap">
                    {{ $announcement->published_at?->diffForHumans() ?? $announcement->created_at?->diffForHumans() }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
