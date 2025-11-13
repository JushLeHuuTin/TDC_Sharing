<script setup>
import { ref, reactive, computed } from 'vue';
import { faUndo } from '@fortawesome/free-solid-svg-icons';
import { useRouter } from 'vue-router'

const router = useRouter()
// --- PROPS VÀ EMITS ---
const props = defineProps({
    handleSlugChange: Function,
    // Danh sách danh mục (từ Pinia Store)
    categories: {
        type: Array,
        default: () => []
    },
    // Trạng thái lọc hiện tại (Sẽ là v-model)
    selectedFilters: {
        type: Object,
        required: true,
        default: () => ({
            categories: [],
            priceRange: null,
            conditions: [],
            location: '',
            negotiable: false,
            hasImages: false,
            verified: false
        })
    }
});
const handleClickCategory = (slug) => {
  router.push(`/danhmuc/${slug}`)
}
const emit = defineEmits(['update:selectedFilters', 'applyFilters', 'resetFilters']);

// --- DỮ LIỆU CỐ ĐỊNH (Hardcoded Filter Options) ---
const priceRanges = [
    { id: 'under_500k', label: 'Dưới 500K', min: 0, max: 500000 },
    { id: '500k_2m', label: '500K - 2M', min: 500000, max: 2000000 },
    { id: '2m_10m', label: '2M - 10M', min: 2000000, max: 10000000 },
    { id: 'over_10m', label: 'Trên 10M', min: 10000000, max: null },
];

const conditions = [
    { value: 'new', label: 'Mới 100%' },
    { value: 'like_new', label: 'Như mới (95%)' },
    { value: 'good', label: 'Tốt (80%)' },
    { value: 'fair', label: 'Khá (60%)' },
];

const locations = [
    { value: 'district1', label: 'Quận 1' },
    { value: 'district3', label: 'Quận 3' },
    { value: 'district7', label: 'Quận 7' },
    { value: 'thu-duc', label: 'Thủ Đức' },
    { value: 'binh-thanh', label: 'Bình Thạnh' },
];

// --- LOGIC XỬ LÝ SỰ KIỆN ---

// Hàm này được gọi khi bất kỳ input nào thay đổi
const handleFilterChange = () => {
    // 💡 Emit sự kiện để component cha biết cần áp dụng bộ lọc
    emit('applyFilters');
};

const handleResetFilters = () => {
    // 💡 Trong thực tế, bạn nên đặt lại giá trị trong component cha
    emit('resetFilters'); 
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Bộ lọc</h2>
            <button @click="handleResetFilters" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                <fa :icon="['fas', 'undo']" class="mr-1" />Đặt lại
            </button>
        </div>

        <div class="mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Danh mục</h3>
            <div class="space-y-2">
                <label v-for="category in categories" :key="category.id" class="flex items-center">
                    <input type="radio" :value="category.id" v-model="props.selectedFilters.categories" @change="handleClickCategory(category.slug)" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                           
                    <span class="text-sm text-gray-700">{{ category.name }}</span>
                    <span class="ml-auto text-xs text-gray-500">({{ category.count || 0 }})</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Khoảng giá</h3>
            <div class="space-y-2">
                <label v-for="range in priceRanges" :key="range.id" class="flex items-center">
                    <input type="radio" :value="range.id" v-model="props.selectedFilters.priceRange" @change="handleFilterChange" 
                           name="priceRange" class="text-blue-600 focus:ring-blue-500 mr-3">
                           
                    <span class="text-sm text-gray-700">{{ range.label }}</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Tình trạng</h3>
            <div class="space-y-2">
                <label v-for="condition in conditions" :key="condition.value" class="flex items-center">
                    <input type="checkbox" :value="condition.value" v-model="props.selectedFilters.conditions" @change="handleFilterChange" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                           
                    <span class="text-sm text-gray-700">{{ condition.label }}</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Khu vực</h3>
            <select v-model="props.selectedFilters.location" @change="handleFilterChange" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả khu vực</option>
                <option v-for="location in locations" :key="location.value" :value="location.value">
                    {{ location.label }}
                </option>
            </select>
        </div>

        <div class="space-y-3">
            <label class="flex items-center">
                <input type="checkbox" v-model="props.selectedFilters.negotiable" @change="handleFilterChange" 
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Có thể thương lượng</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" v-model="props.selectedFilters.hasImages" @change="handleFilterChange" 
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Có ảnh thật</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" v-model="props.selectedFilters.verified" @change="handleFilterChange" 
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                <span class="text-sm text-gray-700">Người bán uy tín</span>
            </label>
        </div>
    </div>
</template>