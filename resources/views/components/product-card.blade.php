{{-- resources/views/components/product-card.blade.php --}}
@props(['product', 'showFavoriteButton' => true])



<div class="bg-white rounded-xl shadow-sm overflow-hidden product-card cursor-pointer" onclick="window.location='{{ route('products.show', $product->id) }}'">
    <!-- Product Image -->
    <div class="relative h-48 bg-gray-100">
        <img src="{{ $product->image ?? 'https://picsum.photos/300/200?random=17' }}" 
             alt="{{ $product->title }}" 
             class="w-full h-full object-cover">

        <!-- Badges -->
        {{-- <div class="absolute top-3 left-3">
            @if($product->is_featured)
                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    <i class="fas fa-star mr-1"></i>Nổi bật
                </span>
            @endif
        </div> --}}

        <div class="absolute top-3 right-3 space-y-2">
            <form action="" method="POST" onsubmit="event.stopPropagation()">
                @csrf
                <button type="submit" 
                        class="bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md hover:scale-110 transition-transform 
                        {{-- {{ $product->is_favorited ? 'text-red-500' : 'text-gray-400' }} --}}
                         ">
                    <i class="fas fa-heart"></i>
                </button>
            </form>
        </div>

        <div class="absolute bottom-3 left-3">
            <span class="condition-badge text-xs px-2 py-1 rounded-full font-medium 
                {{ $product->condition == 'new' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $product->condition == 'new' ? 'Như mới' : 'Đã qua sử dụng' }}
            </span>
        </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ $product->title }}
        </h3>
        
        <!-- Price -->
        <div class="flex items-center justify-between mb-3">
            <div>
                <span class="text-xl font-bold text-blue-600">
                    {{ number_format($product->price, 0, ',', '.') }} ₫
                </span>
                {{-- @if($product->original_price)
                    <span class="text-sm text-gray-500 line-through ml-2">
                        {{ number_format($product->original_price, 0, ',', '.') }} ₫
                    </span>
                @endif --}}
            </div>
           
        </div>

        <!-- Location & Time -->
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-map-marker-alt mr-1"></i>
                <span>{{ $product->location }}</span>
            </div>
            <span>{{ $product->created_at->format('d/m/Y') }}</span>
        </div>

        <!-- Seller Info -->
        <div class="flex items-center mt-3 pt-3 border-t border-gray-100">
            <img src="{{ $product->seller->avatar ?? 'https://ui-avatars.com/api/?name='.$product->seller->name }}" 
                 alt="{{ $product->seller->name }}" 
                 class="w-6 h-6 rounded-full mr-2">
            <span class="text-sm text-gray-700 flex-1">{{ $product->seller->name }}</span>
            <div class="flex items-center">
                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                <span class="text-xs text-gray-600">4</span>
            </div>
        </div>
    </div>
</div>
