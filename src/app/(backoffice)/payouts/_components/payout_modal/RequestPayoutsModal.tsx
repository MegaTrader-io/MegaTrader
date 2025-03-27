import React, {useRef, useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import Dialog from "@/components/Dialog";
import clsx from "clsx";
import IconPaymentMethod from "@/app/(backoffice)/payouts/_components/payout_modal/_components/IconPaymentMethod";
import TabButtonGroup from "@/app/(backoffice)/payouts/_components/payout_modal/_components/TabButtonGroup";

export interface IRequestPayoutForm {
    amount: number | undefined,
    paymentMethodType: string,
    email: string | undefined,
    address: string | undefined,
    fullName: string | undefined,
}

interface IPaymentMethod {
    id: string,
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
    const inputEmail = useRef<HTMLInputElement | null>(null);
    const [form, setForm] = useState<IRequestPayoutForm>({
        amount: undefined,
        email: undefined,
        address: undefined,
        fullName: undefined,
        paymentMethodType: 'riseworks'
    })
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const maxWithdrawal = 150;

    function changeFields(ev: React.ChangeEvent<HTMLInputElement>) {
        const {name, value} = ev.target;
        updateForm(name, value);
    }

    function updateForm(name: string, value: string) {
        setForm(prev => ({...prev, [name]: value}))
    }

    function validateFields() {
        const newErrors: { [key: string]: string } = {};
        if (!form.amount) newErrors.amount = "This field is required.";
        if (!form.email) newErrors.email = "This field is required.";
        if (!form.address) newErrors.address = "This field is required.";
        if (!form.fullName) newErrors.fullName = "This field is required.";

        if (form.amount && (form.amount < 0 || form.amount > maxWithdrawal)) newErrors.amount = "Invalid amount.";
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
        submitRequest(form)
    }

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh'}
                classNameOverlay={'bg-[#131210]'}
                className="w-[calc(100vw-32px)] sm:w-[700px]"
                title={'REQUEST PAYOUTS'}
                onClose={onClose}>
            <form noValidate={true} onSubmit={onSubmit} className="text-white w-full space-y-4">
                <div className="space-y-8">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Payment method
                            <TabButtonGroup selection={form.paymentMethodType}
                                            onClick={(value: string) => updateForm('paymentMethodType', value)}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Enter the amount you wish to withdraw
                            <InputText type={"text"}
                                       name='amount'
                                       placeholder="100"
                                       defaultValue={form.amount}
                                       onChange={changeFields}
                                       errorMessage={fieldErrors.amount}/>
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

                <div className="!mt-4 space-y-4 sm:space-y-0 sm:flex justify-center gap-2">
                    <Button onClick={onClose}
                            className="w-full"
                            styleType={'text'}
                            variant={'light'}>
                        CANCEL
                    </Button>
                    <Button type='submit' className="w-full">
                        CONTINUE
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}

export default RequestPayoutsModal;