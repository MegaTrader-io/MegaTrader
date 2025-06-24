'use client';

import HeroSection from "@/components/landing-page/HeroSection";
import Header from "@/components/landing-page/Header";
import Footer from "@/components/landing-page/Footer";

const Page = () => {
    return <>
        <header id="home" className="px-4 w-full z-50">
            <Header/>
        </header>
        <main
            className="min-h-[calc(100vh-96px)] lg:h-full mt-[124px] w-full lg:max-w-7xl mx-auto">
            <HeroSection className="px-4"/>
        </main>
        <Footer/>
    </>
}


export default Page;