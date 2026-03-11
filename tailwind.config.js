/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./lib/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#FFB34A",
          50: "#FFF8ED",
          100: "#FFEFD4",
          200: "#FFDBA8",
          300: "#FFC471",
          400: "#FFB34A",
          500: "#F59300",
          600: "#CC7700",
          700: "#995900",
          800: "#664000",
          900: "#332000",
        },
        secondary: {
          DEFAULT: "#14B8A6",
          50: "#F0FDFA",
          100: "#CCFBF1",
          200: "#99F6E4",
          300: "#5EEAD4",
          400: "#2DD4BF",
          500: "#14B8A6",
          600: "#0D9488",
          700: "#0F766E",
          800: "#115E59",
          900: "#134E4A",
        },
        background: "#1E1E1E",
        foreground: "#FFFFFF",
        muted: {
          DEFAULT: "#2A2A2A",
          foreground: "#A1A1AA",
        },
        card: {
          DEFAULT: "#262626",
          foreground: "#FFFFFF",
        },
        border: "#3A3A3A",
        input: "#3A3A3A",
        ring: "#FFB34A",
      },
      fontFamily: {
        sans: ["Roboto", "ui-sans-serif", "system-ui", "sans-serif"],
      },
      borderRadius: {
        lg: "12px",
        md: "8px",
        sm: "4px",
      },
    },
  },
  plugins: [],
};
