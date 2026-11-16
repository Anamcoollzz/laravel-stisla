@extends('ecommerce.layouts.app-ecommerce')

@section('title', 'Daftar Alamat - Ecommerce')

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
  <script src="{{ asset('mantu/assets/js/vendor/smoothscroll.min.js') }}"></script>
@endsection

@section('content')
<!-- Main Content -->
<div class="mn-main-content">
	<!-- Breadcrumb -->
	<div class="mn-breadcrumb m-b-30">
		<div class="row">
			<div class="col-12">
				<div class="row gi_breadcrumb_inner">
					<div class="col-md-6 col-sm-12">
						<h2 class="mn-breadcrumb-title">Daftar Alamat</h2>
					</div>
					<div class="col-md-6 col-sm-12">
						<ul class="mn-breadcrumb-list">
							<li class="mn-breadcrumb-item"><a href="{{ route('ecommerce.index.html') }}">Home</a></li>
							<li class="mn-breadcrumb-item active">Daftar Alamat</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Address List section -->
	<section class="mn-address-section p-b-40">
		<div class="container">
			@if (session('success'))
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
					<strong><i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}</strong>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			@if (session('warning'))
				<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
					<strong><i class="ri-alert-line me-1"></i> {{ session('warning') }}</strong>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
					<strong><i class="ri-error-warning-line me-1"></i> Terjadi kesalahan:</strong>
					<ul class="mb-0 mt-1 ps-3">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<!-- Header section -->
			<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
				<div>
					<h3 class="fw-bold mb-1" style="font-size: 22px;">Alamat Pengiriman</h3>
					<p class="text-muted mb-0" style="font-size: 14px;">Kelola alamat tersimpan untuk kemudahan pengiriman pesanan Anda.</p>
				</div>
				<div class="mt-2 mt-sm-0">
					<button type="button" class="mn-btn-1 btn" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
						<span><i class="ri-add-line me-1"></i> Tambah Alamat Baru</span>
					</button>
				</div>
			</div>

			<!-- Address Cards -->
			<div class="row">
				@forelse ($addresses as $address)
					<div class="col-lg-6 col-md-12 mb-4">
						<div class="card h-100 shadow-sm {{ $address->is_default ? 'border-primary' : 'border' }}" style="border-radius: 12px; transition: all 0.2s ease;">
							<div class="card-body p-4 d-flex flex-column">
								<div class="d-flex justify-content-between align-items-start mb-2">
									<div class="d-flex align-items-center gap-2">
										<span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 12px;">{{ $address->label }}</span>
										@if ($address->is_default)
											<span class="badge bg-success px-2 py-1" style="font-size: 12px;">
												<i class="ri-check-line me-1"></i> Utama
											</span>
										@endif
									</div>

									<!-- Action Menu -->
									<div class="d-flex gap-2">
										<button type="button"
											class="btn btn-sm btn-outline-secondary btn-edit-address"
											style="border-radius: 6px; padding: 4px 10px;"
											data-bs-toggle="modal"
											data-bs-target="#modalEditAddress"
											data-id="{{ $address->id }}"
											data-label="{{ $address->label }}"
											data-recipient="{{ $address->recipient_name }}"
											data-phone="{{ $address->phone_number }}"
											data-address="{{ $address->address_line }}"
											data-city="{{ $address->city }}"
											data-state="{{ $address->state }}"
											data-postal="{{ $address->postal_code }}"
											data-notes="{{ $address->notes }}"
											data-default="{{ $address->is_default ? '1' : '0' }}">
											<i class="ri-edit-line"></i> Edit
										</button>

										<form action="{{ route('ecommerce.address-list.destroy', $address->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 10px;">
												<i class="ri-delete-bin-line"></i>
											</button>
										</form>
									</div>
								</div>

								<h5 class="card-title fw-bold mb-1" style="font-size: 16px;">{{ $address->recipient_name }}</h5>
								<p class="text-secondary small mb-2"><i class="ri-phone-line me-1"></i> {{ $address->phone_number }}</p>

								<p class="card-text text-muted mb-2" style="font-size: 14px; line-height: 1.5;">
									{{ $address->address_line }}
								</p>

								@if ($address->city || $address->state || $address->postal_code)
									<p class="text-muted small mb-2">
										<i class="ri-map-pin-2-line me-1"></i>
										{{ collect([$address->city, $address->state, $address->postal_code])->filter()->join(', ') }}
									</p>
								@endif

								@if ($address->notes)
									<p class="text-secondary small mb-3 fst-italic">
										<strong>Catatan:</strong> {{ $address->notes }}
									</p>
								@endif

								<div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
									@if ($address->is_default)
										<span class="text-success fw-bold small">
											<i class="ri-checkbox-circle-fill me-1"></i> Alamat Pengiriman Utama
										</span>
									@else
										<form action="{{ route('ecommerce.address-list.default', $address->id) }}" method="POST">
											@csrf
											<button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
												<i class="ri-checkbox-blank-circle-line me-1"></i> Jadikan Alamat Utama
											</button>
										</form>
									@endif
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12">
						<div class="card border-0 shadow-sm py-5 text-center" style="border-radius: 12px;">
							<div class="card-body">
								<i class="ri-map-pin-line text-muted mb-3" style="font-size: 56px; display: inline-block;"></i>
								<h4 class="fw-bold mb-2">Belum Ada Alamat Tersimpan</h4>
								<p class="text-muted mb-4" style="max-width: 460px; margin: 0 auto;">Tambahkan alamat pengiriman Anda sekarang agar proses checkout menjadi lebih cepat dan mudah.</p>
								<button type="button" class="mn-btn-1 btn" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
									<span><i class="ri-add-line me-1"></i> Tambah Alamat Sekarang</span>
								</button>
							</div>
						</div>
					</div>
				@endforelse
			</div>
		</div>
	</section>
