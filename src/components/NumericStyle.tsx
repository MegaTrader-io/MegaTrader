import React from 'react';
import {formatCurrency} from "@/commons/utils";
import clsx from "clsx";

interface Props {
    value: number,
    positiveColor?: string,
    negativeColor?: string,
    className?: string,
}

const NumericStyle: React.FC<Props> = ({
                                           value,
                                           className = '',
                                           positiveColor = 'text-green-500',
                                           negativeColor = 'text-[#F43F5E]'
                                       }) => {
    const legend = value === 0 ? '' : (value < 0 ? '-' : '+');
    const textColor = value === 0 ? '' : (value < 0 ? negativeColor : positiveColor);

    return (
        <span className={clsx(className, textColor)}>
            {legend}{formatCurrency(Math.abs(value))}
        </span>
    );
}

export default NumericStyle;