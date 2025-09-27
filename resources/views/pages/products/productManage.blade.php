{{-- resources/views/products/show.blade.php --}}
{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', 'iPhone 13 Pro Max 256GB - StudentMarket')

@section('content')
    <!-- Header -->


    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text opacity-75 mb-1">Tổng doanh thu</p>
                        <h4 class="card-title mb-0" id="totalRevenue">45.000.000₫</h4>
                    </div>
                    <i class="fas fa-coins fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-muted mb-1">Đang bán</p>
                        <h4 class="card-title mb-0 text-success" id="activeCount">12</h4>
                    </div>
                    <i class="fas fa-store fa-2x text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-muted mb-1">Đã bán</p>
                        <h4 class="card-title mb-0 text-primary" id="soldCount">8</h4>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-muted mb-1">Lượt xem</p>
                        <h4 class="card-title mb-0 text-info" id="totalViews">1.250</h4>
                    </div>
                    <i class="fas fa-eye fa-2x text-info"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <!-- Status Tabs -->
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="statusTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#active" data-status="active">
                        <i class="fas fa-store me-2"></i>Đang bán
                        <span class="badge bg-secondary ms-2" id="activeTabCount">12</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#draft" data-status="draft">
                        <i class="fas fa-edit me-2"></i>Bản nháp
                        <span class="badge bg-secondary ms-2" id="draftTabCount">3</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pending" data-status="pending">
                        <i class="fas fa-clock me-2"></i>Đang duyệt
                        <span class="badge bg-secondary ms-2" id="pendingTabCount">2</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#sold" data-status="sold">
                        <i class="fas fa-check-circle me-2"></i>Đã bán
                        <span class="badge bg-secondary ms-2" id="soldTabCount">8</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#hidden" data-status="hidden">
                        <i class="fas fa-eye-slash me-2"></i>Đã ẩn
                        <span class="badge bg-secondary ms-2" id="hiddenTabCount">2</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Search & Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortSelect">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="price_high">Giá cao nhất</option>
                        <option value="price_low">Giá thấp nhất</option>
                        <option value="views">Nhiều lượt xem</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">
                        Hiển thị <span id="resultCount">0</span> sản phẩm
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="active">
                    <div class="row" id="productsContainer">
                        <!-- Products will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="draft">
                    <div class="row" id="draftContainer">
                        <!-- Draft products will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="pending">
                    <div class="row" id="pendingContainer">
                        <!-- Pending products will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="sold">
                    <div class="row" id="soldContainer">
                        <!-- Sold products will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="hidden">
                    <div class="row" id="hiddenContainer">
                        <!-- Hidden products will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="text-center py-5 d-none" id="emptyState">
                <div class="mb-4">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted mb-3" id="emptyTitle">Chưa có sản phẩm</h5>
                <p class="text-muted mb-4" id="emptyDescription">Hãy đăng tin đầu tiên để bắt đầu bán hàng</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    <i class="fas fa-plus me-2"></i>Đăng tin ngay
                </button>
            </div>
        </div>
    </div>

    <!-- Create Product Modal -->
    <div class="modal fade" id="createProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đăng tin mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Chọn cách tạo sản phẩm mới:</p>
                    
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary p-4 text-start" onclick="createFromScratch()">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-plus fa-2x me-3 text-muted"></i>
                                <div>
                                    <h6 class="mb-1">Tạo từ đầu</h6>
                                    <small class="text-muted">Nhập thông tin sản phẩm mới</small>
                                </div>
                            </div>
                        </button>
                        
                        <button class="btn btn-outline-success p-4 text-start" onclick="copyFromExisting()">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-copy fa-2x me-3 text-muted"></i>
                                <div>
                                    <h6 class="mb-1">Sao chép từ tin cũ</h6>
                                    <small class="text-muted">Dựa trên sản phẩm đã đăng</small>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let products = [];
        let currentStatus = 'active';
        let filteredProducts = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            setupEventListeners();
        });

        // Sample data generator
        function generateSampleProducts() {
            const statuses = ['active', 'draft', 'pending', 'sold', 'hidden'];
            const titles = [
                'iPhone 13 Pro Max 256GB', 'MacBook Air M2 2022', 'Samsung Galaxy S23 Ultra',
                'iPad Pro 11 inch 2021', 'AirPods Pro Gen 2', 'Sách Toán Cao Cấp A1',
                'Áo Hoodie Uniqlo Size M', 'Giày Nike Air Force 1', 'Bàn học gỗ cao su',
                'Xe đạp thể thao Giant', 'Laptop Dell XPS 13', 'Tai nghe Sony WH-1000XM4'
            ];
            
            const descriptions = [
                'Máy còn mới 95%, fullbox, bảo hành 6 tháng',
                'Sản phẩm chính hãng, còn bảo hành',
                'Đã qua sử dụng nhưng vẫn hoạt động tốt',
                'Hàng xách tay từ Mỹ, chất lượng cao',
                'Giá sinh viên, có thể thương lượng'
            ];
            
            const sampleProducts = [];
            for (let i = 1; i <= 25; i++) {
                sampleProducts.push({
                    id: i,
                    title: titles[Math.floor(Math.random() * titles.length)] + ` #${i}`,
                    price: Math.floor(Math.random() * 20000000) + 500000,
                    status: statuses[Math.floor(Math.random() * statuses.length)],
                    is_negotiable: Math.random() > 0.5,
                    location: ['Quận 1', 'Quận 3', 'Quận 7', 'Thủ Đức'][Math.floor(Math.random() * 4)],
                    image: `https://picsum.photos/300/200?random=${i}`,
                    created_at: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000),
                    views: Math.floor(Math.random() * 500) + 10,
                    favorites: Math.floor(Math.random() * 50) + 1,
                    messages: Math.floor(Math.random() * 10),
                    performance: Math.floor(Math.random() * 40) + 60,
                    description: descriptions[Math.floor(Math.random() * descriptions.length)],
                    isEditing: false
                });
            }
            
            return sampleProducts;
        }

        // Load products
        function loadProducts() {
            products = generateSampleProducts();
            updateTabCounts();
            filterProducts();
        }

        // Setup event listeners
        function setupEventListeners() {
            // Tab switching
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    currentStatus = e.target.getAttribute('data-status');
                    filterProducts();
                });
            });

            // Search
            document.getElementById('searchInput').addEventListener('input', filterProducts);
            
            // Sort
            document.getElementById('sortSelect').addEventListener('change', filterProducts);
        }

        // Update tab counts
        function updateTabCounts() {
            const counts = {
                active: products.filter(p => p.status === 'active').length,
                draft: products.filter(p => p.status === 'draft').length,
                pending: products.filter(p => p.status === 'pending').length,
                sold: products.filter(p => p.status === 'sold').length,
                hidden: products.filter(p => p.status === 'hidden').length
            };

            document.getElementById('activeTabCount').textContent = counts.active;
            document.getElementById('draftTabCount').textContent = counts.draft;
            document.getElementById('pendingTabCount').textContent = counts.pending;
            document.getElementById('soldTabCount').textContent = counts.sold;
            document.getElementById('hiddenTabCount').textContent = counts.hidden;

            // Update stats
            document.getElementById('activeCount').textContent = counts.active;
            document.getElementById('soldCount').textContent = counts.sold;
        }

        // Filter products
        function filterProducts() {
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();
            const sortBy = document.getElementById('sortSelect').value;

            // Filter by status and search
            filteredProducts = products.filter(product => {
                const matchesStatus = product.status === currentStatus;
                const matchesSearch = !searchQuery || 
                    product.title.toLowerCase().includes(searchQuery);
                return matchesStatus && matchesSearch;
            });

            // Sort
            switch (sortBy) {
                case 'oldest':
                    filteredProducts.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'price_high':
                    filteredProducts.sort((a, b) => b.price - a.price);
                    break;
                case 'price_low':
                    filteredProducts.sort((a, b) => a.price - b.price);
                    break;
                case 'views':
                    filteredProducts.sort((a, b) => b.views - a.views);
                    break;
                default: // newest
                    filteredProducts.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            }

            renderProducts();
            updateResultCount();
        }

        // Render products
        function renderProducts() {
            const container = document.getElementById('productsContainer');
            const emptyState = document.getElementById('emptyState');

            if (filteredProducts.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('d-none');
                updateEmptyState();
            } else {
                emptyState.classList.add('d-none');
                container.innerHTML = filteredProducts.map(product => createProductCard(product)).join('');
            }
        }

        // Create product card HTML
        function createProductCard(product) {
            const isEditing = product.isEditing || false;
            
            return `
                <div class="col-12 mb-3">
                    <div class="card product-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Product Image -->
                                <div class="col-md-2 col-3">
                                    <img src="${product.image}" class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;" alt="${product.title}">
                                </div>
                                
                                <!-- Product Info -->
                                <div class="col-md-6 col-9">
                                    ${isEditing ? `
                                        <!-- Edit Mode -->
                                        <div class="mb-2">
                                            <input type="text" class="form-control form-control-sm" id="edit-title-${product.id}" value="${product.title}">
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control" id="edit-price-${product.id}" value="${product.price}">
                                                    <span class="input-group-text">₫</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select form-select-sm" id="edit-negotiable-${product.id}">
                                                    <option value="true" ${product.is_negotiable ? 'selected' : ''}>Có thể thương lượng</option>
                                                    <option value="false" ${!product.is_negotiable ? 'selected' : ''}>Giá cố định</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <textarea class="form-control form-control-sm" id="edit-description-${product.id}" rows="2" placeholder="Mô tả sản phẩm...">${product.description || ''}</textarea>
                                        </div>
                                    ` : `
                                        <!-- View Mode -->
                                        <h6 class="mb-1 fw-bold">${product.title}</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="h5 text-primary mb-0 me-3">${formatPrice(product.price)}</span>
                                            ${product.is_negotiable ? '<span class="badge bg-success me-2">Có TL</span>' : ''}
                                            <span class="badge ${getStatusBadgeClass(product.status)} me-2">
                                                ${getStatusText(product.status)}
                                            </span>
                                        </div>
                                        ${product.description ? `<p class="text-muted small mb-2">${product.description}</p>` : ''}
                                    `}
                                    
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span><i class="fas fa-calendar me-1"></i>${formatTime(product.created_at)}</span>
                                        <span><i class="fas fa-map-marker-alt me-1"></i>${product.location}</span>
                                    </div>
                                </div>
                                
                                <!-- Stats -->
                                <div class="col-md-2 col-6 text-center">
                                    <div class="d-flex justify-content-center gap-2 mb-1">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>${product.views}
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-heart me-1"></i>${product.favorites}
                                        </span>
                                        ${product.messages > 0 ? `
                                            <span class="badge bg-primary">
                                                <i class="fas fa-comment me-1"></i>${product.messages}
                                            </span>
                                        ` : ''}
                                    </div>
                                    <small class="text-muted">
                                        Hiệu suất: <span class="${getPerformanceClass(product.performance)}">${product.performance}%</span>
                                    </small>
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-md-2 col-6 text-end">
                                    ${isEditing ? `
                                        <!-- Edit Actions -->
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <button class="btn btn-success btn-sm" onclick="saveProduct(${product.id})">
                                                <i class="fas fa-check me-1"></i>Lưu
                                            </button>
                                            <button class="btn btn-secondary btn-sm" onclick="cancelEdit(${product.id})">
                                                <i class="fas fa-times me-1"></i>Hủy
                                            </button>
                                        </div>
                                    ` : `
                                        <!-- Normal Actions -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" onclick="startEdit(${product.id})" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Thêm">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" onclick="refreshProduct(${product.id})">
                                                        <i class="fas fa-refresh me-2 text-success"></i>Đẩy tin
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="duplicateProduct(${product.id})">
                                                        <i class="fas fa-copy me-2 text-info"></i>Sao chép
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="viewAnalytics(${product.id})">
                                                        <i class="fas fa-chart-line me-2 text-primary"></i>Thống kê
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    ${getStatusActions(product)}
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(${product.id})">
                                                        <i class="fas fa-trash me-2"></i>Xóa
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Helper functions
        function getStatusBadgeClass(status) {
            const classes = {
                'active': 'bg-success',
                'draft': 'bg-warning',
                'pending': 'bg-info',
                'sold': 'bg-primary',
                'hidden': 'bg-secondary'
            };
            return classes[status] || 'bg-secondary';
        }

        function getStatusText(status) {
            const texts = {
                'active': 'Đang bán',
                'draft': 'Bản nháp',
                'pending': 'Đang duyệt',
                'sold': 'Đã bán',
                'hidden': 'Đã ẩn'
            };
            return texts[status] || status;
        }

        function getStatusActions(product) {
            let actions = '';
            
            if (product.status === 'pending') {
                actions += `<li><a class="dropdown-item" href="#" onclick="viewReviewStatus(${product.id})">
                    <i class="fas fa-info-circle me-2 text-info"></i>Xem trạng thái duyệt
                </a></li>`;
                actions += `<li><a class="dropdown-item" href="#" onclick="changeStatus(${product.id}, 'draft')">
                    <i class="fas fa-arrow-left me-2 text-warning"></i>Rút về nháp
                </a></li>`;
            } else if (product.status !== 'active') {
                actions += `<li><a class="dropdown-item" href="#" onclick="changeStatus(${product.id}, 'active')">
                    <i class="fas fa-eye me-2 text-success"></i>Hiển thị
                </a></li>`;
                if (product.status === 'draft') {
                    actions += `<li><a class="dropdown-item" href="#" onclick="changeStatus(${product.id}, 'pending')">
                        <i class="fas fa-paper-plane me-2 text-info"></i>Gửi duyệt
                    </a></li>`;
                }
            }
            
            if (product.status === 'active') {
                actions += `<li><a class="dropdown-item" href="#" onclick="changeStatus(${product.id}, 'hidden')">
                    <i class="fas fa-eye-slash me-2 text-warning"></i>Ẩn tin
                </a></li>`;
                actions += `<li><a class="dropdown-item" href="#" onclick="changeStatus(${product.id}, 'sold')">
                    <i class="fas fa-check me-2 text-primary"></i>Đánh dấu đã bán
                </a></li>`;
            }
            
            return actions;
        }

        function getPerformanceClass(performance) {
            if (performance >= 80) return 'performance-high';
            if (performance >= 60) return 'performance-medium';
            return 'performance-low';
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price);
        }

        function formatTime(date) {
            const now = new Date();
            const diff = now - new Date(date);
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (days === 0) return 'Hôm nay';
            if (days === 1) return 'Hôm qua';
            if (days < 7) return `${days} ngày trước`;
            return new Date(date).toLocaleDateString('vi-VN');
        }

        function updateResultCount() {
            document.getElementById('resultCount').textContent = filteredProducts.length;
        }

        function updateEmptyState() {
            const titles = {
                'active': 'Chưa có sản phẩm đang bán',
                'draft': 'Chưa có bản nháp',
                'pending': 'Không có sản phẩm đang chờ duyệt',
                'sold': 'Chưa bán được sản phẩm nào',
                'hidden': 'Không có sản phẩm bị ẩn'
            };
            
            const descriptions = {
                'active': 'Hãy đăng tin đầu tiên để bắt đầu bán hàng',
                'draft': 'Các bản nháp sẽ xuất hiện ở đây',
                'pending': 'Sản phẩm đang chờ admin duyệt sẽ hiển thị tại đây',
                'sold': 'Các sản phẩm đã bán sẽ hiển thị tại đây',
                'hidden': 'Sản phẩm bị ẩn sẽ xuất hiện ở đây'
            };
            
            document.getElementById('emptyTitle').textContent = titles[currentStatus];
            document.getElementById('emptyDescription').textContent = descriptions[currentStatus];
        }

        // Product actions
        function editProduct(id) {
            console.log('Editing product:', id);
            showToast('Chuyển đến trang chỉnh sửa...', 'info');
        }

        function startEdit(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                // Exit any other editing mode
                products.forEach(p => p.isEditing = false);
                
                // Enter edit mode for this product
                product.isEditing = true;
                product.originalData = {
                    title: product.title,
                    price: product.price,
                    is_negotiable: product.is_negotiable,
                    description: product.description
                };
                
                filterProducts();
                showToast('Chế độ chỉnh sửa đã bật!', 'info');
            }
        }

        function saveProduct(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                // Get values from form
                const newTitle = document.getElementById(`edit-title-${id}`).value.trim();
                const newPrice = parseInt(document.getElementById(`edit-price-${id}`).value);
                const newNegotiable = document.getElementById(`edit-negotiable-${id}`).value === 'true';
                const newDescription = document.getElementById(`edit-description-${id}`).value.trim();
                
                // Validate
                if (!newTitle) {
                    showToast('Vui lòng nhập tên sản phẩm!', 'error');
                    return;
                }
                
                if (!newPrice || newPrice < 1000) {
                    showToast('Giá sản phẩm phải lớn hơn 1.000₫!', 'error');
                    return;
                }
                
                // Update product
                product.title = newTitle;
                product.price = newPrice;
                product.is_negotiable = newNegotiable;
                product.description = newDescription;
                product.isEditing = false;
                
                // Remove original data backup
                delete product.originalData;
                
                filterProducts();
                showToast('Đã lưu thay đổi!', 'success');
            }
        }

        function cancelEdit(id) {
            const product = products.find(p => p.id === id);
            if (product && product.originalData) {
                // Restore original data
                product.title = product.originalData.title;
                product.price = product.originalData.price;
                product.is_negotiable = product.originalData.is_negotiable;
                product.description = product.originalData.description;
                
                // Exit edit mode
                product.isEditing = false;
                delete product.originalData;
                
                filterProducts();
                showToast('Đã hủy chỉnh sửa!', 'info');
            }
        }

        function refreshProduct(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                product.created_at = new Date();
                filterProducts();
                showToast('Đã đẩy tin lên đầu!', 'success');
            }
        }

        function duplicateProduct(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                const newProduct = {
                    ...product,
                    id: Date.now(),
                    title: product.title + ' (Copy)',
                    status: 'draft',
                    created_at: new Date(),
                    views: 0,
                    favorites: 0,
                    messages: 0
                };
                
                products.unshift(newProduct);
                updateTabCounts();
                filterProducts();
                showToast('Đã sao chép sản phẩm!', 'success');
            }
        }

        function changeStatus(id, newStatus) {
            const product = products.find(p => p.id === id);
            if (product) {
                product.status = newStatus;
                updateTabCounts();
                filterProducts();
                
                const statusTexts = {
                    'active': 'hiển thị',
                    'hidden': 'ẩn',
                    'pending': 'gửi duyệt',
                    'draft': 'rút về nháp',
                    'sold': 'đánh dấu đã bán'
                };
                
                showToast(`Đã ${statusTexts[newStatus]} sản phẩm!`, 'success');
            }
        }

        function viewReviewStatus(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                const reviewInfo = `
                    <div class="alert alert-info">
                        <h6><i class="fas fa-clock me-2"></i>Trạng thái duyệt</h6>
                        <p class="mb-2"><strong>Sản phẩm:</strong> ${product.title}</p>
                        <p class="mb-2"><strong>Ngày gửi:</strong> ${formatTime(product.created_at)}</p>
                        <p class="mb-2"><strong>Trạng thái:</strong> <span class="badge bg-info">Đang chờ duyệt</span></p>
                        <p class="mb-0"><strong>Thời gian dự kiến:</strong> 1-2 ngày làm việc</p>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Admin sẽ kiểm tra nội dung và hình ảnh trước khi phê duyệt
                        </small>
                    </div>
                `;
                
                // Create modal to show review status
                const modalHtml = `
                    <div class="modal fade" id="reviewStatusModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết duyệt tin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ${reviewInfo}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-warning" onclick="changeStatus(${id}, 'draft')" data-bs-dismiss="modal">
                                        <i class="fas fa-arrow-left me-2"></i>Rút về nháp
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                const existingModal = document.getElementById('reviewStatusModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('reviewStatusModal'));
                modal.show();
                
                // Clean up modal after hide
                document.getElementById('reviewStatusModal').addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            }
        }

        function deleteProduct(id) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                products = products.filter(p => p.id !== id);
                updateTabCounts();
                filterProducts();
                showToast('Đã xóa sản phẩm!', 'success');
            }
        }

        function viewAnalytics(id) {
            console.log('Viewing analytics for product:', id);
            showToast('Chuyển đến trang thống kê...', 'info');
        }

        // Modal actions
        function createFromScratch() {
            console.log('Creating new product from scratch');
            showToast('Chuyển đến trang tạo sản phẩm...', 'info');
            bootstrap.Modal.getInstance(document.getElementById('createProductModal')).hide();
        }

        function copyFromExisting() {
            console.log('Creating product from existing');
            showToast('Chọn sản phẩm để sao chép...', 'info');
            bootstrap.Modal.getInstance(document.getElementById('createProductModal')).hide();
        }

        // Utility functions
        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();
            
            const bgClass = {
                'success': 'bg-success',
                'error': 'bg-danger',
                'warning': 'bg-warning',
                'info': 'bg-primary'
            }[type] || 'bg-primary';
            
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        function goBack() {
            console.log('Going back...');
            showToast('Quay lại trang trước...', 'info');
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9854a86c06d3f93b',t:'MTc1ODkwOTU5NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

@endsection

