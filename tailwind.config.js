import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                red: { 50: '#fbf7f5', 100: '#f2e8e4', 200: '#e4d2cc', 300: '#cfb2aa', 400: '#b47f7d', 500: '#96545d', 600: '#7e3444', 700: '#6f1d2c', 800: '#581722', 900: '#42121b', 950: '#2d0b12' },
                amber: { 50: '#faf7f2', 100: '#f3ede5', 200: '#eadfd2', 300: '#e1d3c2', 400: '#d5c2ad', 500: '#c5ad95', 600: '#a88c76', 700: '#806958', 800: '#624f43', 900: '#493a32', 950: '#2d241f' },
                yellow: { 50: '#faf8f4', 100: '#f5f0e8', 200: '#eee5d9', 300: '#e5d8c8', 400: '#d9c8b5', 500: '#c9b49e', 600: '#aa927c', 700: '#826e5d', 800: '#625247', 900: '#493d35', 950: '#2c2520' },
                orange: { 50: '#faf6f4', 100: '#f3e9e5', 200: '#e8d7d1', 300: '#d9beb6', 400: '#c79e98', 500: '#ad7979', 600: '#945e65', 700: '#784650', 800: '#603840', 900: '#4b2c33', 950: '#2d181d' },
                ocean: { 50: '#fbf7f5', 100: '#f2e8e4', 200: '#e4d2cc', 500: '#96545d', 700: '#6f1d2c' },
                coral: { 100: '#f3ede5', 200: '#eadfd2', 300: '#e1d3c2', 500: '#a96b72', 700: '#6f1d2c' },
                jungle: { 100: '#f1e8e7', 500: '#89545f', 700: '#592330' },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['Montserrat', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [forms, typography, aspectRatio],
};
