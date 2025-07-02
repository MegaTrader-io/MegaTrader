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
import GetTheAnswersYouNeed from "@/components/landing-page/GetTheAnswersYouNeed";
import {Button} from "@/components/Button";

const Page = () => {

    function scrollToTop() {
        try {
            if (typeof window !== 'undefined') {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        } catch (error) {
            console.error('unable to scroll to the top:', error);
        }
    }

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
            <GetTheAnswersYouNeed/>
            <div className="flex px-4 justify-end !my-8">
                <Button onClick={scrollToTop} styleType={'filled'} variant={'light'} className="!pl-3 !pr-4">
                    <div className="flex gap-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11468_2063" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11468_2063)">
                                <path d="M11 18V8.8L7.4 12.4L6 11L12 5L18 11L16.6 12.4L13 8.8V18H11Z" fill="black"/>
                            </g>
                        </svg>
                        <div>
                            BACK TO TOP
                        </div>
                    </div>
                </Button>
            </div>
        </main>
        <Footer/>
    </>
}


export default Page;