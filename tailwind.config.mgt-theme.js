/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    corePlugins: {
        preflight: false,
    },
    content: ['./assets/tailwindcss/**/*.css'],
    theme: {
        extend: {
            fontFamily: {
                roboto: ["var(--font-roboto)", "sans-serif"],
            },
            colors: {
                'mgt-primary': 'var(--mgt-color-primary)',
                background: "var(--background)",
                foreground: "var(--foreground)",
                'mgt-gray-light': '#A8A29E',
                'mgt-link': 'var(--mgt-color-link)',
                'mgt-error': 'var(--mgt-color-error)',
                'mgt-link-hover': 'var(--mgt-color-link-hover)',
                primary: '#FFB34A',
                secondary: 'var(--mgt-color-teal)',
                'mgt-dark': 'var(--mgt-dark)',
                light: '#FFFFFF'
            },
        },
    },
    plugins: [
        require('tailwind-scrollbar')({
            nocompatible: true,
            preferredStrategy: 'pseudoelements',
        }),
    ],
}

