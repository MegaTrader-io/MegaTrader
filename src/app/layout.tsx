import type {Metadata} from "next";
import {Space_Grotesk} from "next/font/google";
import "./globals.css";
import Script from "next/script";

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

const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === "production";

export default function RootLayout({children}: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${spaceGrotesk.variable} dark`}>
        <body className="antialiased">
        {children}
        {isProduction && (
            <>
                <Script id="livechat-script" strategy="lazyOnload">
                    {`
            window.__lc = window.__lc || {};
            window.__lc.license = 18972174;
            window.__lc.integration_name = "manual_channels";
            window.__lc.product_name = "livechat";
            ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
            `}
                </Script>
                <noscript><a href="https://www.livechat.com/chat-with/18972174/" rel="nofollow">Chat with us</a>,
                    powered by <a
                        href="https://www.livechat.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a>
                </noscript>
            </>
        )}
        </body>
        </html>
    );
}
