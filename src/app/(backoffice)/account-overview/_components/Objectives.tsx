import React from 'react';
import Card from "@/components/Card";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/16/solid";
import ProgressSteps from "@/app/(backoffice)/account-overview/ProgressSteps";
import {Account} from "@/commons/interfaces";
import NumericStyle from "@/components/NumericStyle";

function Objectives({account}: { account: Account }) {
    return (
        <Card className="space-y-4 lg:max-w-[405px]">
            <div className="text-white text-xl font-light uppercase leading-normal">Objectives</div>

            <div className="space-y-2">
                <div className="flex gap-2">
                    <div className="flex items-center">
                        {account.status === 'unpaid' && <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                        {account.status !== 'unpaid' && <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                    </div>

                    <div className="flex gap-4 w-full">
                        <div className="w-full ">
                            <div className="gap-2">
                                <div className="text-white text-base font-light lg:pr-8 pr-12">Reach and maintain the
                                    $9,000+ <span
                                        className="pl-4 text-mgt-link text-xs font-normal underline leading-tight">Profit
                                    target
                                </span>
                                </div>
                            </div>
                            <div aria-hidden="true" className="my-2">
                                <div className="overflow-hidden rounded-full bg-neutral-700">
                                    <div style={{width: `${account.objectives.progress}%`}}
                                         className="h-2 bg-secondary"/>
                                </div>
                            </div>
                            <div className="text-base font-light text-right">
                                <span>
                                    <NumericStyle
                                        decimal={0}
                                        positiveLegend={''}
                                        positiveColor={'text-mgt-link'}
                                        negativeColor={'text-rose-500'}
                                        value={account.objectives.reach_and_maintain_total}/>
                                </span>
                                <span className="text-stone-400 mx-1">/</span>
                                <span className="text-stone-400">$9,000</span>
                            </div>
                        </div>

                        <div className="flex flex-col items-center gap-2 justify-center">
                            <ProgressSteps
                                variant={account.status !== 'unpaid' ? 'secondary' : 'error'}
                                currentStep={account.objectives.level}
                                totalSteps={5}/>
                            <div
                                className="relative -top-[5px] text-center text-white text-base font-normal leading-normal">
                                Trading Days
                            </div>
                        </div>
                    </div>

                </div>
                <div className="flex gap-2">
                    <div className="flex items-center">
                        {account.status === 'unpaid' && <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                        {account.status !== 'unpaid' && <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                    </div>
                    <div>
                        <div>
                            <div className="text-white text-base font-light leading-normal">Best day cannot be greater
                                than 50% of your total profit.
                                <p
                                    className="block  lg:inline lg:ml-2 text-mgt-link text-xs font-normal underline leading-tight">Consistency
                                    Target
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="text-white text-xl font-light uppercase leading-normal">RULE</div>
            <div className="flex gap-2 ">
                <div className="flex items-center">
                    {account.status === 'unpaid' && <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                    {account.status !== 'unpaid' && <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                </div>
                <div>
                    <div className="flex flex-col">
                        <span className="text-white text-base font-light leading-normal">Do not let your account balance
                            hit or go below $145,500.
                            <p className="block lg:inline lg:ml-2 text-mgt-link text-xs font-normal underline leading-tight">Maximum Loss Limit</p>
                        </span>
                    </div>
                </div>
            </div>

        </Card>
    )
}

export default Objectives;