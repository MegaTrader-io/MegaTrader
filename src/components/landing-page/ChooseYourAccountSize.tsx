import React, {useState} from 'react';
import ButtonsAccountSize, {ACCOUNT_SIZE_PLAN} from "@/components/landing-page/ButtonsAccountSize";
import Card from "@/components/Card";
import {Button} from "@/components/Button";
import clsx from "clsx";

const items = [
    {
        plan: 'elite_plan',
        levels: [
            {
                description: '$39.99 / Month',
                features: [
                    'Profit Target: $1,500',
                    'Max Contracts: 1 Mini (10 Micros)',
                    'Daily Loss Limit: None',
                    'Trailing Max Drawdown: $1,000',
                    'Drawdown Mode: Intraday',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $29.99',
                    'Activation Fee: $99.99',
                ]
            },
            {
                description: '$59.99 / Month',
                features: [
                    'Profit Target: $3,000',
                    'Max Contracts: 5 Minis (50 Micros)',
                    'Daily Loss Limit: None',
                    'Trailing Max Drawdown: $2,000',
                    'Drawdown Mode: Intraday',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $39.99',
                    'Activation Fee: $99.99',
                ]
            },
            {
                description: '$99.99 / Month',
                features: [
                    'Profit Target: $6,000',
                    'Max Contracts: 10 Minis (100 Micros)',
                    'Daily Loss Limit: None',
                    'Trailing Max Drawdown: $3,000',
                    'Drawdown Mode: Intraday',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $59.99',
                    'Activation Fee: $99.99',
                ]
            },
            {
                description: '$119.99 / Month',
                features: [
                    'Profit Target: $9,000',
                    'Max Contracts: 15 Minis (150 Micros)',
                    'Daily Loss Limit: None',
                    'Trailing Max Drawdown: $4,500',
                    'Drawdown Mode: Intraday',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $69.99',
                    'Activation Fee: $99.99',
                ]
            }
        ]
    },
    {
        plan: 'growth_plan',
        levels: [
            {
                description: '$79.99 / Month',
                features: [
                    'Profit Target: $1,500',
                    'Max Contracts: 1 Mini (50 Micros)',
                    'Daily Loss Limit (Soft Breach): $500',
                    'Trailing Max Drawdown: $1,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $49.99',
                    'Activation Fee: $0',
                ]
            },
            {
                description: '$129.99 / Month',
                features: [
                    'Profit Target: $3,000',
                    'Max Contracts: 5 Minis (50 Micros)',
                    'Daily Loss Limit (Soft Breach): $1,250',
                    'Trailing Max Drawdown: $2,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $79.99',
                    'Activation Fee: $0',
                ]
            },
            {
                description: '$239.99 / Month',
                features: [
                    'Profit Target: $6,000',
                    'Max Contracts: 10 Minis (100 Micros)',
                    'Daily Loss Limit (Soft Breach): $2,500',
                    'Trailing Max Drawdown: $3,500',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $159.99',
                    'Activation Fee: $0',
                ]
            },
            {
                description: '$329.99 / Month',
                features: [
                    'Profit Target: $9,000',
                    'Max Contracts: 15 Minis (150 Micros)',
                    'Daily Loss Limit (Soft Breach): $3,750',
                    'Trailing Max Drawdown: $5,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Pass: 1',
                    'Reset Fee: $189.99',
                    'Activation Fee: $99.99',
                ]
            }
        ]
    },
    {
        plan: 'funded_plan',
        levels: [
            {
                description: 'One-Time Fee: $339.99',
                features: [
                    'Max Contracts: 1 Minis (10 Micros)',
                    'Daily Loss Limit (Soft Breach): None',
                    'Trailing Max Drawdown: $1,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Payout: 10',
                    'Consistency: 20%',
                    'Max Accounts: 5'
                ]
            },
            {
                description: 'One-Time Fee: $499.99',
                features: [
                    'Max Contracts: 5 Minis (50 Micros)',
                    'Daily Loss Limit (Soft Breach): $1,250',
                    'Trailing Max Drawdown: $2,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Payout: 10',
                    'Consistency: 20%',
                    'Max Accounts: 5'
                ]
            },
            {
                description: 'One-Time Fee: $599.99',
                features: [
                    'Max Contracts: 10 Minis (100 Micros)',
                    'Daily Loss Limit (Soft Breach): $2,500',
                    'Trailing Max Drawdown: $4,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Payout: 10',
                    'Consistency: 20%',
                    'Max Accounts: 5'
                ]
            },
            {
                description: 'One-Time Fee: $699.99',
                features: [
                    'Max Contracts: 15 Minis (150 Micros)',
                    'Daily Loss Limit (Soft Breach): $3,750',
                    'Trailing Max Drawdown: $6,000',
                    'Drawdown Mode: End Of Day',
                    'Min Trading Days to Payout: 10',
                    'Consistency: 20%',
                    'Max Accounts: 5'
                ]
            }
        ]
    }
]

