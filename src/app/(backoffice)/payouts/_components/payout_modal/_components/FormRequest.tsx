import React from 'react';
import {IRequestPayoutForm, IRequestPayoutRiseWorks, PaymentMethodType} from "@/commons/interfaces";
import {PayoutMethod} from "@/commons/data";
import PayoutTransaction from "@/app/(backoffice)/payouts/_components/payout_modal/_components/PayoutTransaction";

function FormRequest({methodTypeSelected, payload}: {
    methodTypeSelected: PaymentMethodType,
    payload: IRequestPayoutForm,

}) {
    if (methodTypeSelected === PayoutMethod.RISEWORKS) {
        const _payload = payload as IRequestPayoutRiseWorks;
        return <>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Full
                    Name
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{_payload.fullName}
                </div>
            </div>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Email
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{_payload.email}
                </div>
            </div>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Address
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{_payload.address}
                </div>
            </div>
            <PayoutTransaction
                withdrawalAmount={_payload.withdrawalAmount}
                transactionFee={_payload.transactionFee}
                netAmount={_payload.netAmount}
            />
        </>
    }

    return null;
}

export default FormRequest;