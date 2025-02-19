import React from 'react';
import {Account} from "@/commons/interfaces";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import clsx from "clsx";

function ConsistencyProgress({account}: { account: Account }) {
    if (!account.objectives.consistency) {
        return;
    }

    return <div className="w-full">
        <div
            className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
        >
            <div className="w-full h-full">
                <div className="flex items-center gap-2">
                    {account.objectives.consistency.percentage && account.objectives.consistency.percentage >= account.objectives.consistency.minPercentage &&
                        <CheckCircleIcon className="w-6 h-6 text-teal-400"/>}
                    {account.objectives.consistency.percentage && account.objectives.consistency.percentage <= account.objectives.consistency.minPercentage &&
                        <XCircleIcon className="rotate-180 w-6 h-6 text-rose-500"/>}
                    <div>
                        <div className="text-white text-base font-light leading-normal">Consistency</div>
                        <div>
                            <span
                                className="text-white text-xs font-medium leading-tight">You need to maintain at least {account.objectives.consistency.minPercentage}% consistency. </span>
                            <span
                                className="block text-[#ffd78a] text-xs font-medium underline leading-tight">Learn more</span>
                        </div>
                    </div>
                </div>
            </div>
            <div className="w-full h-full">
                <div className="text-base font-medium text-right uppercase leading-normal">
                                <span
                                    className={clsx([account.objectives.consistency.percentage && account.objectives.consistency.percentage <= account.objectives.consistency.minPercentage ? 'text-rose-500' : 'text-teal-400'])}>
                                    {account.objectives.consistency.percentage ?? 0}%
                                </span>
                </div>
                <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                    <div style={{width: `${account.objectives.consistency.percentage}%`}}
                         className="h-2 bg-secondary"/>
                </div>
            </div>
        </div>
    </div>
}

export default ConsistencyProgress;