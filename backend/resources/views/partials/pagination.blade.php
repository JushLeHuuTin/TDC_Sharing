<div x-show="!loading && filteredProducts.length > 0" class="flex items-center justify-center mt-8 space-x-2">
    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
        class="px-3 py-2 border border-gray-300 rounded-lg">
        <i class="fas fa-chevron-left"></i>
    </button>

    <template x-for="page in visiblePages" :key="page">
        <button @click="changePage(page)"
            :class="page === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'"
            class="px-3 py-2 border border-gray-300 rounded-lg" x-text="page">
        </button>
    </template>

    <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
        class="px-3 py-2 border border-gray-300 rounded-lg">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>