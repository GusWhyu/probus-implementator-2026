const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    daisyui: {
        themes: ["light"],
      },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'abu': '#A5A5A5',
                'react': '#61DBFB',
                'abugelap': '#717171',
            },
        },
    },

    plugins: [require('@tailwindcss/forms'),require('daisyui')],
};
