import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
            },
            colors: {
                cyan: {
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                },
                brand: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                primary: {
                    DEFAULT: 'rgb(var(--primary-r, 59) var(--primary-g, 130) var(--primary-b, 246))',
                    50: 'rgb(239 246 255)',
                    100: 'rgb(219 234 254)',
                    200: 'rgb(191 219 254)',
                    300: 'rgb(147 197 253)',
                    400: 'rgb(96 165 250)',
                    500: 'rgb(59 130 246)',
                    600: 'rgb(37 99 235)',
                    700: 'rgb(29 78 216)',
                    800: 'rgb(30 64 175)',
                    900: 'rgb(30 58 138)',
                }
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        color: theme('colors.gray.300'),
                        h1: { color: theme('colors.gray.100') },
                        h2: { color: theme('colors.gray.100') },
                        h3: { color: theme('colors.gray.200') },
                        h4: { color: theme('colors.gray.200') },
                        strong: { color: theme('colors.cyan.400') },
                        a: {
                            color: theme('colors.cyan.400'),
                            '&:hover': { color: theme('colors.cyan.300') },
                        },
                        blockquote: {
                            color: theme('colors.gray.400'),
                            borderLeftColor: theme('colors.cyan.500'),
                        },
                        code: {
                            color: theme('colors.cyan.300'),
                            backgroundColor: theme('colors.gray.800'),
                            borderRadius: '0.25rem',
                            padding: '0.125rem 0.25rem',
                        },
                    },
                },
            }),
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/container-queries'),
    ],
};