</div>

<!-- Modal: Tambah Alamat Baru -->
<div class="modal fade" id="modalAddAddress" tabindex="-1" aria-labelledby="modalAddAddressLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 12px; overflow: hidden;">
			<form action="{{ route('ecommerce.address-list.store') }}" method="POST">
				@csrf
				<div class="modal-header border-bottom py-3 px-4">
					<h5 class="modal-title fw-bold" id="modalAddAddressLabel"><i class="ri-map-pin-add-line me-1"></i> Tambah Alamat Baru</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label fw-bold">Label Alamat <span class="text-danger">*</span></label>
							<input type="text" name="label" class="form-control" placeholder="Contoh: Rumah, Kantor, Toko" value="{{ old('label', 'Rumah') }}" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Nama Penerima <span class="text-danger">*</span></label>
							<input type="text" name="recipient_name" class="form-control" placeholder="Nama lengkap penerima" value="{{ old('recipient_name', $user->name ?? '') }}" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
							<input type="text" name="phone_number" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone_number', $user->phone ?? '') }}" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Kota / Kabupaten</label>
							<input type="text" name="city" class="form-control" placeholder="Nama kota/kabupaten" value="{{ old('city', $user->city ?? '') }}">
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Provinsi / State</label>
							<input type="text" name="state" class="form-control" placeholder="Nama provinsi" value="{{ old('state', $user->state ?? '') }}">
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Kode Pos</label>
							<input type="text" name="postal_code" class="form-control" placeholder="Kode pos" value="{{ old('postal_code', $user->postal_code ?? '') }}">
						</div>
						<div class="col-12">
							<label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
							<textarea name="address_line" class="form-control" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" required>{{ old('address_line', $user->address ?? '') }}</textarea>
						</div>
						<div class="col-12">
							<label class="form-label fw-bold">Catatan / Patokan (Opsional)</label>
							<input type="text" name="notes" class="form-control" placeholder="Contoh: Pagar hitam depan musholla" value="{{ old('notes') }}">
						</div>
						<div class="col-12 pt-2">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="addDefaultSwitch" name="is_default" value="1" {{ $addresses->isEmpty() ? 'checked' : '' }}>
								<label class="form-check-label fw-semibold" for="addDefaultSwitch">Jadikan sebagai alamat pengiriman utama</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-top py-3 px-4">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="mn-btn-1 btn"><span>Simpan Alamat</span></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal: Edit Alamat -->
<div class="modal fade" id="modalEditAddress" tabindex="-1" aria-labelledby="modalEditAddressLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 12px; overflow: hidden;">
			<form id="formEditAddress" action="#" method="POST">
				@csrf
				@method('PUT')
				<div class="modal-header border-bottom py-3 px-4">
					<h5 class="modal-title fw-bold" id="modalEditAddressLabel"><i class="ri-edit-line me-1"></i> Edit Alamat</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label fw-bold">Label Alamat <span class="text-danger">*</span></label>
							<input type="text" id="editLabel" name="label" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Nama Penerima <span class="text-danger">*</span></label>
							<input type="text" id="editRecipient" name="recipient_name" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
							<input type="text" id="editPhone" name="phone_number" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Kota / Kabupaten</label>
							<input type="text" id="editCity" name="city" class="form-control">
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Provinsi / State</label>
							<input type="text" id="editState" name="state" class="form-control">
						</div>
						<div class="col-md-6">
							<label class="form-label fw-bold">Kode Pos</label>
							<input type="text" id="editPostal" name="postal_code" class="form-control">
						</div>
						<div class="col-12">
							<label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
							<textarea id="editAddressLine" name="address_line" class="form-control" rows="3" required></textarea>
						</div>
						<div class="col-12">
							<label class="form-label fw-bold">Catatan / Patokan (Opsional)</label>
							<input type="text" id="editNotes" name="notes" class="form-control">
						</div>
						<div class="col-12 pt-2">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="editDefaultSwitch" name="is_default" value="1">
								<label class="form-check-label fw-semibold" for="editDefaultSwitch">Jadikan sebagai alamat pengiriman utama</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-top py-3 px-4">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="mn-btn-1 btn"><span>Perbarui Alamat</span></button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var editButtons = document.querySelectorAll('.btn-edit-address');
	var editForm = document.getElementById('formEditAddress');
	var baseUrl = "{{ url('/ecommerce/address-list') }}";

	editButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			var id = this.getAttribute('data-id');
			var label = this.getAttribute('data-label') || '';
			var recipient = this.getAttribute('data-recipient') || '';
			var phone = this.getAttribute('data-phone') || '';
			var address = this.getAttribute('data-address') || '';
			var city = this.getAttribute('data-city') || '';
			var state = this.getAttribute('data-state') || '';
			var postal = this.getAttribute('data-postal') || '';
			var notes = this.getAttribute('data-notes') || '';
			var isDefault = this.getAttribute('data-default') === '1';

			editForm.action = baseUrl + '/' + id;
			document.getElementById('editLabel').value = label;
			document.getElementById('editRecipient').value = recipient;
			document.getElementById('editPhone').value = phone;
			document.getElementById('editAddressLine').value = address;
			document.getElementById('editCity').value = city;
			document.getElementById('editState').value = state;
			document.getElementById('editPostal').value = postal;
			document.getElementById('editNotes').value = notes;
			document.getElementById('editDefaultSwitch').checked = isDefault;
		});
	});
});
</script>
@endsection

