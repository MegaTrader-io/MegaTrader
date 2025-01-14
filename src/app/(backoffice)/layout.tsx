import Header from "@/components/Header";
import Footer from "@/components/landing-page/Footer";
import React from "react";

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    return <>
        <Header/>
        <main className="w-full max-w-7xl mx-auto px-4 my-5 flex items-center justify-between">
            {children}
        </main>
        <Footer/>
    </>
}

export default Layout;