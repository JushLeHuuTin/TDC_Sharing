<template>
    <div class="w-64 bg-gray-800 text-white flex flex-col">
      <div class="p-4 text-2xl font-bold border-b border-gray-700">Admin</div>
      <nav class="flex-1 p-4 space-y-2">
        <router-link
          v-for="item in menu"
          :key="item.path"
          :to="item.path"
          class="block p-2 rounded hover:bg-gray-700"
          active-class="bg-gray-700"
        >
          {{ item.label }}
        </router-link>
      </nav>
    </div>
  </template>
  
  <script setup>
  const menu = [
    { label: 'Dashboard', path: '/admin' },
    { label: 'Sản phẩm', path: '/admin/products' },
    { label: 'Người dùng', path: '/admin/users' },
  ]
  </script>
  