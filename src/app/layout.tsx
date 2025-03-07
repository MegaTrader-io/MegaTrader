import type {Metadata} from "next";
import "./globals.css";
import Script from "next/script";
import {roboto} from '@/app/fonts/roboto'
import {LoadingProvider} from '@/context/LoadingContext';
import LoadingOverlay from '@/components/LoadingOverlay';
import NavigationLoader from "@/components/NavigationLoader";
import {LoadingBetweenPagesProvider} from "@/context/LoadingBetweenPagesContext";
import LoadingBetweenPagesOverlay from "@/components/LoadingBetweenPagesOverlay";

export const metadata: Metadata = {
    title: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    description: "MegaTrader | Master the Path to Becoming a Funded Futures Trader.",
    icons: {
        icon: "/favicon.svg"
    }
};

const isProduction = true;//process.env.NEXT_PUBLIC_ENVIRONMENT === "production";

export default function RootLayout({children}: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${roboto.variable} dark`}>
        <body className="font-roboto antialiased">
        <LoadingBetweenPagesProvider>
            <NavigationLoader/>
            <LoadingBetweenPagesOverlay/>
            <LoadingProvider>

                <LoadingOverlay/>
                {children}
                {isProduction && (
                    <>
                        <Script id="livechat-script" strategy="lazyOnload">
                            {`
 // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/izt54gd4'
  (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/izt54gd4';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();

    window.intercomSettings = {
    api_base: "https://api-iam.intercom.io",
    app_id: "izt54gd4",
    user_id: "123", // IMPORTANT: Replace "user.id" with the variable you use to capture the user's ID
    name: "foo bar", // IMPORTANT: Replace "user.name" with the variable you use to capture the user's name
    email: "test@megatrader.io", // IMPORTANT: Replace "user.email" with the variable you use to capture the user's email address
    created_at: new Date().getTime(), // IMPORTANT: Replace "user.createdAt" with the variable you use to capture the user's sign-up date
  };

            `}
                        </Script>
                    </>
                )}
            </LoadingProvider>
        </LoadingBetweenPagesProvider>
        </body>
        </html>
    );
}
