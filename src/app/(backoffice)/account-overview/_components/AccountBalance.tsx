import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";
import {formatCurrency} from "@/commons/utils";

function AccountBalance({account}: { account: Account }) {
    const {accountBalance} = account;

    return (
        <Card>
            <div className="text-white text-lg font-bold mb-4">ACCOUNT BALANCE</div>

            <div className="gap-4 flex items-center">
                <div className="flex flex-col items-start relative flex-1 grow">
                    {[
                        {label: "Current Balance", value: formatCurrency(accountBalance.currentBalance)},
                        {label: "Current Equity", value: formatCurrency(accountBalance.currentEquity)},
                        {label: "High", value: formatCurrency(accountBalance.high)},
                        {label: "Low", value: formatCurrency(accountBalance.low)},
                        {label: "Weekly Net P&L", value: formatCurrency(accountBalance.weeklyNetPnL)}
                    ].map(({label, value}, index) => (
                        <div
                            key={index}
                            className="justify-between px-0 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                        >
                            <div className="relative flex-1 text-stone-400">{label}</div>
                            <div className="relative w-fit text-white">{value}</div>
                        </div>
                    ))}
                </div>

                <div className="flex flex-col items-start relative flex-1 grow">
                    {[
                        {label: "Best Day % of Total Profit", value: accountBalance.bestDayPercentage},
                        {label: "Best Day", value: formatCurrency(accountBalance.bestDay)},
                        {label: "Worst Day", value: formatCurrency(accountBalance.worstDay)},
                        {label: "Avg. Winning Day", value: accountBalance.avgWinningDay},
                        {label: "Avg. Losing Day", value: formatCurrency(accountBalance.avgLosingDay)}
                    ].map(({label, value}, index) => (
                        <div
                            key={index}
                            className="justify-between px-0 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
                        >
                            <div className="relative flex-1 text-stone-400">{label}</div>
                            <div className="relative w-fit text-white">{value}</div>
                        </div>
                    ))}
                </div>
            </div>
        </Card>
    );
}

export default AccountBalance;
