<script setup>
import { ref, reactive, computed } from 'vue';
import { faUndo } from '@fortawesome/free-solid-svg-icons';
import { useRouter } from 'vue-router'
import { useCategoryStore } from '@/stores/categoryStore';

const categoryStore = useCategoryStore();
const router = useRouter()

// const localFilters = reactive({ ...props.selectedFilters });
const emit = defineEmits(['handleFilterChange']);

const handleClickCategory = (slug) => {
  router.push(`/danhmuc/${slug}`)
}
// const emit = defineEmits(['update:selectedFilters', 'applyFilters', 'resetFilters'], { ...localFilters });

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

const handleResetFilters = () => {
    // 💡 Trong thực tế, bạn nên đặt lại giá trị trong component cha
    emit('resetFilters'); 
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">

        <!-- Reset -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Bộ lọc</h2>
            <button @click="props.handleResetFilters" class="text-sm text-blue-600 hover:text-blue-800">
                <fa :icon="['fas', 'undo']" class="mr-1" />Đặt lại
            </button>
        </div>

        <!-- Danh mục -->
        <div class="mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Danh mục</h3>
            <label 
                v-for="category in categoryStore.flattenedCategories" 
                :key="category.id" 
                class="flex items-center"
            >
                <input 
                    type="radio"
                    name="category"
                    :value="category.id"
                    v-model="categoryStore.filters.categoryId"
                    @change="handleClickCategory(category.slug)"
                    class="mr-3"
                >
                <span>{{ category.name }}</span>
                <span class="ml-auto text-xs text-gray-500">({{ category.count || 0 }})</span>
            </label>
        </div>

        <!-- Giá -->
        <div class="mb-6">
            <h3 class="font-medium mb-3">Khoảng giá</h3>
            <label v-for="range in priceRanges" :key="range.id" class="flex items-center">
                <input 
                    type="radio"
                    name="priceRange"
                    :value="range"
                    v-model="categoryStore.filters.priceRange"
                    @change="emit('handleFilterChange')"
                    class="mr-3"
                >
                {{ range.label }}
            </label>
        </div>

        <!-- Tình trạng -->
        <div class="mb-6">
            <h3 class="font-medium mb-3">Tình trạng</h3>
            <label v-for="c in conditions" :key="c.value" class="flex items-center">
                <input 
                    type="checkbox"
                    :value="c.value"
                    v-model="categoryStore.filters.conditions"
                    @change="emit('handleFilterChange')"
                    class="mr-3"
                >
                {{ c.label }}
            </label>
        </div>

        <!-- Khu vực -->
        <div class="mb-6">
            <h3 class="font-medium mb-3">Khu vực</h3>
            <select 
                v-model="categoryStore.filters.location"
                @change="emit('handleFilterChange')"
                class="w-full px-3 py-2 border rounded-lg"
            >
                <option value="">Tất cả khu vực</option>
                <option v-for="l in locations" :key="l.value" :value="l.value">
                    {{ l.label }}
                </option>
            </select>
        </div>

        <!-- Khác -->
        <div class="space-y-3">
            <label class="flex items-center">
                <input 
                    type="checkbox"
                    v-model="categoryStore.filters.negotiable"
                    @change="emit('handleFilterChange')"
                    class="mr-3"
                >
                Có thể thương lượng
            </label>

            <label class="flex items-center">
                <input 
                    type="checkbox"
                    v-model="categoryStore.filters.hasImages"
                    @change="emit('handleFilterChange')"
                    class="mr-3"
                >
                Có ảnh thật
            </label>

            <label class="flex items-center">
                <input 
                    type="checkbox"
                    v-model="categoryStore.filters.verified"
                    @change="emit('handleFilterChange')"
                    class="mr-3"
                >
                Người bán uy tín
            </label>
        </div>
    </div>
</template>
