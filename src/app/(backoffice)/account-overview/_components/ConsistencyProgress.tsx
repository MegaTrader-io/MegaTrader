import React from 'react';
import {Account} from "@/commons/interfaces";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";

function ConsistencyProgress({account}: { account: Account }) {
    return (
        <div>
            <div className="flex justify-between">
                <div className="text-white text-base font-light leading-normal">Consistency</div>
                <div className="flex items-center">
                    {account.objectives.consistency.percentage && account.objectives.consistency.percentage === 0 &&
                        <XCircleIcon className="w-6 h-6 text-rose-500"/>}
                    {account.objectives.consistency.percentage && account.objectives.consistency.percentage > 0 &&
                        <CheckCircleIcon className="w-6 h-6 text-secondary"/>}
                </div>
            </div>
            <div>
                <div aria-hidden="true" className="my-2">
                    <div className="overflow-hidden rounded-full bg-neutral-700">
                        <div style={{width: `${account.objectives.consistency.percentage}%`}}
                             className="h-2 bg-secondary"/>
                    </div>
                </div>
            </div>
            <div className="text-right text-teal-400 text-base font-normal leading-normal">
                {account.objectives.consistency.percentage}%
            </div>
        </div>
    );
}

export default ConsistencyProgress;