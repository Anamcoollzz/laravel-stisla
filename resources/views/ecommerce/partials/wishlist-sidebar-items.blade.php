@forelse ($wishlistProducts as $item)
	<li class="wishlist-sidebar-list" data-product-id="{{ $item->id }}">
		<a href="{{ route('ecommerce.products.detail', $item->slug) }}" class="mn-pro-img">
			<img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}">
		</a>
		<div class="mn-pro-content">
			<a href="{{ route('ecommerce.products.detail', $item->slug) }}" class="wishlist-pro-title">{{ $item->title }}</a>
			<span class="wishlist-price">
				<span>Rp {{ number_format($item->final_price, 0, ',', '.') }}</span>
				<span class="stock">- {{ $item->stock }} in Stock</span>
			</span>
			<a href="javascript:void(0)" class="wishlist-remove-item" data-product-id="{{ $item->id }}" title="Remove from Wishlist">×</a>
		</div>
	</li>
@empty
	<li class="wishlist-empty-msg text-center p-3">
		<p class="mn-wishlist-msg m-0">Your Wishlist is empty!</p>
	</li>
@endforelse

