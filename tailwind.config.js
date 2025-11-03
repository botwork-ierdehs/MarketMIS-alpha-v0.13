import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
        colors: {
            primary: ' #0A45B9',
            secondary: ' #5e9bf8',
        },
        fontFamily: {
            montserrat: ['Montserrat', 'sans-serif'],
        },
        backgroundImage: {
            'btn-gradient': 'linear-gradient(to right,  #0A4B94, #73AAE6)',
        },
    },
  },

    plugins: [forms, typography],
};
