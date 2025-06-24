import React from 'react';
import {Account} from "@/commons/interfaces";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import clsx from "clsx";
import {QuestionIcon} from "@/app/(backoffice)/account-overview/_components/QuestionIcon";
import Link from "next/link";

function ConsistencyProgress({account}: { account: Account }) {
    if (!account.objectives.consistency) {
        return;
    }

    const passConsistency = account.objectives.consistency.percentage && account.objectives.consistency.percentage >= account.objectives.consistency.minPercentage;

    return <div className="w-full">
        <div
            className="justify-between px-0 gap-4 py-4 self-stretch w-full border-b border-neutral-700 flex items-center"
        >
            <div className="w-full h-full">
                <div className="grid grid-cols-[24px_1fr] items-center gap-2">
                    {passConsistency &&
                        <CheckCircleIcon className="w-6 h-6 text-teal-400"/>}
                    {!passConsistency &&
                        <XCircleIcon className="rotate-180 w-6 h-6 text-rose-500"/>}
                    <div>
                        <div className="text-white text-base font-light leading-normal flex items-center gap-2">
                            <span>Consistency</span>
                            <div className="inline-flex">
                                <QuestionIcon contentClassName={'!w-[300px]'} content={
                                    <div className="space-y-4">
                                        <div className="h-12 justify-start items-center gap-2 inline-flex">
                                            <div
                                                className="grow shrink basis-0 flex-col justify-start items-start gap-1 inline-flex">
                                                <div
                                                    className="self-stretch text-stone-400 text-xs font-medium leading-tight">$0
                                                    (biggest day)
                                                </div>
                                                <div className="self-stretch h-[0px] border border-neutral-700"></div>
                                                <div
                                                    className="self-stretch text-stone-400 text-xs font-medium leading-tight">$1,984
                                                    (profit since payout)
                                                </div>
                                            </div>
                                            <div className="text-stone-400 text-xs font-medium leading-tight">=
                                                100% consistency
                                            </div>
                                        </div>
                                        <div>
                                            <div className="text-stone-400 text-xs font-medium leading-tight">Profit
                                                required to reach 20% consistency: $0
                                            </div>
                                            <div
                                                className="text-stone-400 text-xs font-medium leading-tight">Consistency
                                                resets when a payout is approved
                                            </div>
                                        </div>

                                        <div className="text-stone-400 text-xs font-medium leading-tight">If
                                            there is not profit overall your account will be N/A
                                        </div>
                                    </div>
                                }/>
                            </div>
                        </div>
                        <div>
                            <span
                                className="text-white text-xs font-medium leading-tight tracking-tight">You need to maintain at least {account.objectives.consistency.minPercentage}%
                                consistency. {!account.objectives.consistency.link && (
                                    <span className="text-[#ffd78a] text-xs font-medium underline leading-tight">Learn more</span>
                                )} {account.objectives.consistency.link && (
                                    <Link target={'_blank'} href={account.objectives.consistency.link}
                                          className=" text-[#ffd78a] text-xs font-medium underline leading-tight">
                                        Learn more
                                    </Link>
                                )}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div className="w-full h-full">
                <div className="text-base font-medium text-right uppercase leading-normal">
                                <span
                                    className={clsx([!passConsistency ? 'text-rose-500' : 'text-teal-400'])}>
                                    {account.objectives.consistency.percentage ?? 0}%
                                </span>
                </div>
                <div className="overflow-hidden rounded-full mt-1 bg-neutral-700">
                    <div style={{width: `${account.objectives.consistency.percentage}%`}}
                         className={clsx("h-2", clsx([!passConsistency ? 'bg-rose-500' : 'bg-teal-400']))}/>
                </div>
            </div>
        </div>
    </div>
}

export default ConsistencyProgress;