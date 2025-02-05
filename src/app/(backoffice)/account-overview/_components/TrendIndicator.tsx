import React from 'react';
import clsx from "clsx";
import NumericStyle from "@/components/NumericStyle";

const ArrowUp = () => (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_2604_1333" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
              height="24">
            <rect width="24" height="24" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_2604_1333)">
            <path d="M11 18V8.8L7.4 12.4L6 11L12 5L18 11L16.6 12.4L13 8.8V18H11Z" fill="currentColor"/>
        </g>
    </svg>
)

const ArrowDown = () => (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_4398_10486" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
              y="0" width="24" height="24">
            <rect width="24" height="24" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_4398_10486)">
            <path d="M12 18L6 12L7.4 10.6L11 14.2V5H13V14.2L16.6 10.6L18 12L12 18Z"
                  fill="currentColor"/>
        </g>
    </svg>
)

function TrendIndicator({value}: { value: number }) {
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