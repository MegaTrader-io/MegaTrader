'use client';

import React, {useMemo, useState} from 'react';
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

    }
]


function ChooseYourAccountSize() {
    const [plan, setPlan] = useState<ACCOUNT_SIZE_PLAN>('elite_plan')

    const planDetail = items.find(item => item.plan === plan);

    function changePlan(plan: ACCOUNT_SIZE_PLAN) {
        console.info('plan', plan);
        setPlan(plan)
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

    return (
        <section className="px-4">
            <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
                Choose your account size
            </div>

            <div
                className="mx-auto pb-8 max-w-[760px] text-center text-xl leading-loose font-medium text-stone-400 md:max-w-[860px]">
                Choose from flexible account sizes and plans tailored to your trading style—whether you{'\''}re growing
                your skills or ready to trade real capital with confidence
            </div>

            <ButtonsAccountSize
                className="mb-4"
                defaultPlan={plan}
                changePlan={changePlan}
            />

            <div className="flex gap-2">
                {planDetail?.levels.map((level, index) => (
                    <Card key={index} className={clsx(
                        'group px-0 w-full !border-none !border-transparent',
                        [
                            index === 2 ? 'py-12 bg-primary mark' : 'my-6'
                        ]
                    )}>
                        <div className="px-4 flex items-center gap-2">
                            <div
                                className="bg-primary group-[.mark]:text-primary group-[.mark]:bg-black rounded-full w-12 h-12 flex content-center items-center justify-center">
                                <svg width="24" height="24" viewBox="0 -2 24 24" fill="none"
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
                            </div>
                            <div>
                                <div
                                    className="self-stretch justify-start text-[#ffb34a] group-[.mark]:text-black text-xl font-medium leading-8">
                                    {getTitleByLevel(index + 1)}
                                </div>
                                <div
                                    className="self-stretch justify-start text-[#ffb34a] group-[.mark]:text-black text-sm font-medium leading-5">
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

export default ChooseYourAccountSize;