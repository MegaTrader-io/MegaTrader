'use client'

import React, {useState} from 'react';
import Card from "@/components/Card";
import QuestionIcon from "@/components/QuestionIcon";
import {Button} from "@/components/Button";
import ExclamationIcon from "@/components/ExclamationIcon";
import ChartGauge from "@/app/(backoffice)/account-overview/_components/ChartGauge";


const Options = [
    {id: 'overview', label: 'Overview'},
    {id: 'e_mini_sp_500', label: 'E-mini S&P 500'},
    {id: 'british_pound', label: 'British pound'},
    {id: 'micro_e_mini_nasdaq_100', label: 'Micro E-mini nasdaq 100'},
    {id: 'micro_australian', label: 'Micro Australian'},
]

function FeatureContent() {
    const [selection, setSelection] = useState('overview');

    return (
        <div className="w-full space-y-4">
            <div className="flex gap-2">
                {Options.map(option => (
                    <Button key={option.id}
                            variant={option.id === selection ? "primary" : 'dark'}
                            onClick={() => {
                                setSelection(option.id)
                            }}>
                        {option.label}
                    </Button>
                ))}
            </div>
            <Card className="w-full space-y-8">
                <div className="flex justify-between">
                    <div
                        className="px-3 py-1 gap-2 inline-flex items-center justify-center    bg-stone-800 rounded-2xl">
                        <div
                            className="text-xs font-medium text-white uppercase leading-normal">
                            AVG. PROFITABILITY PER TRADE
                        </div>

                        <ExclamationIcon className="text-white"/>

                        <div className="flex items-center gap-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_4398_10486" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                      y="0" width="24" height="24">
                                    <rect width="24" height="24" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_4398_10486)">
                                    <path d="M12 18L6 12L7.4 10.6L11 14.2V5H13V14.2L16.6 10.6L18 12L12 18Z"
                                          fill="#F43F5E"/>
                                </g>
                            </svg>

                            <div
                                className="text-xs font-bold text-rose-500 leading-tight">
                                -$127.16
                            </div>
                        </div>
                    </div>

                    <QuestionIcon className="text-white"/>
                </div>

                <div className="h-[328px] justify-between grid grid-cols-[288px_288px_288px] w-full px-4">
                    <div className="gap-2 flex flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Avg. Winning Trade</span>
                            <ExclamationIcon className="text-[#d9d9d9]"/>
                        </div>

                        <div className="h-72 justify-center items-center flex">
                            <ChartGauge />
                        </div>
                    </div>
                    <div className="gap-2 flex  flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Winning Trade %</span>
                            <ExclamationIcon className="text-[#d9d9d9]"/>
                        </div>
                        <div className="bg-gray-600 h-72 justify-center items-center flex">
                            chart
                        </div>
                    </div>
                    <div className="gap-2 flex  flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Avg. Losing Trade</span>
                            <ExclamationIcon className="text-[#d9d9d9]"/>
                        </div>

                        <div className="bg-gray-600 h-72 justify-center items-center flex">
                            chart
                        </div>
                    </div>
                </div>


                <div className="w-full">
                    <div
                        className="px-3 py-1 gap-2 inline-flex items-center justify-center    bg-stone-800 rounded-2xl">
                        <div
                            className="text-xs font-medium text-white uppercase leading-normal">
                            REWARD-TO-RISK RATIO
                        </div>

                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_4398_10484" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_4398_10484)">
                                <path
                                    d="M11 17H13V11H11V17ZM12 9C12.2833 9 12.5208 8.90417 12.7125 8.7125C12.9042 8.52083 13 8.28333 13 8C13 7.71667 12.9042 7.47917 12.7125 7.2875C12.5208 7.09583 12.2833 7 12 7C11.7167 7 11.4792 7.09583 11.2875 7.2875C11.0958 7.47917 11 7.71667 11 8C11 8.28333 11.0958 8.52083 11.2875 8.7125C11.4792 8.90417 11.7167 9 12 9ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
                                    fill="white"/>
                            </g>
                        </svg>

                        <div className="flex items-center">
                            <div
                                className="text-white text-xs font-medium uppercase leading-normal">
                                1:1.52
                            </div>
                        </div>
                    </div>
                </div>

            </Card>
        </div>
    );
}

export default FeatureContent;