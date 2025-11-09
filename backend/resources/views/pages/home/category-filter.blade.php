{{-- resources/views/components/home/category-filter.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
    <!-- Filter Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Bộ lọc</h2>
        <button @click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800">
            <i class="fas fa-undo mr-1"></i>Đặt lại
        </button>
    </div>

    <!-- Categories -->
    <div class="mb-6">
        <h3 class="font-medium text-gray-900 mb-3">Danh mục</h3>
        <div class="space-y-2">
            <template x-for="category in categories" :key="category.id">
                <label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                    <span class="text-sm text-gray-700" x-text="category.name"></span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'"></span>
                </label>
            </template><label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="1">
                    <span class="text-sm text-gray-700" x-text="category.name">Điện tử</span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'">(234)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="2">
                    <span class="text-sm text-gray-700" x-text="category.name">Sách vở</span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'">(189)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="3">
                    <span class="text-sm text-gray-700" x-text="category.name">Thời trang</span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'">(156)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="4">
                    <span class="text-sm text-gray-700" x-text="category.name">Đồ dùng</span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'">(98)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="5">
                    <span class="text-sm text-gray-700" x-text="category.name">Xe cộ</span>
                    <span class="ml-auto text-xs text-gray-500" x-text="'(' + category.count + ')'">(67)</span>
                </label>
        </div>
    </div>

    <!-- Price Range -->
    <div class="mb-6">
        <h3 class="font-medium text-gray-900 mb-3">Khoảng giá</h3>
        <div class="space-y-2">
            <template x-for="range in priceRanges" :key="range.id">
                <label class="flex items-center">
                    <input type="radio" :value="range.id" x-model="selectedPriceRange" @change="applyFilters" name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3">
                    <span class="text-sm text-gray-700" x-text="range.label"></span>
                </label>
            </template><label class="flex items-center">
                    <input type="radio" :value="range.id" x-model="selectedPriceRange" @change="applyFilters" name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3" value="1">
                    <span class="text-sm text-gray-700" x-text="range.label">Dưới 500K</span>
                </label><label class="flex items-center">
                    <input type="radio" :value="range.id" x-model="selectedPriceRange" @change="applyFilters" name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3" value="2">
                    <span class="text-sm text-gray-700" x-text="range.label">500K - 2M</span>
                </label><label class="flex items-center">
                    <input type="radio" :value="range.id" x-model="selectedPriceRange" @change="applyFilters" name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3" value="3">
                    <span class="text-sm text-gray-700" x-text="range.label">2M - 10M</span>
                </label><label class="flex items-center">
                    <input type="radio" :value="range.id" x-model="selectedPriceRange" @change="applyFilters" name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3" value="4">
                    <span class="text-sm text-gray-700" x-text="range.label">Trên 10M</span>
                </label>
        </div>
    </div>

    <!-- Condition -->
    <div class="mb-6">
        <h3 class="font-medium text-gray-900 mb-3">Tình trạng</h3>
        <div class="space-y-2">
            <template x-for="condition in conditions" :key="condition.value">
                <label class="flex items-center">
                    <input type="checkbox" :value="condition.value" x-model="selectedConditions" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                    <span class="text-sm text-gray-700" x-text="condition.label"></span>
                </label>
            </template><label class="flex items-center">
                    <input type="checkbox" :value="condition.value" x-model="selectedConditions" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="new">
                    <span class="text-sm text-gray-700" x-text="condition.label">Mới 100%</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="condition.value" x-model="selectedConditions" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="like_new">
                    <span class="text-sm text-gray-700" x-text="condition.label">Như mới (95%)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="condition.value" x-model="selectedConditions" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="good">
                    <span class="text-sm text-gray-700" x-text="condition.label">Tốt (80%)</span>
                </label><label class="flex items-center">
                    <input type="checkbox" :value="condition.value" x-model="selectedConditions" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3" value="fair">
                    <span class="text-sm text-gray-700" x-text="condition.label">Khá (60%)</span>
                </label>
        </div>
    </div>

    <!-- Location -->
    <div class="mb-6">
        <h3 class="font-medium text-gray-900 mb-3">Khu vực</h3>
        <select x-model="selectedLocation" @change="applyFilters" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả khu vực</option>
            <template x-for="location in locations" :key="location.value">
                <option :value="location.value" x-text="location.label"></option>
            </template><option :value="location.value" x-text="location.label" value="district1">Quận 1</option><option :value="location.value" x-text="location.label" value="district3">Quận 3</option><option :value="location.value" x-text="location.label" value="district7">Quận 7</option><option :value="location.value" x-text="location.label" value="thu-duc">Thủ Đức</option><option :value="location.value" x-text="location.label" value="binh-thanh">Bình Thạnh</option>
        </select>
    </div>

    <!-- Additional Filters -->
    <div class="space-y-3">
        <label class="flex items-center">
            <input type="checkbox" x-model="filters.negotiable" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
            <span class="text-sm text-gray-700">Có thể thương lượng</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" x-model="filters.hasImages" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
            <span class="text-sm text-gray-700">Có ảnh thật</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" x-model="filters.verified" @change="applyFilters" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
            <span class="text-sm text-gray-700">Người bán uy tín</span>
        </label>
    </div>
</div>
