<template>
    <div class="flex h-screen bg-gray-100">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Content -->
      <div class="flex-1 p-6 overflow-y-auto">
        <router-view />
      </div>
    </div>
  </template>
  
  <script setup>
  import Sidebar from './Sidebar.vue'
  </script>
  