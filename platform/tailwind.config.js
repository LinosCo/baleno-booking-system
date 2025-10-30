const { fontFamily } = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class'],
  content: [
    './src/pages/**/*.{ts,tsx}',
    './src/components/**/*.{ts,tsx}',
    './src/app/**/*.{ts,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#fff4ed',
          100: '#ffe5d5',
          200: '#ffc3ad',
          300: '#ff9d84',
          400: '#ff755a',
          500: '#ff543c',
          600: '#e63d2b',
          700: '#b62b21',
          800: '#871c17',
          900: '#5c110f',
        },
        accent: {
          500: '#a855f7',
        },
      },
      fontFamily: {
        sans: ['"Inter"', ...fontFamily.sans],
      },
      boxShadow: {
        soft: '0 10px 30px -15px rgba(239, 68, 68, 0.3)',
      },
    },
  },
  plugins: [require('@tailwindcss/forms')({ strategy: 'class' })],
};
