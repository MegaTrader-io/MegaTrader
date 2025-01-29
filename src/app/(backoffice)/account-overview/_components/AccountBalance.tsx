import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";
import Image from "next/image";
import * as BaseTooltip from "@radix-ui/react-tooltip";
import clsx from "clsx";

function AccountBalance({account}: { account: Account }) {
    const {accountBalance} = account;

    return (
        <Card className="space-y-4">
            <div className="text-white text-xl font-light uppercase leading-normal">ACCOUNT BALANCE</div>
            <div className="gap-4 lg:flex lg:items-center">
                <div className="glflex flex-col items-start relative flex-1 grow">
                    {[
                        {label: "Current Balance", value: accountBalance.currentBalance},
                        {label: "Current Equity", value: accountBalance.currentEquity},
                        {label: "High", value: accountBalance.high},
                        {label: "Low", value: accountBalance.low},
                        {label: "Weekly Net P&L", value: accountBalance.weeklyNetPnL}
                    ].map(({label, value}, index) => (
                        <div
                            key={index}
                            className="justify-between h-14 px-0 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                        >
                            <div className="relative flex-1 flex text-stone-400 text-base font-normal leading-normal">
                                {label}
                                {label === 'Weekly Net P&L' && <QuestionIcon/>}
                            </div>
                            <div className="relative w-fit text-white text-base font-bold leading-normal">{value}</div>
                        </div>
                    ))}
                </div>

                <div className="flex flex-col items-start relative flex-1 grow">
                    {[
                        {label: "Best Day % of Total Profit", value: accountBalance.bestDayPercentage},
                        {label: "Best Day", value: accountBalance.bestDay},
                        {label: "Worst Day", value: accountBalance.worstDay},
                        {label: "Avg. Winning Day", value: accountBalance.avgWinningDay},
                        {label: "Avg. Losing Day", value: accountBalance.avgLosingDay}
                    ].map(({label, value}, index) => (
                        <div
                            key={index}
                            className="justify-between h-14 px-0 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                        >
                            <div className="relative flex-1 text-stone-400">{label}</div>
                            <div className="relative w-fit text-white text-base font-bold leading-normal">{value}</div>
                        </div>
                    ))}
                </div>
            </div>
        </Card>
    );
}

function QuestionIcon() {
    return (
        <BaseTooltip.Provider>
            <BaseTooltip.Root>
                <BaseTooltip.Trigger>
                    <div className="ml-1">
                        <Image src={'/assets/images/question-icon.svg'}
                               alt={'question icon'}
                               width={24}
                               height={24}
                        />
                    </div>
                </BaseTooltip.Trigger>
                <BaseTooltip.Portal>
                    <BaseTooltip.Content
                        sideOffset={10}
                        className={clsx('px-3 py-2 bg-black rounded-lg border border-neutral-700 box-border w-max max-w-[calc(100vw-10px)] text-stone-400')}>
                        <div className="w-[265px] text-stone-400 text-xs font-normal leading-tight">
                            Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)

                            <BaseTooltip.Arrow asChild>
                                <svg width="16" height="10" viewBox="0 2 16 10"
                                     className="absolute -top-[1px] -translate-x-1/2" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_d_5037_5816)">
                                        <path d="M8.5 8L16.5 0H0.5L8.5 8Z" fill="black"/>
                                    </g>
                                    <defs>
                                        <filter id="filter0_d_5037_5816" x="0.5" y="0" width="16" height="10"
                                                filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                                            <feFlood floodOpacity="0" result="BackgroundImageFix"/>
                                            <feColorMatrix in="SourceAlpha" type="matrix"
                                                           values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                           result="hardAlpha"/>
                                            <feOffset dy="2"/>
                                            <feComposite in2="hardAlpha" operator="out"/>
                                            <feColorMatrix type="matrix"
                                                           values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                                            <feBlend mode="normal" in2="BackgroundImageFix"
                                                     result="effect1_dropShadow_5037_5816"/>
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_5037_5816"
                                                     result="shape"/>
                                        </filter>
                                    </defs>
                                </svg>
                            </BaseTooltip.Arrow>
                        </div>
                    </BaseTooltip.Content>
                </BaseTooltip.Portal>
            </BaseTooltip.Root>
        </BaseTooltip.Provider>
    )
}

export default AccountBalance;
