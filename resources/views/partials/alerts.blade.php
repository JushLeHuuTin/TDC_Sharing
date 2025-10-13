{{-- resources/views/partials/alerts.blade.php --}}
@props(['type' => 'info', 'message'])

@php
    $alertClasses = [
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    ];
    
    $iconClasses = [
        'success' => 'fas fa-check-circle text-green-400',
        'error' => 'fas fa-exclamation-circle text-red-400',
        'warning' => 'fas fa-exclamation-triangle text-yellow-400',
        'info' => 'fas fa-info-circle text-blue-400',
    ];
@endphp

<div class="alert {{ $alertClasses[$type] ?? $alertClasses['info'] }} border rounded-lg p-4 mb-6 fade-in" 
     x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90">
    
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="{{ $iconClasses[$type] ?? $iconClasses['info'] }}"></i>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium">{{ $message }}</p>
        </div>
        <div class="ml-auto pl-3">
            <button @click="show = false" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.__x && alert.__x.$data.show) {
                alert.__x.$data.show = false;
            }
        });
    }, 5000);
});
</script>
@endpush