import {OverallPerformance} from "@/commons/interfaces";
import NumericStyle from "@/components/NumericStyle";
import {ArrowDown, ArrowUp} from "@/components/Arrows";
import Badge from "@/components/Badge";
import React from "react";

export function TotalProfit({overallPerformance}: { overallPerformance: OverallPerformance }) {
    return <div className="flex items-center gap-2">
        <NumericStyle positiveColor={'text-teal-400'}
                      zeroColor={'text-teal-400'}
                      value={overallPerformance.totalProfit.value}/>
        <div className="flex">
            {overallPerformance.totalProfit.percentage > 0 ? <ArrowUp circle={false} className="text-teal-400"/> :
                <ArrowDown circle={false} className="text-rose-500"/>}
            <Badge shape={'pill'} size={'sm'}
                   variant={overallPerformance.totalProfit.percentage > 0 ? 'secondary' : 'error'}>
                {overallPerformance.totalProfit.percentage.toFixed(2)}%
            </Badge>
        </div>
    </div>
}