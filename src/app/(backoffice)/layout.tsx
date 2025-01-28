import Footer from "@/components/landing-page/Footer";
import React from "react";
import Header from "@/components/backoffice/Header";

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    return <>
        <Header/>
        <main className="w-full max-w-7xl mx-auto mt-[96px] lg:mt-[100px] px-4 py-8 flex items-center justify-between flex-col space-y-8">
            {children}
        </main>
        <Footer/>
    </>
}

export default Layout;