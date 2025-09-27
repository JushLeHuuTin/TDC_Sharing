{{-- resources/views/components/search-bar.blade.php --}}
<form action="{{ route('products.search') }}" method="GET" class="relative">
    <div class="relative">
        <input 
            type="text" 
            name="q" 
            value="{{ request('q') }}"
            placeholder="Tìm kiếm sản phẩm..." 
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            autocomplete="off"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <span class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                Tìm
            </span>
        </button>
    </div>
    
    <!-- Search Suggestions -->
    <div id="search-suggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-50">
        <!-- Dynamic content will be loaded here -->
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    const suggestionsContainer = document.getElementById('search-suggestions');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestionsContainer.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            // Simulate API call
            const mockData = [
                { id: 1, title: 'iPhone 13 Pro Max', price: '25000000', image_url: 'https://via.placeholder.com/40' },
                { id: 2, title: 'MacBook Air M1', price: '22000000', image_url: 'https://via.placeholder.com/40' },
                { id: 3, title: 'Samsung Galaxy S22', price: '18000000', image_url: 'https://via.placeholder.com/40' }
            ];
            
            if (mockData.length > 0) {
                suggestionsContainer.innerHTML = mockData.map(item => `
                    <a href="#" class="block px-4 py-2 hover:bg-gray-50 flex items-center space-x-3">
                        <img src="${item.image_url}" alt="${item.title}" class="w-10 h-10 object-cover rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${item.title}</p>
                            <p class="text-xs text-gray-500">${item.price}₫</p>
                        </div>
                    </a>
                `).join('');
                suggestionsContainer.classList.remove('hidden');
            } else {
                suggestionsContainer.classList.add('hidden');
            }
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.classList.add('hidden');
        }
    });
});
</script>
@endpush