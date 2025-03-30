import React, {useState} from 'react';
import Dialog from "@/components/Dialog";
import TabButtonGroup from "@/app/(backoffice)/payouts/_components/payout_modal/_components/TabButtonGroup";
import ConfirmRequestPanel from "@/app/(backoffice)/payouts/_components/payout_modal/_components/ConfirmRequestPanel";
import RiseworksForm from "@/app/(backoffice)/payouts/_components/payout_modal/forms/RiseworksForm";
import {
    IPaymentMethod,
    IRequestPayoutCryptoBTC, IRequestPayoutForm,
    IRequestPayoutRiseWorks,
    PaymentMethodType
} from "@/commons/interfaces";
import {PayoutMethod} from "@/commons/data";
import CryptoBTCForm from "@/app/(backoffice)/payouts/_components/payout_modal/forms/CryptoBTCForm";

export const PaymentMethodList: IPaymentMethod[] = [
    {id: 'riseworks', name: 'Riseworks'},
    {id: 'crypto_btc', name: 'Crypto - BTC'},
    {id: 'crypto_eth', name: 'Crypto - ETH'},
    {id: 'wire_ach', name: 'Wire / ACH'},
];

function RequestPayoutsModal({open, onClose, submitRequest}: {
    open: boolean,
    onClose: () => void,
    submitRequest: (form: IRequestPayoutForm) => void
}) {
    const [methodTypeSelected, setMethodTypeSelected] = useState<PaymentMethodType>(PayoutMethod.RISEWORKS);
    const [confirmData, setConfirmData] = useState<IRequestPayoutForm | null>(null);

    function showConfirmRequestDialog(form: IRequestPayoutRiseWorks | IRequestPayoutCryptoBTC) {
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
                classNameOverlay={'bg-[#131210]'}
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
                <CryptoBTCForm onClose={onClose}
                               network={'BTC'}
                               showConfirmRequestDialog={showConfirmRequestDialog}
                />
            )}

            {!confirmData && methodTypeSelected === PayoutMethod.CRYPTO_ETH && (
                <CryptoBTCForm onClose={onClose}
                               network={'ETH'}
                               showConfirmRequestDialog={showConfirmRequestDialog}
                />
            )}
        </Dialog>
    );
}

export default RequestPayoutsModal;