<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';
import CategoryChooserModal from '@/pages/Client/products/CategoryChooserModal.vue';
import { faCloudUploadAlt, faPlus, faEdit, faRocket, faSave, faTimes, faChevronDown, faSpinner } from '@fortawesome/free-solid-svg-icons'; // Import các icon cần thiết
import { storeToRefs } from 'pinia';

import { useAuthStore } from '@/stores/auth';
import { useProductStore } from '@/stores/productStore';
import { useCategoryStore } from '@/stores/categoryStore';

import axios from 'axios';
import { getCurrentInstance } from 'vue';
// --- STORES VÀ SETUP ---
const router = useRouter();

const authStore = useAuthStore();
const productStore = useProductStore();
const categoryStore = useCategoryStore();

const { submissionError, isCreating } = storeToRefs(productStore);
const { categoriesTree, isLoading: isLoadingCategories, error: categoryError } = storeToRefs(categoryStore);
const { user, isLoggedIn, isAdmin } = storeToRefs(authStore);
const { dynamicAttributes, isLoadingAttributes } = storeToRefs(categoryStore);
const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;

// state cua form
const form = reactive({
    title: '',
    description: '',
    category_id: '',
    stocks: 1,
    price: null,
    attributes: [],
});
// show category modal
const showCategoryModal = ref(false);
// --- STATE CỤC BỘ ---
const maxTitleLength = 100;
const maxDescriptionLength = 2000;
const imageFiles = ref(new Array(7).fill(null));
const imagePreviews = ref(new Array(7).fill(null));
const isSubmitting = ref(false);
const errorMessages = ref({});
// --- COMPUTED PROPERTIES ---
const titleCount = computed(() => form.title.length);
const descCount = computed(() => form.description.length);
let stopWatcher;
watch(() => form.category_id, async (newId) => {
    if (newId) {
        const mappedData = await categoryStore.fetchDynamicAttributes(newId);
        console.log(dynamicAttributes.value);
        form.attributes = mappedData;
    } else {
        dynamicAttributes.value = [];
        form.attributes = [];
    }
});
// --- XỬ LÝ SUBMIT (Cần gửi Attribute Values) ---
const submitForm = async (action) => {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('category_id', form.category_id);
    formData.append('stocks', form.stocks);
    formData.append('price', form.price);
    formData.append('featured_image_index', 0);

    imageFiles.value.forEach((file, index) => {
        if (file) {
            formData.append(`images[${index}]`, file);
        }
    });
    form.attributes.forEach((attr, index) => {
        const def = dynamicAttributes.value[index];
        const value = attr.value?.trim() ?? '';

        // Kiểm tra bắt buộc
        if (def.required && value === '') {
            alert(`Thuộc tính "${def.label || def.name}" là bắt buộc.`);
            isSubmitting.value = false;
            throw new Error('Thiếu thuộc tính bắt buộc');
        }

        // Append vào formData nếu có giá trị
        console.log(attr);
        formData.append(`attributes[${index}][attribute_id]`, attr.attribute_id);
        formData.append(`attributes[${index}][value]`, value);
    });
    errorMessages.value = {};
    productStore.submissionError = null;

    try {
        const newProduct = await productStore.createProduct(formData);
        // 4. Xử lý thành công
        $toast.success(`Đăng bán thành công!`);
        router.push({ name: 'products.my' });

    } catch (e) {
        if (e.message === 'Validation Failed') {
            errorMessages.value = productStore.submissionError;
            $toast.warning('Lỗi xác thực dữ liệu! Vui lòng kiểm tra các trường đã tô đỏ.');
            console.log(errorMessages.value);
        } else if (e.message === 'Unauthorized') {
            // Đã được xử lý chuyển hướng trong Store
            alert('Phiên làm việc đã hết hạn.');
        } else {
            const generalError = productStore.submissionError?.general?.[0] || 'Lỗi không xác định.';
            $toast.error(`Lỗi hệ thống: ${generalError}`);
        }
    }
};

