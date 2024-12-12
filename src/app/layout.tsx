import type {Metadata} from "next";
import {Space_Grotesk} from "next/font/google";
import "./globals.css";

export const metadata: Metadata = {
    title: "Mega Trader",
    description: "Mega Trader",
};

const spaceGrotesk = Space_Grotesk({
    subsets: ["latin"],
    weight: ["300", "400", "500", "600", "700"],
    variable: "--font-space-grotesk",
});

export default function RootLayout({children}: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${spaceGrotesk.variable} dark`}>
        <body className="antialiased">
        {children}
        </body>
        </html>
    );
}
