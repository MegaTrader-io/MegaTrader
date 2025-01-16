import React from "react";
import Card from "@/components/Card";
import {Account} from "@/commons/interfaces";

function AccountBalance({account}: { account: Account }) {
    const {accountBalance} = account;

    return (
        <Card>
            <div className="text-white text-lg font-bold mb-4">ACCOUNT BALANCE</div>

            <div className="gap-4 flex items-center">
                <div className="flex flex-col items-start relative flex-1 grow">
                    {[
                        {label: "Current Balance", value: accountBalance.currentBalance},
                        {label: "Current Equity", value: accountBalance.currentEquity},
                        {label: "High", value: accountBalance.high},
                        {label: "Low", value: accountBalance.low},
                        {label: "Weekly Net P&L", value: accountBalance.weeklyNetPnL}
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
                        {label: "Best Day", value: accountBalance.bestDay},
                        {label: "Worst Day", value: accountBalance.worstDay},
                        {label: "Avg. Winning Day", value: accountBalance.avgWinningDay},
                        {label: "Avg. Losing Day", value: accountBalance.avgLosingDay}
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
