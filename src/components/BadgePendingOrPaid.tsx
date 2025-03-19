import React from 'react';
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";
import {EllipsisHorizontalCircleIcon} from "@heroicons/react/16/solid";

interface Props {
    status: 'pending' | 'approved' | 'rejected'
}

const BadgePendingOrPaid: React.FC<Props> = ({status}) => {
    return (
        <>
            <div className="flex gap-2 items-center">
                {status === 'approved' && <CheckCircleIcon className={'fill-[#2DD4BF] w-5 h-5'}/>}
                {status === 'pending' && <EllipsisHorizontalCircleIcon className={'fill-[#FB923C] stroke-1 w-5 h-5'}/>}
                {status === 'rejected' && <XCircleIcon className={'fill-[#F43F5E] stroke-1 w-5 h-5'}/>}
                <span className="uppercase text-sm font-bold leading-tight">{status}</span>
            </div>
        </>
    );
}

export default BadgePendingOrPaid;