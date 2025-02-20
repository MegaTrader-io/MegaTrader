import {ObjectiveType} from "@/commons/interfaces";
import {CheckCircleIcon} from "@heroicons/react/20/solid";
import clsx from "clsx";
import React from "react";

export function ObjectiveItemPoint({label, objective}: { label: string, objective: ObjectiveType }) {
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
                    <span className="text-white">{label}</span>
                </div>
            </div>
            <div className="w-full h-full">
                <div className="text-base font-medium text-right uppercase leading-normal">
                                <span className={clsx([currentValue <= 0 ? 'text-rose-500' : 'text-teal-400'])}>
                                    {currentValue ?? 0}
                                </span>
                    <span
                        className="text-white text-base font-medium uppercase leading-normal mx-1">/</span>
                    <span className="text-white text-base font-medium uppercase leading-normal">
                                    {currentTarget}
                                </span>
                </div>
                <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                    <div style={{width: `${percentage}%`}}
                         className="h-2 bg-secondary"/>
                </div>
            </div>
        </div>
    </div>
}