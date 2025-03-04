import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import Dialog from "@/components/Dialog";
import {Radio, RadioGroup} from '@headlessui/react'
import clsx from "clsx";
import Alert from "@/components/Alert";

export interface IRequestPayoutForm {
    amount: number | undefined,
    paymentMethodType: string
}

interface IPaymentMethod {
    key: string,
    name: string
}

const PaymentMethodList: IPaymentMethod[] = [
    {key: 'crypto_btc', name: 'Crypto - BTC'},
    {key: 'crypto_eth', name: 'Crypto - ETH'},
    {key: 'crypto_usdc_erc20', name: 'Crypto - USDC-ERC20'},
    {key: 'wire_ach', name: 'Wire/ACH'},
    {key: 'riseworks', name: 'Riseworks'},
];

function RequestPayoutsModal({open, onClose, submitRequest}: {
    open: boolean,
    onClose: () => void,
    submitRequest: (form: IRequestPayoutForm) => void
}) {
    const [form, setForm] = useState<IRequestPayoutForm>({
        amount: undefined,
        paymentMethodType: 'crypto_btc'
    })
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

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
                className="w-[calc(100vw-32px)] sm:w-[600px]"
                title={'REQUEST PAYOUTS'}
                onClose={onClose}>
            {Object.keys(fieldErrors).length > 0 && (
                <Alert className={'text-black w-full mb-4'}
                       type={'error'}
                       message={'You must select, at lease, one of the options in Payment method'}/>
            )}
            <form onSubmit={onSubmit} className="text-white w-full space-y-8">
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
                </div>

                <div className="text-white text-xl font-medium uppercase leading-normal">
                    MAX WITHDRAWAL: $150
                </div>

                <div>
                    <label className="text-stone-400 text-base font-bold leading-normal space-y-2">
                        <span>
                            Payment method
                        </span>

                        <RadioGroup value={form.paymentMethodType}
                                    onChange={(value: string) => updateForm('paymentMethodType', value)}
                                    className="flex flex-col space-y-4">
                            {PaymentMethodList.map((option) => (
                                <Radio
                                    key={option.key}
                                    value={option.key}
                                    aria-label={option.name}
                                    className="flex cursor-pointer items-center space-x-2"
                                >
            <span className={clsx(
                "relative flex items-center justify-center size-6 rounded-full border-2 border-primary",
                option.key === form.paymentMethodType ? "bg-black" : "bg-transparent"
            )}>
                {option.key === form.paymentMethodType && (
                    <span className="size-5 bg-primary rounded-full border-black border-4"></span>
                )}
            </span>

                                    <span className="text-white text-lg font-medium">{option.name}</span>
                                </Radio>
                            ))}
                        </RadioGroup>

                    </label>
                </div>
                <div className="!mt-4 space-y-4 sm:space-y-0 sm:flex justify-center gap-2">
                    <Button type='submit' className="w-full">
                        CONFIRM
                    </Button>
                    <Button onClick={onClose}
                            className="w-full"
                            variant={'dark'}>
                        CANCEL
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}

export default RequestPayoutsModal;