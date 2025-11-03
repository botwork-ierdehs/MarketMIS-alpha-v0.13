<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Logo from '@/Shared/Logo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Footer from '@/Components/Footer.vue';

defineProps({
  status: String,
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <Head title="MARKETMIS" />
  <div class="flex flex-col md:flex-row h-screen w-full overflow-hidden">

    <!-- LEFT SIDE (Form Section) -->
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white px-6 md:px-16 py-8 relative">

      <div class="absolute top-6 left-6">
        <Link
          :href="route('login')"
          class="flex items-center gap-2 text-primary hover:text-secondary transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          <span class="text-sm font-medium">Back to Login</span>
        </Link>
      </div>

      <div class="w-full max-w-md space-y-6 mt-10 md:mt-0">
        <!-- 🏷 Logo -->
        <div class="flex justify-center mb-4">
          <Logo class="w-32 md:w-40 h-auto" />
        </div>
        <!-- 💬 Instructions -->
        <p class="text-gray-700 text-sm text-center leading-relaxed bg-gray-50 border border-gray-100 p-4 rounded-lg shadow-sm">
          Forgot your password? No problem.<br />
          Enter your email address below and we’ll send you a password reset link.
        </p>

        <!-- ✅ Status Message -->
        <div v-if="status" class="text-center text-green-600 text-sm font-medium">
          {{ status }}
        </div>

        <!-- ✉️ Email Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <InputLabel for="email" value="Email Address" />
            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
              required
              autofocus
              autocomplete="username"
              placeholder="Enter your registered email"
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div class="flex items-center justify-end mt-4">
                 <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="w-1/2 py-2 mt-4 text-white font-semibold rounded-md bg-btn-gradient hover:bg-primary/80 transition"
                >
              Email Password Reset Link
                </PrimaryButton>
            </div>

         
        </form>
      </div>
    </div>

     <!-- Right Side: Background -->
    <div class="hidden md:block md:w-1/2 relative">
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/bg.jpg)' }"
      ></div>
      <div class="absolute inset-0 bg-primary/60"></div>
      <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center px-8">
        <h1 class="text-4xl font-bold mb-2">Welcome to MarketMIS</h1>
        <p class="text-lg text-gray-100">Empowering Local Market Management</p>
      </div>
    </div>
  </div>

  <Footer />
</template>
