import React from 'react';
import {Button} from "@/components/Button";
import Alert from "@/components/Alert";
import {PayoutSummary, IRequestPayoutTransfer, PaymentMethodType} from "@/commons/interfaces";
import FormRequest from "@/app/(backoffice)/payouts/_components/payout_modal/_components/FormRequest";

function ConfirmRequestPanel({
                                 methodTypeSelected,
                                 submitForm,
                                 payload,
                                 goBack
                             }: {
    goBack: () => void,
    payload: PayoutSummary | IRequestPayoutTransfer,
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