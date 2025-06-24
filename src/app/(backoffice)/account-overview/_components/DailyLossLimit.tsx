import React from 'react';
import {formatCurrency} from "@/commons/utils";
import ExclamationTooltip from "@/app/(backoffice)/account-overview/_components/ExclamationTooltip";

function DailyLossLimit({dailyLossLimit}: { dailyLossLimit: number|null }): React.ReactNode {
    return <div className="flex items-center gap-2">
        {dailyLossLimit !== null ? formatCurrency(dailyLossLimit, 0) : 'None'}
        <ExclamationTooltip
            className='text-stone-400'
            title={'Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)'}/>
    </div>
}

export default DailyLossLimit;