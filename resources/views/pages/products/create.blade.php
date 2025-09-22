{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng bán sản phẩm - StudentMarket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Đăng bán sản phẩm</h1>
            <p class="text-gray-600">Điền thông tin chi tiết để thu hút người mua</p>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Product Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Hình ảnh sản phẩm <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4" id="imagePreview">
                    <!-- Main upload area -->
                    <div class="col-span-2 md:col-span-2 row-span-2">
                        <label for="mainImage" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Ảnh chính</span>
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG (MAX. 5MB)</p>
                            </div>
                            <input id="mainImage" name="images[]" type="file" class="hidden" accept="image/*" multiple>
                        </label>
                    </div>
                    
                    <!-- Additional image slots -->
                    @for($i = 1; $i <= 6; $i++)
                        <div class="aspect-square">
                            <label for="image{{ $i }}" class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <i class="fas fa-plus text-2xl text-gray-400"></i>
                                <input id="image{{ $i }}" name="images[]" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                    @endfor
                </div>
                <p class="text-sm text-gray-500">Tối đa 8 ảnh. Ảnh đầu tiên sẽ là ảnh đại diện.</p>
            </div>

            <!-- Product Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       required
                       maxlength="100"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                       placeholder="VD: iPhone 13 Pro Max 256GB màu xanh, như mới 95%">
                <div class="flex justify-between mt-1">
                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="text-sm text-gray-500">Tiêu đề hấp dẫn sẽ thu hút nhiều người mua hơn</p>
                    @enderror
                    <span class="text-sm text-gray-500" id="titleCount">0/100</span>
                </div>
            </div>

            <!-- Category -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Danh mục <span class="text-red-500">*</span>
                    </label>
                    <select id="category" 
                            name="category_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                        <option value="">Chọn danh mục</option>
                        <option value="1">📱 Điện tử</option>
                        <option value="2">📚 Sách vở</option>
                        <option value="3">👕 Thời trang</option>
                        <option value="4">🏠 Đồ dùng</option>
                        <option value="5">🚲 Xe cộ</option>
                        <option value="6">⚽ Thể thao</option>
                        <option value="7">🎵 Âm nhạc</option>
                        <option value="8">🎨 Nghệ thuật</option>
                        <option value="9">🔧 Khác</option>
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                        Tình trạng <span class="text-red-500">*</span>
                    </label>
                    <select id="condition" 
                            name="condition" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('condition') border-red-500 @enderror">
                        <option value="">Chọn tình trạng</option>
                        <option value="new">🆕 Mới 100%</option>
                        <option value="like_new">✨ Như mới (95%)</option>
                        <option value="good">👍 Tốt (80%)</option>
                        <option value="fair">👌 Khá (60%)</option>
                        <option value="poor">🔧 Cần sửa chữa</option>
                    </select>
                    @error('condition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Price -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Giá bán <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="price" 
                               name="price" 
                               required
                               min="0"
                               step="1000"
                               class="w-full pl-3 pr-12 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror"
                               placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₫</span>
                        </div>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @endif
                </div>

                <div>
                    <label for="original_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Giá gốc (tùy chọn)
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="original_price" 
                               name="original_price" 
                               min="0"
                               step="1000"
                               class="w-full pl-3 pr-12 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₫</span>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Để hiển thị % giảm giá</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả chi tiết <span class="text-red-500">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="6" 
                          required
                          maxlength="2000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          placeholder="Mô tả chi tiết về sản phẩm:
- Tình trạng hiện tại
- Lý do bán
- Phụ kiện kèm theo
- Thông tin bảo hành
- Điều kiện giao dịch"></textarea>
                <div class="flex justify-between mt-1">
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="text-sm text-gray-500">Mô tả càng chi tiết, cơ hội bán càng cao</p>
                    @enderror
                    <span class="text-sm text-gray-500" id="descCount">0/2000</span>
                </div>
            </div>

            <!-- Location -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="district" class="block text-sm font-medium text-gray-700 mb-2">
                        Quận/Huyện <span class="text-red-500">*</span>
                    </label>
                    <select id="district" 
                            name="district" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('district') border-red-500 @enderror">
                        <option value="">Chọn quận/huyện</option>
                        <option value="district1">Quận 1</option>
                        <option value="district3">Quận 3</option>
                        <option value="district5">Quận 5</option>
                        <option value="district7">Quận 7</option>
                        <option value="district10">Quận 10</option>
                        <option value="thu-duc">Thủ Đức</option>
                        <option value="binh-thanh">Bình Thạnh</option>
                        <option value="tan-binh">Tân Bình</option>
                    </select>
                    @error('district')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="university" class="block text-sm font-medium text-gray-700 mb-2">
                        Trường đại học
                    </label>
                    <select id="university" 
                            name="university" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Chọn trường (tùy chọn)</option>
                        <option value="hcmus">ĐH Khoa học Tự nhiên</option>
                        <option value="hcmut">ĐH Bách khoa</option>
                        <option value="hcmue">ĐH Sư phạm</option>
                        <option value="ueh">ĐH Kinh tế</option>
                        <option value="hcmuaf">ĐH Nông Lâm</option>
                        <option value="other">Khác</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Giúp sinh viên cùng trường dễ tìm thấy</p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Số điện thoại liên hệ <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="contact_phone" 
                           name="contact_phone" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_phone') border-red-500 @enderror"
                           placeholder="0123 456 789">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="preferred_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Cách liên hệ ưu tiên
                    </label>
                    <select id="preferred_contact" 
                            name="preferred_contact" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="chat">💬 Chat trên app</option>
                        <option value="phone">📞 Gọi điện</option>
                        <option value="both">📱 Cả hai</option>
                    </select>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tùy chọn thêm</h3>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="negotiable" value="1" class="rounded border-gray-300 text-blue-600 mr-3">
                        <span class="text-sm text-gray-700">Có thể thương lượng giá</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="delivery_available" value="1" class="rounded border-gray-300 text-blue-600 mr-3">
                        <span class="text-sm text-gray-700">Hỗ trợ giao hàng (phí ship tùy khoảng cách)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="urgent_sale" value="1" class="rounded border-gray-300 text-blue-600 mr-3">
                        <span class="text-sm text-gray-700">Cần bán gấp</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                            name="action" 
                            value="publish"
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-rocket mr-2"></i>
                        Đăng bán ngay
                    </button>
                    
                    <button type="submit" 
                            name="action" 
                            value="draft"
                            class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Lưu nháp
                    </button>
                    
                    <a href="#" class="flex-1 text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Hủy bỏ
                    </a>
                </div>
                
                <p class="text-sm text-gray-500 mt-4 text-center">
                    Bằng cách đăng sản phẩm, bạn đồng ý với 
                    <a href="#" class="text-blue-600 hover:text-blue-800">Điều khoản sử dụng</a> 
                    của StudentMarket
                </p>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Character counters
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('titleCount').textContent = this.value.length + '/100';
});

document.getElementById('description').addEventListener('input', function() {
    document.getElementById('descCount').textContent = this.value.length + '/2000';
});

// Image preview functionality
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const label = input.closest('label');
                label.style.backgroundImage = `url(${e.target.result})`;
                label.style.backgroundSize = 'cover';
                label.style.backgroundPosition = 'center';
                label.innerHTML = '<div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity"><i class="fas fa-edit text-white text-xl"></i></div>';
            };
            
            reader.readAsDataURL(file);
        }
    });
});

// Price formatting
document.getElementById('price').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    this.value = value;
});

document.getElementById('original_price').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    this.value = value;
});
</script>
@endpush
@endsection