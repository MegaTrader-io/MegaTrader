import React from 'react';
import ArrowUp from "@/app/(backoffice)/account-overview/_components/ArrowUp";
import Badge from "@/components/Badge";
import {Account} from "@/commons/interfaces";

function ProfitProgress({account}: { account: Account }) {
    return (
        <div>
            <div className="flex justify-between">
                <div className="text-white text-base font-light leading-normal">Profit</div>
                <div className="flex items-center">
                    <span className="text-secondary"><ArrowUp/></span>
                    <Badge className=" font-medium uppercase leading-none !px-2"
                           shape={'pill'}>{account.objectives.profit.percentage?.toFixed(2)}%</Badge>
                </div>
            </div>
            <div>
                <div aria-hidden="true" className="my-2">
                    <div className="overflow-hidden rounded-full bg-neutral-700">
                        <div style={{width: `${account.objectives.profit.percentage}%`}}
                             className="h-2 bg-secondary"/>
                    </div>
                </div>
            </div>
            <div className="text-right text-teal-400 text-base font-normal leading-normal">
                ${account.objectives.profit.current?.toFixed(2)}
            </div>
        </div>
    );
}

export default ProfitProgress;