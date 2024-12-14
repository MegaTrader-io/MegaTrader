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

export const experimental_ppr = true

const Home = () => {
    return <>
        <Header/>
        <main className="mx-auto max-w-7xl mt-[59px] mb-[101px] px-4">
            <HeroSection/>
            <FeatureHighlightSection/>
            <SponsorLogosSection/>
            <Suspense fallback={'loading...'}>
                <MarketOverviewSection/>
            </Suspense>
            <TradingStepsSection/>
            <Subscriptions/>
            <UnlockThePowerOfMegaTrader/>
            <BenefitsSection/>
        </main>
        <Footer/>
    </>
}


export default Home;