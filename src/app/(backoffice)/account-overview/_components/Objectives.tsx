import React from 'react';
import Card from "@/components/Card";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import {Account} from "@/commons/interfaces";
import NumericStyle from "@/components/NumericStyle";
import ProgressSteps from "@/app/(backoffice)/account-overview/_components/ProgressSteps";
import {formatCurrency} from "@/commons/utils";
import ProfitProgress from "@/app/(backoffice)/account-overview/_components/ProfitProgress";
import ConsistencyProgress from "@/app/(backoffice)/account-overview/_components/ConsistencyProgress";
import {SkeletonTemplate} from "@/components/Skeleton";

function Objectives({account, isLoadingAccount}: { account: Account, isLoadingAccount: boolean }) {
    return (
        <Card className="space-y-4 lg:max-w-[405px]">
            <div className="text-white text-xl font-light uppercase leading-normal">Your Challenge Objective</div>

            {isLoadingAccount && (
                <div className="min-w-[371px] h-[281px]">
                    <SkeletonTemplate>

                    </SkeletonTemplate>
                </div>
            )}

            {!isLoadingAccount && (
                <>
                    {account.status === 'funded' && (
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <ProfitProgress account={account}/>
                                <ConsistencyProgress account={account}/>
                            </div>

                            <div>
                                <span className="text-white text-base font-normal">Trading Days</span>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="w-full">
                                        <div className="flex items-center gap-2">
                                            <div>
                                                <ProgressSteps
                                                    variant={'secondary'}
                                                    currentStep={account.objectives.tradingDays.betweenPayouts.current ?? 0}
                                                    totalSteps={account.objectives.tradingDays.betweenPayouts.total ?? 0}/>
                                            </div>
                                            <div
                                                className="text-white text-base font-normal leading-normal">
                                                Between payouts
                                            </div>
                                        </div>
                                    </div>
                                    <div className="w-full">
                                        <div className="flex items-center gap-2">
                                            <div>
                                                <ProgressSteps
                                                    variant={'secondary'}
                                                    currentStep={account.objectives.tradingDays.daysWithMinProfit.current ?? 0}
                                                    totalSteps={account.objectives.tradingDays.daysWithMinProfit.total ?? 0}/>
                                            </div>
                                            <div
                                                className="text-white text-base font-normal leading-normal">
                                                Days with
                                                ${account.objectives.tradingDays.daysWithMinProfit.minProfit} profit
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {account.status !== 'funded' && (
                        <div className="space-y-2">
                            <div className="flex gap-2">
                                <div className="flex items-center">
                                    {account.status === 'unpaid' && <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                                    {account.status !== 'unpaid' &&
                                        <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                                </div>

                                <div className="flex gap-4 w-full">
                                    <div className="w-full">
                                        <div className="gap-2">
                                            <div className="text-white text-base font-light lg:pr-8 pr-12">Reach and
                                                maintain
                                                the
                                                $9,000+ <span
                                                    className="pl-4 text-mgt-link text-xs font-normal underline leading-tight">Profit
                                    target
                                </span>
                                            </div>
                                        </div>
                                        <div aria-hidden="true" className="my-2">
                                            <div className="overflow-hidden rounded-full bg-neutral-700">
                                                <div style={{width: `${account.objectives.profit.percentage}%`}}
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
                                        value={account.objectives.profit.current ?? 0}/>
                                </span>
                                            <span className="text-stone-400 mx-1">/</span>
                                            <span className="text-stone-400">
                                    {formatCurrency(account.objectives.profit.goal ?? 0, 0)}
                                </span>
                                        </div>
                                    </div>

                                    <div className="flex flex-col items-center gap-2 justify-center">
                                        <ProgressSteps
                                            variant={account.status !== 'unpaid' ? 'secondary' : 'error'}
                                            currentStep={account.objectives.tradingDays.current ?? 0}
                                            totalSteps={account.objectives.tradingDays.total ?? 0}/>
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
                                    {account.status !== 'unpaid' &&
                                        <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                                </div>
                                <div>
                                    <div>
                                        <div className="text-white text-base font-light leading-normal">Best day cannot
                                            be
                                            greater
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
                    )}

                    <div className="text-white text-xl font-light uppercase leading-normal">RULE</div>
                    <div className="flex gap-2">
                        <div className="flex items-center">
                            {account.status === 'unpaid' && <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                            {account.status !== 'unpaid' && <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                        </div>
                        <div>
                            <div className="flex flex-col">
                        <span
                            className="text-white text-base font-light leading-normal">{account.objectives.rule.maximumLossLimit.description}
                            <p className="block lg:inline lg:ml-2 text-mgt-link text-xs font-normal underline leading-tight">Maximum Loss Limit</p>
                        </span>
                            </div>
                        </div>
                    </div>
                </>
            )}
        </Card>
    )
}

export default Objectives;