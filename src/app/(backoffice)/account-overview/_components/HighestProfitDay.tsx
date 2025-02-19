import React from 'react';
import {Account} from "@/commons/interfaces";
import {formatCurrency} from "@/commons/utils";

function ConsistencyProgress({account}: { account: Account }) {
    if (account.objectives.highestProfitDaySinceLastPayout === undefined) {
        return;
    }

    return <div className="w-full">
        <div
            className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
        >
            <div className="w-full h-full">
                <div className="flex items-center gap-2">
                    <div>
                        <div className="text-white text-base font-light leading-normal">Highest Profit Day Since Last
                            Payout
                        </div>
                    </div>
                </div>
            </div>
            <div className="w-full h-full">
                <div className="text-base font-medium text-right uppercase leading-normal">
                                <span
                                    className="text-teal-400">
                                    {formatCurrency(account.objectives.highestProfitDaySinceLastPayout)}
                                </span>
                </div>
            </div>
        </div>
    </div>
}

export default ConsistencyProgress;