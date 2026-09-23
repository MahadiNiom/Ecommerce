import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', 'Figtree', 'Georgia', 'serif'],
            },

            colors: {
                brand: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                },
            },

            boxShadow: {
                soft: '0 2px 16px -2px rgb(16 185 129 / 0.14)',
                lift: '0 12px 32px -12px rgb(0 0 0 / 0.28)',
                glow: '0 0 0 1px rgb(16 185 129 / 0.25), 0 8px 30px -8px rgb(16 185 129 / 0.45)',
            },

            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in-down': {
                    '0%': { opacity: '0', transform: 'translateY(-16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in-left': {
                    '0%': { opacity: '0', transform: 'translateX(-16px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                'slide-in-right': {
                    '0%': { opacity: '0', transform: 'translateX(110%)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                'slide-out-right': {
                    '0%': { opacity: '1', transform: 'translateX(0)' },
                    '100%': { opacity: '0', transform: 'translateX(110%)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-12px)' },
                },
                'pop-in': {
                    '0%': { opacity: '0', transform: 'scale(0.85)' },
                    '70%': { transform: 'scale(1.05)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                'gradient-pan': {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },

            animation: {
                'fade-in': 'fade-in 0.6s ease-out both',
                'fade-in-up': 'fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) both',
                'fade-in-down': 'fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
                'fade-in-left': 'fade-in-left 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
                'slide-in-right': 'slide-in-right 0.4s cubic-bezier(0.16, 1, 0.3, 1) both',
                'slide-out-right': 'slide-out-right 0.3s ease-in both',
                float: 'float 6s ease-in-out infinite',
                'pop-in': 'pop-in 0.35s cubic-bezier(0.16, 1, 0.3, 1) both',
                'gradient-pan': 'gradient-pan 8s ease infinite',
                shimmer: 'shimmer 2.2s linear infinite',
            },
        },
    },

    plugins: [forms],
};