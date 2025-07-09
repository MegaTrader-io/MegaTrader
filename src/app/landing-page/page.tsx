'use client';

import {Suspense, useEffect} from 'react'
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
import ToTopButton from "@/components/ToTopButton";
import Intercom, {shutdown} from '@intercom/messenger-js-sdk';

const Page = () => {
    useEffect(() => {

        if (typeof window !== 'undefined' && window.__intercomInitialized) return;

        Intercom({
            app_id: 'izt54gd4',
        });

        window.__intercomInitialized = true;


        return () => {
            window.__intercomInitialized = false;

            shutdown();
        }
    }, []);


    return <>
        <header className="px-4 w-full z-50">
            <Header/>
        </header>
        <main
            className="min-h-[calc(100vh-96px)] relative lg:h-full mt-[124px] w-full lg:max-w-7xl space-y-12 mx-auto mb-24">
            <HeroSection className="px-4"/>
            <section id="market-data" className="px-4 !mt-0 pb-12 space-y-8">
                <Suspense fallback={'loading...'}>
                    <MarketOverviewSection/>
                </Suspense>
            </section>
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
        </main>
        <Footer/>
        <ToTopButton/>
    </>
}


export default Page;