import type { Metadata, Viewport } from "next";
import { Roboto } from "next/font/google";
import "./globals.css";

const roboto = Roboto({
  subsets: ["latin"],
  weight: ["300", "400", "500", "700"],
  variable: "--font-roboto",
});

export const metadata: Metadata = {
  title: "MegaTrader | Get Funded Up to $750K",
  description:
    "Supercharge your trading with up to $750K in funding. Start a challenge, get instant funding, and enjoy lightning fast payouts in just a few hours.",
  keywords: [
    "prop trading",
    "funded trader",
    "trading challenge",
    "forex funding",
    "trading capital",
    "MegaTrader",
  ],
  openGraph: {
    title: "MegaTrader | Get Funded Up to $750K",
    description:
      "Supercharge your trading with up to $750K in funding. Lightning fast payouts in just a few hours.",
    type: "website",
  },
};

export const viewport: Viewport = {
  themeColor: "#0a0a0a",
  width: "device-width",
  initialScale: 1,
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={roboto.variable}>
      <body className="font-sans antialiased">{children}</body>
    </html>
  );
}
