import type {Config} from "tailwindcss";

export default {
    darkMode: 'class',
    content: [
        "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["var(--font-space-grotesk)", "sans-serif"], // Usa tu variable CSS
            },
            colors: {
                'mgt-primary': 'var(--mgt-color-primary)',
                background: "var(--background)",
                foreground: "var(--foreground)",
                'mgt-gray-light': '#A8A29E'
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
