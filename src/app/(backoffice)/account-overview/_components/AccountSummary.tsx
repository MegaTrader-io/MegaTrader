import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";
import Image from "next/image";
import Tooltip from "@/components/Tooltip";
import {CheckCircleIcon, ExclamationCircleIcon} from "@heroicons/react/20/solid";
import NumericStyle from "@/components/NumericStyle";
import {formatCurrency} from "@/commons/utils";
import ConsistencyProgress from "@/app/(backoffice)/account-overview/_components/ConsistencyProgress";
import {ObjectiveItemPoint} from "@/app/(backoffice)/account-overview/_components/ObjectiveItemPoint";
import {ObjectiveItemMoney} from "@/app/(backoffice)/account-overview/_components/ObjectiveItemMoney";
import {TotalProfit} from "@/app/(backoffice)/account-overview/TotalProfit";
import HighestProfitDay from "@/app/(backoffice)/account-overview/_components/HighestProfitDay";


function AccountSummary({account}: { account: Account, isLoadingAccount: boolean }) {
    const {overallPerformance} = account;

    return (
        <Card className="grid grid-cols-2 gap-8 w-full">
            <div className="space-y-4">
                <div className="text-white text-xl font-light uppercase leading-normal">OVERALL PERFORMANCE</div>
                <div className="gap-4 lg:flex lg:items-center">
                    <div className="lg:flex flex-col items-start relative flex-1 grow">
                        {[
                            {
                                label: "Account Balance",
                                value: <NumericStyle positiveLegend={''}
                                                     positiveColor={account.status === 'inactive' ? 'text-rose-500' : 'text-teal-400'}
                                                     negativeColor={'text-rose-500'}
                                                     value={overallPerformance.currentBalance}/>
                            },
                            {
                                label: "Total Profit", value: <TotalProfit overallPerformance={overallPerformance}/>
                            },
                            {label: "Trading Days", value: overallPerformance.tradingDays},
                            {label: "Daily Loss Limit", value: formatCurrency(overallPerformance.dailyLossLimit, 0)},
                            {
                                label: "Current Equity",
                                value: <>{formatCurrency(Math.abs(overallPerformance.currentEquity))}</>
                            },
                            {
                                label: "Weekly Net P&L",
                                value: <NumericStyle positiveColor={'text-teal-400'}
                                                     zeroColor={'text-teal-400'}
                                                     value={overallPerformance.weeklyNetPnL}/>
                            }
                        ].map(({label, value}, index) => (
                            <div
                                key={index}
                                className="justify-between h-14 px-0 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                            >
                                <div
                                    className="relative flex-1 flex text-stone-400 text-base font-normal leading-normal">
                                    {label}
                                    {label === 'Weekly Net P&L' && <QuestionIcon/>}
                                </div>

                                <div
                                    className="relative w-fit text-white text-base font-medium leading-normal">{value}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <div className="space-y-4">
                <div className="text-white text-xl font-light uppercase leading-normal">Your Challenge Objective</div>
                <div className="gap-4 lg:flex lg:items-center">
                    <div className="lg:flex flex-col items-start relative flex-1 grow space-y-8">
                        <div className="w-full">
                            {account.objectives.profitTarget &&
                                <ObjectiveItemMoney label={'Profit Target'}
                                                    objective={account.objectives.profitTarget}/>}
                            {account.objectives.daysTraded &&
                                <ObjectiveItemPoint label={'Days Traded'}
                                                    objective={account.objectives.daysTraded}/>}
                            {account.objectives.profit &&
                                <ObjectiveItemMoney label={'Profit'}
                                                    objective={account.objectives.profit}/>}
                            {account.objectives.tradingDayBetweenPayouts &&
                                <ObjectiveItemPoint label={'Trading Days Between Payouts'}
                                                    objective={account.objectives.tradingDayBetweenPayouts}/>}
                            {account.objectives.tradingDayWithProfit &&
                                <ObjectiveItemPoint label={'Trading Days with $150 Profit'}
                                                    objective={account.objectives.tradingDayWithProfit}/>}
                            {account.objectives.consistency &&
                                <ConsistencyProgress account={account}/>}

                            {account.objectives.highestProfitDaySinceLastPayout !== undefined &&
                                <HighestProfitDay account={account}/>}
                        </div>
                        <div className="w-full">
                            <div className="text-white text-xl font-light uppercase leading-normal mb-4">RULE</div>
                            <div className="flex gap-2">
                                <div className="flex items-center gap-2 h-full">
                                    <div className="h-full">
                                        <div className="flex items-center gap-2">
                                            {!account.objectives.rule.maximumLossLimit.pass &&
                                                <ExclamationCircleIcon className="rotate-180 w-6 h-6 text-rose-500"/>}
                                            {account.objectives.rule.maximumLossLimit.pass &&
                                                <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                                        </div>
                                    </div>
                                    <div className="flex flex-col">
                                        <div
                                            className="text-white text-base font-medium leading-normal">
                                            <div>
                                                {account.objectives.rule.maximumLossLimit.description}
                                            </div>
                                            <div
                                                className="block lg:inline text-mgt-link text-xs font-medium underline leading-tight">
                                                Maximum Loss Limit
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Card>
    );
}

function QuestionIcon() {
    return (
        <Tooltip
            className="ml-1"
            content={'Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)'}>
            <Image
                src={'/assets/images/question-icon.svg'}
                alt={'question icon'}
                width={24}
                height={24}
            />
        </Tooltip>
    )
}

export default AccountSummary;
