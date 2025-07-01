import {NextPage, GetStaticProps} from "next";
import PlanVerticalList from "@/components/landing-page/PlanVerticalList";
import Card from "@/components/Card";
import Image from "next/image";
import React from "react";

const EasyWayToBecomeAFundedTrader: NextPage = () => (
    <section className="px-4">
        <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
            Easy way to become a funded trader
        </div>

        <div
            className="mx-auto pb-8 max-w-[760px] text-center text-xl leading-8 font-medium text-stone-400 md:w-[760px]">
            Guiding traders through a simple, step-by-step process to secure funding, prove their skills, and start
            earning with confidence and clarity.
        </div>

        <div>
            <div className="grid grid-cols-[1fr_14px_1fr] gap-8">
                <div className="flex justify-center">
                    <PlanVerticalList/>
                </div>
                <div></div>
                <div className="grid grid-rows-[35%_1fr] wrapper-timeline">
                    <div></div>
                    <div
                        className="vertical-line-timeline after:-left-[40px] w-full max-w-[321px] lg:max-w-[585px] relative">
                        <div
                            className="justify-start relative text-white text-[32px] font-medium uppercase leading-10">
                            01
                            <div
                                className="w-3.5 h-3.5 bg-[#ffb34a] rounded-full absolute top-[13px] -left-[46px]"></div>
                        </div>
                        <div
                            className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">Select
                            Your Plan & Account Size
                        </div>
                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-normal">Choose
                            the right plan that fits your trading style and goals. Pick your preferred account size
                            and
                            take
                            on
                            the challenge to become a funded trader.
                        </div>
                    </div>
                </div>
            </div>
            <div className="grid grid-cols-[1fr_14px_1fr] gap-8">
                <div className="flex items-center justify-end">
                    <div
                        className="text-right after:!top-0 after:-right-[40px] w-full max-w-[321px] space lg:max-w-[585px] relative">
                        <div
                            className="justify-start relative text-white text-[32px] font-medium uppercase leading-10">
                            02
                            <div
                                className="w-3.5 h-3.5 bg-[#ffb34a] rounded-full absolute top-[13px] -right-[46px]"></div>
                        </div>
                        <div
                            className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">
                            Complete the Evaluation Challenge
                        </div>
                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-normal">
                            Trade responsibly and meet the required profit targets while following risk management
                            rules. Once you succeed, you can move on to the next step.
                        </div>
                    </div>
                </div>
                <div>
                    <div className="timeline-vertica-line w-[2px] bg-[#ffb34a] h-full mx-auto"></div>
                </div>
                <div className="flex justify-center">
                    <Card className="w-full max-w-[585px] pb-0 flex justify-center border-b-0">
                        <Image src={`/assets/images/Window-congrats.svg`} alt="veriff" width={440} height={543}/>
                    </Card>
                </div>
            </div>
            <div className="grid grid-cols-[1fr_14px_1fr] gap-8">
                <div className="flex justify-center">
                    <Card className="w-full max-w-[585px] pb-0 flex justify-center border-b-0 relative">
                        <Image src={`/assets/images/Window-request-payouts.svg`} alt="veriff" width={360} height={524}/>
                        <Image src={`/assets/images/icons.png`} alt="veriff" className="absolute right-4 bottom-4"
                               width={160} height={48}/>
                    </Card>
                </div>
                <div>
                    <div className="timeline-vertica-line w-[2px] bg-[#ffb34a] h-full mx-auto"></div>
                </div>
                <div className="flex items-center">
                    <div
                        className="text-left after:!top-0 after:-left-[40px] w-full max-w-[321px] lg:max-w-[585px] relative">
                        <div
                            className="justify-start relative text-white text-[32px] font-medium uppercase leading-10">
                            03
                            <div
                                className="w-3.5 h-3.5 bg-[#ffb34a] rounded-full absolute top-[13px] -left-[46px]"></div>
                        </div>
                        <div
                            className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">
                            Request Your First Payout
                        </div>
                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-normal">
                            Once you’ve passed the challenge and made profits in your funded account, submit a payout
                            request. This step initiates the verification process.
                        </div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-[1fr_14px_1fr] gap-8">
                <div className="flex items-center justify-end">
                    <div
                        className="text-right after:!top-0 after:-right-[40px] w-full max-w-[321px] lg:max-w-[585px] relative">
                        <div
                            className="justify-start relative text-white text-[32px] font-medium uppercase leading-10">
                            04
                            <div
                                className="w-3.5 h-3.5 bg-[#ffb34a] rounded-full absolute top-[13px] -right-[46px]"></div>
                        </div>
                        <div
                            className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">
                            Get Verified
                        </div>
                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-normal">
                            Before receiving your payout, you’ll go through a quick verification process to confirm your
                            identity and compliance with the platform’s rules
                        </div>
                    </div>
                </div>
                <div>
                    <div className="timeline-vertica-line w-[2px] bg-[#ffb34a] h-full mx-auto"></div>
                </div>
                <div className="flex justify-center">
                    <Card className="w-full max-w-[585px] pb-0 flex justify-center border-b-0">
                        <Image src={`/assets/images/Window-selfie-veriff.svg`} alt="veriff" width={440} height={543}/>
                    </Card>
                </div>
            </div>
            <div className="grid grid-cols-[1fr_14px_1fr] gap-8">
                <div className="flex justify-center">
                    <Image src={`/assets/images/card-get-paid-keep-trading.svg`} alt="veriff" width={585}
                           height={457}/>
                </div>
                <div></div>
                <div className="grid grid-rows-[35%_1fr] wrapper-timeline">
                    <div className="relative h-full">
                        <div className=" w-[2px] bg-[#ffb34a] h-full absolute -left-[40px]"></div>
                    </div>
                    <div
                        className="after:-left-[40px] w-full max-w-[321px] lg:max-w-[585px] relative">
                        <div
                            className="justify-start relative text-white text-[32px] font-medium uppercase leading-10">
                            05
                            <div
                                className="last-timeline-point w-3.5 h-3.5 bg-[#ffb34a] rounded-full absolute top-[13px] -left-[46px]"></div>
                        </div>
                        <div
                            className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">
                            Get Paid & Keep Trading
                        </div>
                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-normal">
                            Once verified, your payout is processed, and you can continue trading. Earn up to a 90%
                            profit split while scaling your funded account!
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>
);

export const getStaticProps: GetStaticProps = async () => {
    return {
        props: {},
    };
};

export default EasyWayToBecomeAFundedTrader;
