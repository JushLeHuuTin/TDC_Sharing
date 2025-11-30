<template>
    <div>
      <h1 class="text-2xl font-bold mb-4">Quản lý sản phẩm</h1>
      <button
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        @click="addProduct"
      >
        + Thêm sản phẩm
      </button>
  
      <ul class="mt-4 space-y-2">
        <li
          v-for="(p, i) in products"
          :key="i"
          class="p-3 bg-white rounded shadow flex justify-between"
        >
          {{ p.name }}
          <button @click="remove(i)" class="text-red-500">X</button>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const products = ref([
    { name: 'Sản phẩm A' },
    { name: 'Sản phẩm B' }
  ])
  
  const addProduct = () => {
    const name = prompt('Nhập tên sản phẩm mới:')
    if (name) products.value.push({ name })
  }
  
  const remove = (i) => {
    products.value.splice(i, 1)
  }
  </script>
  