@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Ticket List - Ecommerce')

@section('vendor-css')
  <link href="{{ asset('mantu/assets/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/slick.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('mantu/assets/css/vendor/nouislider.css') }}" rel="stylesheet">
@endsection

@section('vendor-js')
  <script src="{{ asset('mantu/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/slick.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/countdownTimer.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/infiniteslidev2.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/nouislider.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/smoothscroll.min.js') }}"></script>
  <script src="{{ asset('mantu/assets/js/vendor/jquery.zoom.min.js') }}"></script>
@endsection

@section('content')
<!-- Main Content -->
		<div class="mn-main-content">
			<div class="mn-breadcrumb m-b-30">
				<div class="row">
					<div class="col-12">
						<div class="row gi_breadcrumb_inner">
							<div class="col-md-6 col-sm-12">
								<h2 class="mn-breadcrumb-title">Ticket List</h2>
							</div>
							<div class="col-md-6 col-sm-12">
								<ul class="mn-breadcrumb-list">
									<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
									<li class="mn-breadcrumb-item active">Ticket List</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Ticket List section -->
			<section class="p-b-40">
				@if (session('success'))
					<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif

				@if (session('warning'))
					<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
						{{ session('warning') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif

				@if ($errors->any())
					<div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
				@endif

				@php
					$hasOpenTicket = $tickets->contains(fn ($t) => $t->status !== 'closed');
				@endphp

				<div class="d-flex align-items-center justify-content-between mb-3">
					<h4 class="mb-0 fw-bold">Tiket &amp; Pesan Saya</h4>
					@if ($hasOpenTicket)
						<span class="badge bg-info-subtle text-info-emphasis border px-3 py-2" style="font-size: 12px;">
							<i class="ri-information-line me-1"></i> Tutup tiket aktif untuk membuat pesan baru
						</span>
					@else
						<a href="{{ route('ecommerce.contact-us') }}" class="mn-btn-2">
							<span><i class="ri-add-line me-1"></i> Kirim Pesan Baru</span>
						</a>
					@endif
				</div>

				@if ($tickets->isEmpty())
					<div class="card border shadow-sm p-5 text-center" style="border-radius: 12px;">
						<div class="mb-3">
							<i class="ri-customer-service-2-line" style="font-size: 52px; color: #adb5bd;"></i>
						</div>
						<h4 class="fw-bold">Belum ada tiket</h4>
						<p class="text-muted mb-4">Anda belum pernah mengirim pesan atau tiket bantuan.</p>
						<div>
							<a href="{{ route('ecommerce.contact-us') }}" class="mn-btn-2"><span>Hubungi Kami</span></a>
						</div>
					</div>
				@else
					<div class="row g-4">
						<!-- Left: Ticket List -->
						<div class="col-lg-4 col-md-12">
							<div class="card border shadow-sm" style="border-radius: 12px; overflow: hidden;">
								<div class="card-header bg-white py-3 border-bottom">
									<h6 class="mb-0 fw-bold">Daftar Tiket ({{ $tickets->count() }})</h6>
								</div>
								<div class="list-group list-group-flush">
									@foreach ($tickets as $ticket)
										@php
											$isActive = $activeTicket && $activeTicket->id === $ticket->id;
											$statusBadge = match ($ticket->status) {
												'open' => 'bg-success',
												'in_progress' => 'bg-info text-dark',
												'closed' => 'bg-secondary',
												default => 'bg-light text-dark',
											};
										@endphp
										<a href="{{ route('ecommerce.ticket-list', ['ticket' => $ticket->id]) }}"
											class="list-group-item list-group-item-action p-3 {{ $isActive ? 'active' : '' }}">
											<div class="d-flex justify-content-between align-items-start mb-1">
												<span class="fw-bold small">#{{ $ticket->id }} &bull; {{ \Illuminate\Support\Str::limit($ticket->title, 36) }}</span>
												<span class="badge {{ $statusBadge }}" style="font-size: 10px;">
													{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
												</span>
											</div>
											<p class="mb-1 small {{ $isActive ? 'text-white-50' : 'text-muted' }} text-truncate">
												{{ $ticket->description }}
											</p>
											<div class="d-flex justify-content-between align-items-center" style="font-size: 11px;">
												<span class="{{ $isActive ? 'text-white-50' : 'text-muted' }}">
													{{ $ticket->created_at?->format('d M Y, H:i') }}
												</span>
												<span class="{{ $isActive ? 'text-white-50' : 'text-muted' }}">
													<i class="ri-chat-3-line"></i> {{ $ticket->replies->count() }}
												</span>
											</div>
										</a>
									@endforeach
								</div>
							</div>
						</div>

						<!-- Right: Active Ticket Thread -->
						<div class="col-lg-8 col-md-12">
							@if ($activeTicket)
								@php
									$activeStatusBadge = match ($activeTicket->status) {
										'open' => 'bg-success',
										'in_progress' => 'bg-info text-dark',
										'closed' => 'bg-secondary',
										default => 'bg-light text-dark',
									};
								@endphp
								<div class="card border shadow-sm" style="border-radius: 12px;">
									<div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
										<div>
											<h5 class="mb-1 fw-bold text-dark">#{{ $activeTicket->id }} &mdash; {{ $activeTicket->title }}</h5>
											<small class="text-muted">Dibuat pada {{ $activeTicket->created_at?->format('d F Y, H:i') }} WIB</small>
										</div>
										<div class="d-flex align-items-center gap-2">
											<span class="badge {{ $activeStatusBadge }} px-3 py-2">
												{{ ucfirst(str_replace('_', ' ', $activeTicket->status)) }}
											</span>
											@if ($activeTicket->status !== 'closed')
												<form action="{{ route('ecommerce.ticket-list.close', $activeTicket) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup tiket ini?');">
													@csrf
													@method('PATCH')
													<button type="submit" class="btn btn-sm btn-outline-danger">
														<i class="ri-close-circle-line me-1"></i> Tutup Tiket
													</button>
												</form>
											@endif
										</div>
									</div>
									<div class="card-body p-4" style="max-height: 480px; overflow-y: auto; background: #f8f9fa;">
										<!-- Initial Customer Message -->
										<div class="mb-3 p-3 bg-white border rounded-3 shadow-sm">
											<div class="d-flex justify-content-between align-items-center mb-2">
												<span class="fw-bold text-primary small">
													<i class="ri-user-3-line me-1"></i> {{ auth()->user()->name }} (Anda)
												</span>
												<small class="text-muted">{{ $activeTicket->created_at?->format('d M Y, H:i') }}</small>
											</div>
											<p class="mb-0 text-dark" style="white-space: pre-line;">{{ $activeTicket->description }}</p>
										</div>

										<!-- Replies -->
										@foreach ($activeTicket->replies as $reply)
											@php
												$isSelf = (int) $reply->user_id === (int) auth()->id();
											@endphp
											<div class="mb-3 p-3 rounded-3 shadow-sm {{ $isSelf ? 'bg-white border' : 'bg-primary-subtle border border-primary-subtle' }}">
												<div class="d-flex justify-content-between align-items-center mb-2">
													<span class="fw-bold small {{ $isSelf ? 'text-primary' : 'text-dark' }}">
														@if ($isSelf)
															<i class="ri-user-3-line me-1"></i> {{ auth()->user()->name }} (Anda)
														@else
															<i class="ri-customer-service-2-line me-1 text-primary"></i> {{ $reply->user?->name ?? 'Admin Support' }} (Support)
														@endif
													</span>
													<small class="text-muted">{{ $reply->created_at?->format('d M Y, H:i') }}</small>
												</div>
												<p class="mb-0 text-dark" style="white-space: pre-line;">{{ $reply->message }}</p>
											</div>
										@endforeach
									</div>
									<div class="card-footer bg-white p-3 border-top">
										@if ($activeTicket->status === 'closed')
											<div class="alert alert-secondary mb-0 text-center small">
												Tiket ini sudah ditutup. Silakan buat pesan baru di halaman <a href="{{ route('ecommerce.contact-us') }}" class="fw-bold">Contact Us</a> jika masih membutuhkan bantuan.
											</div>
										@else
											<form action="{{ route('ecommerce.ticket-list.reply', $activeTicket) }}" method="POST">
												@csrf
												<div class="mb-2">
													<textarea name="message" rows="3" class="form-control" placeholder="Tulis balasan Anda..." required>{{ old('message') }}</textarea>
												</div>
												<div class="text-end">
													<button type="submit" class="mn-btn-2">
														<span><i class="ri-send-plane-line me-1"></i> Kirim Balasan</span>
													</button>
												</div>
											</form>
										@endif
									</div>
								</div>
							@endif
						</div>
					</div>
				@endif
			</section>
		</div>
@endsection
