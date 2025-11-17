<script setup>
import { computed } from 'vue';
import { faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
    slug: {
        type: String,
        required: true,
    },
    onPageChange: Function,
});

const pageNumbers = computed(() => {
    const pages = [];
    const maxPages = 5;
    const startPage = Math.max(1, props.pagination.currentPage - Math.floor(maxPages / 2));
    const endPage = Math.min(props.pagination.totalPages, startPage + maxPages - 1);

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }
    return pages;
});
</script>

<template>
    <nav v-if="pagination.totalPages > 1" class="flex justify-center mt-8">
        <ul class="flex items-center space-x-2 bg-white shadow-md px-4 py-2 rounded-2xl">
            
            <!-- Nút Trước -->
            <li>
                <button
                    class="pagination-btn"
                    :disabled="pagination.currentPage === 1"
                    @click="onPageChange(pagination.currentPage - 1)"
                >
                    <fa :icon="['fas', 'chevron-left']" />
                </button>
            </li>

            <!-- Các số trang -->
            <li v-for="page in pageNumbers" :key="page">
                <button
                    class="pagination-btn"
                    :class="{
                        'bg-blue-600 text-white font-semibold shadow-sm': page === pagination.currentPage,
                        'hover:bg-blue-50 text-gray-700': page !== pagination.currentPage
                    }"
                    @click="onPageChange(page)"
                >
                    {{ page }}
                </button>
            </li>

            <!-- Nút Sau -->
            <li>
                <button
                    class="pagination-btn"
                    :disabled="pagination.currentPage === pagination.totalPages"
                    @click="onPageChange(pagination.currentPage + 1)"
                >
                    <fa :icon="['fas', 'chevron-right']" />
                </button>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.pagination-btn {
    @apply px-3 py-2 rounded-full text-sm font-medium transition-colors duration-150 border border-gray-200;
}
.pagination-btn:disabled {
    @apply opacity-50 cursor-not-allowed;
}
</style>
