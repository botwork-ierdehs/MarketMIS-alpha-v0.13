<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next'; // 👁 Import icons here
import Logo from '@/Shared/Logo.vue';
import Background from '@/Shared/Background.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Footer from '@/Components/Footer.vue';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const showPassword = ref(false); // 👁 renamed for clarity

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? 'on' : '',
    }))
    .post(route('login'), {
      onFinish: () => form.reset('password'),
    });
};
</script>
<template>
  <Head title="MARKETMIS" />
  <div class="flex flex-col md:flex-row h-screen w-full overflow-hidden">

    <!-- Left Side: Registration Form -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-white px-6 md:px-12 py-8">
    <div class="w-full max-w-md space-y-6 mt-6 md:mt-0 mb-10">
        <!-- 🏷 Logo -->
        <div class="flex justify-center mb-4">
          <Logo class="w-32 md:w-40 h-auto" />
        </div>

        <!-- Form -->
        <form @submit.prevent="submit">
        <!-- Email -->
        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <!-- Password with Show/Hide -->
        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <div class="relative">
            <TextInput
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="mt-1 block w-full pr-10"
              required
              autocomplete="current-password"
            />
            <!-- 👁 Button -->
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
              tabindex="-1"
            >
              <component :is="showPassword ? EyeOffIcon : EyeIcon" class="w-5 h-5" />
            </button>
          </div>
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <!-- Remember me -->
        <div class="block mt-4">
          <label class="flex items-center">
            <Checkbox v-model:checked="form.remember" name="remember" />
            <span class="ms-2 text-sm text-gray-600">Remember me</span>
          </label>
        </div>

        <!-- Submit -->
        <div class="block my-4">
          <button
            type="submit"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="w-full bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary/70 transition-colors"
          >
            Sign In
          </button>
        </div>

        <!-- Links -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-between items-center">
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Forgot your password?
          </Link>

          <Link
            v-if="canResetPassword"
            :href="route('register')"
            class="underline font-bold text-sm text-red-600 hover:text-red-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Not yet registered?
          </Link>
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

