import React from 'react';
import {formatCurrency} from "@/commons/utils";
import clsx from "clsx";

interface Props {
    value: number,
    positiveColor?: string,
    decimal?: number,
    positiveLegend?: string,
    negativeColor?: string,
    className?: string,
}

const NumericStyle: React.FC<Props> = ({
                                           value,
                                           decimal = 2,
                                           positiveLegend = '+',
                                           className = '',
                                           positiveColor = 'text-green-500',
                                           negativeColor = 'text-[#F43F5E]'
                                       }) => {
    const legend = value === 0 ? '' : (value < 0 ? '-' : positiveLegend);
    const textColor = value === 0 ? '' : (value < 0 ? negativeColor : positiveColor);

    return (
        <span className={clsx(className, textColor)}>
            {legend}{formatCurrency(Math.abs(value), decimal)}
        </span>
    );
}

export default NumericStyle;