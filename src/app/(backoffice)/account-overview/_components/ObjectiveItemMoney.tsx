import {ObjectiveType} from "@/commons/interfaces";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import NumericStyle from "@/components/NumericStyle";
import {formatCurrency} from "@/commons/utils";
import React from "react";

export function ObjectiveItemMoney({label, objective}: { label: string, objective?: ObjectiveType }) {
    if (!objective) {
        return
    }

    const percentage: number = objective.target > 0 ? (objective.target * 100) / objective.value : 0
    const currentTarget: number = objective.target;
    const currentValue: number = objective.value;

    return <div className="w-full">
        <div
            className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
        >
            <div className="w-full h-full">
                <div className="flex items-center gap-2">
                    {currentTarget > 0 && currentTarget < currentValue &&
                        <CheckCircleIcon className="w-6 h-6 text-neutral-700"/>}
                    {currentTarget >= currentValue &&
                        <CheckCircleIcon className="w-6 h-6 text-teal-400"/>}
                    {currentTarget <= 0 &&
                        <XCircleIcon className="rotate-180 w-6 h-6 text-rose-500"/>}
                    <span className="text-white">{label}</span>
                </div>
            </div>
            <div className="w-full h-full">
                <div className="text-base font-medium text-right">
                                <span>
                                    <NumericStyle
                                        positiveLegend={''}
                                        zeroColor={'text-teal-400'}
                                        positiveColor={currentTarget <= 0 ? 'text-rose-500' : 'text-teal-400'}
                                        negativeColor={'text-rose-500'}
                                        value={currentTarget ?? 0}/>
                                </span>
                    <span
                        className="text-white text-base font-medium uppercase leading-normal mx-1">/</span>
                    <span className="text-white text-base font-medium uppercase leading-normal">
                                    {formatCurrency(currentValue ?? 0)}
                                </span>
                </div>
                <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                    <div
                        style={{width: `${percentage}%`}}
                        className="h-2 bg-secondary"/>
                </div>
            </div>
        </div>
    </div>
}
