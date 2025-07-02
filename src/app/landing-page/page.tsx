'use client';

import {Suspense} from 'react'
import HeroSection from "@/components/landing-page/HeroSection";
import Header from "@/components/landing-page/Header";
import Footer from "@/components/landing-page/Footer";

import Image from "next/image";
import React from "react";
import MarketOverviewSection from "@/components/landing-page/MarketOverviewSection";
import SponsorLogosSection from "@/components/landing-page/SponsorLogosSection";
import MegatraderInNumbers from "@/components/landing-page/MegatraderInNumbers";
import EasyWayToBecomeAFundedTrader from "@/components/landing-page/EasyWayToBecomeAFundedTrader";
import ChooseYourAccountSize from "@/components/landing-page/ChooseYourAccountSize";
import SmarterToolsForConfidentTrading from "@/components/landing-page/SmarterToolsForConfidentTrading";
import YourPathToProfitable from "@/components/landing-page/YourPathToProfitable";
import EarnMoreThrouchOurAffiliateProgram from "@/components/landing-page/EarnMoreThrouchOurAffiliateProgram";
import DiscoverThePlatformsPoweringYourTrades from "@/components/landing-page/DiscoverThePlatformsPoweringYourTrades";
import OurWithDrawalMethods from "@/components/landing-page/OurWithDrawalMethods";
import TrustedByLeadres from "@/components/landing-page/TrustedByLeadres";

const Page = () => {
    return <>
        <header id="home" className="px-4 w-full z-50">
            <Header/>
        </header>
        <main
            className="min-h-[calc(100vh-96px)] lg:h-full mt-[124px] w-full lg:max-w-7xl space-y-12 mx-auto">
            <HeroSection className="px-4"/>
            <div className="px-4 pb-12 space-y-8">
                <div className="mx-auto flex justify-center">

                    <Image
                        src="/assets/images/Window-mobile.png"
                        alt="window tablet"
                        width={328}
                        height={620}
                        quality={100}
                        className="min-w-[328px] w-full h-auto block md:hidden"
                    />

                    <Image
                        src="/assets/images/Window-tablet.png"
                        alt="window tablet"
                        width={748}
                        height={1100}
                        className="max-w-[748px] h-auto hidden md:block lg:hidden"
                    />

                    <Image
                        src="/assets/images/Window-desktop.png"
                        alt="window desktop"
                        width={1280}
                        height={900}
                        className="w-[1280px] h-auto hidden lg:block"
                    />
                </div>

                <Suspense fallback={'loading...'}>
                    <MarketOverviewSection/>
                </Suspense>
            </div>
            <SponsorLogosSection className="px-4"/>
            <MegatraderInNumbers/>
            <EasyWayToBecomeAFundedTrader/>
            <ChooseYourAccountSize/>
            <SmarterToolsForConfidentTrading/>
            <YourPathToProfitable/>
            <EarnMoreThrouchOurAffiliateProgram/>
            <DiscoverThePlatformsPoweringYourTrades/>
            <OurWithDrawalMethods/>
            <TrustedByLeadres/>
            <div></div>
        </main>
        <Footer/>
    </>
}


export default Page;