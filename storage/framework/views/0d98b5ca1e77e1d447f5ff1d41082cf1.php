
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'showFavoriteButton' => true]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product', 'showFavoriteButton' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>



<div class="bg-white rounded-xl shadow-sm overflow-hidden product-card cursor-pointer" onclick="window.location='<?php echo e(route('products.show', $product->id)); ?>'">
    <!-- Product Image -->
    <div class="relative h-48 bg-gray-100">
        <img src="<?php echo e($product->image ?? 'https://picsum.photos/300/200?random=17'); ?>" 
             alt="<?php echo e($product->title); ?>" 
             class="w-full h-full object-cover">

        <!-- Badges -->
        

        <div class="absolute top-3 right-3 space-y-2">
            <form action="" method="POST" onsubmit="event.stopPropagation()">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        class="bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md hover:scale-110 transition-transform 
                        
                         ">
                    <i class="fas fa-heart"></i>
                </button>
            </form>
        </div>

        <div class="absolute bottom-3 left-3">
            <span class="condition-badge text-xs px-2 py-1 rounded-full font-medium 
                <?php echo e($product->condition == 'new' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'); ?>">
                <?php echo e($product->condition == 'new' ? 'Như mới' : 'Đã qua sử dụng'); ?>

            </span>
        </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
            <?php echo e($product->title); ?>

        </h3>
        
        <!-- Price -->
        <div class="flex items-center justify-between mb-3">
            <div>
                <span class="text-xl font-bold text-blue-600">
                    <?php echo e(number_format($product->price, 0, ',', '.')); ?> ₫
                </span>
                
            </div>
           
        </div>

        <!-- Location & Time -->
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-map-marker-alt mr-1"></i>
                <span><?php echo e($product->location); ?></span>
            </div>
            <span><?php echo e($product->created_at->format('d/m/Y')); ?></span>
        </div>

        <!-- Seller Info -->
        <div class="flex items-center mt-3 pt-3 border-t border-gray-100">
            <img src="<?php echo e($product->seller->avatar ?? 'https://ui-avatars.com/api/?name='.$product->seller->name); ?>" 
                 alt="<?php echo e($product->seller->name); ?>" 
                 class="w-6 h-6 rounded-full mr-2">
            <span class="text-sm text-gray-700 flex-1"><?php echo e($product->seller->name); ?></span>
            <div class="flex items-center">
                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                <span class="text-xs text-gray-600">4</span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\TDC_Sharing\TDC_Sharing\resources\views/components/product-card.blade.php ENDPATH**/ ?>