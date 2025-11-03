<template>
  <div class="flex h-screen w-full">
    <!-- Left side: Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-white relative z-20 shadow-lg">
      <div class="w-full max-w-md px-6 py-6 overflow-y-auto sm:rounded-lg" style="max-height: 90vh;">
        <div><slot name="logo" /></div>
        <div><slot name="status" /></div>
        <div><slot name="forms" /></div>
      </div>
    </div>

    <!-- Right side: Background Image -->
    <div class="hidden md:block w-1/2 relative">
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/bg.jpg)' }"
      ></div>
      <div class="absolute inset-0 bg-primary/60"></div>
    </div>
  </div>
</template>
