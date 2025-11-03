<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm, Head } from '@inertiajs/vue3';
import Logo from '@/Shared/Logo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Footer from '@/Components/Footer.vue';
import Radio from '@/Shared/Radio.vue';
import DatePick from '@/Shared/DatePick.vue';
import SelectInput from '@/Shared/SelectInput.vue';
import barangays from '@/data/barangays.json';
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next';
import PrivacyPolicy from '../PrivacyPolicy.vue';
import TermsOfService from '../TermsOfService.vue';
import SuccesfullRegistration from './SuccesfullRegistration.vue';

const selectedGender = ref(1);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const showTermsModal = ref(false);
const showPrivacyModal = ref(false);
const showSuccessRegistration = ref(false);

const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  role_id: 3,
  gender_id: selectedGender.value,
  sub_admin_type_id: null,
  user_type_id: null,
  department_id: null,
  address: '',
  mobile: '',
  birthday: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post(route('register'), {
    onSuccess: () => {
      showSuccessRegistration.value = true;
      form.terms = false;
      form.reset(
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'address',
        'birthday',
        'gender_id',
        'sub_admin_type_id',
        'user_type_id',
        'department_id',
        'password',
        'password_confirmation'
      );
    }
  });
};

const genderOptions = [
  { label: 'Female', value: 1 },
  { label: 'Male', value: 2 },
  { label: 'Other', value: 3 },
];

const passwordStrength = computed(() => {
  const pwd = form.password || '';
  let score = 0;
  if (pwd.length >= 8) score++;
  if (/[A-Z]/.test(pwd)) score++;
  if (/[a-z]/.test(pwd)) score++;
  if (/[0-9]/.test(pwd)) score++;
  if (/[@$!%*?&#]/.test(pwd)) score++;

  if (score <= 2) return 'Weak';
  if (score === 3 || score === 4) return 'Moderate';
  if (score === 5) return 'Strong';
  return '';
});

watch(selectedGender, (val) => {
  form.gender_id = val;
});

const handlePrivacyAccept = () => {
  form.terms = true;
  showPrivacyModal.value = false;
};

const handlePrivacyDecline = () => {
  form.terms = false;
  showPrivacyModal.value = false;
};

const handleTermsAccept = () => {
  form.terms = true;
  showTermsModal.value = false;
};

const handleCloseSuccess = () => {
  showSuccessRegistration.value = false;
};

const handleTermsDecline = () => {
  form.terms = false;
  showTermsModal.value = false;
};
</script>

<template>
  <Head title="MARKETMIS" />
  <div class="flex flex-col md:flex-row min-h-screen w-full bg-gray-50">

    <!-- Left Side: Registration Form -->
    <div
      class="w-full md:w-1/2 flex flex-col bg-white px-6 md:px-16 py-8
             overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100"
    >
      <!-- Scrollable content -->
      <div class="flex-grow flex flex-col justify-center items-center">
        <div class="w-full max-w-md space-y-6 mt-6 md:mt-0 mb-10">
          <!-- 🏷 Logo -->
          <div class="flex justify-center mb-4">
            <Logo class="w-32 md:w-40 h-auto" />
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-4">
            <!-- Name fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput id="first_name" v-model="form.first_name" required class="w-full" />
                <InputError :message="form.errors.first_name" />
              </div>
              <div>
                <InputLabel for="middle_name" value="Middle Name" />
                <TextInput id="middle_name" v-model="form.middle_name" class="w-full" />
                <InputError :message="form.errors.middle_name" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel for="last_name" value="Last Name" />
                <TextInput id="last_name" v-model="form.last_name" required class="w-full" />
                <InputError :message="form.errors.last_name" />
              </div>
              <div>
                <InputLabel for="birthday" value="Date of Birth" />
                <DatePick v-model="form.birthday" id="birthday" required class="w-full" />
                <InputError :message="form.errors.birthday" />
              </div>
            </div>

            <div>
              <InputLabel for="gender_id" value="Gender" />
              <Radio v-model="selectedGender" :options="genderOptions" name="gender_id" />
            </div>

            <div>
              <InputLabel for="address" value="Address" />
              <SelectInput v-model="form.address" id="address" required class="w-full">
                <option
                  v-for="stall in barangays.barangays"
                  :key="stall.name"
                  :value="stall.name"
                >
                  {{ stall.name }}
                </option>
              </SelectInput>
              <InputError :message="form.errors.address" />
            </div>

            <!-- Email & Mobile -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" required class="w-full" />
                <InputError :message="form.errors.email" />
              </div>
              <div>
                <InputLabel for="mobile" value="Mobile Number" />
                <TextInput id="mobile" v-model="form.mobile" type="number" required class="w-full" />
                <InputError :message="form.errors.mobile" />
              </div>
            </div>

            <!-- Password Fields -->
            <div class="flex flex-col md:flex-row gap-4">
              <div class="w-full md:w-1/2 relative">
                <InputLabel for="password" value="Password" />
                <div class="relative">
                  <TextInput
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="mt-1 block w-full pr-10"
                    required
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    tabindex="-1"
                  >
                    <component :is="showPassword ? EyeOffIcon : EyeIcon" class="w-5 h-5" />
                  </button>
                </div>
                <p
                  v-if="form.password"
                  :class="[
                    'text-sm mt-1 font-medium',
                    passwordStrength === 'Weak'
                      ? 'text-red-500'
                      : passwordStrength === 'Moderate'
                      ? 'text-yellow-500'
                      : 'text-green-600',
                  ]"
                >
                  Strength: {{ passwordStrength }}
                </p>
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <div class="w-full md:w-1/2 relative">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                  <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="mt-1 block w-full pr-10"
                    required
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    tabindex="-1"
                  >
                    <component :is="showConfirmPassword ? EyeOffIcon : EyeIcon" class="w-5 h-5" />
                  </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
              </div>
            </div>

            <!-- Terms -->
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-2">
              <InputLabel for="terms">
                <div class="flex items-start gap-2">
                  <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                  <div class="text-sm text-gray-600 leading-snug">
                    I agree to the
                     <button
                      type="button"
                      @click="showTermsModal = true"
                      class="underline hover:text-gray-900 text-blue-600"
                    >
                      Terms of Service
                    </button>
                    and
                     <button
                      type="button"
                      @click="showPrivacyModal = true"
                      class="underline hover:text-gray-900 text-blue-600"
                    >
                      Privacy Policy
                    </button>
                  </div>
                </div>
                <InputError :message="form.errors.terms" />
              </InputLabel>
            </div>

            <!-- Submit -->
            <div class="pt-2">
              <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-btn-gradient text-white font-bold uppercase py-2 rounded shadow hover:bg-primary-700 transition"
              >
                Sign Up
              </button>
            </div>

            <div class="text-center">
              <Link :href="route('login')" class="underline text-sm text-red-600 hover:text-red-900">
                Already registered?
              </Link>
            </div>
          </form>
        </div>
      </div>

      <!-- Unified Footer -->
      <!-- <div class="mt-auto pt-4 border-t border-gray-200">
        <Footer />
      </div> -->
    </div>

    <!-- Right Side: Background -->
    <div class="hidden md:flex md:w-1/2 relative items-center justify-center text-white text-center">
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/bg.jpg)' }"
      ></div>
      <div class="absolute inset-0 bg-primary/60"></div>

      <div class="relative z-10 px-8">
        <h1 class="text-4xl font-bold mb-2">Welcome to MarketMIS</h1>
        <p class="text-lg text-gray-100">Empowering Local Market Management</p>
      </div>
    </div>
  </div>
