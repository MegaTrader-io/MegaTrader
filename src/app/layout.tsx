import type {Metadata} from "next";
import {Space_Grotesk} from "next/font/google";
import "./globals.css";
import {roboto} from '@/app/fonts/roboto'

export const metadata: Metadata = {
    title: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    description: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    icons: {
        icon: "/favicon.svg"
    }
};

const spaceGrotesk = Space_Grotesk({
    subsets: ["latin"],
    weight: ["300", "400", "500", "600", "700"],
    variable: "--font-space-grotesk"
});

export default function RootLayout({children}: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${spaceGrotesk.variable} ${roboto.variable} dark`}>
        <body className="font-sans antialiased">
        {children}
        </body>
        </html>
    );
}
