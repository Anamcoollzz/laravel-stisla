@forelse ($cartItems as $item)
	<li class="cart-sidebar-list" data-product-id="{{ $item['id'] }}">
		<a href="{{ route('ecommerce.products.detail', $item['slug']) }}" class="mn-pro-img">
			<img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}">
		</a>
		<div class="mn-pro-content">
			<a href="{{ route('ecommerce.products.detail', $item['slug']) }}" class="cart-pro-title">{{ $item['title'] }}</a>
			<span class="cart-price">
				<span>Rp {{ number_format($item['price'], 0, ',', '.') }}</span> x <span class="cart-item-qty">{{ $item['quantity'] }}</span>
			</span>
			<div class="qty-plus-minus cart-item-qty-control" data-product-id="{{ $item['id'] }}">
				<div class="dec mn-qtybtn">-</div>
				<input class="qty-input cart-qty-input" type="text" name="mn-qtybtn" value="{{ $item['quantity'] }}" data-product-id="{{ $item['id'] }}">
				<div class="inc mn-qtybtn">+</div>
			</div>
			<a href="javascript:void(0)" class="cart-remove-item" data-product-id="{{ $item['id'] }}" title="Remove item">×</a>
		</div>
	</li>
@empty
	<li class="cart-empty-msg text-center p-3">
		<p class="mn-cart-msg m-0">Your Cart is empty!</p>
	</li>
@endforelse
