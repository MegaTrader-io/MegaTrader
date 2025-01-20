import type {Config} from "tailwindcss";

export default {
    darkMode: 'class',
    content: [
        "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
    ],
    theme: {
        screens: {
            'sm': '640px',
            // => @media (min-width: 640px) { ... }

            'md': '768px',
            // => @media (min-width: 768px) { ... }

            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }

            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }

            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }

            '3xl': '1600px',
            // => @media (min-width: 1600px) { ... }
        },
        extend: {
            fontFamily: {
                sans: ["var(--font-space-grotesk)", "sans-serif"],
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
                secondary: '#14B8A6',
                light: '#FFFFFF',
            },
            animation: {
                'rotate-animation': 'rotate-animation 10s linear infinite',
            },
            keyframes: {
                'rotate-animation': {
                    '0%': {transform: 'rotate(360deg)'},
                    '100%': {transform: 'rotate(0deg)'},
                },
            },
            clipPath: {
                'custom-card': 'polygon(0 0, calc(100% - 0px) 0, 100% 110px, 100% 100%, 0 100%)',
            },
        },
    },
    plugins: [],
} satisfies Config;