const handleInitialCategorySelected = (selectedId) => {
    showCategoryModal.value = false;

    if (selectedId) {
        form.category_id = selectedId;
        console.log(`Danh mục đã chọn: ID ${selectedId}. Bắt đầu tải thuộc tính động.`);

    } else {
        router.push('/');
    }
};
const handleImageChange = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        imageFiles.value[index] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value[index] = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        imageFiles.value[index] = null;
        imagePreviews.value[index] = null;
    }
};

// Xóa ảnh đã chọn
const removeImage = (index) => {
    imageFiles.value[index] = null;
    imagePreviews.value[index] = null;
    // Đặt lại giá trị của input file
    const inputElement = document.getElementById(index === 0 ? 'mainImage' : `image${index}`);
    if (inputElement) {
        inputElement.value = null;
    }
};
// --- LIFECYCLE HOOKS (Giữ nguyên) ---
onMounted(() => {
    categoryStore.fetchCategories();
    stopWatcher = watch(isLoadingCategories, (newVal) => {
        if (newVal === false && !form.category_id) {
            setTimeout(() => { showCategoryModal.value = true; }, 100);
            if (stopWatcher) {
                stopWatcher();
            }
        }
    }, { immediate: true }); // Chạy ngay lập tức khi component mount
});

</script>
<template>
    <AppLayout :user="user" title="Đăng bán sản phẩm - TDC_Sharing">
        <CategoryChooserModal :is-visible="showCategoryModal" :on-category-selected="handleInitialCategorySelected" />
        <div class="max-w-4xl mx-auto">
            <div v-if="form.category_id" class="bg-white rounded-lg shadow-xl p-6">
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Đăng bán sản phẩm</h1>
                    <p class="text-gray-600">Danh mục:
                        <span class="text-blue-600 fw-bold">{{categoriesTree.find(c => c.id ==
                            form.category_id)?.name}}</span>
                    </p>
                </div>

                <form @submit.prevent class="space-y-8">
                    <!-- html hinh anh -->
                    <div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4" id="imagePreview">

                            <div class="col-span-2 md:col-span-2 row-span-2 relative">
                                <label for="mainImage"
                                    :class="['upload-area group transition-all duration-300', { 'uploaded-main': imagePreviews[0] }]"
                                    :style="{ backgroundImage: imagePreviews[0] ? `url(${imagePreviews[0]})` : 'none' }"
                                    class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">

                                    <div v-if="!imagePreviews[0]"
                                        class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <fa :icon="faCloudUploadAlt" class="text-4xl text-gray-400 mb-4" />
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Ảnh chính (Bắt
                                                buộc)</span></p>
                                        <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
                                    </div>
                                    <input id="mainImage" name="images[]" type="file" class="hidden" accept="image/*"
                                        @change="handleImageChange($event, 0)">

                                    <button v-if="imagePreviews[0]" @click.prevent="removeImage(0)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 z-10 hover:bg-red-700 transition-colors shadow-md remove-btn-main">
                                        <fa :icon="faTimes" class="w-3 h-3" />
                                    </button>
                                    <div v-if="imagePreviews[0]" class="image-overlay group-hover:opacity-100">
                                        <fa :icon="faEdit" class="text-white text-3xl" />
                                    </div>

                                </label>
                            </div>

                            <div v-for="i in 4" :key="i" class="aspect-square relative">
                                <label :for="`image${i}`"
                                    :class="['upload-area group transition-all duration-300', { 'uploaded-sub': imagePreviews[i] }]"
                                    :style="{ backgroundImage: imagePreviews[i] ? `url(${imagePreviews[i]})` : 'none' }"
                                    class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">

                                    <fa v-if="!imagePreviews[i]" :icon="faPlus" class="text-2xl text-gray-400" />
                                    <input :id="`image${i}`" :name="`images[${i}]`" type="file" class="hidden"
                                        accept="image/*" @change="handleImageChange($event, i)">

                                    <button v-if="imagePreviews[i]" @click.prevent="removeImage(i)" type="button"
                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 z-10 hover:bg-red-700 transition-colors text-xs shadow-md">
                                        <fa :icon="faTimes" class="w-3 h-3" />
                                    </button>

                                    <div v-if="imagePreviews[i]" class="image-overlay-sub group-hover:opacity-100">
                                    </div>
                                </label>
                                <p v-if="errorMessages['images.' + i]"
                                    class="text-sm text-red-600 mt-1 absolute bottom-[-20px] left-0">
                                    {{ errorMessages['images.' + i][0] }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">Tối đa 5 ảnh (1 ảnh chính + 4 ảnh phụ). Ảnh chính là bắt buộc.
                        </p>
                        <p v-if="errorMessages['images.0']" class="text-sm text-red-600 mt-1">{{
                            errorMessages['images.0'][0] }}</p>
                    </div>
                    <!-- title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tiêu đề <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required v-model="form.title"
                            :maxlength="maxTitleLength"
                            :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['title'] ? 'border-red-500' : 'border-gray-300']"
                            placeholder="VD: Giáo trình Lập trình Hướng đối tượng C++, còn mới 95%">
                        <div class="flex justify-between mt-1">
                            <p v-if="errorMessages['title']" class="text-sm text-red-600">{{ errorMessages['title'][0]
                            }}</p>
                            <p v-else class="text-sm text-gray-500">Tiêu đề hấp dẫn sẽ thu hút nhiều người mua hơn</p>
                            <span class="text-sm text-gray-500" id="titleCount">{{ titleCount }}/{{ maxTitleLength
                            }}</span>
                        </div>
                    </div>
                    <!-- category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Danh mục <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category_id" required v-model="form.category_id"
                                :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['category_id'] ? 'border-red-500' : 'border-gray-300']">
                                <option value="">Chọn danh mục</option>
                                <option v-for="cat in categoriesTree" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="errorMessages['category_id']" class="mt-1 text-sm text-red-600">{{
                                errorMessages['category_id'][0] }}</p>
                        </div>

                        <div>
                            <label for="stocks" class="block text-sm font-medium text-gray-700 mb-2">
                                Số lượng <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="stocks" name="stocks" required min="1" step="1"
                                    v-model.number="form.stocks"
                                    :class="['w-full pl-3 pr-12 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['stocks'] ? 'border-red-500' : 'border-gray-300']"
                                    placeholder="1" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">sản phẩm</span>
                                </div>
                            </div>
                            <p v-if="errorMessages['stocks']" class="mt-1 text-sm text-red-600">{{
                                errorMessages['stocks'][0] }}</p>
                        </div>
                    </div>
                    <!-- price -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Giá bán <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="price" name="price" required min="0" v-model="form.price"
                                    @input="formatPriceInput"
                                    :class="['w-full pl-3 pr-12 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['price'] ? 'border-red-500' : 'border-gray-300']"
                                    placeholder="0">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₫</span>
                                </div>
                            </div>
                            <p v-if="errorMessages['price']" class="mt-1 text-sm text-red-600">{{
                                errorMessages['price'][0] }}</p>
                        </div>

                        <div class="col-span-1 border-l pl-5">
                            <h6 class="text-sm font-semibold text-gray-700 mb-2">Thuộc tính chi tiết</h6>

                            <div v-if="isLoadingAttributes" class="text-center text-gray-500 pt-3">
                                <fa :icon="faSpinner" class="fa-spin me-2" /> Đang tải thuộc tính...
                            </div>

                            <div v-else-if="!dynamicAttributes || dynamicAttributes.length === 0"
                                class="text-sm text-muted pt-3">
                                Không có thuộc tính bổ sung cho danh mục này.
                            </div>

                            <div v-else class="pt-3">
                                <div v-for="(attr, index) in dynamicAttributes" :key="attr.id" class="mb-3">
                                    <template v-if="form.attributes[index]">
                                    <label :for="attr.name" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ attr.label }}
                                        <span v-if="attr.required" class="text-red-500">*</span>
                                    </label>

                                    <template v-if="attr.data_type === 'select' && attr.attributesOptions">
                                        <select :id="attr.name" v-model="form.attributes[index].value"
                                            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">-- Chọn {{ attr.label }} --</option>
                                            <option v-for="option in attr.attributesOptions" :key="option.value"
                                                :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                    </template>

                                    <template v-else-if="attr.data_type === 'NUMBER'">
                                        <input type="number" :id="attr.name" v-model="form.attributes[index].value"
                                            class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :placeholder="attr.placeholder || 'Nhập số'" />
                                    </template>

                                    <!-- Mặc định: text -->
                                    <template v-else>
                                        <input type="text" :id="attr.name" v-model="form.attributes[index].value"
                                            class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :placeholder="attr.placeholder || ''" />
                                    </template>
                                    <p v-if="errorMessages['attributes.' + index + '.value']"
                                        class="text-sm text-red-600">
                                        {{ errorMessages['attributes.' + index + '.value'][0] }}
                                    </p>
                                </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả chi tiết <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="6" required v-model="form.description"
                            :maxlength="maxDescriptionLength"
                            :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['description'] ? 'border-red-500' : 'border-gray-300']"
                            placeholder="Mô tả chi tiết về sản phẩm: Tình trạng hiện tại, lý do bán, phụ kiện kèm theo, điều kiện giao dịch..."></textarea>
                        <div class="flex justify-between mt-1">
                            <p v-if="errorMessages['description']" class="text-sm text-red-600">{{
                                errorMessages['description'][0] }}</p>
                            <p v-else class="text-sm text-gray-500">Mô tả càng chi tiết, cơ hội bán càng cao</p>
                            <span class="text-sm text-gray-500" id="descCount">{{ descCount }}/{{ maxDescriptionLength
                            }}</span>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button @click="submitForm('publish')" :disabled="isCreating"
                                :class="['flex-1 py-3 px-6 rounded-lg font-medium transition-colors', isCreating ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700']">
                                <fa :icon="faRocket" class="mr-2" />
                                {{ isCreating ? 'Đang đăng...' : 'Đăng bán ngay' }}
                            </button>

                            <button @click="submitForm('draft')" :disabled="isCreating"
                                :class="['flex-1 py-3 px-6 rounded-lg font-medium transition-colors', isCreating ? 'bg-gray-400 cursor-not-allowed' : 'bg-gray-600 text-white hover:bg-gray-700']">
                                <fa :icon="faSave" class="mr-2" />
                                {{ isCreating ? 'Đang lưu...' : 'Lưu nháp' }}
                            </button>

                            <RouterLink to="/"
                                class="flex-1 text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                <fa :icon="faTimes" class="mr-2" />
                                Hủy bỏ
                            </RouterLink>
                        </div>

                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Bằng cách đăng sản phẩm, bạn đồng ý với
                            <a href="#" class="text-blue-600 hover:text-blue-800">Điều khoản sử dụng</a>
                            của TDC_Sharing
                        </p>
                    </div>
                </form>
            </div>
            <div v-else class="text-center py-5">
                <p class="text-muted">Đang tải cấu hình form...</p>
            </div>
        </div>
    </AppLayout>
