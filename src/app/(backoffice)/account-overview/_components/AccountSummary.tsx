import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";
import Tooltip from "@/components/Tooltip";
import {CheckCircleIcon, ExclamationCircleIcon} from "@heroicons/react/20/solid";
import NumericStyle from "@/components/NumericStyle";
import {formatCurrency} from "@/commons/utils";
import ConsistencyProgress from "@/app/(backoffice)/account-overview/_components/ConsistencyProgress";
import {ObjectiveItemPoint} from "@/app/(backoffice)/account-overview/_components/ObjectiveItemPoint";
import {ObjectiveItemMoney} from "@/app/(backoffice)/account-overview/_components/ObjectiveItemMoney";
import {TotalProfit} from "@/app/(backoffice)/account-overview/TotalProfit";
import HighestProfitDay from "@/app/(backoffice)/account-overview/_components/HighestProfitDay";

function DailyLossLimit({dailyLossLimit}: { dailyLossLimit: number }): React.ReactNode {
    return <div className="flex items-center gap-2">
        {formatCurrency(dailyLossLimit, 0)}
        <ExclamationTooltip
            title={'Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)'}/>
    </div>
}

function ExclamationTooltip({className = 'text-white', title}: { className?: string, title: string }) {
    return (
        <Tooltip
            className={className}
            content={title}>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_6366_9388" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                      height="24">
                    <rect width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_6366_9388)">
                    <path
                        d="M11 17H13V11H11V17ZM12 9C12.2833 9 12.5208 8.90417 12.7125 8.7125C12.9042 8.52083 13 8.28333 13 8C13 7.71667 12.9042 7.47917 12.7125 7.2875C12.5208 7.09583 12.2833 7 12 7C11.7167 7 11.4792 7.09583 11.2875 7.2875C11.0958 7.47917 11 7.71667 11 8C11 8.28333 11.0958 8.52083 11.2875 8.7125C11.4792 8.90417 11.7167 9 12 9ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
                        fill="currentColor"/>
                </g>
            </svg>
        </Tooltip>
    )
}


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
                            {
                                label: "Daily Loss Limit",
                                value: <DailyLossLimit dailyLossLimit={overallPerformance.dailyLossLimit}/>
                            },
                            {
                                label: "Current Equity",
                                value: <>{formatCurrency(Math.abs(overallPerformance.currentEquity))}</>
                            },
                            {
                                label: <div className="flex items-center gap-2">
                                    <span>Weekly Net P&L</span> <ExclamationTooltip
                                    className="text-stone-400"
                                    title={'Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)'}/>
                                </div>,
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
                                                    objective={account.objectives.profitTarget}/>
                            }
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
    )
}

export default AccountSummary;
