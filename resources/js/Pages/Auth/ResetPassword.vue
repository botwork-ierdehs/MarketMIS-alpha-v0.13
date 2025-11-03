<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Background from '@/Shared/Background.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Footer from '@/Components/Footer.vue';
import Logo from '@/Shared/Logo.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  email: String,
  token: String,
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

// Show/hide password
const showPassword = ref(false);
const showConfirm = ref(false);

// Password strength logic
const passwordStrength = ref('');
watch(() => form.password, (val) => {
  if (!val) passwordStrength.value = '';
  else if (val.length < 6) passwordStrength.value = 'Weak';
  else if (val.match(/[A-Z]/) && val.match(/[0-9]/) && val.length >= 8)
    passwordStrength.value = 'Strong';
  else passwordStrength.value = 'Medium';
});

const strengthColor = computed(() => {
  switch (passwordStrength.value) {
    case 'Weak':
      return 'text-red-500';
    case 'Medium':
      return 'text-yellow-500';
    case 'Strong':
      return 'text-green-500';
    default:
      return 'text-gray-400';
  }
});

const submit = () => {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Reset Password - MARKETMIS" />

  <div class="flex flex-col md:flex-row h-screen w-full overflow-hidden">
    <!-- Left section -->
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white px-6 md:px-12 py-8 relative">

      <!-- Back to Login -->
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


      <!-- Form Container -->
      <div class="w-full max-w-md space-y-6 mt-6 md:mt-0 mb-10">
        <!-- 🏷 Logo -->
        <div class="flex justify-center mb-4">
          <Logo class="w-32 md:w-40 h-auto" />
        </div>


        <h2 class="text-2xl font-semibold text-center text-primary">
          Reset Your Password
        </h2>
        <p class="text-center text-sm text-gray-500 mb-6">
          Enter your new password below to regain access to your account.
        </p>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Password -->
          <div>
            <InputLabel for="password" value="New Password" />
            <div class="relative">
              <TextInput
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-primary focus:border-primary"
                required
                autocomplete="new-password"
                placeholder="Enter a strong password"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-primary"
              >
                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.076.172-2.11.492-3.076M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88M15 15l6 6M9 9L3 3" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            <p v-if="passwordStrength" :class="['text-xs mt-1 font-medium', strengthColor]">
              Password Strength: {{ passwordStrength }}
            </p>
            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <!-- Confirm Password -->
          <div>
            <InputLabel for="password_confirmation" value="Confirm Password" />
            <div class="relative">
              <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-primary focus:border-primary"
                required
                autocomplete="new-password"
                placeholder="Confirm your password"
              />
              <button
                type="button"
                @click="showConfirm = !showConfirm"
                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-primary"
              >
                <svg v-if="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.076.172-2.11.492-3.076M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88M15 15l6 6M9 9L3 3" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
          </div>
           <div class="flex items-center justify-end mt-4">
                 <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="w-1/2 py-2 mt-4 text-white font-semibold rounded-md bg-btn-gradient hover:bg-primary/80 transition"
                >
                    Reset Password
                </PrimaryButton>
            </div>

         
        </form>
      </div>
    </div>

    <!-- Right Side -->
    <div class="hidden md:block md:w-1/2 relative">
      <div class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: 'url(/images/bg.jpg)' }"></div>
      <div class="absolute inset-0 bg-primary/60"></div>
      <div class="absolute inset-0 flex flex-col justify-center items-center text-white text-center px-8">
        <h1 class="text-4xl font-bold mb-2">Welcome Back to MarketMIS</h1>
        <p class="text-lg text-gray-100">Secure and Simplify Your Market Management</p>
      </div>
    </div>
  </div>

  <Footer />
</template>
