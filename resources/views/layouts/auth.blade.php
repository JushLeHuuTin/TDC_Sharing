{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'StudentMarket - Chợ Sinh Viên')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            box-sizing: border-box;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
        }

        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .category-tree {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .category-item {
            padding: 10px 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 8px;
            background: white;
            transition: all 0.3s ease;
        }

        .category-item:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }

        .category-level-1 {
            border-left: 4px solid #0d6efd;
            font-weight: 600;
        }

        .category-level-2 {
            margin-left: 30px;
            border-left: 4px solid #6c757d;
            background-color: #f8f9fa;
        }

        .category-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .category-item:hover .category-actions {
            opacity: 1;
        }

        .breadcrumb {
            background: none;
            padding: 0;
        }

        .page-header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>
   @include('components.admin.sidebar')
    <main>

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let categories = [];
        let selectedCategoryId = null;
        let editingCategoryId = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPage('categories'); // Load categories page by default
        });

        // Load page content
        function loadPage(pageName) {
            const mainContent = document.getElementById('mainContent');
            
            switch(pageName) {
                case 'categories':
                    mainContent.innerHTML = getCategoriesPageHTML();
                    loadCategories();
                    loadParentOptions();
                    break;
                case 'users':
                    mainContent.innerHTML = getUsersPageHTML();
                    loadUsers();
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
                        <button class="btn btn-primary" onclick="addUser()">
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
                html += createCategoryItem(category);
                
                // Add level 2 categories
                const level2Categories = categories
                    .filter(c => c.parent_id === category.id)
                    .sort((a, b) => a.order - b.order);
                
                level2Categories.forEach(subCategory => {
                    html += createCategoryItem(subCategory);
                });
            });
            
            container.innerHTML = html;
        }

        // Create category item HTML
        function createCategoryItem(category) {
            const isSelected = selectedCategoryId === category.id;
            const levelClass = `category-level-${category.level}`;
            
            return `
                <div class="category-item ${levelClass} ${isSelected ? 'border-primary bg-light' : ''}" 
                     onclick="selectCategory(${category.id})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
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

        // Expand/Collapse functions
        function expandAll() {
            showToast('Đã mở rộng tất cả danh mục!', 'info');
        }

        function collapseAll() {
            showToast('Đã thu gọn tất cả danh mục!', 'info');
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

        // Load users data
        function loadUsers() {
            const users = [
                { id: 1, name: 'Nguyễn Văn An', email: 'an@student.com', role: 'Sinh viên', status: 'active', joinDate: '2024-01-15', avatar: 'https://ui-avatars.io/api/?name=Nguyen+Van+An&background=0d6efd&color=fff' },
                { id: 2, name: 'Trần Thị Bình', email: 'binh@student.com', role: 'Sinh viên', status: 'active', joinDate: '2024-01-20', avatar: 'https://ui-avatars.io/api/?name=Tran+Thi+Binh&background=198754&color=fff' },
                { id: 3, name: 'Lê Minh Cường', email: 'cuong@student.com', role: 'Premium', status: 'active', joinDate: '2024-02-01', avatar: 'https://ui-avatars.io/api/?name=Le+Minh+Cuong&background=fd7e14&color=fff' },
                { id: 4, name: 'Phạm Thu Dung', email: 'dung@student.com', role: 'Moderator', status: 'inactive', joinDate: '2024-01-10', avatar: 'https://ui-avatars.io/api/?name=Pham+Thu+Dung&background=dc3545&color=fff' },
                { id: 5, name: 'Hoàng Văn Em', email: 'em@student.com', role: 'Sinh viên', status: 'active', joinDate: '2024-02-15', avatar: 'https://ui-avatars.io/api/?name=Hoang+Van+Em&background=6f42c1&color=fff' }
            ];
            
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
                        <span class="badge ${user.role === 'Premium' ? 'bg-warning' : user.role === 'Moderator' ? 'bg-info' : 'bg-secondary'}">
                            ${user.role}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${user.status === 'active' ? 'bg-success' : 'bg-danger'}">
                            ${user.status === 'active' ? 'Hoạt động' : 'Tạm khóa'}
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
        function addUser() {
            showToast('Mở form thêm người dùng mới...', 'info');
        }

        function editUser(id) {
            showToast(`Chỉnh sửa người dùng #${id}...`, 'info');
        }

        function viewUserDetails(id) {
            showToast(`Xem chi tiết người dùng #${id}...`, 'info');
        }

        function deleteUser(id) {
            if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
                showToast(`Đã xóa người dùng #${id}!`, 'success');
            }
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
                if (['categories', 'users', 'notifications', 'settings'].includes(pageName)) {
                    loadPage(pageName);
                } else {
                    // Handle other navigation
                    switch(pageName) {
                        case 'dashboard':
                            showToast('Chuyển đến Dashboard...', 'info');
                            break;
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985518a8929085d3',t:'MTc1ODkxNDE5My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

{{-- @endsection --}}
