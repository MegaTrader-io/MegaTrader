import React from 'react';
import {formatCurrency} from "@/commons/utils";
import clsx from "clsx";

interface Props {
    value: number,
    positiveColor?: string,
    decimal?: number,
    nevativeLegend?: string,
    positiveLegend?: string,
    zeroColor?: string,
    negativeColor?: string,
    className?: string,
}

const NumericStyle: React.FC<Props> = ({
                                           value,
                                           decimal = 2,
                                           nevativeLegend = '-',
                                           positiveLegend = '+',
                                           className = '',
                                           zeroColor = 'text-white',
                                           positiveColor = 'text-green-500',
                                           negativeColor = 'text-[#F43F5E]'
                                       }) => {
    let legend = '',
        textColor = zeroColor;

    if (value !== 0) {
        legend = (value < 0 ? nevativeLegend : positiveLegend);
        textColor = (value < 0 ? negativeColor : positiveColor);
    }

    return (
        <span className={clsx(className, textColor)}>
            {legend}{formatCurrency(Math.abs(value), decimal)}
        </span>
    );
}

export default NumericStyle;