import type { Metadata } from "next";
import "./globals.css";
import Script from "next/script";
import { roboto } from "@/app/fonts/roboto";
import { LoadingProvider } from "@/context/LoadingContext";
import LoadingOverlay from "@/components/LoadingOverlay";
import NavigationLoader from "@/components/NavigationLoader";
import { LoadingBetweenPagesProvider } from "@/context/LoadingBetweenPagesContext";
import LoadingBetweenPagesOverlay from "@/components/LoadingBetweenPagesOverlay";

export const metadata: Metadata = {
    title: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    description: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    icons: {
        icon: "/favicon.svg"
    }
};

const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === "production";

export default function RootLayout({ children }: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${roboto.variable} dark`}>
        <head>
            {isProduction && (
                <script
                    dangerouslySetInnerHTML={{
                        __html: `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement('script'),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-K7TW2RR7');`
                    }}
                />
            )}
        </head>
        <body className="font-roboto antialiased">
        {isProduction && (
            <noscript>
                <iframe
                    src="https://www.googletagmanager.com/ns.html?id=GTM-K7TW2RR7"
                    height="0"
                    width="0"
                    style={{ display: "none", visibility: "hidden" }}
                />
            </noscript>
        )}
        <LoadingBetweenPagesProvider>
            <NavigationLoader />
            <LoadingBetweenPagesOverlay />
            <LoadingProvider>
                <LoadingOverlay />
                {children}
                {isProduction && (
                    <Script id="livechat-script" strategy="lazyOnload">
                        {`
(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/izt54gd4';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
window.intercomSettings = {
api_base: "https://api-iam.intercom.io",
app_id: "izt54gd4",
user_id: "123",
name: "foo bar",
email: "test@megatrader.io",
created_at: new Date().getTime(),
};
                `}
                    </Script>
                )}
            </LoadingProvider>
        </LoadingBetweenPagesProvider>
        </body>
        </html>
    );
}