<div
  v-if="showPrivacyModal"
  class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
>
  <div
    class="bg-white rounded-2xl shadow-xl w-full max-w-4xl p-0 relative overflow-hidden max-h-[90vh] flex flex-col"
  >
    <button
      @click="showPrivacyModal = false"
      class="absolute top-3 right-3 text-gray-500 hover:text-black z-10"
    >
      <XIcon class="w-6 h-6" />
    </button>

    <PrivacyPolicy :onAccept="handlePrivacyAccept" :onDecline="handlePrivacyDecline" />
  </div>
</div>
<div
  v-if="showTermsModal"
  class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
>
  <div
    class="bg-white rounded-2xl shadow-xl w-full max-w-4xl p-0 relative overflow-hidden max-h-[90vh] flex flex-col"
  >
    <button
      @click="showTermsModal= false"
      class="absolute top-3 right-3 text-gray-500 hover:text-black z-10"
    >
      <XIcon class="w-6 h-6" />
    </button>

    <TermsOfService :onAccept="handleTermsAccept" :onDecline="handleTermsDecline" />
  </div>
</div>
<div
  v-if="showSuccessRegistration"
  class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
>
  <div
    class="bg-white rounded-2xl shadow-xl w-full max-w-4xl p-0 relative overflow-hidden max-h-[90vh] flex flex-col"
  >
    <button
      @click="showSuccessRegistration= false"
      class="absolute top-3 right-3 text-gray-500 hover:text-black z-10"
    >
      <XIcon class="w-6 h-6" />
    </button>

    <SuccesfullRegistration :onAccept="handleCloseSuccess" />
  </div>
</div>
</template>



