import type {Config} from "tailwindcss";
import tailwindScrollbar from 'tailwind-scrollbar';
import {ScrollbarOptions} from "swiper/types";

export default {
    content: [
        "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
    ],
    darkMode: 'class',
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
            backgroundImage: {
                shimmer: 'linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent)',
            },
            backgroundSize: {
                shimmer: '200% 100%',
            },
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
                secondary: 'var(--mgt-color-teal)',
                'mgt-dark': 'var(--mgt-dark)',
                light: '#FFFFFF'
            },
            keyframes: {
                'rotate-animation': {
                    '0%': {transform: 'rotate(360deg)'},
                    '100%': {transform: 'rotate(0deg)'},
                },
                slideDownAndFade: {
                    from: {opacity: "0", transform: "translateY(-2px)"},
                    to: {opacity: "1", transform: "translateY(0)"},
                },
                slideLeftAndFade: {
                    from: {opacity: "0", transform: "translateX(2px)"},
                    to: {opacity: "1", transform: "translateX(0)"},
                },
                slideUpAndFade: {
                    from: {opacity: "0", transform: "translateY(2px)"},
                    to: {opacity: "1", transform: "translateY(0)"},
                },
                slideRightAndFade: {
                    from: {opacity: "0", transform: "translateX(-2px)"},
                    to: {opacity: "1", transform: "translateX(0)"},
                },
                shimmer: {
                    '0%': {backgroundPosition: '200% 0'},
                    '100%': {backgroundPosition: '-200% 0'},
                },
            },
            animation: {
                shimmer: 'shimmer 2s linear infinite',
                'rotate-animation': 'rotate-animation 10s linear infinite',
                slideDownAndFade:
                    "slideDownAndFade 400ms cubic-bezier(0.16, 1, 0.3, 1)",
                slideLeftAndFade:
                    "slideLeftAndFade 400ms cubic-bezier(0.16, 1, 0.3, 1)",
                slideUpAndFade: "slideUpAndFade 400ms cubic-bezier(0.16, 1, 0.3, 1)",
                slideRightAndFade:
                    "slideRightAndFade 400ms cubic-bezier(0.16, 1, 0.3, 1)",
            },
            clipPath: {
                'custom-card': 'polygon(0 0, calc(100% - 0px) 0, 100% 110px, 100% 100%, 0 100%)',
            },
        },
    },
    plugins: [
        tailwindScrollbar({nocompatible: true, preferredStrategy: 'pseudoelements'} as ScrollbarOptions)
    ],
} satisfies Config;
