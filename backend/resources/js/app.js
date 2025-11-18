// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;
Alpine.start();

// StudentMarket App JavaScript
class StudentMarketApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.setupAjaxDefaults();
    }

    setupEventListeners() {
        // Search functionality
        this.setupSearch();
        
        // Favorite functionality
        this.setupFavorites();
        
        // Chat functionality
        this.setupChat();
        
        // Form validations
        this.setupFormValidations();
        
        // Image upload preview
        this.setupImagePreview();
    }

    setupSearch() {
        const searchForm = document.querySelector('form[action*="search"]');
        if (searchForm) {
            const searchInput = searchForm.querySelector('input[name="q"]');
            let searchTimeout;

            searchInput?.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }
    }

    async performSearch(query) {
        if (query.length < 2) return;

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            this.displaySearchResults(results);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displaySearchResults(results) {
        // Implementation for displaying search results
        const resultsContainer = document.getElementById('search-results');
        if (resultsContainer) {
            resultsContainer.innerHTML = results.map(product => `
                <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <img src="${product.image_url}" alt="${product.title}" class="w-12 h-12 object-cover rounded">
                        <div>
                            <h4 class="font-medium">${product.title}</h4>
                            <p class="text-sm text-gray-600">${product.price}₫</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }

    setupFavorites() {
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.heart-btn')) {
                e.preventDefault();
                const button = e.target.closest('.heart-btn');
                const form = button.closest('form');
                
                if (form) {
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });

                        if (response.ok) {
                            const result = await response.json();
                            this.updateFavoriteButton(button, result.is_favorite);
                            this.showToast(result.message, 'success');
                        }
                    } catch (error) {
                        console.error('Favorite error:', error);
                        this.showToast('Có lỗi xảy ra', 'error');
                    }
                }
            }
        });
    }

    updateFavoriteButton(button, isFavorite) {
        const icon = button.querySelector('i');
        if (isFavorite) {
            button.classList.add('text-red-500');
            button.classList.remove('text-gray-400');
        } else {
            button.classList.remove('text-red-500');
            button.classList.add('text-gray-400');
        }
    }

    setupChat() {
        const chatContainer = document.getElementById('chat-container');
        if (chatContainer) {
            this.initializeChat();
        }
    }

    initializeChat() {
        // Setup WebSocket or polling for real-time chat
        if (window.Echo) {
            window.Echo.private(`chat.${window.userId}`)
                .listen('MessageSent', (e) => {
                    this.addMessageToChat(e.message);
                });
        }

        // Setup chat form submission
        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendMessage(chatForm);
            });
        }
    }

    async sendMessage(form) {
        const formData = new FormData(form);
        const messageInput = form.querySelector('input[name="message"]');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            if (response.ok) {
                const result = await response.json();
                this.addMessageToChat(result.message);
                messageInput.value = '';
            }
        } catch (error) {
            console.error('Send message error:', error);
        }
    }

    addMessageToChat(message) {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message mb-4';
            messageElement.innerHTML = `
                <div class="flex ${message.sender_id === window.userId ? 'justify-end' : 'justify-start'}">
                    <div class="chat-bubble px-4 py-2 rounded-lg ${message.sender_id === window.userId ? 'sent text-white' : 'received'}">
                        <p>${message.content}</p>
                        <span class="text-xs opacity-75">${new Date(message.created_at).toLocaleTimeString()}</span>
                    </div>
                </div>
            `;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    setupFormValidations() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Trường này là bắt buộc');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        // Email validation
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Email không hợp lệ');
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('border-red-500');
        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'field-error text-red-500 text-xs mt-1';
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('border-red-500');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    setupImagePreview() {
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.previewImage(e.target);
            });
        });
    }

    previewImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewContainer = input.parentNode.querySelector('.image-preview');
                if (previewContainer) {
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                    `;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    setupAjaxDefaults() {
        // Setup CSRF token for all AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize modals
        this.initModals();
        
        // Initialize dropdowns
        this.initDropdowns();
    }

    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.dataset.tooltip);
            });
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip absolute bg-gray-800 text-white px-2 py-1 rounded text-sm z-50';
        tooltip.textContent = text;
        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    }

    hideTooltip() {
        const tooltip = document.querySelector('.tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    initModals() {
        // Modal functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-target]')) {
                const modalId = e.target.dataset.modalTarget;
                this.openModal(modalId);
            }
            
            if (e.target.matches('[data-modal-close]')) {
                this.closeModal(e.target.closest('.modal'));
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    closeModal(modal) {
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    initDropdowns() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-dropdown-toggle]')) {
                const dropdownId = e.target.dataset.dropdownToggle;
                const dropdown = document.getElementById(dropdownId);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.matches('[data-dropdown-toggle]')) {
                const dropdowns = document.querySelectorAll('[data-dropdown]');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new StudentMarketApp();
});

// Global utility functions
window.togglePassword = function(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
};

window.formatPrice = function(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
};

window.timeAgo = function(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - new Date(date)) / 1000);
    
    if (diffInSeconds < 60) return 'Vừa xong';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} phút trước`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} giờ trước`;
    return `${Math.floor(diffInSeconds / 86400)} ngày trước`;
};