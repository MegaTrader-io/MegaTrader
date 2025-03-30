import React from "react";
import {PayoutSummary} from "@/commons/interfaces";

function PayoutTransaction({withdrawalAmount, transactionFee, netAmount}: PayoutSummary) {
    return <>
        <div
            className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
            <div
                className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Amount to
                Withdraw
            </div>
            <div
                className="text-right justify-start text-base font-medium leading-normal">${withdrawalAmount}
            </div>
        </div>
        <div
            className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
            <div
                className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Transaction Fee
            </div>
            <div
                className="text-right justify-start text-base font-medium leading-normal">${transactionFee}
            </div>
        </div>

        <div
            className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
            <div
                className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Amount to
                Receive
            </div>
            <div
                className="text-right justify-start text-base font-medium leading-normal">${netAmount}
            </div>
        </div>
    </>
}

export default PayoutTransaction;