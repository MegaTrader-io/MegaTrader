import type {Metadata} from "next";
import "./globals.css";
import Script from "next/script";
import {roboto} from "@/app/fonts/roboto";
import {LoadingProvider} from "@/context/LoadingContext";
import LoadingOverlay from "@/components/LoadingOverlay";
import NavigationLoader from "@/components/NavigationLoader";
import {
    LoadingBetweenPagesProvider,
} from "@/context/LoadingBetweenPagesContext";
import LoadingBetweenPagesOverlay from "@/components/LoadingBetweenPagesOverlay";
import {FlashProvider} from "@/app/providers/FlashContext";

export const metadata: Metadata = {
    title: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    description: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    icons: {
        icon: "/favicon.svg",
    },
};

const GTM_ID = process.env.NEXT_PUBLIC_GTM_ID;
const GTM_VISITOR_ID = process.env.NEXT_PUBLIC_GTM_VISITOR_ID;
const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === "production";

export default function RootLayout({
                                       children,
                                   }: {
    children: React.ReactNode;
}) {
    if (isProduction && !GTM_ID) {
        console.warn(
            "[GTM] NEXT_PUBLIC_GTM_ID no está definido. Revisa tu .env.local"
        );
    }

    return (
        <html lang="en" className={`${roboto.variable} dark`}>
        <head>
            {isProduction && GTM_ID && (
                <Script
                    src={`https://www.googletagmanager.com/gtm.js?id=${GTM_ID}`}
                    strategy="afterInteractive"
                />
            )}

            {isProduction && GTM_ID && (
                <Script id="gtm-init" strategy="afterInteractive">
                    {`
              window.dataLayer = window.dataLayer || [];
              window.dataLayer.push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
              });
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '${GTM_VISITOR_ID}');
              console.info('visitor loaded');
            `}
                </Script>
            )}
        </head>

        <body className="font-roboto antialiased">
        {isProduction && GTM_ID && (
            <noscript>
                <iframe
                    src={`https://www.googletagmanager.com/ns.html?id=${GTM_ID}`}
                    height="0"
                    width="0"
                    style={{display: "none", visibility: "hidden"}}
                />
            </noscript>
        )}

        <LoadingBetweenPagesProvider>
            <NavigationLoader/>
            <LoadingBetweenPagesOverlay/>
            <FlashProvider>
                <LoadingProvider>
                    <LoadingOverlay/>
                    {children}
                </LoadingProvider>
            </FlashProvider>
        </LoadingBetweenPagesProvider>
        </body>
        </html>
    );
}
