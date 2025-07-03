import React from 'react';
import Card from "@/components/Card";
import Image from "next/image";

function EarnMoreThrouchOurAffiliateProgram() {
    return (
        <section id="feature-earn-more-throuch" className="flex flex-col lg:grid lg:grid-cols-[auto_1fr] gap-12 px-4 py-12">
            <Card className="hidden order-2 sm:flex h-full w-full items-center lg:order-none">
                <Image src={`/assets/images/FeatureContent.png`}
                       width={689}
                       className="w-full lg:w-auto"
                       alt={'chart performance'}
                       quality={100}
                       height={453}/>
            </Card>

            <div className="block order-2 sm:order-none sm:hidden">
                <Image src={`/assets/images/FeatureContent-mobile.png`}
                       width={689}
                       className="w-full lg:w-auto"
                       alt={'chart performance'}
                       quality={100}
                       height={453}/>
            </div>

            <div className="lg:w-[479px] space-y-12 order-1 lg:order-none">
                <div
                    className="justify-start text-white text-[40px] font-light uppercase leading-[48px]">Earn
                    More Through Our Affiliate Program
                </div>

                <div
                    className="justify-start text-stone-400 text-xl font-medium leading-8">Join
                    our affiliate program and earn recurring commissions by referring new traders to the platform. It’s
                    a simple way to build passive income while helping others start their funded trading journey.
                </div>

                <div
                    className="pl-4 border-l-2 border-neutral-700 inline-flex flex-col justify-start items-start gap-4">
                    <div
                        className="justify-start text-white text-xl font-medium leading-8">&#34;I
                        started as a funded trader, but affiliate commissions became my second income stream. The more
                        people I help, the more I earn—it’s a win-win.&#34;
                    </div>
                    <div className="flex flex-col justify-start items-start gap-1">
                        <div
                            className="justify-start text-[#ffb34a] text-base font-medium leading-6">Jordan
                            Blake
                        </div>
                        <div
                            className="justify-start text-stone-400 text-sm font-medium leading-5">Top
                            Affiliate, MegaTrader
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default EarnMoreThrouchOurAffiliateProgram;