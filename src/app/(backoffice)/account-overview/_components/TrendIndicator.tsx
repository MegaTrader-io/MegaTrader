import React from 'react';
import clsx from "clsx";
import NumericStyle from "@/components/NumericStyle";
import ArrowUp from "@/app/(backoffice)/account-overview/_components/ArrowUp";
import ArrowDown from "@/app/(backoffice)/account-overview/_components/ArrowDown";

function  TrendIndicator({value}: { value: number }) {
    const textColor = value < 0 ? 'text-[#F43F5E]' : 'text-green-500';
    return (
        <div className="flex items-center gap-1">
            <span className={clsx([textColor])}>
                {value && value > 0 && <ArrowUp/>}
                {value && value < 0 && <ArrowDown/>}
            </span>
            <div
                className={clsx('text-nowrap flex text-xs font-bold items-center', [textColor])}>
                <NumericStyle value={value} />
            </div>
        </div>
    );
}

export default TrendIndicator;