{{-- resources/views/components/home/category-filter.blade.php --}}
<div class="bg-white rounded-lg shadow-sm p-6 mb-8" x-data="categoryFilter()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Lọc theo danh mục</h2>
        <button 
            @click="resetFilters"
            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-undo mr-1"></i>Đặt lại
        </button>
    </div>

    <!-- Main Categories Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <template x-for="category in mainCategories" :key="category.id">
            <div 
                @click="selectCategory(category)"
                :class="selectedCategory?.id === category.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'hover:shadow-md'"
                class="bg-white border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-200 text-center group">
                
                <!-- Category Icon -->
                <div 
                    :class="selectedCategory?.id === category.id ? 'bg-blue-500 text-white' : category.color + ' text-white group-hover:scale-110'"
                    class="w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 transition-transform duration-200">
                    <i :class="category.icon" class="text-lg"></i>
                </div>
                
                <!-- Category Name -->
                <h3 
                    :class="selectedCategory?.id === category.id ? 'text-blue-700' : 'text-gray-900'"
                    class="font-semibold mb-1" 
                    x-text="category.name">
                </h3>
                
                <!-- Product Count -->
                <p class="text-sm text-gray-500" x-text="category.count + ' sản phẩm'"></p>
            </div>
        </template>
    </div>

    <!-- Subcategories (shown when main category is selected) -->
    <div x-show="selectedCategory && selectedCategory.subcategories.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="border-t border-gray-200 pt-6">
        
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Danh mục con của <span x-text="selectedCategory?.name" class="text-blue-600"></span>
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <template x-for="subcategory in selectedCategory?.subcategories || []" :key="subcategory.id">
                <button 
                    @click="selectSubcategory(subcategory)"
                    :class="selectedSubcategory?.id === subcategory.id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <span x-text="subcategory.name"></span>
                    <span class="ml-1 text-xs opacity-75" x-text="'(' + subcategory.count + ')'"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Price Range Filter -->
    <div class="border-t border-gray-200 pt-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Khoảng giá</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <template x-for="range in priceRanges" :key="range.id">
                <button 
                    @click="selectPriceRange(range)"
                    :class="selectedPriceRange?.id === range.id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                    x-text="range.label">
                </button>
            </template>
        </div>
        
        <!-- Custom Price Range -->
        <div class="flex items-center space-x-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ (₫)</label>
                <input 
                    type="number" 
                    x-model="customPriceFrom"
                    @input="updateCustomPrice"
                    placeholder="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến (₫)</label>
                <input 
                    type="number" 
                    x-model="customPriceTo"
                    @input="updateCustomPrice"
                    placeholder="Không giới hạn"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Additional Filters -->
    <div class="border-t border-gray-200 pt-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bộ lọc khác</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Condition Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tình trạng</label>
                <select 
                    x-model="selectedCondition"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả tình trạng</option>
                    <option value="new">Mới 100%</option>
                    <option value="like_new">Như mới (95%)</option>
                    <option value="good">Tốt (80%)</option>
                    <option value="fair">Khá (60%)</option>
                </select>
            </div>
            
            <!-- Location Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Khu vực</label>
                <select 
                    x-model="selectedLocation"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả khu vực</option>
                    <option value="district1">Quận 1</option>
                    <option value="district3">Quận 3</option>
                    <option value="district5">Quận 5</option>
                    <option value="district7">Quận 7</option>
                    <option value="thu-duc">Thủ Đức</option>
                    <option value="binh-thanh">Bình Thạnh</option>
                    <option value="tan-binh">Tân Bình</option>
                </select>
            </div>
            
            <!-- Sort By -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                <select 
                    x-model="sortBy"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="newest">Mới nhất</option>
                    <option value="price_low">Giá thấp đến cao</option>
                    <option value="price_high">Giá cao đến thấp</option>
                    <option value="popular">Phổ biến nhất</option>
                    <option value="nearest">Gần nhất</option>
                </select>
            </div>
        </div>
        
        <!-- Checkbox Filters -->
        <div class="mt-4 space-y-3">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    x-model="filters.negotiable"
                    @change="applyFilters"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Có thể thương lượng</span>
            </label>
            
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    x-model="filters.delivery"
                    @change="applyFilters"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Hỗ trợ giao hàng</span>
            </label>
            
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    x-model="filters.hasImages"
                    @change="applyFilters"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Có ảnh thật</span>
            </label>
            
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    x-model="filters.verified"
                    @change="applyFilters"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Người bán đã xác thực</span>
            </label>
        </div>
    </div>

    <!-- Apply Filters Button -->
    <div class="border-t border-gray-200 pt-6 mt-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span x-text="filteredCount"></span> sản phẩm phù hợp
            </div>
            <div class="space-x-3">
                <button 
                    @click="resetFilters"
                    class="px-4 py-2  border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    Đặt lại
                </button>
                <button 
                    @click="applyFilters"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Áp dụng bộ lọc
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function categoryFilter() {
    return {
        selectedCategory: null,
        selectedSubcategory: null,
        selectedPriceRange: null,
        selectedCondition: '',
        selectedLocation: '',
        sortBy: 'newest',
        customPriceFrom: '',
        customPriceTo: '',
        filteredCount: 1234,
        
        filters: {
            negotiable: false,
            delivery: false,
            hasImages: false,
            verified: false
        },
        
        mainCategories: [
            {
                id: 1,
                name: 'Điện tử',
                icon: 'fas fa-laptop',
                color: 'bg-blue-500',
                count: 234,
                subcategories: [
                    { id: 11, name: 'Laptop', count: 89 },
                    { id: 12, name: 'Điện thoại', count: 67 },
                    { id: 13, name: 'Tablet', count: 34 },
                    { id: 14, name: 'Tai nghe', count: 28 },
                    { id: 15, name: 'Phụ kiện', count: 16 }
                ]
            },
            {
                id: 2,
                name: 'Sách vở',
                icon: 'fas fa-book',
                color: 'bg-green-500',
                count: 189,
                subcategories: [
                    { id: 21, name: 'Sách giáo khoa', count: 78 },
                    { id: 22, name: 'Sách tham khảo', count: 45 },
                    { id: 23, name: 'Tiểu thuyết', count: 34 },
                    { id: 24, name: 'Văn phòng phẩm', count: 32 }
                ]
            },
            {
                id: 3,
                name: 'Thời trang',
                icon: 'fas fa-tshirt',
                color: 'bg-pink-500',
                count: 156,
                subcategories: [
                    { id: 31, name: 'Áo thun', count: 45 },
                    { id: 32, name: 'Áo hoodie', count: 38 },
                    { id: 33, name: 'Quần jeans', count: 29 },
                    { id: 34, name: 'Giày dép', count: 44 }
                ]
            },
            {
                id: 4,
                name: 'Đồ dùng',
                icon: 'fas fa-home',
                color: 'bg-purple-500',
                count: 98,
                subcategories: [
                    { id: 41, name: 'Bàn ghế', count: 34 },
                    { id: 42, name: 'Đồ trang trí', count: 28 },
                    { id: 43, name: 'Đồ gia dụng', count: 36 }
                ]
            },
            {
                id: 5,
                name: 'Xe cộ',
                icon: 'fas fa-bicycle',
                color: 'bg-orange-500',
                count: 67,
                subcategories: [
                    { id: 51, name: 'Xe đạp', count: 34 },
                    { id: 52, name: 'Xe máy', count: 23 },
                    { id: 53, name: 'Phụ tung', count: 10 }
                ]
            },
            {
                id: 6,
                name: 'Khác',
                icon: 'fas fa-ellipsis-h',
                color: 'bg-gray-500',
                count: 45,
                subcategories: [
                    { id: 61, name: 'Thể thao', count: 20 },
                    { id: 62, name: 'Sở thích', count: 15 },
                    { id: 63, name: 'Khác', count: 10 }
                ]
            }
        ],
        
        priceRanges: [
            { id: 1, label: 'Dưới 500K', min: 0, max: 500000 },
            { id: 2, label: '500K - 2M', min: 500000, max: 2000000 },
            { id: 3, label: '2M - 10M', min: 2000000, max: 10000000 },
            { id: 4, label: 'Trên 10M', min: 10000000, max: null }
        ],
        
        selectCategory(category) {
            if (this.selectedCategory?.id === category.id) {
                this.selectedCategory = null;
                this.selectedSubcategory = null;
            } else {
                this.selectedCategory = category;
                this.selectedSubcategory = null;
            }
            this.applyFilters();
        },
        
        selectSubcategory(subcategory) {
            if (this.selectedSubcategory?.id === subcategory.id) {
                this.selectedSubcategory = null;
            } else {
                this.selectedSubcategory = subcategory;
            }
            this.applyFilters();
        },
        
        selectPriceRange(range) {
            if (this.selectedPriceRange?.id === range.id) {
                this.selectedPriceRange = null;
            } else {
                this.selectedPriceRange = range;
                this.customPriceFrom = '';
                this.customPriceTo = '';
            }
            this.applyFilters();
        },
        
        updateCustomPrice() {
            if (this.customPriceFrom || this.customPriceTo) {
                this.selectedPriceRange = null;
            }
            this.applyFilters();
        },
        
        applyFilters() {
            // Simulate filtering logic
            let count = 1234;
            
            if (this.selectedCategory) count = Math.floor(count * 0.7);
            if (this.selectedSubcategory) count = Math.floor(count * 0.5);
            if (this.selectedPriceRange || this.customPriceFrom || this.customPriceTo) count = Math.floor(count * 0.6);
            if (this.selectedCondition) count = Math.floor(count * 0.8);
            if (this.selectedLocation) count = Math.floor(count * 0.4);
            
            Object.values(this.filters).forEach(filter => {
                if (filter) count = Math.floor(count * 0.9);
            });
            
            this.filteredCount = Math.max(count, 0);
            
            // In real app, this would trigger a search/filter request
            console.log('Applying filters:', {
                category: this.selectedCategory,
                subcategory: this.selectedSubcategory,
                priceRange: this.selectedPriceRange,
                customPrice: { from: this.customPriceFrom, to: this.customPriceTo },
                condition: this.selectedCondition,
                location: this.selectedLocation,
                sortBy: this.sortBy,
                filters: this.filters
            });
        },
        
        resetFilters() {
            this.selectedCategory = null;
            this.selectedSubcategory = null;
            this.selectedPriceRange = null;
            this.selectedCondition = '';
            this.selectedLocation = '';
            this.sortBy = 'newest';
            this.customPriceFrom = '';
            this.customPriceTo = '';
            this.filters = {
                negotiable: false,
                delivery: false,
                hasImages: false,
                verified: false
            };
            this.filteredCount = 1234;
        }
    }
}
</script>
