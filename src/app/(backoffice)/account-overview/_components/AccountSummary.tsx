import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";
import Image from "next/image";
import Tooltip from "@/components/Tooltip";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import NumericStyle from "@/components/NumericStyle";
import {formatCurrency} from "@/commons/utils";

function AccountSummary({account}: { account: Account, isLoadingAccount: boolean }) {
    const {accountBalance} = account;

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
                                                     positiveColor={'text-teal-400'}
                                                     value={accountBalance.currentBalance}
                                                     decimal={0}/>
                            },
                            {
                                label: "Total Profit", value: <NumericStyle positiveLegend={''}
                                                                            positiveColor={'text-teal-400'}
                                                                            zeroColor={'text-teal-400'}
                                                                            value={accountBalance.totalProfit}
                                                                            decimal={0}/>
                            },
                            {label: "Trading Days", value: accountBalance.tradingDays},
                            {
                                label: "Current Equity",
                                value: <>{formatCurrency(Math.abs(accountBalance.currentEquity))}</>
                            },
                            {
                                label: "Weekly Net P&L",
                                value: <>{accountBalance.weeklyNetPnL > 0 ? formatCurrency(Math.abs(accountBalance.weeklyNetPnL), 2) : '$0'}</>
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
                            <div className="w-full">
                                <div
                                    className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                                >
                                    <div className="w-full h-full">
                                        <div className="flex items-center gap-2">
                                            {account.status === 'unpaid' &&
                                                <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                                            {account.status !== 'unpaid' &&
                                                <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                                            <span className="text-white">Profit Target</span>
                                        </div>
                                    </div>
                                    <div className="w-full h-full">
                                        <div className="text-base font-light text-right">
                                <span>
                                    <NumericStyle
                                        decimal={account.objectives.profit.current === 0 ? 0 : 2}
                                        positiveLegend={''}
                                        zeroColor={'text-teal-400'}
                                        positiveColor={'text-teal-400'}
                                        negativeColor={'text-rose-500'}
                                        value={account.objectives.profit.current ?? 0}/>
                                </span>
                                            <span
                                                className="text-white text-base font-medium uppercase leading-normal mx-1">/</span>
                                            <span className="text-white text-base font-medium uppercase leading-normal">
                                    {formatCurrency(account.objectives.profit.goal ?? 0)}
                                </span>
                                        </div>
                                        <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                                            <div style={{width: `${account.objectives.profit.percentage}%`}}
                                                 className="h-2 bg-secondary"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="w-full">
                                <div
                                    className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                                >
                                    <div className="w-full h-full">
                                        <div className="flex items-center gap-2">
                                            {account.status === 'unpaid' &&
                                                <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                                            {account.status !== 'unpaid' &&
                                                <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                                            <span className="text-white">Days Traded</span>
                                        </div>
                                    </div>
                                    <div className="w-full h-full">
                                        <div className="text-base font-light text-right">
                                <span className="text-teal-400">
{account.objectives.tradingDays.current ?? 0}
                                </span>
                                            <span
                                                className="text-white text-base font-medium uppercase leading-normal mx-1">/</span>
                                            <span className="text-white text-base font-medium uppercase leading-normal">
                                    {account.objectives.tradingDays.total ?? 0}
                                </span>
                                        </div>
                                        <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                                            <div style={{width: `${account.objectives.profit.percentage}%`}}
                                                 className="h-2 bg-secondary"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="w-full">
                            <div className="text-white text-xl font-light uppercase leading-normal mb-4">RULE</div>
                            <div className="flex gap-2">
                                <div>
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
