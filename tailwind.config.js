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
                sans: ['DM Sans', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cream: {
                    DEFAULT: '#F5F0E8',
                    raised: '#FAF6F0',
                    inset: '#EDE8DF',
                    pressed: '#E5DFD4',
                },
                teal: {
                    DEFAULT: '#1A6B6B',
                    dark: '#14504F',
                    light: '#D0E8E8',
                    muted: '#2D8585',
                },
                brand: {
                    text: '#2C2C2C',
                    secondary: '#6B6560',
                    muted: '#9E9790',
                },
            },
            borderRadius: {
                '2xl': '16px',
                '3xl': '24px',
            },
            boxShadow: {
                'raised': '4px 4px 10px rgba(0,0,0,0.10), -2px -2px 6px rgba(255,255,255,0.70)',
                'raised-hover': '6px 6px 14px rgba(0,0,0,0.13), -3px -3px 8px rgba(255,255,255,0.75)',
                'inset-soft': 'inset 2px 2px 6px rgba(0,0,0,0.08), inset -2px -2px 4px rgba(255,255,255,0.60)',
                'modal': '0 20px 60px rgba(0,0,0,0.18)',
                'teal': '2px 4px 10px rgba(26,107,107,0.35)',
            },
        },
    },

    plugins: [forms],
};
