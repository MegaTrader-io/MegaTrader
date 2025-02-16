import Footer from "@/components/landing-page/Footer";
import React from "react";
import Header from "@/components/backoffice/Header";
import {AccountProvider} from "@/app/providers/AccountContext";

const Layout = ({children}: {
    children: React.ReactNode
}) => {
    return <AccountProvider>
        <Header/>
        <main
            className="w-full max-w-7xl h-full mx-auto px-4 py-8 flex items-center justify-between flex-col space-y-8">
            {children}
        </main>
        <Footer/>
    </AccountProvider>
}

export default Layout;