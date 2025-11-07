<script setup>
// Footer thường là component tĩnh nên không cần logic trong script setup
// Nếu bạn có các liên kết động (ví dụ: lấy từ Vuex Store), bạn có thể thêm logic ở đây.

// Giả định không có props nào được truyền vào Footer
</script>

<template>
    <!-- Nội dung Footer được chuyển trực tiếp từ HTML/Tailwind -->
    <footer class="bg-gray-800 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Cột 1: Logo và Mô tả -->
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-2">
                            <fa :icon="['fas', 'graduation-cap']" class="text-white" />
                        </div>
                        <h3 class="text-xl font-bold">TDC_Sharing</h3>
                    </div>
                    <p class="text-gray-400">Nền tảng mua bán dành riêng cho sinh viên Việt Nam</p>
                </div>
                
                <!-- Cột 2: Danh mục -->
                <div>
                    <h4 class="font-semibold mb-4">Danh mục</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Sách giáo khoa</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Đồ điện tử</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Quần áo</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Đồ dùng học tập</a></li>
                    </ul>
                </div>
                
                <!-- Cột 3: Hỗ trợ -->
                <div>
                    <h4 class="font-semibold mb-4">Hỗ trợ</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Hướng dẫn mua bán</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Liên hệ</a></li>
                    </ul>
                </div>
                
                <!-- Cột 4: Kết nối (Social Media) -->
                <div>
                    <h4 class="font-semibold mb-4">Kết nối</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <fa icon="facebook" class="text-xl" />
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <fa :icon="['fab', 'instagram']" class="text-xl" />
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <fa :icon="['fab', 'twitter']" class="text-xl" />
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <fa :icon="['fab', 'youtube']" class="text-xl" />
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Bản quyền -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 TDC_Sharing. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* Không cần style bổ sung nếu chỉ dùng Tailwind CSS */
</style>