'use client';

import {Suspense} from 'react'
import HeroSection from "@/components/landing-page/HeroSection";
import Header from "@/components/landing-page/Header";
import Footer from "@/components/landing-page/Footer";
import FeatureHighlightSection from "@/components/landing-page/FeatureHighlightSection";
import SponsorLogosSection from "@/components/landing-page/SponsorLogosSection";
import TradingStepsSection from "@/components/landing-page/TradingStepsSection";
import BenefitsSection from "@/components/landing-page/BenefitsSection";
import Subscriptions from "@/components/landing-page/Subscriptions";
import MarketOverviewSection from "@/components/landing-page/MarketOverviewSection";
import UnlockThePowerOfMegaTrader from "@/components/landing-page/UnlockThePowerOfMegaTrader";
import FaqsSection from "@/components/landing-page/Faqs";
import FlexibleFuturesTradingAndAnalytics from "@/components/landing-page/FlexibleFuturesTradingAndAnalytics";

export const experimental_ppr = true;

const Home = () => {
    return <>
        <header id="home" className="px-4 py-6 w-full z-50">
            <Header/>
        </header>
        <main
            className="min-h-[calc(100vh-96px)] lg:h-full lg:mt-8 xl:mt-[229px] w-full lg:max-w-7xl mx-auto mt-24">
            <HeroSection className="px-4"/>
            <FeatureHighlightSection className="px-4"/>
            <SponsorLogosSection className="px-4"/>
            <Suspense fallback={'loading...'}>
                <MarketOverviewSection className="px-4"/>
            </Suspense>
            <TradingStepsSection className="px-4"/>
            <Subscriptions className="px-4"/>
            <UnlockThePowerOfMegaTrader className="lg:px-4"/>
            <FlexibleFuturesTradingAndAnalytics className="px-4"/>
            <BenefitsSection className="px-4"/>
            <FaqsSection className="px-4"/>
        </main>
        <Footer/>
    </>
}


export default Home;