{{-- resources/views/components/product-card.blade.php --}}
@props(['product', 'showFavoriteButton' => true])

<div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow transition-all duration-300 hover:transform hover:-translate-y-1">
    <a>
        <div class="relative">
            <img 
                src="{{ $product->image_url }}" 
                alt="{{ $product->title }}" 
                class="w-full h-48 object-cover"
            >
            <div class="absolute top-3 left-3">
                @switch($product->status)
                    @case('available')
                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Còn hàng</span>
                        @break
                    @case('hot')
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Hot</span>
                        @break
                    @case('new')
                        <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Mới</span>
                        @break
                    @case('sale')
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Giảm giá</span>
                        @break
                @endswitch
            </div>
            
            @if($showFavoriteButton && auth()->check())
                <form method="POST" class="absolute top-3 right-3">
                    @csrf
                    <button type="submit" 
                            class="heart-btn w-8 h-8 bg-white rounded-full flex items-center justify-center transition-colors {{ auth()->user()->favorites()->where('product_id', $product->id)->exists() ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                        <i class="fas fa-heart"></i>
                    </button>
                </form>
            @endif
        </div>
        
        <div class="p-4">
            <h4 class="font-semibold text-gray-900 mb-2 line-clamp-1">{{ $product->title }}</h4>
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
            
            <div class="flex justify-between items-center mb-3">
                <span class="text-lg font-bold text-blue-600">{{ number_format($product->price) }}₫</span>
                @if($product->original_price)
                    <span class="text-sm text-gray-500 line-through">{{ number_format($product->original_price) }}₫</span>
                @endif
            </div>
            
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-user mr-1"></i>
                    <span>{{ $product->seller->name }}</span>
                </div>
                <div class="flex text-yellow-400 text-xs">

                </div>
            </div>
            
            <a href="{{ route('products.show',$product->id) }}" class="w-full inline-block text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-cart mr-2"></i>Xem chi tiết
            </a>
        </div>
    </a>
</div>