function ChooseYourAccountSize() {
    const [plan, setPlan] = useState<ACCOUNT_SIZE_PLAN>('elite_plan')

    const planDetail = items.find(item => item.plan === plan);

    function changePlan(plan: ACCOUNT_SIZE_PLAN) {
        setPlan(plan)
    }

    return (
        <section id="pricing" className="px-4">
            <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
                Choose your account size
            </div>

            <div
                className="mx-auto pb-8 max-w-[760px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[860px]">
                Choose from flexible account sizes and plans tailored to your trading style—whether you{'\''}re growing
                your skills or ready to trade real capital with confidence
            </div>

            <ButtonsAccountSize
                className="mb-4"
                defaultPlan={plan}
                changePlan={changePlan}
            />

            <div className="space-y-2 sm:space-y-0 sm:grid sm:grid-cols-2 gap-2 lg:flex lg:items-center">
                {planDetail?.levels.map((level, index) => (
                    <Card key={index} className={clsx(
                        'group px-0 w-full !border-none !border-transparent',
                        [
                            index === 2 ? 'lg:py-12 bg-primary mark' : ''
                        ]
                    )}>
                        <div className="px-4 flex items-center gap-2">
                            <div
                                className="bg-primary group-[.mark]:text-primary group-[.mark]:bg-black rounded-full w-12 h-12 flex content-center items-center justify-center">
                                <PlanIcon plan={plan}/>
                            </div>
                            <div>
                                <div
                                    className="self-stretch justify-start text-white group-[.mark]:text-black text-xl font-medium leading-8">
                                    {getTitleByLevel(index + 1)}
                                </div>
                                <div
                                    className="self-stretch justify-start text-[#ffb34a] group-[.mark]:text-black text-xl font-medium leading-8">
                                    {level.description}
                                </div>
                            </div>
                        </div>

                        <div className="py-6">
                            {level.features.map((feature, index) => (
                                <div
                                    key={index}
                                    className="w-full p-4 border-b border-stone-800 group-[.mark]:border-[#f1a035] inline-flex justify-start items-center gap-2">
                                    <div className="w-6 h-6 relative text-[#A8A29E] group-[.mark]:text-[#131210]">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_6710_48977" style={{maskType: 'alpha'}}
                                                  maskUnits="userSpaceOnUse"
                                                  x="0"
                                                  y="0"
                                                  width="24" height="24">
                                                <rect width="24" height="24" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_6710_48977)">
                                                <path
                                                    d="M9.54998 18.0001L3.84998 12.3001L5.27498 10.8751L9.54998 15.1501L18.725 5.9751L20.15 7.4001L9.54998 18.0001Z"
                                                    fill="currentColor"/>
                                            </g>
                                        </svg>
                                    </div>
                                    <div
                                        className="flex-1 justify-start text-stone-400 group-[.mark]:text-[#131210] text-base font-medium leading-normal">
                                        {feature}
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="px-4">
                            <Button className="w-full" variant={'dark'}>
                                GET PLAN
                            </Button>
                        </div>
                    </Card>
                ))}
            </div>
        </section>
    );
}

function getTitleByLevel(level: number) {
    if (level === 1) {
        return '25K Account'
    } else if (level === 2) {
        return '50k Account'
    } else if (level === 3) {
        return '100k Account'
    }

    return '150k Account'
}

function PlanIcon({plan}: { plan: ACCOUNT_SIZE_PLAN }) {
    if (plan === 'elite_plan') {
        return <svg width="24" height="24" viewBox="0 -2 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7474_36021"
                  style={{maskType: 'alpha'}}
                  maskUnits="userSpaceOnUse"
                  x="0"
                  y="0"
                  width="24" height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7474_36021)">
                <path
                    d="M9.2 8.25L11.85 3H12.15L14.8 8.25H9.2ZM11.25 20.1L2.625 9.75H11.25V20.1ZM12.75 20.1V9.75H21.375L12.75 20.1ZM16.45 8.25L13.85 3H19L21.625 8.25H16.45ZM2.375 8.25L5 3H10.15L7.55 8.25H2.375Z"
                    fill="currentColor"/>
            </g>
        </svg>
    }

    if (plan === 'growth_plan') {
        return <svg width="24" height="24" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11380_432" style={{maskType: 'alpha'}}
                  maskUnits="userSpaceOnUse" x="0" y="0" width="37"
                  height="36">
                <rect x="0.666626" width="36" height="36" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11380_432)">
                <path
                    d="M4.34158 15.8624L10.6416 9.56243C10.9916 9.21243 11.4041 8.96243 11.8791 8.81243C12.3541 8.66243 12.8416 8.63743 13.3416 8.73743L15.2916 9.14993C13.9416 10.7499 12.8791 12.1999 12.1041 13.4999C11.3291 14.7999 10.5791 16.3749 9.85408 18.2249L4.34158 15.8624ZM12.0291 19.2749C12.6041 17.4749 13.3853 15.7749 14.3728 14.1749C15.3603 12.5749 16.5541 11.0749 17.9541 9.67493C20.1541 7.47493 22.6666 5.83118 25.4916 4.74368C28.3166 3.65618 30.9541 3.32493 33.4041 3.74993C33.8291 6.19993 33.5041 8.83743 32.4291 11.6624C31.3541 14.4874 29.7166 16.9999 27.5166 19.1999C26.1416 20.5749 24.6416 21.7687 23.0166 22.7812C21.3916 23.7937 19.6791 24.5874 17.8791 25.1624L12.0291 19.2749ZM22.3791 14.7749C22.9541 15.3499 23.6603 15.6374 24.4978 15.6374C25.3353 15.6374 26.0416 15.3499 26.6166 14.7749C27.1916 14.1999 27.4791 13.4937 27.4791 12.6562C27.4791 11.8187 27.1916 11.1124 26.6166 10.5374C26.0416 9.96243 25.3353 9.67493 24.4978 9.67493C23.6603 9.67493 22.9541 9.96243 22.3791 10.5374C21.8041 11.1124 21.5166 11.8187 21.5166 12.6562C21.5166 13.4937 21.8041 14.1999 22.3791 14.7749ZM21.3291 32.8124L18.9291 27.2999C20.7791 26.5749 22.3603 25.8249 23.6728 25.0499C24.9853 24.2749 26.4416 23.2124 28.0416 21.8624L28.4166 23.8124C28.5166 24.3124 28.4916 24.8062 28.3416 25.2937C28.1916 25.7812 27.9416 26.1999 27.5916 26.5499L21.3291 32.8124ZM6.74158 24.0749C7.61658 23.1999 8.67908 22.7562 9.92908 22.7437C11.1791 22.7312 12.2416 23.1624 13.1166 24.0374C13.9916 24.9124 14.4291 25.9749 14.4291 27.2249C14.4291 28.4749 13.9916 29.5374 13.1166 30.4124C12.4916 31.0374 11.4478 31.5749 9.98533 32.0249C8.52283 32.4749 6.50408 32.8749 3.92908 33.2249C4.27908 30.6499 4.67908 28.6374 5.12908 27.1874C5.57908 25.7374 6.11658 24.6999 6.74158 24.0749Z"
                    fill="currentColor"/>
            </g>
        </svg>
    }

    return <svg width="24" height="24" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_11380_1595" style={{maskType: 'alpha'}}
              maskUnits="userSpaceOnUse" x="0" y="0" width="37"
              height="36">
            <rect x="0.333374" width="36" height="36" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_11380_1595)">
            <path
                d="M7.83325 25.5V15H10.8333V25.5H7.83325ZM16.8333 25.5V15H19.8333V25.5H16.8333ZM3.33325 31.5V28.5H33.3333V31.5H3.33325ZM25.8333 25.5V15H28.8333V25.5H25.8333ZM3.33325 12V9L18.3333 1.5L33.3333 9V12H3.33325Z"
                fill="currentColor"/>
        </g>
    </svg>
}


export default ChooseYourAccountSize;