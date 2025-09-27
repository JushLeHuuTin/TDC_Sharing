@extends('layouts.admin')
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 d-md-none">
            <button class="btn btn-outline-primary" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0">Quản lý danh mục</h5>
        </div>

        <!-- Dynamic Content Container -->
        <div id="mainContent">
            <!-- Content will be loaded here based on navigation -->
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm" onsubmit="saveCategory(event)">
                    <div class="modal-body">
                        <input type="hidden" id="categoryId">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Danh mục cha</label>
                            <select class="form-select" id="parentCategory">
                                <option value="">-- Danh mục gốc (Cấp 1) --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="iconPreview" class="fas fa-tag"></i>
                                </span>
                                <input type="text" class="form-control" id="categoryIcon" placeholder="fas fa-tag" onchange="updateIconPreview()">
                            </div>
                            <div class="form-text">Sử dụng class FontAwesome (vd: fas fa-laptop, fas fa-book)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Màu sắc</label>
                            <input type="color" class="form-control form-control-color" id="categoryColor" value="#0d6efd">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="categoryOrder" value="0" min="0">
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="categoryActive" checked>
                            <label class="form-check-label" for="categoryActive">
                                Kích hoạt danh mục
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6>Bạn có chắc muốn xóa danh mục này?</h6>
                        <p class="text-muted mb-0" id="deleteMessage">Hành động này không thể hoàn tác!</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="userForm" onsubmit="saveUser(event)">
                    <div class="modal-body">
                        <input type="hidden" id="userId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="userName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="userEmail" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="userPhone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vai trò</label>
                                <select class="form-select" id="userRole">
                                    <option value="student">Sinh viên</option>
                                    <option value="premium">Premium</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" id="userStatus">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tạm khóa</option>
                                    <option value="pending">Chờ xác thực</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trường học</label>
                                <input type="text" class="form-control" id="userSchool">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="userAddress" rows="2"></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="userEmailVerified">
                            <label class="form-check-label" for="userEmailVerified">
                                Email đã được xác thực
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu người dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- User details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="editUserFromDetails()">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Promotion Modal -->
    <div class="modal fade" id="addPromotionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promotionModalTitle">Thêm chương trình khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="promotionForm" onsubmit="savePromotion(event)">
                    <div class="modal-body">
                        <input type="hidden" id="promotionId">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Tên chương trình <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="promotionName" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loại giảm giá</label>
                                <select class="form-select" id="promotionType" onchange="updatePromotionValueLabel()">
                                    <option value="percentage">Phần trăm (%)</option>
                                    <option value="fixed">Số tiền cố định</option>
                                    <option value="buy_x_get_y">Mua X tặng Y</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả chương trình</label>
                            <textarea class="form-control" id="promotionDescription" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" id="promotionValueLabel">Giá trị giảm (%)</label>
                                <input type="number" class="form-control" id="promotionValue" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Giá trị tối đa (VNĐ)</label>
                                <input type="number" class="form-control" id="promotionMaxValue" min="0">
                                <div class="form-text">Để trống nếu không giới hạn</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                                <input type="number" class="form-control" id="promotionMinOrder" min="0" value="0">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="promotionStartDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="promotionEndDate" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Danh mục áp dụng</label>
                                <select class="form-select" id="promotionCategories" multiple>
                                    <option value="">Tất cả danh mục</option>
                                    <option value="1">Điện tử - Công nghệ</option>
                                    <option value="4">Sách & Học tập</option>
                                    <option value="7">Thời trang</option>
                                    <option value="10">Đồ gia dụng</option>
                                </select>
                                <div class="form-text">Giữ Ctrl để chọn nhiều danh mục</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đối tượng áp dụng</label>
                                <select class="form-select" id="promotionTarget">
                                    <option value="all">Tất cả khách hàng</option>
                                    <option value="new">Khách hàng mới</option>
                                    <option value="student">Sinh viên</option>
                                    <option value="premium">Thành viên Premium</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lần sử dụng tối đa</label>
                                <input type="number" class="form-control" id="promotionUsageLimit" min="1">
                                <div class="form-text">Để trống nếu không giới hạn</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mỗi khách hàng dùng tối đa</label>
                                <input type="number" class="form-control" id="promotionUserLimit" min="1" value="1">
                            </div>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="promotionActive" checked>
                            <label class="form-check-label" for="promotionActive">
                                Kích hoạt chương trình ngay
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu chương trình
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add/Edit Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voucherModalTitle">Thêm voucher giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="voucherForm" onsubmit="saveVoucher(event)">
                    <div class="modal-body">
                        <input type="hidden" id="voucherId">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mã voucher <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="voucherCode" required style="text-transform: uppercase;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateVoucherCode()">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                <div class="form-text">Mã voucher phải là duy nhất</div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Tên voucher <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="voucherName" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả voucher</label>
                            <textarea class="form-control" id="voucherDescription" rows="2"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loại voucher</label>
                                <select class="form-select" id="voucherType" onchange="updateVoucherValueLabel()">
                                    <option value="percentage">Phần trăm (%)</option>
                                    <option value="fixed">Số tiền cố định</option>
                                    <option value="shipping">Miễn phí vận chuyển</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" id="voucherValueLabel">Giá trị giảm (%)</label>
                                <input type="number" class="form-control" id="voucherValue" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Giá trị tối đa (VNĐ)</label>
                                <input type="number" class="form-control" id="voucherMaxValue" min="0">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                                <input type="number" class="form-control" id="voucherMinOrder" min="0" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lượng voucher <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="voucherQuantity" min="1" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="voucherStartDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="voucherEndDate" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mỗi khách hàng dùng tối đa</label>
                                <input type="number" class="form-control" id="voucherUserLimit" min="1" value="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đối tượng sử dụng</label>
                                <select class="form-select" id="voucherTarget">
                                    <option value="all">Tất cả khách hàng</option>
                                    <option value="new">Khách hàng mới</option>
                                    <option value="student">Sinh viên</option>
                                    <option value="premium">Thành viên Premium</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="voucherActive" checked>
                            <label class="form-check-label" for="voucherActive">
                                Kích hoạt voucher ngay
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let categories = [];
        let selectedCategoryId = null;
        let editingCategoryId = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPage('dashboard'); // Load dashboard page by default
        });

        // Load page content
        function loadPage(pageName) {
            const mainContent = document.getElementById('mainContent');
            
            switch(pageName) {
                case 'dashboard':
                    mainContent.innerHTML = getDashboardPageHTML();
                    loadDashboard();
                    break;
                case 'categories':
                    mainContent.innerHTML = getCategoriesPageHTML();
                    loadCategories();
                    loadParentOptions();
                    break;
                case 'users':
                    mainContent.innerHTML = getUsersPageHTML();
                    loadUsers();
                    break;
                case 'promotions':
                    mainContent.innerHTML = getPromotionsPageHTML();
                    loadPromotions();
                    break;
                case 'notifications':
                    mainContent.innerHTML = getNotificationsPageHTML();
                    loadNotifications();
                    break;
                case 'settings':
                    mainContent.innerHTML = getSettingsPageHTML();
                    loadSettings();
                    break;
                default:
                    mainContent.innerHTML = `
                        <div class="page-header">
                            <h2>Trang đang phát triển</h2>
                            <p class="text-muted">Tính năng này sẽ sớm được cập nhật!</p>
                        </div>
                    `;
            }
        }

        // Dashboard page template
        function getDashboardPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">Dashboard</h2>
                            <p class="text-muted mb-0">Tổng quan hệ thống StudentMarket</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt me-2"></i>Làm mới
                            </button>
                            <button class="btn btn-primary" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>Xuất báo cáo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Overview Stats -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="dashTotalUsers">1,234</h3>
                                        <p class="mb-0 opacity-75">Tổng người dùng</p>
                                        <small class="opacity-75">+12% so với tháng trước</small>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="dashTotalProducts">5,678</h3>
                                        <p class="mb-0 opacity-75">Tổng sản phẩm</p>
                                        <small class="opacity-75">+8% so với tháng trước</small>
                                    </div>
                                    <i class="fas fa-box fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="dashTotalOrders">892</h3>
                                        <p class="mb-0 opacity-75">Đơn hàng tháng này</p>
                                        <small class="opacity-75">+15% so với tháng trước</small>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="dashRevenue">45.2M</h3>
                                        <p class="mb-0 opacity-75">Doanh thu (VNĐ)</p>
                                        <small class="opacity-75">+22% so với tháng trước</small>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Analytics -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Biểu đồ doanh thu 7 ngày qua</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Danh mục bán chạy</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Điện tử - Công nghệ</small>
                                        <small>35%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: 35%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Sách & Học tập</small>
                                        <small>28%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 28%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Thời trang</small>
                                        <small>22%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 22%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Đồ gia dụng</small>
                                        <small>15%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: 15%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Đơn hàng gần đây</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Khách hàng</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentOrdersTable">
                                            <!-- Recent orders will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Người dùng mới</h5>
                            </div>
                            <div class="card-body p-0">
                                <div id="newUsersList">
                                    <!-- New users will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Categories page template
        function getCategoriesPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Quản lý danh mục</li>
                                </ol>
                            </nav>
                            <h2 class="mb-0">Quản lý danh mục sản phẩm</h2>
                            <p class="text-muted mb-0">Tổ chức và quản lý danh mục theo cấp độ</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-2"></i>Thêm danh mục
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="totalCategories">24</h3>
                                        <p class="mb-0 opacity-75">Tổng danh mục</p>
                                    </div>
                                    <i class="fas fa-tags fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="level1Categories">8</h3>
                                        <p class="mb-0 opacity-75">Danh mục cấp 1</p>
                                    </div>
                                    <i class="fas fa-layer-group fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="level2Categories">16</h3>
                                        <p class="mb-0 opacity-75">Danh mục cấp 2</p>
                                    </div>
                                    <i class="fas fa-sitemap fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="activeProducts">1,247</h3>
                                        <p class="mb-0 opacity-75">Sản phẩm đang bán</p>
                                    </div>
                                    <i class="fas fa-box fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Management -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="category-tree">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Cây danh mục</h5>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm" onclick="expandAll()">
                                        <i class="fas fa-expand-alt me-1"></i>Mở rộng tất cả
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
                                        <i class="fas fa-compress-alt me-1"></i>Thu gọn tất cả
                                    </button>
                                </div>
                            </div>
                            
                            <div id="categoryTree">
                                <!-- Categories will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Thông tin danh mục</h6>
                            </div>
                            <div class="card-body" id="categoryInfo">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                                    <p>Chọn một danh mục để xem thông tin chi tiết</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Thao tác nhanh</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                        <i class="fas fa-plus me-2"></i>Thêm danh mục mới
                                    </button>
                                    <button class="btn btn-outline-info" onclick="importCategories()">
                                        <i class="fas fa-file-import me-2"></i>Import danh mục
                                    </button>
                                    <button class="btn btn-outline-success" onclick="exportCategories()">
                                        <i class="fas fa-file-export me-2"></i>Export danh mục
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Users page template
        function getUsersPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Quản lý người dùng</li>
                                </ol>
                            </nav>
                            <h2 class="mb-0">Quản lý người dùng</h2>
                            <p class="text-muted mb-0">Quản lý tài khoản và quyền hạn người dùng</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" onclick="addUser()">
                            <i class="fas fa-user-plus me-2"></i>Thêm người dùng
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="totalUsers">1,234</h3>
                                        <p class="mb-0 opacity-75">Tổng người dùng</p>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="activeUsers">987</h3>
                                        <p class="mb-0 opacity-75">Đang hoạt động</p>
                                    </div>
                                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="newUsers">45</h3>
                                        <p class="mb-0 opacity-75">Mới trong tháng</p>
                                    </div>
                                    <i class="fas fa-user-plus fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="premiumUsers">156</h3>
                                        <p class="mb-0 opacity-75">Tài khoản Premium</p>
                                    </div>
                                    <i class="fas fa-crown fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Danh sách người dùng</h5>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" placeholder="Tìm kiếm..." id="userSearch" style="width: 200px;">
                                <select class="form-select" id="userFilter" style="width: 150px;">
                                    <option value="">Tất cả</option>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tạm khóa</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Người dùng</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tham gia</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Users will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        // Notifications page template
        function getNotificationsPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Thông báo</li>
                                </ol>
                            </nav>
                            <h2 class="mb-0">Quản lý thông báo</h2>
                            <p class="text-muted mb-0">Gửi và quản lý thông báo hệ thống</p>
                        </div>
                        <button class="btn btn-primary" onclick="createNotification()">
                            <i class="fas fa-plus me-2"></i>Tạo thông báo
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="totalNotifications">89</h3>
                                        <p class="mb-0 opacity-75">Tổng thông báo</p>
                                    </div>
                                    <i class="fas fa-bell fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="sentNotifications">67</h3>
                                        <p class="mb-0 opacity-75">Đã gửi</p>
                                    </div>
                                    <i class="fas fa-paper-plane fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="pendingNotifications">12</h3>
                                        <p class="mb-0 opacity-75">Chờ gửi</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="readRate">78%</h3>
                                        <p class="mb-0 opacity-75">Tỷ lệ đọc</p>
                                    </div>
                                    <i class="fas fa-eye fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Management -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Danh sách thông báo</h5>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary active" onclick="filterNotifications('all')">Tất cả</button>
                                        <button class="btn btn-outline-primary" onclick="filterNotifications('sent')">Đã gửi</button>
                                        <button class="btn btn-outline-primary" onclick="filterNotifications('pending')">Chờ gửi</button>
                                        <button class="btn btn-outline-primary" onclick="filterNotifications('draft')">Nháp</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="notificationsList">
                                    <!-- Notifications will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Thống kê thông báo</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Thông báo hệ thống</small>
                                        <small>45</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: 60%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Khuyến mãi</small>
                                        <small>23</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 30%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Cập nhật</small>
                                        <small>21</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-info" style="width: 28%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Thao tác nhanh</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" onclick="createNotification()">
                                        <i class="fas fa-plus me-2"></i>Tạo thông báo mới
                                    </button>
                                    <button class="btn btn-outline-info" onclick="sendBulkNotification()">
                                        <i class="fas fa-broadcast-tower me-2"></i>Gửi hàng loạt
                                    </button>
                                    <button class="btn btn-outline-success" onclick="scheduleNotification()">
                                        <i class="fas fa-calendar me-2"></i>Lên lịch gửi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Promotions page template
        function getPromotionsPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Khuyến mãi & Voucher</li>
                                </ol>
                            </nav>
                            <h2 class="mb-0">Quản lý khuyến mãi & Voucher</h2>
                            <p class="text-muted mb-0">Tạo và quản lý các chương trình khuyến mãi</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-plus me-2"></i>Tạo mới
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="createPromotion()">
                                    <i class="fas fa-percentage me-2"></i>Chương trình khuyến mãi
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="createVoucher()">
                                    <i class="fas fa-ticket-alt me-2"></i>Voucher giảm giá
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="totalPromotions">15</h3>
                                        <p class="mb-0 opacity-75">Tổng khuyến mãi</p>
                                    </div>
                                    <i class="fas fa-percentage fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="activePromotions">8</h3>
                                        <p class="mb-0 opacity-75">Đang hoạt động</p>
                                    </div>
                                    <i class="fas fa-play-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="totalVouchers">234</h3>
                                        <p class="mb-0 opacity-75">Voucher đã tạo</p>
                                    </div>
                                    <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-1" id="usedVouchers">156</h3>
                                        <p class="mb-0 opacity-75">Đã sử dụng</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promotion Tabs -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="promotionTabs">
                            <li class="nav-item">
                                <button class="nav-link active" onclick="showPromotionTab('promotions')">
                                    <i class="fas fa-percentage me-2"></i>Chương trình khuyến mãi
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" onclick="showPromotionTab('vouchers')">
                                    <i class="fas fa-ticket-alt me-2"></i>Voucher giảm giá
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="promotionTabContent">
                            <!-- Tab content will be loaded here -->
                        </div>
                    </div>
                </div>
            `;
        }

        // Settings page template
        function getSettingsPageHTML() {
            return `
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Cài đặt hệ thống</li>
                                </ol>
                            </nav>
                            <h2 class="mb-0">Cài đặt hệ thống</h2>
                            <p class="text-muted mb-0">Cấu hình và tùy chỉnh hệ thống</p>
                        </div>
                        <button class="btn btn-success" onclick="saveAllSettings()">
                            <i class="fas fa-save me-2"></i>Lưu tất cả
                        </button>
                    </div>
                </div>

                <!-- Settings Tabs -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="nav flex-column nav-pills" id="settingsTabs">
                                    <button class="nav-link active" onclick="showSettingsTab('general')">
                                        <i class="fas fa-cog me-2"></i>Cài đặt chung
                                    </button>
                                    <button class="nav-link" onclick="showSettingsTab('email')">
                                        <i class="fas fa-envelope me-2"></i>Email & SMTP
                                    </button>
                                    <button class="nav-link" onclick="showSettingsTab('payment')">
                                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                                    </button>
                                    <button class="nav-link" onclick="showSettingsTab('security')">
                                        <i class="fas fa-shield-alt me-2"></i>Bảo mật
                                    </button>
                                    <button class="nav-link" onclick="showSettingsTab('appearance')">
                                        <i class="fas fa-palette me-2"></i>Giao diện
                                    </button>
                                    <button class="nav-link" onclick="showSettingsTab('backup')">
                                        <i class="fas fa-database me-2"></i>Sao lưu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div id="settingsContent">
                            <!-- Settings content will be loaded here -->
                        </div>
                    </div>
                </div>
            `;
        }

        // Sample data generator
        function generateSampleCategories() {
            return [
                {
                    id: 1,
                    name: 'Điện tử - Công nghệ',
                    parent_id: null,
                    level: 1,
                    description: 'Các sản phẩm điện tử và công nghệ',
                    icon: 'fas fa-laptop',
                    color: '#0d6efd',
                    order: 1,
                    active: true,
                    product_count: 245,
                    created_at: new Date('2024-01-15')
                },
                {
                    id: 2,
                    name: 'Laptop & Máy tính',
                    parent_id: 1,
                    level: 2,
                    description: 'Laptop, PC, linh kiện máy tính',
                    icon: 'fas fa-desktop',
                    color: '#198754',
                    order: 1,
                    active: true,
                    product_count: 89,
                    created_at: new Date('2024-01-15')
                },
                {
                    id: 3,
                    name: 'Điện thoại & Tablet',
                    parent_id: 1,
                    level: 2,
                    description: 'Smartphone, tablet và phụ kiện',
                    icon: 'fas fa-mobile-alt',
                    color: '#dc3545',
                    order: 2,
                    active: true,
                    product_count: 156,
                    created_at: new Date('2024-01-15')
                },
                {
                    id: 4,
                    name: 'Sách & Học tập',
                    parent_id: null,
                    level: 1,
                    description: 'Sách giáo khoa, tài liệu học tập',
                    icon: 'fas fa-book',
                    color: '#fd7e14',
                    order: 2,
                    active: true,
                    product_count: 423,
                    created_at: new Date('2024-01-16')
                },
                {
                    id: 5,
                    name: 'Sách giáo khoa',
                    parent_id: 4,
                    level: 2,
                    description: 'Sách giáo khoa các cấp',
                    icon: 'fas fa-graduation-cap',
                    color: '#20c997',
                    order: 1,
                    active: true,
                    product_count: 234,
                    created_at: new Date('2024-01-16')
                },
                {
                    id: 6,
                    name: 'Tài liệu tham khảo',
                    parent_id: 4,
                    level: 2,
                    description: 'Sách tham khảo, đề thi',
                    icon: 'fas fa-file-alt',
                    color: '#6f42c1',
                    order: 2,
                    active: true,
                    product_count: 189,
                    created_at: new Date('2024-01-16')
                },
                {
                    id: 7,
                    name: 'Thời trang',
                    parent_id: null,
                    level: 1,
                    description: 'Quần áo, giày dép, phụ kiện',
                    icon: 'fas fa-tshirt',
                    color: '#e83e8c',
                    order: 3,
                    active: true,
                    product_count: 312,
                    created_at: new Date('2024-01-17')
                },
                {
                    id: 8,
                    name: 'Quần áo nam',
                    parent_id: 7,
                    level: 2,
                    description: 'Thời trang nam',
                    icon: 'fas fa-male',
                    color: '#0dcaf0',
                    order: 1,
                    active: true,
                    product_count: 145,
                    created_at: new Date('2024-01-17')
                },
                {
                    id: 9,
                    name: 'Quần áo nữ',
                    parent_id: 7,
                    level: 2,
                    description: 'Thời trang nữ',
                    icon: 'fas fa-female',
                    color: '#f8d7da',
                    order: 2,
                    active: true,
                    product_count: 167,
                    created_at: new Date('2024-01-17')
                },
                {
                    id: 10,
                    name: 'Đồ gia dụng',
                    parent_id: null,
                    level: 1,
                    description: 'Đồ dùng trong gia đình',
                    icon: 'fas fa-home',
                    color: '#6c757d',
                    order: 4,
                    active: true,
                    product_count: 178,
                    created_at: new Date('2024-01-18')
                }
            ];
        }

        // Load categories
        function loadCategories() {
            categories = generateSampleCategories();
            updateStats();
            renderCategoryTree();
            loadParentOptions();
        }

        // Update statistics
        function updateStats() {
            const level1Count = categories.filter(c => c.level === 1).length;
            const level2Count = categories.filter(c => c.level === 2).length;
            const totalProducts = categories.reduce((sum, c) => sum + c.product_count, 0);

            document.getElementById('totalCategories').textContent = categories.length;
            document.getElementById('level1Categories').textContent = level1Count;
            document.getElementById('level2Categories').textContent = level2Count;
            document.getElementById('activeProducts').textContent = totalProducts.toLocaleString();
        }

        // Render category tree
        function renderCategoryTree() {
            const container = document.getElementById('categoryTree');
            const level1Categories = categories.filter(c => c.level === 1).sort((a, b) => a.order - b.order);
            
            let html = '';
            
            level1Categories.forEach(category => {
                const hasChildren = categories.some(c => c.parent_id === category.id);
                const isExpanded = expandedCategories.has(category.id);
                
                html += createCategoryItem(category, hasChildren, isExpanded);
                
                // Add level 2 categories if expanded
                if (isExpanded) {
                    const level2Categories = categories
                        .filter(c => c.parent_id === category.id)
                        .sort((a, b) => a.order - b.order);
                    
                    level2Categories.forEach(subCategory => {
                        html += createCategoryItem(subCategory, false, false);
                    });
                }
            });
            
            container.innerHTML = html;
        }

        // Create category item HTML
        function createCategoryItem(category, hasChildren = false, isExpanded = false) {
            const isSelected = selectedCategoryId === category.id;
            const levelClass = `category-level-${category.level}`;
            
            return `
                <div class="category-item ${levelClass} ${isSelected ? 'border-primary bg-light' : ''}" 
                     onclick="selectCategory(${category.id})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            ${hasChildren ? `
                                <button class="btn btn-sm btn-outline-secondary me-2" 
                                        onclick="event.stopPropagation(); toggleCategory(${category.id})" 
                                        title="${isExpanded ? 'Thu gọn' : 'Mở rộng'}">
                                    <i class="fas fa-chevron-${isExpanded ? 'down' : 'right'}"></i>
                                </button>
                            ` : category.level === 2 ? '<span class="me-4"></span>' : ''}
                            <i class="${category.icon} me-3" style="color: ${category.color}"></i>
                            <div>
                                <h6 class="mb-1">${category.name}</h6>
                                <small class="text-muted">
                                    ${category.product_count} sản phẩm
                                    ${!category.active ? ' • <span class="text-danger">Đã tắt</span>' : ''}
                                </small>
                            </div>
                        </div>
                        
                        <div class="category-actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="event.stopPropagation(); editCategory(${category.id})" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${category.level === 1 ? `
                                    <button class="btn btn-outline-success" onclick="event.stopPropagation(); addSubCategory(${category.id})" title="Thêm danh mục con">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-outline-danger" onclick="event.stopPropagation(); deleteCategory(${category.id})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Select category
        function selectCategory(id) {
            selectedCategoryId = id;
            renderCategoryTree();
            showCategoryInfo(id);
        }

        // Show category information
        function showCategoryInfo(id) {
            const category = categories.find(c => c.id === id);
            if (!category) return;
            
            const parentCategory = category.parent_id ? 
                categories.find(c => c.id === category.parent_id) : null;
            
            const subCategories = categories.filter(c => c.parent_id === id);
            
            const html = `
                <div class="text-center mb-3">
                    <i class="${category.icon} fa-3x mb-2" style="color: ${category.color}"></i>
                    <h5>${category.name}</h5>
                    <span class="badge ${category.active ? 'bg-success' : 'bg-secondary'}">
                        ${category.active ? 'Đang hoạt động' : 'Đã tắt'}
                    </span>
                </div>
                
                <div class="mb-3">
                    <strong>Mô tả:</strong>
                    <p class="text-muted mb-0">${category.description || 'Chưa có mô tả'}</p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Cấp độ:</strong>
                        <p class="mb-0">Cấp ${category.level}</p>
                    </div>
                    <div class="col-6">
                        <strong>Thứ tự:</strong>
                        <p class="mb-0">${category.order}</p>
                    </div>
                </div>
                
                ${parentCategory ? `
                    <div class="mb-3">
                        <strong>Danh mục cha:</strong>
                        <p class="mb-0">
                            <i class="${parentCategory.icon} me-1"></i>
                            ${parentCategory.name}
                        </p>
                    </div>
                ` : ''}
                
                <div class="mb-3">
                    <strong>Số sản phẩm:</strong>
                    <p class="mb-0 text-primary fw-bold">${category.product_count} sản phẩm</p>
                </div>
                
                ${subCategories.length > 0 ? `
                    <div class="mb-3">
                        <strong>Danh mục con (${subCategories.length}):</strong>
                        <ul class="list-unstyled mt-2">
                            ${subCategories.map(sub => `
                                <li class="mb-1">
                                    <i class="${sub.icon} me-2"></i>
                                    ${sub.name} (${sub.product_count})
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
                
                <div class="mb-3">
                    <strong>Ngày tạo:</strong>
                    <p class="mb-0">${formatDate(category.created_at)}</p>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="editCategory(${category.id})">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </button>
                    ${category.level === 1 ? `
                        <button class="btn btn-success btn-sm" onclick="addSubCategory(${category.id})">
                            <i class="fas fa-plus me-2"></i>Thêm danh mục con
                        </button>
                    ` : ''}
                    <button class="btn btn-outline-info btn-sm" onclick="viewProducts(${category.id})">
                        <i class="fas fa-box me-2"></i>Xem sản phẩm
                    </button>
                </div>
            `;
            
            document.getElementById('categoryInfo').innerHTML = html;
        }

        // Load parent category options
        function loadParentOptions() {
            const select = document.getElementById('parentCategory');
            const level1Categories = categories.filter(c => c.level === 1);
            
            // Clear existing options except the first one
            select.innerHTML = '<option value="">-- Danh mục gốc (Cấp 1) --</option>';
            
            level1Categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        }

        // Add new category
        function addCategory() {
            editingCategoryId = null;
            document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryActive').checked = true;
            updateIconPreview();
            
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        // Add subcategory
        function addSubCategory(parentId) {
            editingCategoryId = null;
            document.getElementById('modalTitle').textContent = 'Thêm danh mục con';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('parentCategory').value = parentId;
            document.getElementById('categoryActive').checked = true;
            updateIconPreview();
            
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        // Edit category
        function editCategory(id) {
            const category = categories.find(c => c.id === id);
            if (!category) return;
            
            editingCategoryId = id;
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('parentCategory').value = category.parent_id || '';
            document.getElementById('categoryDescription').value = category.description || '';
            document.getElementById('categoryIcon').value = category.icon;
            document.getElementById('categoryColor').value = category.color;
            document.getElementById('categoryOrder').value = category.order;
            document.getElementById('categoryActive').checked = category.active;
            
            updateIconPreview();
            
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        // Save category
        function saveCategory(event) {
            event.preventDefault();
            
            const formData = {
                id: editingCategoryId || Date.now(),
                name: document.getElementById('categoryName').value.trim(),
                parent_id: document.getElementById('parentCategory').value || null,
                description: document.getElementById('categoryDescription').value.trim(),
                icon: document.getElementById('categoryIcon').value.trim() || 'fas fa-tag',
                color: document.getElementById('categoryColor').value,
                order: parseInt(document.getElementById('categoryOrder').value) || 0,
                active: document.getElementById('categoryActive').checked
            };
            
            // Validate
            if (!formData.name) {
                showToast('Vui lòng nhập tên danh mục!', 'error');
                return;
            }
            
            // Check duplicate name
            const existingCategory = categories.find(c => 
                c.name.toLowerCase() === formData.name.toLowerCase() && 
                c.id !== formData.id &&
                c.parent_id === formData.parent_id
            );
            
            if (existingCategory) {
                showToast('Tên danh mục đã tồn tại!', 'error');
                return;
            }
            
            // Set level
            formData.level = formData.parent_id ? 2 : 1;
            
            if (editingCategoryId) {
                // Update existing category
                const index = categories.findIndex(c => c.id === editingCategoryId);
                if (index !== -1) {
                    categories[index] = { ...categories[index], ...formData };
                    showToast('Đã cập nhật danh mục!', 'success');
                }
            } else {
                // Add new category
                formData.product_count = 0;
                formData.created_at = new Date();
                categories.push(formData);
                showToast('Đã thêm danh mục mới!', 'success');
            }
            
            // Refresh UI
            updateStats();
            renderCategoryTree();
            loadParentOptions();
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
        }

        // Delete category
        function deleteCategory(id) {
            const category = categories.find(c => c.id === id);
            if (!category) return;
            
            // Check if category has subcategories
            const hasSubCategories = categories.some(c => c.parent_id === id);
            
            let message = 'Hành động này không thể hoàn tác!';
            if (hasSubCategories) {
                message = 'Danh mục này có danh mục con. Tất cả sẽ bị xóa!';
            }
            if (category.product_count > 0) {
                message += ` Có ${category.product_count} sản phẩm sẽ bị ảnh hưởng.`;
            }
            
            document.getElementById('deleteMessage').innerHTML = message;
            
            // Set up delete confirmation
            document.getElementById('confirmDelete').onclick = function() {
                // Delete category and its subcategories
                categories = categories.filter(c => c.id !== id && c.parent_id !== id);
                
                // Clear selection if deleted category was selected
                if (selectedCategoryId === id) {
                    selectedCategoryId = null;
                    document.getElementById('categoryInfo').innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <p>Chọn một danh mục để xem thông tin chi tiết</p>
                        </div>
                    `;
                }
                
                updateStats();
                renderCategoryTree();
                loadParentOptions();
                
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                showToast('Đã xóa danh mục!', 'success');
            };
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Update icon preview
        function updateIconPreview() {
            const iconClass = document.getElementById('categoryIcon').value.trim() || 'fas fa-tag';
            document.getElementById('iconPreview').className = iconClass;
        }

        // Category tree state
        let expandedCategories = new Set();

        // Expand/Collapse functions
        function expandAll() {
            const level1Categories = categories.filter(c => c.level === 1);
            level1Categories.forEach(cat => expandedCategories.add(cat.id));
            renderCategoryTree();
            showToast('Đã mở rộng tất cả danh mục!', 'info');
        }

        function collapseAll() {
            expandedCategories.clear();
            renderCategoryTree();
            showToast('Đã thu gọn tất cả danh mục!', 'info');
        }

        function toggleCategory(categoryId) {
            if (expandedCategories.has(categoryId)) {
                expandedCategories.delete(categoryId);
            } else {
                expandedCategories.add(categoryId);
            }
            renderCategoryTree();
        }

        // Import/Export functions
        function importCategories() {
            showToast('Tính năng import đang được phát triển!', 'info');
        }

        function exportCategories() {
            const dataStr = JSON.stringify(categories, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'categories.json';
            link.click();
            URL.revokeObjectURL(url);
            
            showToast('Đã xuất danh sách danh mục!', 'success');
        }

        // View products in category
        function viewProducts(categoryId) {
            const category = categories.find(c => c.id === categoryId);
            showToast(`Chuyển đến sản phẩm của "${category.name}"...`, 'info');
        }

        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Utility functions
        function formatDate(date) {
            return new Date(date).toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

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

        // Load dashboard data
        function loadDashboard() {
            loadRecentOrders();
            loadNewUsers();
            createRevenueChart();
        }

        function loadRecentOrders() {
            const orders = [
                { id: 'DH001', customer: 'Nguyễn Văn An', total: '450,000', status: 'completed' },
                { id: 'DH002', customer: 'Trần Thị Bình', total: '320,000', status: 'processing' },
                { id: 'DH003', customer: 'Lê Minh Cường', total: '180,000', status: 'pending' },
                { id: 'DH004', customer: 'Phạm Thu Dung', total: '750,000', status: 'completed' },
                { id: 'DH005', customer: 'Hoàng Văn Em', total: '290,000', status: 'shipping' }
            ];
            
            const tbody = document.getElementById('recentOrdersTable');
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td><strong>${order.id}</strong></td>
                    <td>${order.customer}</td>
                    <td>${order.total} VNĐ</td>
                    <td>
                        <span class="badge ${getOrderStatusClass(order.status)}">
                            ${getOrderStatusText(order.status)}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        function loadNewUsers() {
            const users = [
                { name: 'Nguyễn Thị Lan', email: 'lan@student.com', joinDate: '2024-02-20', avatar: 'https://ui-avatars.io/api/?name=Nguyen+Thi+Lan&background=0d6efd&color=fff' },
                { name: 'Trần Văn Nam', email: 'nam@student.com', joinDate: '2024-02-19', avatar: 'https://ui-avatars.io/api/?name=Tran+Van+Nam&background=198754&color=fff' },
                { name: 'Lê Thị Oanh', email: 'oanh@student.com', joinDate: '2024-02-18', avatar: 'https://ui-avatars.io/api/?name=Le+Thi+Oanh&background=fd7e14&color=fff' },
                { name: 'Phạm Minh Phúc', email: 'phuc@student.com', joinDate: '2024-02-17', avatar: 'https://ui-avatars.io/api/?name=Pham+Minh+Phuc&background=dc3545&color=fff' }
            ];
            
            const container = document.getElementById('newUsersList');
            container.innerHTML = users.map(user => `
                <div class="d-flex align-items-center p-3 border-bottom">
                    <img src="${user.avatar}" class="rounded-circle me-3" width="40" height="40">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${user.name}</h6>
                        <small class="text-muted">${user.email}</small>
                    </div>
                    <small class="text-muted">${formatDate(new Date(user.joinDate))}</small>
                </div>
            `).join('');
        }

        function createRevenueChart() {
            const canvas = document.getElementById('revenueChart');
            const ctx = canvas.getContext('2d');
            
            // Simple chart drawing
            const data = [12, 19, 15, 25, 22, 30, 28];
            const labels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
            
            canvas.width = canvas.offsetWidth;
            canvas.height = 300;
            
            const padding = 40;
            const chartWidth = canvas.width - padding * 2;
            const chartHeight = canvas.height - padding * 2;
            const maxValue = Math.max(...data);
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw grid lines
            ctx.strokeStyle = '#e9ecef';
            ctx.lineWidth = 1;
            
            for (let i = 0; i <= 5; i++) {
                const y = padding + (chartHeight / 5) * i;
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(canvas.width - padding, y);
                ctx.stroke();
            }
            
            // Draw chart line
            ctx.strokeStyle = '#0d6efd';
            ctx.lineWidth = 3;
            ctx.beginPath();
            
            data.forEach((value, index) => {
                const x = padding + (chartWidth / (data.length - 1)) * index;
                const y = padding + chartHeight - (value / maxValue) * chartHeight;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
            
            // Draw data points
            ctx.fillStyle = '#0d6efd';
            data.forEach((value, index) => {
                const x = padding + (chartWidth / (data.length - 1)) * index;
                const y = padding + chartHeight - (value / maxValue) * chartHeight;
                
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fill();
            });
            
            // Draw labels
            ctx.fillStyle = '#6c757d';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            
            labels.forEach((label, index) => {
                const x = padding + (chartWidth / (data.length - 1)) * index;
                ctx.fillText(label, x, canvas.height - 10);
            });
        }

        function getOrderStatusClass(status) {
            const classes = {
                'completed': 'bg-success',
                'processing': 'bg-warning',
                'pending': 'bg-secondary',
                'shipping': 'bg-info',
                'cancelled': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        }

        function getOrderStatusText(status) {
            const texts = {
                'completed': 'Hoàn thành',
                'processing': 'Đang xử lý',
                'pending': 'Chờ xác nhận',
                'shipping': 'Đang giao',
                'cancelled': 'Đã hủy'
            };
            return texts[status] || 'Không xác định';
        }

        function refreshDashboard() {
            loadDashboard();
            showToast('Đã làm mới dữ liệu dashboard!', 'success');
        }

        function exportReport() {
            showToast('Đang xuất báo cáo...', 'info');
        }

        // Load users data
        function loadUsers() {
            // Initialize sample data if empty
            if (users.length === 0) {
                users = [
                    { id: 1, name: 'Nguyễn Văn An', email: 'an@student.com', phone: '0123456789', role: 'student', status: 'active', joinDate: '2024-01-15', avatar: 'https://ui-avatars.io/api/?name=Nguyen+Van+An&background=0d6efd&color=fff', school: 'ĐH Bách Khoa', emailVerified: true },
                    { id: 2, name: 'Trần Thị Bình', email: 'binh@student.com', phone: '0987654321', role: 'student', status: 'active', joinDate: '2024-01-20', avatar: 'https://ui-avatars.io/api/?name=Tran+Thi+Binh&background=198754&color=fff', school: 'ĐH Kinh tế', emailVerified: true },
                    { id: 3, name: 'Lê Minh Cường', email: 'cuong@student.com', phone: '0369852147', role: 'premium', status: 'active', joinDate: '2024-02-01', avatar: 'https://ui-avatars.io/api/?name=Le+Minh+Cuong&background=fd7e14&color=fff', school: 'ĐH Khoa học Tự nhiên', emailVerified: true },
                    { id: 4, name: 'Phạm Thu Dung', email: 'dung@student.com', phone: '0147258369', role: 'moderator', status: 'inactive', joinDate: '2024-01-10', avatar: 'https://ui-avatars.io/api/?name=Pham+Thu+Dung&background=dc3545&color=fff', school: 'ĐH Sư phạm', emailVerified: false },
                    { id: 5, name: 'Hoàng Văn Em', email: 'em@student.com', phone: '0258147963', role: 'student', status: 'active', joinDate: '2024-02-15', avatar: 'https://ui-avatars.io/api/?name=Hoang+Van+Em&background=6f42c1&color=fff', school: 'ĐH Công nghệ', emailVerified: true }
                ];
            }
            
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>#${user.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${user.avatar}" class="rounded-circle me-2" width="32" height="32">
                            <div>
                                <div class="fw-bold">${user.name}</div>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge ${user.role === 'premium' ? 'bg-warning' : user.role === 'moderator' ? 'bg-info' : user.role === 'admin' ? 'bg-danger' : 'bg-secondary'}">
                            ${getRoleText(user.role)}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${user.status === 'active' ? 'bg-success' : user.status === 'inactive' ? 'bg-danger' : 'bg-warning'}">
                            ${getStatusText(user.status)}
                        </span>
                    </td>
                    <td>${formatDate(new Date(user.joinDate))}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editUser(${user.id})" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-info" onclick="viewUserDetails(${user.id})" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteUser(${user.id})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Load notifications data
        function loadNotifications() {
            const notifications = [
                { id: 1, title: 'Chào mừng sinh viên mới', type: 'system', status: 'sent', readCount: 234, totalSent: 300, date: '2024-02-20' },
                { id: 2, title: 'Khuyến mãi sách giáo khoa', type: 'promotion', status: 'pending', readCount: 0, totalSent: 0, date: '2024-02-21' },
                { id: 3, title: 'Cập nhật chính sách bảo mật', type: 'update', status: 'sent', readCount: 156, totalSent: 200, date: '2024-02-19' },
                { id: 4, title: 'Thông báo bảo trì hệ thống', type: 'system', status: 'draft', readCount: 0, totalSent: 0, date: '2024-02-22' }
            ];
            
            const container = document.getElementById('notificationsList');
            container.innerHTML = notifications.map(notif => `
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${notif.title}</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge ${notif.type === 'system' ? 'bg-primary' : notif.type === 'promotion' ? 'bg-success' : 'bg-info'}">
                                    ${notif.type === 'system' ? 'Hệ thống' : notif.type === 'promotion' ? 'Khuyến mãi' : 'Cập nhật'}
                                </span>
                                <span class="badge ${notif.status === 'sent' ? 'bg-success' : notif.status === 'pending' ? 'bg-warning' : 'bg-secondary'}">
                                    ${notif.status === 'sent' ? 'Đã gửi' : notif.status === 'pending' ? 'Chờ gửi' : 'Nháp'}
                                </span>
                            </div>
                            <small class="text-muted">
                                ${notif.status === 'sent' ? `${notif.readCount}/${notif.totalSent} đã đọc` : 'Chưa gửi'} • ${formatDate(new Date(notif.date))}
                            </small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editNotification(${notif.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="sendNotification(${notif.id})">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteNotification(${notif.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Load settings data
        function loadSettings() {
            showSettingsTab('general');
        }

        // Show settings tab
        function showSettingsTab(tabName) {
            // Update active tab
            document.querySelectorAll('#settingsTabs .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
            
            const content = document.getElementById('settingsContent');
            
            switch(tabName) {
                case 'general':
                    content.innerHTML = `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Cài đặt chung</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tên website</label>
                                            <input type="text" class="form-control" value="StudentMarket">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Slogan</label>
                                            <input type="text" class="form-control" value="Nền tảng mua bán sinh viên">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả website</label>
                                        <textarea class="form-control" rows="3">Nền tảng mua bán trực tuyến dành riêng cho sinh viên</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email liên hệ</label>
                                            <input type="email" class="form-control" value="admin@studentmarket.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="tel" class="form-control" value="0123456789">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" value="123 Đường ABC, Quận XYZ, TP.HCM">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                </form>
                            </div>
                        </div>
                    `;
                    break;
                case 'email':
                    content.innerHTML = `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Cài đặt Email & SMTP</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" value="smtp.gmail.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" value="587">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" value="noreply@studentmarket.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" value="••••••••">
                                        </div>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="smtpSSL" checked>
                                        <label class="form-check-label" for="smtpSSL">
                                            Sử dụng SSL/TLS
                                        </label>
                                    </div>
                                    <button type="button" class="btn btn-outline-info me-2">Test kết nối</button>
                                    <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                                </form>
                            </div>
                        </div>
                    `;
                    break;
                case 'security':
                    content.innerHTML = `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Cài đặt bảo mật</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Thời gian session (phút)</label>
                                        <input type="number" class="form-control" value="60">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Số lần đăng nhập sai tối đa</label>
                                        <input type="number" class="form-control" value="5">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="twoFA" checked>
                                        <label class="form-check-label" for="twoFA">
                                            Bật xác thực 2 bước
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailVerification" checked>
                                        <label class="form-check-label" for="emailVerification">
                                            Yêu cầu xác thực email
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                                </form>
                            </div>
                        </div>
                    `;
                    break;
                default:
                    content.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                                <h5>Tính năng đang phát triển</h5>
                                <p class="text-muted">Phần cài đặt này sẽ sớm được cập nhật!</p>
                            </div>
                        </div>
                    `;
            }
        }

        // User management functions
        let users = [];
        let editingUserId = null;

        function addUser() {
            editingUserId = null;
            document.getElementById('userModalTitle').textContent = 'Thêm người dùng mới';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
        }

        function editUser(id) {
            const user = users.find(u => u.id === id);
            if (!user) return;
            
            editingUserId = id;
            document.getElementById('userModalTitle').textContent = 'Chỉnh sửa người dùng';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userPhone').value = user.phone || '';
            document.getElementById('userRole').value = user.role;
            document.getElementById('userStatus').value = user.status;
            document.getElementById('userSchool').value = user.school || '';
            document.getElementById('userAddress').value = user.address || '';
            document.getElementById('userEmailVerified').checked = user.emailVerified || false;
            
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.show();
        }

        function saveUser(event) {
            event.preventDefault();
            
            const formData = {
                id: editingUserId || Date.now(),
                name: document.getElementById('userName').value.trim(),
                email: document.getElementById('userEmail').value.trim(),
                phone: document.getElementById('userPhone').value.trim(),
                role: document.getElementById('userRole').value,
                status: document.getElementById('userStatus').value,
                school: document.getElementById('userSchool').value.trim(),
                address: document.getElementById('userAddress').value.trim(),
                emailVerified: document.getElementById('userEmailVerified').checked
            };
            
            // Validate
            if (!formData.name || !formData.email) {
                showToast('Vui lòng nhập đầy đủ thông tin bắt buộc!', 'error');
                return;
            }
            
            // Check duplicate email
            const existingUser = users.find(u => 
                u.email.toLowerCase() === formData.email.toLowerCase() && 
                u.id !== formData.id
            );
            
            if (existingUser) {
                showToast('Email đã tồn tại!', 'error');
                return;
            }
            
            if (editingUserId) {
                // Update existing user
                const index = users.findIndex(u => u.id === editingUserId);
                if (index !== -1) {
                    users[index] = { ...users[index], ...formData };
                    showToast('Đã cập nhật thông tin người dùng!', 'success');
                }
            } else {
                // Add new user
                formData.joinDate = new Date().toISOString().split('T')[0];
                formData.avatar = `https://ui-avatars.io/api/?name=${encodeURIComponent(formData.name)}&background=0d6efd&color=fff`;
                users.push(formData);
                showToast('Đã thêm người dùng mới!', 'success');
            }
            
            // Refresh user list
            loadUsers();
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
        }

        function viewUserDetails(id) {
            const user = users.find(u => u.id === id);
            if (!user) return;
            
            const content = document.getElementById('userDetailsContent');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="${user.avatar}" class="rounded-circle mb-3" width="120" height="120">
                        <h5>${user.name}</h5>
                        <span class="badge ${user.role === 'premium' ? 'bg-warning' : user.role === 'moderator' ? 'bg-info' : user.role === 'admin' ? 'bg-danger' : 'bg-secondary'}">
                            ${getRoleText(user.role)}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${user.email}</td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td>${user.phone || 'Chưa cập nhật'}</td>
                            </tr>
                            <tr>
                                <td><strong>Trường học:</strong></td>
                                <td>${user.school || 'Chưa cập nhật'}</td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ:</strong></td>
                                <td>${user.address || 'Chưa cập nhật'}</td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    <span class="badge ${user.status === 'active' ? 'bg-success' : user.status === 'inactive' ? 'bg-danger' : 'bg-warning'}">
                                        ${getStatusText(user.status)}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email xác thực:</strong></td>
                                <td>
                                    <span class="badge ${user.emailVerified ? 'bg-success' : 'bg-secondary'}">
                                        ${user.emailVerified ? 'Đã xác thực' : 'Chưa xác thực'}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ngày tham gia:</strong></td>
                                <td>${formatDate(new Date(user.joinDate))}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
            
            // Store current user ID for edit button
            window.currentViewingUserId = id;
            
            const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
            modal.show();
        }

        function editUserFromDetails() {
            bootstrap.Modal.getInstance(document.getElementById('userDetailsModal')).hide();
            setTimeout(() => {
                editUser(window.currentViewingUserId);
            }, 300);
        }

        function deleteUser(id) {
            const user = users.find(u => u.id === id);
            if (!user) return;
            
            if (confirm(`Bạn có chắc muốn xóa người dùng "${user.name}"?`)) {
                users = users.filter(u => u.id !== id);
                loadUsers();
                showToast(`Đã xóa người dùng "${user.name}"!`, 'success');
            }
        }

        function getRoleText(role) {
            const roles = {
                'student': 'Sinh viên',
                'premium': 'Premium',
                'moderator': 'Moderator',
                'admin': 'Admin'
            };
            return roles[role] || role;
        }

        function getStatusText(status) {
            const statuses = {
                'active': 'Hoạt động',
                'inactive': 'Tạm khóa',
                'pending': 'Chờ xác thực'
            };
            return statuses[status] || status;
        }

        // Notification management functions
        function createNotification() {
            showToast('Mở form tạo thông báo mới...', 'info');
        }

        function editNotification(id) {
            showToast(`Chỉnh sửa thông báo #${id}...`, 'info');
        }

        function sendNotification(id) {
            showToast(`Gửi thông báo #${id}...`, 'success');
        }

        function deleteNotification(id) {
            if (confirm('Bạn có chắc muốn xóa thông báo này?')) {
                showToast(`Đã xóa thông báo #${id}!`, 'success');
            }
        }

        function filterNotifications(type) {
            // Update active filter button
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            showToast(`Lọc thông báo: ${type}`, 'info');
        }

        function sendBulkNotification() {
            showToast('Mở form gửi thông báo hàng loạt...', 'info');
        }

        function scheduleNotification() {
            showToast('Mở form lên lịch gửi thông báo...', 'info');
        }

        function saveAllSettings() {
            showToast('Đã lưu tất cả cài đặt!', 'success');
        }

        // Promotions and Vouchers data
        let promotions = [];
        let vouchers = [];
        let editingPromotionId = null;
        let editingVoucherId = null;

        // Initialize sample data
        function initializePromotionsData() {
            if (promotions.length === 0) {
                promotions = [
                    {
                        id: 1,
                        name: 'Khuyến mãi sinh viên mới',
                        description: 'Giảm giá cho sinh viên đăng ký lần đầu',
                        type: 'percentage',
                        value: 20,
                        maxValue: 100000,
                        minOrder: 0,
                        startDate: '2024-02-01T00:00',
                        endDate: '2024-02-29T23:59',
                        categories: ['all'],
                        target: 'new',
                        usageLimit: null,
                        userLimit: 1,
                        usedCount: 45,
                        active: true,
                        createdAt: new Date('2024-01-25')
                    },
                    {
                        id: 2,
                        name: 'Flash Sale cuối tuần',
                        description: 'Giảm giá sốc trong 48h',
                        type: 'fixed',
                        value: 50000,
                        maxValue: null,
                        minOrder: 300000,
                        startDate: '2024-02-24T00:00',
                        endDate: '2024-02-25T23:59',
                        categories: ['1', '7'],
                        target: 'all',
                        usageLimit: 100,
                        userLimit: 1,
                        usedCount: 100,
                        active: false,
                        createdAt: new Date('2024-02-20')
                    },
                    {
                        id: 3,
                        name: 'Ưu đãi sách giáo khoa',
                        description: 'Giảm giá đặc biệt cho sách học',
                        type: 'percentage',
                        value: 15,
                        maxValue: 50000,
                        minOrder: 100000,
                        startDate: '2024-03-01T00:00',
                        endDate: '2024-03-31T23:59',
                        categories: ['4'],
                        target: 'student',
                        usageLimit: 500,
                        userLimit: 2,
                        usedCount: 0,
                        active: false,
                        createdAt: new Date('2024-02-28')
                    }
                ];
            }

            if (vouchers.length === 0) {
                vouchers = [
                    {
                        id: 1,
                        code: 'STUDENT20',
                        name: 'Giảm giá sinh viên',
                        description: 'Voucher dành riêng cho sinh viên',
                        type: 'percentage',
                        value: 20,
                        maxValue: 100000,
                        minOrder: 0,
                        quantity: 100,
                        used: 55,
                        startDate: '2024-02-01T00:00',
                        endDate: '2024-03-31T23:59',
                        userLimit: 1,
                        target: 'student',
                        active: true,
                        createdAt: new Date('2024-01-30')
                    },
                    {
                        id: 2,
                        code: 'FREESHIP',
                        name: 'Miễn phí vận chuyển',
                        description: 'Miễn phí ship cho đơn từ 200k',
                        type: 'shipping',
                        value: 0,
                        maxValue: null,
                        minOrder: 200000,
                        quantity: 200,
                        used: 89,
                        startDate: '2024-02-15T00:00',
                        endDate: '2024-04-15T23:59',
                        userLimit: 3,
                        target: 'all',
                        active: true,
                        createdAt: new Date('2024-02-10')
                    },
                    {
                        id: 3,
                        code: 'FLASH50',
                        name: 'Flash Sale 50k',
                        description: 'Giảm ngay 50k cho đơn từ 300k',
                        type: 'fixed',
                        value: 50000,
                        maxValue: null,
                        minOrder: 300000,
                        quantity: 50,
                        used: 50,
                        startDate: '2024-02-20T00:00',
                        endDate: '2024-02-22T23:59',
                        userLimit: 1,
                        target: 'all',
                        active: false,
                        createdAt: new Date('2024-02-18')
                    }
                ];
            }
        }

        // Promotions management functions
        function loadPromotions() {
            initializePromotionsData();
            showPromotionTab('promotions');
        }

        function showPromotionTab(tabName) {
            // Update active tab
            document.querySelectorAll('#promotionTabs .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Find and activate the correct tab
            const targetTab = document.querySelector(`#promotionTabs .nav-link[onclick*="${tabName}"]`);
            if (targetTab) {
                targetTab.classList.add('active');
            }
            
            const content = document.getElementById('promotionTabContent');
            
            if (tabName === 'promotions') {
                content.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Danh sách chương trình khuyến mãi</h6>
                        <button class="btn btn-primary btn-sm" onclick="createPromotion()">
                            <i class="fas fa-plus me-2"></i>Tạo khuyến mãi
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên chương trình</th>
                                    <th>Loại giảm giá</th>
                                    <th>Giá trị</th>
                                    <th>Thời gian</th>
                                    <th>Sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="promotionsTableBody">
                                ${renderPromotionsTable()}
                            </tbody>
                        </table>
                    </div>
                `;
            } else if (tabName === 'vouchers') {
                content.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Danh sách voucher giảm giá</h6>
                        <button class="btn btn-primary btn-sm" onclick="createVoucher()">
                            <i class="fas fa-plus me-2"></i>Tạo voucher
                        </button>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Tìm kiếm mã voucher..." id="voucherSearch" onkeyup="filterVouchers()">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="voucherFilter" onchange="filterVouchers()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="expired">Đã hết hạn</option>
                                <option value="used">Đã sử dụng hết</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="voucherTypeFilter" onchange="filterVouchers()">
                                <option value="">Tất cả loại</option>
                                <option value="percentage">Phần trăm</option>
                                <option value="fixed">Số tiền cố định</option>
                                <option value="shipping">Miễn phí vận chuyển</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="resetVoucherFilters()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã voucher</th>
                                    <th>Tên voucher</th>
                                    <th>Loại & Giá trị</th>
                                    <th>Số lượng</th>
                                    <th>Thời hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="vouchersTableBody">
                                ${renderVouchersTable()}
                            </tbody>
                        </table>
                    </div>
                `;
            }
        }

        // Render functions
        function renderPromotionsTable() {
            return promotions.map(promo => {
                const now = new Date();
                const startDate = new Date(promo.startDate);
                const endDate = new Date(promo.endDate);
                
                let status = 'pending';
                let statusClass = 'bg-warning';
                let statusText = 'Chờ kích hoạt';
                
                if (now >= startDate && now <= endDate && promo.active) {
                    status = 'active';
                    statusClass = 'bg-success';
                    statusText = 'Đang hoạt động';
                } else if (now > endDate || (promo.usageLimit && promo.usedCount >= promo.usageLimit)) {
                    status = 'ended';
                    statusClass = 'bg-secondary';
                    statusText = 'Đã kết thúc';
                } else if (!promo.active) {
                    status = 'inactive';
                    statusClass = 'bg-danger';
                    statusText = 'Đã tắt';
                }
                
                const typeText = promo.type === 'percentage' ? 'Phần trăm' : promo.type === 'fixed' ? 'Số tiền' : 'Mua X tặng Y';
                const typeBadge = promo.type === 'percentage' ? 'bg-info' : promo.type === 'fixed' ? 'bg-warning' : 'bg-success';
                const valueText = promo.type === 'percentage' ? `${promo.value}%` : `${promo.value.toLocaleString()} VNĐ`;
                
                return `
                    <tr>
                        <td>
                            <div>
                                <strong>${promo.name}</strong>
                                <br><small class="text-muted">${promo.description}</small>
                            </div>
                        </td>
                        <td><span class="badge ${typeBadge}">${typeText}</span></td>
                        <td><strong>${valueText}</strong></td>
                        <td>
                            <small>${formatDate(startDate)} - ${formatDate(endDate)}</small>
                        </td>
                        <td>
                            ${promo.usageLimit ? `${promo.usedCount}/${promo.usageLimit}` : promo.usedCount}
                        </td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editPromotion(${promo.id})" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="viewPromotionStats(${promo.id})" title="Thống kê">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                                ${status === 'pending' ? `
                                    <button class="btn btn-outline-success" onclick="activatePromotion(${promo.id})" title="Kích hoạt">
                                        <i class="fas fa-play"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-outline-danger" onclick="deletePromotion(${promo.id})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderVouchersTable(filteredVouchers = null) {
            const vouchersToRender = filteredVouchers || vouchers;
            
            return vouchersToRender.map(voucher => {
                const now = new Date();
                const startDate = new Date(voucher.startDate);
                const endDate = new Date(voucher.endDate);
                const usagePercent = (voucher.used / voucher.quantity) * 100;
                
                let status = 'pending';
                let statusClass = 'bg-warning';
                let statusText = 'Chờ kích hoạt';
                
                if (voucher.used >= voucher.quantity) {
                    status = 'used';
                    statusClass = 'bg-danger';
                    statusText = 'Hết lượt';
                } else if (now > endDate) {
                    status = 'expired';
                    statusClass = 'bg-secondary';
                    statusText = 'Hết hạn';
                } else if (now >= startDate && now <= endDate && voucher.active) {
                    status = 'active';
                    statusClass = 'bg-success';
                    statusText = 'Hoạt động';
                } else if (!voucher.active) {
                    status = 'inactive';
                    statusClass = 'bg-danger';
                    statusText = 'Đã tắt';
                }
                
                const typeText = voucher.type === 'percentage' ? `${voucher.value}%` : 
                               voucher.type === 'fixed' ? `${voucher.value.toLocaleString()} VNĐ` : 'Free Ship';
                const typeBadge = voucher.type === 'percentage' ? 'bg-info' : 
                                voucher.type === 'fixed' ? 'bg-warning' : 'bg-success';
                
                const progressColor = usagePercent >= 90 ? 'bg-danger' : usagePercent >= 70 ? 'bg-warning' : 'bg-success';
                
                return `
                    <tr>
                        <td><code>${voucher.code}</code></td>
                        <td>${voucher.name}</td>
                        <td>
                            <span class="badge ${typeBadge}">${typeText}</span>
                            ${voucher.maxValue ? `<br><small class="text-muted">Tối đa ${voucher.maxValue.toLocaleString()}đ</small>` : ''}
                        </td>
                        <td>
                            <span class="${usagePercent >= 90 ? 'text-danger' : usagePercent >= 70 ? 'text-warning' : 'text-success'}">${voucher.used}</span> / ${voucher.quantity}
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar ${progressColor}" style="width: ${usagePercent}%"></div>
                            </div>
                        </td>
                        <td>
                            <small>Đến ${formatDate(endDate)}</small>
                        </td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editVoucher(${voucher.id})" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="viewVoucherUsage(${voucher.id})" title="Lịch sử sử dụng">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="duplicateVoucher(${voucher.id})" title="Sao chép">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteVoucher(${voucher.id})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // CRUD Functions for Promotions
        function createPromotion() {
            editingPromotionId = null;
            document.getElementById('promotionModalTitle').textContent = 'Thêm chương trình khuyến mãi';
            document.getElementById('promotionForm').reset();
            document.getElementById('promotionId').value = '';
            document.getElementById('promotionActive').checked = true;
            updatePromotionValueLabel();
            
            const modal = new bootstrap.Modal(document.getElementById('addPromotionModal'));
            modal.show();
        }

        function editPromotion(id) {
            const promotion = promotions.find(p => p.id === id);
            if (!promotion) return;
            
            editingPromotionId = id;
            document.getElementById('promotionModalTitle').textContent = 'Chỉnh sửa chương trình khuyến mãi';
            document.getElementById('promotionId').value = promotion.id;
            document.getElementById('promotionName').value = promotion.name;
            document.getElementById('promotionDescription').value = promotion.description || '';
            document.getElementById('promotionType').value = promotion.type;
            document.getElementById('promotionValue').value = promotion.value;
            document.getElementById('promotionMaxValue').value = promotion.maxValue || '';
            document.getElementById('promotionMinOrder').value = promotion.minOrder;
            document.getElementById('promotionStartDate').value = promotion.startDate;
            document.getElementById('promotionEndDate').value = promotion.endDate;
            document.getElementById('promotionUsageLimit').value = promotion.usageLimit || '';
            document.getElementById('promotionUserLimit').value = promotion.userLimit;
            document.getElementById('promotionTarget').value = promotion.target;
            document.getElementById('promotionActive').checked = promotion.active;
            
            updatePromotionValueLabel();
            
            const modal = new bootstrap.Modal(document.getElementById('addPromotionModal'));
            modal.show();
        }

        function savePromotion(event) {
            event.preventDefault();
            
            const formData = {
                id: editingPromotionId || Date.now(),
                name: document.getElementById('promotionName').value.trim(),
                description: document.getElementById('promotionDescription').value.trim(),
                type: document.getElementById('promotionType').value,
                value: parseFloat(document.getElementById('promotionValue').value),
                maxValue: document.getElementById('promotionMaxValue').value ? parseFloat(document.getElementById('promotionMaxValue').value) : null,
                minOrder: parseFloat(document.getElementById('promotionMinOrder').value) || 0,
                startDate: document.getElementById('promotionStartDate').value,
                endDate: document.getElementById('promotionEndDate').value,
                usageLimit: document.getElementById('promotionUsageLimit').value ? parseInt(document.getElementById('promotionUsageLimit').value) : null,
                userLimit: parseInt(document.getElementById('promotionUserLimit').value) || 1,
                target: document.getElementById('promotionTarget').value,
                active: document.getElementById('promotionActive').checked
            };
            
            // Validate
            if (!formData.name || !formData.value || !formData.startDate || !formData.endDate) {
                showToast('Vui lòng nhập đầy đủ thông tin bắt buộc!', 'error');
                return;
            }
            
            if (new Date(formData.startDate) >= new Date(formData.endDate)) {
                showToast('Ngày kết thúc phải sau ngày bắt đầu!', 'error');
                return;
            }
            
            if (editingPromotionId) {
                // Update existing promotion
                const index = promotions.findIndex(p => p.id === editingPromotionId);
                if (index !== -1) {
                    promotions[index] = { ...promotions[index], ...formData };
                    showToast('Đã cập nhật chương trình khuyến mãi!', 'success');
                }
            } else {
                // Add new promotion
                formData.usedCount = 0;
                formData.createdAt = new Date();
                promotions.push(formData);
                showToast('Đã thêm chương trình khuyến mãi mới!', 'success');
            }
            
            // Refresh table
            if (document.getElementById('promotionsTableBody')) {
                document.getElementById('promotionsTableBody').innerHTML = renderPromotionsTable();
            }
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addPromotionModal')).hide();
        }

        function deletePromotion(id) {
            const promotion = promotions.find(p => p.id === id);
            if (!promotion) return;
            
            if (confirm(`Bạn có chắc muốn xóa chương trình "${promotion.name}"?`)) {
                promotions = promotions.filter(p => p.id !== id);
                
                // Refresh table
                if (document.getElementById('promotionsTableBody')) {
                    document.getElementById('promotionsTableBody').innerHTML = renderPromotionsTable();
                }
                
                showToast(`Đã xóa chương trình "${promotion.name}"!`, 'success');
            }
        }

        function activatePromotion(id) {
            const promotion = promotions.find(p => p.id === id);
            if (!promotion) return;
            
            promotion.active = true;
            
            // Refresh table
            if (document.getElementById('promotionsTableBody')) {
                document.getElementById('promotionsTableBody').innerHTML = renderPromotionsTable();
            }
            
            showToast(`Đã kích hoạt chương trình "${promotion.name}"!`, 'success');
        }

        // CRUD Functions for Vouchers
        function createVoucher() {
            editingVoucherId = null;
            document.getElementById('voucherModalTitle').textContent = 'Thêm voucher giảm giá';
            document.getElementById('voucherForm').reset();
            document.getElementById('voucherId').value = '';
            document.getElementById('voucherActive').checked = true;
            updateVoucherValueLabel();
            
            const modal = new bootstrap.Modal(document.getElementById('addVoucherModal'));
            modal.show();
        }

        function editVoucher(id) {
            const voucher = vouchers.find(v => v.id === id);
            if (!voucher) return;
            
            editingVoucherId = id;
            document.getElementById('voucherModalTitle').textContent = 'Chỉnh sửa voucher';
            document.getElementById('voucherId').value = voucher.id;
            document.getElementById('voucherCode').value = voucher.code;
            document.getElementById('voucherName').value = voucher.name;
            document.getElementById('voucherDescription').value = voucher.description || '';
            document.getElementById('voucherType').value = voucher.type;
            document.getElementById('voucherValue').value = voucher.value;
            document.getElementById('voucherMaxValue').value = voucher.maxValue || '';
            document.getElementById('voucherMinOrder').value = voucher.minOrder;
            document.getElementById('voucherQuantity').value = voucher.quantity;
            document.getElementById('voucherStartDate').value = voucher.startDate;
            document.getElementById('voucherEndDate').value = voucher.endDate;
            document.getElementById('voucherUserLimit').value = voucher.userLimit;
            document.getElementById('voucherTarget').value = voucher.target;
            document.getElementById('voucherActive').checked = voucher.active;
            
            updateVoucherValueLabel();
            
            const modal = new bootstrap.Modal(document.getElementById('addVoucherModal'));
            modal.show();
        }

        function saveVoucher(event) {
            event.preventDefault();
            
            const formData = {
                id: editingVoucherId || Date.now(),
                code: document.getElementById('voucherCode').value.trim().toUpperCase(),
                name: document.getElementById('voucherName').value.trim(),
                description: document.getElementById('voucherDescription').value.trim(),
                type: document.getElementById('voucherType').value,
                value: parseFloat(document.getElementById('voucherValue').value) || 0,
                maxValue: document.getElementById('voucherMaxValue').value ? parseFloat(document.getElementById('voucherMaxValue').value) : null,
                minOrder: parseFloat(document.getElementById('voucherMinOrder').value) || 0,
                quantity: parseInt(document.getElementById('voucherQuantity').value),
                startDate: document.getElementById('voucherStartDate').value,
                endDate: document.getElementById('voucherEndDate').value,
                userLimit: parseInt(document.getElementById('voucherUserLimit').value) || 1,
                target: document.getElementById('voucherTarget').value,
                active: document.getElementById('voucherActive').checked
            };
            
            // Validate
            if (!formData.code || !formData.name || !formData.quantity || !formData.startDate || !formData.endDate) {
                showToast('Vui lòng nhập đầy đủ thông tin bắt buộc!', 'error');
                return;
            }
            
            // Check duplicate code
            const existingVoucher = vouchers.find(v => 
                v.code === formData.code && v.id !== formData.id
            );
            
            if (existingVoucher) {
                showToast('Mã voucher đã tồn tại!', 'error');
                return;
            }
            
            if (new Date(formData.startDate) >= new Date(formData.endDate)) {
                showToast('Ngày hết hạn phải sau ngày bắt đầu!', 'error');
                return;
            }
            
            if (editingVoucherId) {
                // Update existing voucher
                const index = vouchers.findIndex(v => v.id === editingVoucherId);
                if (index !== -1) {
                    vouchers[index] = { ...vouchers[index], ...formData };
                    showToast('Đã cập nhật voucher!', 'success');
                }
            } else {
                // Add new voucher
                formData.used = 0;
                formData.createdAt = new Date();
                vouchers.push(formData);
                showToast('Đã thêm voucher mới!', 'success');
            }
            
            // Refresh table
            if (document.getElementById('vouchersTableBody')) {
                document.getElementById('vouchersTableBody').innerHTML = renderVouchersTable();
            }
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addVoucherModal')).hide();
        }

        function deleteVoucher(id) {
            const voucher = vouchers.find(v => v.id === id);
            if (!voucher) return;
            
            if (confirm(`Bạn có chắc muốn xóa voucher "${voucher.code}"?`)) {
                vouchers = vouchers.filter(v => v.id !== id);
                
                // Refresh table
                if (document.getElementById('vouchersTableBody')) {
                    document.getElementById('vouchersTableBody').innerHTML = renderVouchersTable();
                }
                
                showToast(`Đã xóa voucher "${voucher.code}"!`, 'success');
            }
        }

        function duplicateVoucher(id) {
            const voucher = vouchers.find(v => v.id === id);
            if (!voucher) return;
            
            const newVoucher = {
                ...voucher,
                id: Date.now(),
                code: voucher.code + '_COPY',
                name: voucher.name + ' (Copy)',
                used: 0,
                createdAt: new Date()
            };
            
            vouchers.push(newVoucher);
            
            // Refresh table
            if (document.getElementById('vouchersTableBody')) {
                document.getElementById('vouchersTableBody').innerHTML = renderVouchersTable();
            }
            
            showToast(`Đã sao chép voucher "${voucher.code}"!`, 'success');
        }

        // Utility functions
        function updatePromotionValueLabel() {
            const type = document.getElementById('promotionType').value;
            const label = document.getElementById('promotionValueLabel');
            
            switch(type) {
                case 'percentage':
                    label.textContent = 'Giá trị giảm (%)';
                    break;
                case 'fixed':
                    label.textContent = 'Số tiền giảm (VNĐ)';
                    break;
                case 'buy_x_get_y':
                    label.textContent = 'Số lượng mua';
                    break;
            }
        }

        function updateVoucherValueLabel() {
            const type = document.getElementById('voucherType').value;
            const label = document.getElementById('voucherValueLabel');
            const valueField = document.getElementById('voucherValue');
            
            switch(type) {
                case 'percentage':
                    label.textContent = 'Giá trị giảm (%)';
                    valueField.required = true;
                    valueField.disabled = false;
                    break;
                case 'fixed':
                    label.textContent = 'Số tiền giảm (VNĐ)';
                    valueField.required = true;
                    valueField.disabled = false;
                    break;
                case 'shipping':
                    label.textContent = 'Miễn phí vận chuyển';
                    valueField.required = false;
                    valueField.disabled = true;
                    valueField.value = 0;
                    break;
            }
        }

        function generateVoucherCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('voucherCode').value = result;
        }

        function filterVouchers() {
            const searchTerm = document.getElementById('voucherSearch')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('voucherFilter')?.value || '';
            const typeFilter = document.getElementById('voucherTypeFilter')?.value || '';
            
            let filtered = vouchers;
            
            // Search filter
            if (searchTerm) {
                filtered = filtered.filter(v => 
                    v.code.toLowerCase().includes(searchTerm) ||
                    v.name.toLowerCase().includes(searchTerm)
                );
            }
            
            // Status filter
            if (statusFilter) {
                const now = new Date();
                filtered = filtered.filter(v => {
                    const endDate = new Date(v.endDate);
                    const startDate = new Date(v.startDate);
                    
                    switch(statusFilter) {
                        case 'active':
                            return v.active && now >= startDate && now <= endDate && v.used < v.quantity;
                        case 'expired':
                            return now > endDate;
                        case 'used':
                            return v.used >= v.quantity;
                        default:
                            return true;
                    }
                });
            }
            
            // Type filter
            if (typeFilter) {
                filtered = filtered.filter(v => v.type === typeFilter);
            }
            
            // Update table
            if (document.getElementById('vouchersTableBody')) {
                document.getElementById('vouchersTableBody').innerHTML = renderVouchersTable(filtered);
            }
        }

        function resetVoucherFilters() {
            document.getElementById('voucherSearch').value = '';
            document.getElementById('voucherFilter').value = '';
            document.getElementById('voucherTypeFilter').value = '';
            
            // Refresh table with all vouchers
            if (document.getElementById('vouchersTableBody')) {
                document.getElementById('vouchersTableBody').innerHTML = renderVouchersTable();
            }
            
            showToast('Đã reset bộ lọc!', 'info');
        }

        function viewPromotionStats(id) {
            const promotion = promotions.find(p => p.id === id);
            if (!promotion) return;
            
            showToast(`Xem thống kê chương trình "${promotion.name}"...`, 'info');
        }

        function viewVoucherUsage(id) {
            const voucher = vouchers.find(v => v.id === id);
            if (!voucher) return;
            
            showToast(`Xem lịch sử sử dụng voucher "${voucher.code}"...`, 'info');
        }

        // Navigation handlers
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                const href = this.getAttribute('href');
                const pageName = href.replace('#', '');
                
                // Load the appropriate page
                if (['dashboard', 'categories', 'users', 'notifications', 'settings', 'promotions'].includes(pageName)) {
                    loadPage(pageName);
                } else {
                    // Handle other navigation
                    switch(pageName) {
                        case 'products':
                            showToast('Chuyển đến Quản lý sản phẩm...', 'info');
                            break;
                        case 'orders':
                            showToast('Chuyển đến Quản lý đơn hàng...', 'info');
                            break;
                        case 'blog':
                            showToast('Chuyển đến Quản lý blog...', 'info');
                            break;
                        case 'reports':
                            showToast('Chuyển đến Báo cáo thống kê...', 'info');
                            break;
                        case 'reviews':
                            showToast('Chuyển đến Quản lý đánh giá...', 'info');
                            break;
                        case 'support':
                            showToast('Chuyển đến Hỗ trợ khách hàng...', 'info');
                            break;
                        case 'profile':
                            showToast('Chuyển đến Hồ sơ admin...', 'info');
                            break;
                        case 'logout':
                            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                                showToast('Đang đăng xuất...', 'info');
                            }
                            break;
                    }
                }
            });
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9855e6bd66ccdd32',t:'MTc1ODkyMjYzNC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

@endsection