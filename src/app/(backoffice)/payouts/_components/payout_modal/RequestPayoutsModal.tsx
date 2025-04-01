import React, {useState} from 'react';
import Dialog from "@/components/Dialog";
import TabButtonGroup from "@/app/(backoffice)/payouts/_components/payout_modal/_components/TabButtonGroup";
import ConfirmRequestPanel from "@/app/(backoffice)/payouts/_components/payout_modal/_components/ConfirmRequestPanel";
import RiseworksForm from "@/app/(backoffice)/payouts/_components/payout_modal/forms/RiseworksForm";

import {
    IPaymentMethod,
    IRequestPayoutCrypto,
    IRequestPayoutForm,
    IRequestPayoutRiseWorks, IRequestPayoutTransfer,
    PaymentMethodType
} from "@/commons/interfaces";
import {PayoutMethod} from "@/commons/data";
import CryptoForm from "@/app/(backoffice)/payouts/_components/payout_modal/forms/CryptoForm";

export const PaymentMethodList: IPaymentMethod[] = [
    {id: 'riseworks', name: 'Riseworks'},
    {id: 'crypto_btc', name: 'Crypto - BTC'},
    {id: 'crypto_eth', name: 'Crypto - ETH'},
];

function RequestPayoutsModal({open, onClose, submitRequest}: {
    open: boolean,
    onClose: () => void,
    submitRequest: (form: IRequestPayoutForm | IRequestPayoutTransfer) => void
}) {
    const [methodTypeSelected, setMethodTypeSelected] = useState<PaymentMethodType>(PayoutMethod.RISEWORKS);
    const [confirmData, setConfirmData] = useState<IRequestPayoutForm | IRequestPayoutTransfer | null>(null);

    function showConfirmRequestDialog(form: IRequestPayoutRiseWorks | IRequestPayoutCrypto | IRequestPayoutTransfer) {
        setConfirmData(form);
    }

    function goBack() {
        setConfirmData(null);
    }

    function submitForm() {
        if (!confirmData) {
            return
        }

        submitRequest(confirmData)
    }

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh'}
                className="w-[calc(100vw-32px)] sm:w-[700px]"
                title={'REQUEST PAYOUTS'}
                onClose={onClose}>

            {confirmData && (<ConfirmRequestPanel
                submitForm={submitForm}
                payload={confirmData}
                methodTypeSelected={methodTypeSelected}
                goBack={goBack}
            />)}

            {!confirmData && (
                <>
                    <div className="mb-8">
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Payment method
                            <TabButtonGroup selection={methodTypeSelected}
                                            onClick={(value: PaymentMethodType) => setMethodTypeSelected(value)}/>
                        </label>
                    </div>

                </>
            )}

            {!confirmData && methodTypeSelected === PayoutMethod.RISEWORKS && (
                <RiseworksForm onClose={onClose}
                               showConfirmRequestDialog={showConfirmRequestDialog}
                />
            )}

            {!confirmData && methodTypeSelected === PayoutMethod.CRYPTO_BTC && (
                <CryptoForm onClose={onClose}
                            network={'BTC'}
                            showConfirmRequestDialog={showConfirmRequestDialog}
                />
            )}

            {!confirmData && methodTypeSelected === PayoutMethod.CRYPTO_ETH && (
                <CryptoForm onClose={onClose}
                            network={'ETH'}
                            showConfirmRequestDialog={showConfirmRequestDialog}
                />
            )}
        </Dialog>
    );
}

export default RequestPayoutsModal;