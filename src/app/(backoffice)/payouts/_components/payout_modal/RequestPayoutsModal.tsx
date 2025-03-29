import React, {useRef, useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import Dialog from "@/components/Dialog";
import TabButtonGroup from "@/app/(backoffice)/payouts/_components/payout_modal/_components/TabButtonGroup";
import ConfirmRequestPanel from "@/app/(backoffice)/payouts/_components/payout_modal/_components/ConfirmRequestPanel";

export type PaymentMethodType = 'riseworks' | 'crypto_btc' | 'crypto_eth' | 'wire_ach';

const TRANSACTION_PERCENTAGE = 0.08;
const MAX_WITHDRAWAL = 150;

export interface PayoutSummary {
    withdrawalAmount: number | undefined;
    transactionFee: number;
    netAmount: number;
}

export interface IRequestPayoutForm extends PayoutSummary {
    email: string | undefined,
    address: string | undefined,
    fullName: string | undefined,
}

interface IPaymentMethod {
    id: PaymentMethodType,
    name: string
}

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
    const [methodTypeSelected, setMethodTypeSelected] = useState<PaymentMethodType>('riseworks');
    const [confirmData, setConfirmData] = useState<IRequestPayoutForm | null>(null);
    const inputEmail = useRef<HTMLInputElement | null>(null);

    const [form, setForm] = useState<IRequestPayoutForm>({
        fullName: undefined,
        email: undefined,
        address: undefined,
        withdrawalAmount: undefined,
        netAmount: 0,
        transactionFee: 0,
    })

    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

    function changeFields(ev: React.ChangeEvent<HTMLInputElement>) {
        const {name, value} = ev.target;
        updateForm(name, value);
    }

    function calculateAmountToReceive(amount: number) {
        const transactionFee = amount * TRANSACTION_PERCENTAGE;
        return {transactionFee, netAmount: Math.max(0, amount - transactionFee)};
    }

    function updateForm(name: string, value: string | number) {
        setForm(prev => {
            const newData = {...prev, [name]: value}
            if (name === 'withdrawalAmount') {
                const {transactionFee, netAmount} = calculateAmountToReceive(Number(value));
                newData.netAmount = netAmount;
                newData.transactionFee = transactionFee;
            }

            return newData
        })
    }

    function validateFields() {
        const newErrors: { [key: string]: string } = {};
        if (!form.withdrawalAmount || !!form.withdrawalAmount && form.withdrawalAmount.toString().trim() === '') newErrors.withdrawalAmount = "This field is required.";
        if (!form.email) newErrors.email = "This field is required.";
        if (!form.address) newErrors.address = "This field is required.";
        if (!form.fullName) newErrors.fullName = "This field is required.";

        if (form.withdrawalAmount && (form.withdrawalAmount <= 0 || form.withdrawalAmount > MAX_WITHDRAWAL)) newErrors.withdrawalAmount = "Invalid amount.";
        if (form.email && inputEmail.current && !inputEmail.current.validity.valid) newErrors.email = "Invalid email address";

        return newErrors;
    }

    function onSubmit(ev: React.FormEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setFieldErrors(validationErrors);
            return;
        }

        setFieldErrors({});

        showConfirmRequestDialog()
    }

    function showConfirmRequestDialog() {
        setConfirmData(form);
    }

    function goBack() {
        setConfirmData(null);
    }

    function submitForm() {
        submitRequest(form)
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

                    <form noValidate={true} onSubmit={onSubmit} className="text-white w-full space-y-4">
                        <div className="space-y-8">
                            <div>
                                <label className="text-stone-400 text-base font-bold leading-normal">
                                    Enter the amount you wish to withdraw
                                    <InputText type={"text"}
                                               name='withdrawalAmount'
                                               placeholder="100"
                                               defaultValue={form.withdrawalAmount}
                                               onChange={changeFields}
                                               errorMessage={fieldErrors.withdrawalAmount}/>
                                </label>
                                <span
                                    className="self-stretch text-stone-400 justify-start text-sm font-medium leading-tight">Max
                        withdrawal: $150
                    </span>
                            </div>
                        </div>

                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                Email
                                <InputText
                                    ref={inputEmail}
                                    type={"email"}
                                    name='email'
                                    defaultValue={form.email}
                                    onChange={changeFields}
                                    errorMessage={fieldErrors.email}/>
                            </label>
                        </div>

                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                Full Name or Business Name
                                <InputText type={"text"}
                                           name='fullName'
                                           defaultValue={form.fullName}
                                           onChange={changeFields}
                                           errorMessage={fieldErrors.fullName}/>
                            </label>
                        </div>

                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                Address
                                <InputText type={"text"}
                                           name='address'
                                           defaultValue={form.address}
                                           onChange={changeFields}
                                           errorMessage={fieldErrors.address}/>
                            </label>
                        </div>

                        <div
                            className="flex flex-col !mt-4 space-y-4 sm:space-y-0 sm:flex sm:flex-row justify-center gap-2">
                            <Button onClick={onClose}
                                    className="w-full order-2 sm:order-1"
                                    styleType={'text'}
                                    variant={'light'}>
                                CANCEL
                            </Button>
                            <Button type='submit' className="w-full order-1 sm:order-2">
                                CONTINUE
                            </Button>
                        </div>
                    </form>
                </>
            )}

        </Dialog>
    );
}

export default RequestPayoutsModal;