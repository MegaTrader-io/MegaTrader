import React from 'react';
import {Button} from "@/components/Button";
import {
    IRequestPayoutForm,
    PaymentMethodType, PayoutSummary
} from "@/app/(backoffice)/payouts/_components/payout_modal/RequestPayoutsModal";
import Alert from "@/components/Alert";

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

function FormRequest({methodTypeSelected, payload}: {
    methodTypeSelected: PaymentMethodType,
    payload: IRequestPayoutForm
}) {
    if (methodTypeSelected === 'riseworks') {
        return <>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Full
                    Name
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{payload.fullName}
                </div>
            </div>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Email
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{payload.email}
                </div>
            </div>
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Address
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{payload.address}
                </div>
            </div>
            <PayoutTransaction
                withdrawalAmount={payload.withdrawalAmount}
                transactionFee={payload.transactionFee}
                netAmount={payload.netAmount}
            />
        </>
    }

    return null;
}

function ConfirmRequestPanel({
                                 methodTypeSelected,
                                 submitForm,
                                 payload,
                                 goBack
                             }: {
    goBack: () => void,
    payload: IRequestPayoutForm,
    submitForm: () => void,
    methodTypeSelected: PaymentMethodType
}) {

    return (
        <div className="text-white w-full">
            <FormRequest methodTypeSelected={methodTypeSelected} payload={payload}/>

            <Alert type={'info'} className='my-4'
                   message={'Please verify your details before confirming. Incorrect information may cause payment delays.'}/>

            <div
                className="flex flex-col !mt-4 space-y-4 sm:space-y-0 sm:flex sm:flex-row justify-center gap-2">
                <Button onClick={goBack} className="w-full order-2 sm:order-1"
                        styleType={'text'}
                        variant={'light'}>
                    GO BACK
                </Button>
                <Button onClick={submitForm} className="w-full order-1 sm:order-2">
                    CONFIRM REQUEST
                </Button>
            </div>
        </div>
    );
}

export default ConfirmRequestPanel;