</template>
<style>
.upload-area {
    background-repeat: no-repeat;
    /* 🎯 Đặt căn giữa (CENTER) làm mặc định cho mọi ảnh */
    background-position: center;

    border-color: #d1d5db;
    position: relative;
    overflow: hidden;
}

/* 🎯 1. ẢNH CHÍNH (SLOT 0) - Dùng COVER và CENTER */
.upload-area.uploaded-main {
    background-size: cover;
    /* Đảm bảo lấp đầy */
    border-color: #3b82f6;
    background-color: transparent !important;
}

/* 🎯 2. ẢNH PHỤ (SLOTS 1-4) - Dùng CONTAIN và CENTER */
.upload-area.uploaded-sub {
    background-size: contain;
    /* Đảm bảo không bị cắt */
    /* background-position: center đã được thiết lập mặc định */
    border-style: solid;
    border-color: #3b82f6;
    background-color: white !important;
}

/* --- OVERLAY VÀ HIỆU ỨNG (Giữ nguyên) --- */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 5;
    border-radius: 6px;
}

.upload-area:hover .image-overlay {
    opacity: 1;
}

.upload-area:hover .image-overlay .fa-edit {
    opacity: 1;
}

.image-overlay-sub {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 6px;
    z-index: 5;
}

.upload-area:hover .image-overlay-sub {
    opacity: 1;
}

.remove-btn-main {
    z-index: 15;
}

/* Ẩn các icon mặc định khi đã có ảnh */
.upload-area.uploaded-main>div:not(.image-overlay),
.upload-area.uploaded-sub>svg,
.upload-area.uploaded-sub>input+svg {
    display: none;
}
</style>